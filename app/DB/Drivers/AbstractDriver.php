<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2023 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\DB\Drivers;

use Fisharebest\Webtrees\DB\Exceptions\SchemaException;
use Fisharebest\Webtrees\DB\Expression;
use Fisharebest\Webtrees\DB\Schema\ColumnInterface;
use Fisharebest\Webtrees\DB\Schema\ForeignKey;
use Fisharebest\Webtrees\DB\Schema\Index;
use Fisharebest\Webtrees\DB\Schema\PrimaryKey;
use Fisharebest\Webtrees\DB\Schema\Schema;
use Fisharebest\Webtrees\DB\Schema\Table;
use Fisharebest\Webtrees\DB\Schema\UniqueIndex;
use PDO;
use PDOException;
use RuntimeException;

use function array_map;
use function is_bool;
use function is_int;

/**
 * Common functionality for all drivers.
 */
abstract class AbstractDriver
{
    protected const IDENTIFIER_OPEN_QUOTE  = '"';
    protected const IDENTIFIER_CLOSE_QUOTE = '"';

    protected readonly string $server_version;

    /**
     * @param PDO    $pdo
     * @param string $prefix
     */
    public function __construct(protected readonly PDO $pdo, protected readonly string $prefix)
    {
        $this->server_version = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * @return array<string>
     */
    abstract public function listTables(): array;

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    abstract public function listColumns(string $table_name): array;

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    abstract public function listPrimaryKeys(string $table_name): array;

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    abstract public function listUniqueIndexes(string $table_name): array;

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    abstract public function listIndexes(string $table_name): array;

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    abstract public function listForeignKeys(string $table_name): array;

    /**
     * @return Schema
     */
    public function introspectSchema(): Schema
    {
        return new Schema(array_map($this->introspectTable(...), $this->listTables()));
    }

    /**
     * @param string $table_name
     *
     * @return Table
     * @throws SchemaException
     */
    public function introspectTable(string $table_name): Table
    {
        $components = [];

        foreach ($this->listColumns($table_name) as $column_name) {
            $components[] = $this->introspectColumn($table_name, $column_name);
        }

        foreach ($this->listPrimaryKeys($table_name) as $key_name) {
            $components[] = $this->introspectPrimaryKey($table_name, $key_name);
        }

        foreach ($this->listUniqueIndexes($table_name) as $key_name) {
            $components[] = $this->introspectUniqueIndex($table_name, $key_name);
        }

        foreach ($this->listIndexes($table_name) as $key_name) {
            $components[] = $this->introspectIndex($table_name, $key_name);
        }

        foreach ($this->listForeignKeys($table_name) as $key_name) {
            $components[] = $this->introspectForeignKey($table_name, $key_name);
        }

        return new Table($table_name, $components);
    }

    /**
     * @param string $table_name
     * @param string $column_name
     *
     * @return ColumnInterface
     */
    abstract public function introspectColumn(string $table_name, string $column_name): ColumnInterface;

    /**
     * @param string $table_name
     * @param string $key_name
     *
     * @return PrimaryKey
     */
    abstract public function introspectPrimaryKey(string $table_name, string $key_name): PrimaryKey;

    /**
     * @param string $table_name
     * @param string $key_name
     *
     * @return UniqueIndex
     */
    abstract public function introspectUniqueIndex(string $table_name, string $key_name): UniqueIndex;

    /**
     * @param string $table_name
     * @param string $key_name
     *
     * @return Index
     */
    abstract public function introspectIndex(string $table_name, string $key_name): Index;

    /**
     * @param string $table_name
     * @param string $key_name
     *
     * @return ForeignKey
     */
    abstract public function introspectForeignKey(string $table_name, string $key_name): ForeignKey;

    /**
     * @param string $identifier
     *
     * @return Expression
     */
    public function quoteIdentifier(string $identifier): Expression
    {
        $escaped = strtr($identifier, [static::IDENTIFIER_CLOSE_QUOTE => static::IDENTIFIER_CLOSE_QUOTE . static::IDENTIFIER_CLOSE_QUOTE]);

        return new Expression(static::IDENTIFIER_OPEN_QUOTE . $escaped . static::IDENTIFIER_CLOSE_QUOTE);
    }

    /**
     * For quoting strings in DDL statements which cannot use placeholders. e.g. COMMENT 'foo' and DEFAULT 'bar'.
     *
     * @param string $value
     *
     * @return Expression
     */
    public function quoteValue(string $value): Expression
    {
        return new Expression($this->pdo->quote($value));
    }
    
    /**
     * Prepare, bind and execute a select query.
     *
     * @param string                            $sql
     * @param array<bool|int|float|string|null> $bindings
     *
     * @return array<object>
     */
    public function query(string $sql, array $bindings = []): array
    {
        try {
            $statement = $this->pdo->prepare($sql);
        } catch (PDOException) {
            $statement = false;
        }

        if ($statement === false) {
            throw new RuntimeException('Failed to prepare statement: ' . $sql);
        }

        foreach ($bindings as $param => $value) {
            $type = match (true) {
                $value === null => PDO::PARAM_NULL,
                is_bool($value) => PDO::PARAM_BOOL,
                is_int($value)  => PDO::PARAM_INT,
                default         => PDO::PARAM_STR,
            };

            if (is_int($param)) {
                // Positional parameters are numeric, starting at 1.
                $statement->bindValue($param + 1, $value, $type);
            } else {
                // Named parameters are (optionally) prefixed with a colon.
                $statement->bindValue(':' . $param, $value, $type);
            }
        }

        if ($statement->execute()) {
            return $statement->fetchAll(PDO::FETCH_OBJ);
        }

        throw new RuntimeException('Failed to execute statement: ' . $sql);
    }
}
