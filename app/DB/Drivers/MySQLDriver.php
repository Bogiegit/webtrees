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
use Fisharebest\Webtrees\DB\Schema\BinaryColumn;
use Fisharebest\Webtrees\DB\Schema\BlobColumn;
use Fisharebest\Webtrees\DB\Schema\CharacterColumn;
use Fisharebest\Webtrees\DB\Schema\ColumnInterface;
use Fisharebest\Webtrees\DB\Schema\EnumColumn;
use Fisharebest\Webtrees\DB\Schema\FloatColumn;
use Fisharebest\Webtrees\DB\Schema\ForeignKey;
use Fisharebest\Webtrees\DB\Schema\Index;
use Fisharebest\Webtrees\DB\Schema\IntegerColumn;
use Fisharebest\Webtrees\DB\Schema\PrimaryKey;
use Fisharebest\Webtrees\DB\Schema\ReferentialAction;
use Fisharebest\Webtrees\DB\Schema\Schema;
use Fisharebest\Webtrees\DB\Schema\Table;
use Fisharebest\Webtrees\DB\Schema\TextColumn;
use Fisharebest\Webtrees\DB\Schema\TimestampColumn;
use Fisharebest\Webtrees\DB\Schema\UniqueIndex;
use Fisharebest\Webtrees\DB\Schema\UuidColumn;
use LogicException;

use function array_diff_key;
use function array_filter;
use function array_keys;
use function array_map;
use function get_class;
use function implode;
use function is_numeric;
use function is_string;
use function preg_match_all;
use function str_contains;
use function strlen;

/**
 * Driver for MySQL
 */
class MySQLDriver extends AbstractDriver implements DriverInterface
{
    protected const IDENTIFIER_OPEN_QUOTE  = '`';
    protected const IDENTIFIER_CLOSE_QUOTE = '`';

    private const INTEGER_TYPES = [
        8  => 'TINYINT',
        16 => 'SMALLINT',
        24 => 'MEDIUMINT',
        32 => 'INT',
        64 => 'BIGINT',
    ];

    private const TEXT_TYPES = [
        1 => 'TINYTEXT',
        2 => 'SMALLTEXT',
        3 => 'MEDIUMTEXT',
        4 => 'TEXT',
    ];

    private const BLOB_TYPES = [
        1 => 'TINYBLOB',
        2 => 'SMALLBLOB',
        3 => 'MEDIUMBLOB',
        4 => 'BLOB',
    ];

    /**
     * @return array<string>
     */
    public function listTables(): array
    {
        $sql =
            'SELECT    TABLE_NAME' .
            ' FROM     INFORMATION_SCHEMA.TABLES' .
            " WHERE    TABLE_TYPE = 'BASE TABLE' AND TABLE_SCHEMA = DATABASE()" .
            ' ORDER BY TABLE_NAME';

        $data = $this->query(sql: $sql);

        $data = array_filter($data, fn (object $datum): bool => str_starts_with($datum->TABLE_NAME, $this->prefix));

        return array_map(fn (object $datum): string => substr($datum->TABLE_NAME, strlen($this->prefix)), $data);
    }

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    public function listColumns(string $table_name): array
    {
        $sql =
            'SELECT    COLUMN_NAME' .
            ' FROM     INFORMATION_SCHEMA.COLUMNS' .
            ' WHERE    TABLE_SCHEMA = DATABASE()' .
            '   AND    TABLE_NAME = :table_name' .
            ' ORDER BY ORDINAL_POSITION';

        $data = $this->query(sql: $sql, bindings: ['table_name' => $table_name]);

        return array_map(static fn (object $datum): string => $datum->COLUMN_NAME, $data);
    }

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    public function listPrimaryKeys(string $table_name): array
    {
        $sql =
            'SELECT    CONSTRAINT_NAME' .
            ' FROM     INFORMATION_SCHEMA.TABLE_CONSTRAINTS' .
            ' WHERE    TABLE_SCHEMA = DATABASE()' .
            '   AND    TABLE_NAME = :table_name' .
            '   AND    CONSTRAINT_TYPE = :constraint_type' .
            ' ORDER BY CONSTRAINT_NAME';

        $data = $this->query(sql: $sql, bindings: ['table_name' => $table_name, 'constraint_type' => 'PRIMARY KEY']);

        return array_map(static fn (object $datum): string => $datum->CONSTRAINT_NAME, $data);
    }

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    public function listUniqueIndexes(string $table_name): array
    {
<<<<<<< HEAD
        return [];
=======
        $sql =
            'SELECT    CONSTRAINT_NAME' .
            ' FROM     INFORMATION_SCHEMA.TABLE_CONSTRAINTS' .
            ' WHERE    TABLE_SCHEMA = DATABASE()' .
            '   AND    TABLE_NAME = :table_name' .
            '   AND    CONSTRAINT_TYPE = :constraint_type' .
            ' ORDER BY CONSTRAINT_NAME';

        $data = $this->query(sql: $sql, bindings: ['table_name' => $table_name, 'constraint_type' => 'UNIQUE']);

        return array_map(static fn (object $datum): string => $datum->CONSTRAINT_NAME, $data);
>>>>>>> d90173f7b5 (Create a new database abstraction layer)
    }

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    public function listIndexes(string $table_name): array
    {
<<<<<<< HEAD
        return [];
=======
        $sql =
            'SELECT     DISTINCT STATISTICS.INDEX_NAME' .
            ' FROM      INFORMATION_SCHEMA.STATISTICS' .
            ' LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS' .
            '        ON TABLE_CONSTRAINTS.TABLE_SCHEMA    = STATISTICS.TABLE_SCHEMA' .
            '       AND TABLE_CONSTRAINTS.TABLE_NAME      = STATISTICS.TABLE_NAME' .
            '       AND TABLE_CONSTRAINTS.CONSTRAINT_NAME = STATISTICS.INDEX_NAME' .
            ' WHERE     TABLE_CONSTRAINTS.CONSTRAINT_NAME IS NULL' .
            '   AND     STATISTICS.TABLE_SCHEMA = DATABASE()' .
            '   AND     STATISTICS.TABLE_NAME   = :table_name' .
            ' ORDER BY  INDEX_NAME';

        $data = $this->query(sql: $sql, bindings: ['table_name' => $table_name]);

        return array_map(static fn (object $datum): string => $datum->INDEX_NAME, $data);
>>>>>>> d90173f7b5 (Create a new database abstraction layer)
    }

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    public function listForeignKeys(string $table_name): array
    {
<<<<<<< HEAD
        return [];
    }

    /**
     * @param string $column
     * @param string $definition
     *
     * @return string
     */
    protected function addColumnSQL(string $column, string $definition): string
    {
        return 'ADD COLUMN ' . $this->quoteIdentifier($column) . ' ' . $definition;
=======
        $sql =
            'SELECT    CONSTRAINT_NAME' .
            ' FROM     INFORMATION_SCHEMA.TABLE_CONSTRAINTS' .
            ' WHERE    TABLE_SCHEMA = DATABASE()' .
            '   AND    TABLE_NAME = :table_name' .
            '   AND    CONSTRAINT_TYPE = :constraint_type' .
            ' ORDER BY CONSTRAINT_NAME';

        $data = $this->query(sql: $sql, bindings: ['table_name' => $table_name, 'constraint_type' => 'FOREIGN KEY']);

        return array_map(static fn (object $datum): string => $datum->CONSTRAINT_NAME, $data);
    }

    /**
     * @param ColumnInterface $column
     *
     * @return string
     */
    public function addColumnSQL(ColumnInterface $column): string
    {
        return 'ADD COLUMN ' . $this->columnSql($column);
>>>>>>> d90173f7b5 (Create a new database abstraction layer)
    }

    /**
     * @param string $column
     *
     * @return string
     */
<<<<<<< HEAD
    protected function dropColumnSQL(string $column): string
=======
    public function dropColumnSQL(string $column): string
>>>>>>> d90173f7b5 (Create a new database abstraction layer)
    {
        return 'DROP COLUMN ' . $this->quoteIdentifier($column);
    }

    /**
     * @param string $column
     * @param string $definition
     *
     * @return string
     */
<<<<<<< HEAD
    protected function alterColumnSQL(string $column, string $definition): string
    {
        return 'CHANGE COLUMN ' . $this->quoteIdentifier($column) . ' ' . $this->quoteIdentifier($column) . ' ' . $definition;
=======
    public function alterColumnSQL(string $column, string $definition): string
    {
        return 'CHANGE COLUMN ' . $this->quoteIdentifier($column) . ' ' . $definition;
>>>>>>> d90173f7b5 (Create a new database abstraction layer)
    }

    /**
     * @param Table $table
     *
     * @return array<string>
     */
    public function generateTableSql(Table $table): array
    {
        $table_name = $this->prefix . $table->getName();

        $components = [
            ...array_map($this->columnSql(...), $table->getColumns()),
<<<<<<< HEAD
            ...array_map($this->primaryKeySqlFromSchema(...), $table->getPrimaryKeys()),
            ...array_map($this->indexSqlFromDefintion(...), $table->getIndexes()),
            ...array_map($this->uniqueIndexSqlFromSchema(...), $table->getUniqueIndexes()),
=======
            ...array_map($this->primaryKeySql(...), $table->getPrimaryKeys()),
            ...array_map($this->uniqueIndexSql(...), $table->getUniqueIndexes()),
            ...array_map($this->indexSql(...), $table->getIndexes()),
>>>>>>> d90173f7b5 (Create a new database abstraction layer)
        ];

        return [
            'CREATE TABLE ' . $this->quoteIdentifier($table_name) . ' (' . implode(', ', $components) . ')',
        ];
    }

    /**
     * @param ColumnInterface $column
     *
     * @return string
     */
<<<<<<< HEAD
    protected function columnSql(ColumnInterface $column): string
=======
    public function columnSql(ColumnInterface $column): string
>>>>>>> d90173f7b5 (Create a new database abstraction layer)
    {
        $sql = $this->quoteIdentifier($column->getName()) . ' ';

        if ($column instanceof BinaryColumn) {
            $sql .= $column->varying ? 'VARBINARY' : 'BINARY';
            $sql .= '(' . $column->length . ')';
        } elseif ($column instanceof BlobColumn) {
            $sql .= self::BLOB_TYPES[$column->length];
        } elseif ($column instanceof CharacterColumn) {
            $collation = $column->collation ?? $column->national ? $this->utf8Charset() : 'ascii_bin';
            $sql       .= $column->varying ? 'VARCHAR' : 'CHAR';
            $sql       .= '(' . $column->length . ') COLLATE ' . $collation;
        } elseif ($column instanceof FloatColumn) {
            $sql .= $column->precision_bits > 23 ? 'DOUBLE' : 'FLOAT';
        } elseif ($column instanceof IntegerColumn) {
            $sql .= self::INTEGER_TYPES[$column->bits];
            $sql .= $column->auto_increment ? ' AUTO_INCREMENT' : '';
        } elseif ($column instanceof TextColumn) {
            $sql .= self::TEXT_TYPES[$column->length] . ' COLLATE ' . $this->utf8Charset();
        } elseif ($column instanceof TimestampColumn) {
            $sql .= 'TIMESTAMP';
            $sql .= $column->precision === 0 ? '' : '(' . $column->precision . ')';
        } elseif ($column instanceof UuidColumn) {
            $sql .= 'CHAR(36) COLLATE ascii_bin';
        } elseif ($column instanceof EnumColumn) {
            $sql .= 'ENUM(' . implode(',', array_map(self::quoteValue(...), $column->values())) . ')';
        } else {
            throw new LogicException('Driver ' . self::class . ' has no definition for ' . get_class($column));
        }

        if ($column->isNullable()) {
            $sql .= ' NULL';
        }

        if ($column->getDefault() instanceof Expression || is_numeric($column->getDefault())) {
            $sql .= ' DEFAULT ' . $column->getDefault();
        } elseif (is_string($column->getDefault())) {
            $sql .= ' DEFAULT ' . $this->quoteValue($column->getDefault());
        }

        if ($column->isInvisible()) {
            $sql .= ' /*!80023 INVISIBLE */';
        }

        if ($column->getComment() !== '') {
            $sql .= ' COMMENT ' . $this->quoteValue($column->getComment());
        }

        return $sql;
    }

    /**
     * @param PrimaryKey $primary_key
     *
     * @return string
     */
<<<<<<< HEAD
    protected function primaryKeySqlFromSchema(PrimaryKey $primary_key): string
=======
    protected function primaryKeySql(PrimaryKey $primary_key): string
>>>>>>> d90173f7b5 (Create a new database abstraction layer)
    {
        return
            'PRIMARY KEY (' .
            implode(', ', array_map($this->quoteIdentifier(...), $primary_key->columns())) .
            ')';
    }

    /**
     * @param UniqueIndex $unique_index
     *
     * @return string
     */
<<<<<<< HEAD
    protected function uniqueIndexSqlFromSchema(UniqueIndex $unique_index): string
=======
    protected function uniqueIndexSql(UniqueIndex $unique_index): string
>>>>>>> d90173f7b5 (Create a new database abstraction layer)
    {
        return
            'UNIQUE INDEX ' .
            $this->quoteIdentifier($unique_index->name) .
            ' (' .
            implode(', ', array_map($this->quoteIdentifier(...), $unique_index->columns())) .
            ')';
    }

    /**
     * @param Index $index
     *
     * @return string
     */
<<<<<<< HEAD
    protected function indexSqlFromDefintion(Index $index): string
=======
    protected function indexSql(Index $index): string
>>>>>>> d90173f7b5 (Create a new database abstraction layer)
    {
        return
            'INDEX ' .
            $this->quoteIdentifier($index->name) .
            ' (' .
            implode(', ', array_map($this->quoteIdentifier(...), $index->columns())) .
            ')';
    }

    /**
     * @param string $table_name
     * @param string $column_name
     *
     * @return ColumnInterface
     * @throws SchemaException
     */
    public function introspectColumn(string $table_name, string $column_name): ColumnInterface
    {
        $sql =
            'SELECT    *' .
            ' FROM     INFORMATION_SCHEMA.COLUMNS' .
            ' WHERE    TABLE_SCHEMA = DATABASE()' .
            '   AND    TABLE_NAME = :table_name' .
            ' ORDER BY ORDINAL_POSITION';

        $data = $this->query(sql: $sql, bindings: ['table_name' => $table_name])[0];

        $auto_increment = str_contains($data->EXTRA, 'auto_increment');
        $unsigned       = str_contains($data->COLUMN_TYPE, 'unsigned');

        if ($data->COLUMN_DEFAULT === null || is_numeric($data->COLUMN_DEFAULT)) {
            $default = $data->COLUMN_DEFAULT;
        } elseif ($data->DATA_TYPE === 'timestamp' && str_starts_with($data->COLUMN_DEFAULT, 'CURRENT_TIMESTAMP')) {
            $default = new Expression($data->COLUMN_DEFAULT);
        } else {
            $default = $this->quoteValue($data->COLUMN_DEFAULT);
        }

<<<<<<< HEAD
        if (preg_match_all("/'(([^']|'')*)'/", $data->COLUMN_TYPE, $matches)) {
=======
        if (preg_match_all("/'((?:[^']|'')*)'/", $data->COLUMN_TYPE, $matches)) {
>>>>>>> d90173f7b5 (Create a new database abstraction layer)
            $items[] = $matches[1];
        } else {
            $items = [];
        }

        $column = match ($data->DATA_TYPE) {
            'bigint'     => Schema::bigInteger($data->COLUMN_NAME)->autoIncrement($auto_increment)->unsigned($unsigned),
            'binary'     => Schema::binary($data->COLUMN_NAME, $data->CHARACTER_MAXIMUM_LENGTH),
            'blob'       => Schema::blob($data->COLUMN_NAME, 2),
            'char'       => str_starts_with($data->COLLATION, 'utf') ?
                Schema::nChar($data->COLUMN_NAME, $data->CHARACTER_MAXIMUM_LENGTH)->collation($data->COLLATION_NAME) :
                Schema::char($data->COLUMN_NAME, $data->CHARACTER_MAXIMUM_LENGTH)->collation($data->COLLATION_NAME),
            'date'       => Schema::date($data->COLUMN_NAME),
            'datetime'   => Schema::datetime($data->COLUMN_NAME, $data->DATETIME_PRECISION),
            'decimal'    => Schema::decimal($data->COLUMN_NAME, $data->NUMERIC_PRECISION, $data->NUMERIC_SCALE),
            'double'     => Schema::double($data->COLUMN_NAME),
            'enum'       => Schema::enum($data->COLUMN_NAME, $items),
            'float'      => Schema::float($data->COLUMN_NAME),
            'geometry'   => Schema::geometry($data->COLUMN_NAME),
            'int'        => Schema::integer($data->COLUMN_NAME)->autoIncrement($auto_increment)->unsigned($unsigned),
            'json'       => Schema::json($data->COLUMN_NAME),
            'longblob'   => Schema::blob($data->COLUMN_NAME, 4),
            'longtext'   => Schema::text($data->COLUMN_NAME, 4),
            'mediumblob' => Schema::blob($data->COLUMN_NAME, 3),
            'mediumint'  => Schema::mediumInteger($data->COLUMN_NAME)->autoIncrement($auto_increment)->unsigned($unsigned),
            'mediumtext' => Schema::text($data->COLUMN_NAME, 3),
            'point'      => Schema::point($data->COLUMN_NAME),
            'set'        => Schema::set($data->COLUMN_NAME, $items),
            'smallint'   => Schema::smallInteger($data->COLUMN_NAME)->autoIncrement($auto_increment)->unsigned($unsigned),
            'text'       => Schema::text($data->COLUMN_NAME, 2),
            'time'       => Schema::time($data->COLUMN_NAME),
            'timestamp'  => Schema::timestamp($data->COLUMN_NAME),
            'tinyblob'   => Schema::blob($data->COLUMN_NAME, 1),
            'tinyint'    => Schema::tinyInteger($data->COLUMN_NAME)->autoIncrement($auto_increment)->unsigned($unsigned),
            'tinytext'   => Schema::text($data->COLUMN_NAME, 1),
            'varbinary'  => Schema::varBinary($data->COLUMN_NAME, $data->CHARACTER_MAXIMUM_LENGTH),
            'varchar'    => str_starts_with($data->COLLATION, 'utf') ?
                Schema::nVarchar($data->COLUMN_NAME, $data->CHARACTER_MAXIMUM_LENGTH)->collation($data->COLLATION_NAME) :
                Schema::varchar($data->COLUMN_NAME, $data->CHARACTER_MAXIMUM_LENGTH)->collation($data->COLLATION_NAME),
            default      => throw new SchemaException('INFORMATION_SCHEMA.COLUMNS.DATA_TYPE: ' . $data->DATA_TYPE),
        };

        return $column
            ->nullable($data->IS_NULLABLE === 'YES')
            ->default($default)
            ->invisible(str_contains($data->EXTRA, 'INVISIBLE'))
            ->comment($data->COLUMN_COMMENT);
    }

    /**
     * @param string $table_name
     * @param string $key_name
     *
     * @return PrimaryKey
     */
    public function introspectPrimaryKey(string $table_name, string $key_name): PrimaryKey
    {
        $sql =
            'SELECT    COLUMN_NAME' .
            ' FROM     INFORMATION_SCHEMA.KEY_COLUMN_USAGE' .
            ' WHERE    TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name AND CONSTRAINT_NAME = :key_name' .
            ' ORDER BY ORDINAL_POSITION';

        $data = $this->query(sql: $sql, bindings: ['table' => $this->prefix . $table_name, 'constraint' => $key_name]);

        $columns = array_map(static fn (object $datum): string => $datum->COLUMN_NAME, $data);

        return Schema::primaryKey($columns, $key_name);
    }

    /**
     * @param string $table_name
     * @param string $key_name
     *
     * @return UniqueIndex
     */
    public function introspectUniqueIndex(string $table_name, string $key_name): UniqueIndex
    {
        $sql =
            'SELECT    COLUMN_NAME' .
            ' FROM     INFORMATION_SCHEMA.KEY_COLUMN_USAGE' .
            ' WHERE    TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name AND CONSTRAINT_NAME = :key_name' .
            ' ORDER BY ORDINAL_POSITION';

        $data = $this->query(sql: $sql, bindings: ['table' => $this->prefix . $table_name, 'constraint' => $key_name]);

        $columns = array_map(static fn (object $datum): string => $datum->COLUMN_NAME, $data);

        return Schema::uniqueIndex($columns, $key_name);
    }

    /**
     * @param string $table_name
     * @param string $key_name
     *
     * @return Index
     */
    public function introspectIndex(string $table_name, string $key_name): Index
    {
        $sql =
            'SELECT    COLUMN_NAME' .
            ' FROM     INFORMATION_SCHEMA.KEY_COLUMN_USAGE' .
            ' WHERE    TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name AND CONSTRAINT_NAME = :key_name' .
            ' ORDER BY ORDINAL_POSITION';

        $data = $this->query(sql: $sql, bindings: ['table' => $this->prefix . $table_name, 'constraint' => $key_name]);

        $columns = array_map(static fn (object $datum): string => $datum->COLUMN_NAME, $data);

        return Schema::index($columns, $key_name);
    }

    /**
     * @param string $table_name
     * @param string $key_name
     *
     * @return ForeignKey
     */
    public function introspectForeignKey(string $table_name, string $key_name): ForeignKey
    {
        $sql =
            'SELECT    COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME' .
            ' FROM     INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS' .
            ' WHERE    CONSTRAINT_SCHEMA = DATABASE()' .
            '   AND    TABLE_NAME        = :table_name' .
            '   AND    CONSTRAINT_NAME   = :key_name' .
            ' ORDER BY ORDINAL_POSITION';

        $data = $this->query(sql: $sql, bindings: ['table' => $this->prefix . $table_name, 'constraint' => $key_name]);

        $local_columns   = array_map(static fn (object $datum): string => $datum->COLUMN_NAME, $data);
        $foreign_columns = array_map(static fn (object $datum): string => $datum->REFERENCED_COLUMN_NAME, $data);

        $foreign_table = $data[0]->REFERENCED_TABLE_NAME;
        $on_update     = $data[0]->UPDATE_ACTION;
        $on_delete     = $data[0]->DELETE_ACTION;

        return Schema::foreignKey($local_columns, $foreign_table, $foreign_columns, $key_name)
            ->onUpdate(ReferentialAction::from($on_update))
            ->onDelete(ReferentialAction::from($on_delete));
    }

    /**
     * Does this database support utf8mb4 or utf8mb3?
     *
     * @return string
     */
    private function utf8Charset(): string
    {
        // MariaDB 10.2 and later
        if (version_compare($this->server_version, '10.2') >= 0) {
            return 'utf8mb4_bin';
        }

        // MySQL 5.7 and 8.0
        if (version_compare($this->server_version, '5.7') >= 0 && version_compare($this->server_version, '10.0') < 0) {
            return 'utf8mb4_bin';
        }

        return 'utf8mb3_bin';
    }
}
