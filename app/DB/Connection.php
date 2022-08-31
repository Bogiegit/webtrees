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

namespace Fisharebest\Webtrees\DB;

use DomainException;
use Fisharebest\Webtrees\DB\Drivers\DriverInterface;
use Fisharebest\Webtrees\DB\Drivers\MySQLDriver;
use Fisharebest\Webtrees\DB\Drivers\PostgreSQLDriver;
use Fisharebest\Webtrees\DB\Drivers\SQLiteDriver;
use Fisharebest\Webtrees\DB\Drivers\SQLServerDriver;
use Fisharebest\Webtrees\DB\Exceptions\SchemaException;
use Fisharebest\Webtrees\DB\Schema\Schema;
use Fisharebest\Webtrees\DB\Schema\Table;
use PDO;

use function array_diff_key;
use function array_intersect_key;
use function array_keys;
use function array_map;
use function implode;
use function in_array;

/**
 * Extend the PDO database connection to support prefixes and introspection.
 */
class Connection
{
    private DriverInterface $driver;

    /**
     * @param PDO    $pdo
     * @param string $prefix
     */
    public function __construct(private readonly PDO $pdo, private readonly string $prefix = '')
    {
        $driver_name = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        $this->driver = match ($driver_name) {
            'mysql'    => new MySQLDriver($pdo, $prefix),
            'postgres' => new PostgreSQLDriver($pdo, $prefix),
            'sqlite'   => new SQLiteDriver($pdo, $prefix),
            'sqlsrv'   => new SQLServerDriver($pdo, $prefix),
            default    => throw new DomainException('No driver available for ' . $driver_name),
        };
    }

    /**
     * @param Schema $target
     *
     * @return array<string>
     */
    public function diffSchema(Schema $target): array
    {
        $source = $this->driver->introspectSchema();

        // Collect SQL statements into three groups
        $drop_foreign_key_sql       = [];
        $create_and_alter_table_sql = [];
        $create_foreign_key_sql     = [];

        foreach ($target_schema->getTables() as $target_table) {
            $source_table = $source_schema->getTable($target_table->getName());

            if ($source_table === null) {
                // Table does not currently exist
                $create_and_alter_table_sql = [
                    ...$create_and_alter_table_sql,
                    ...$this->driver->generateTableSql($target_table),
                ];
            } else {
                // Table exists - check columns
                $drop_columns  = array_map(
                    $this->driver->dropColumnSQL(...),
                    array_keys(array_diff_key($source_table->getColumns(), $target_table->getColumns()))
                );
                $add_columns   = array_map(
                    $this->driver->addColumnSQL(...),
                    array_diff_key($target_table->getColumns(), $source_table->getColumns())
                );
                $alter_columns = array_map(
                    $this->driver->alterColumnSQL(...),
                    array_intersect_key($target_table->getColumns(), $source_table->getColumns())
                );

                $changes = [...$drop_columns, ...$alter_columns, ...$add_columns];

                if ($changes !== []) {
                    $alter_table = 'ALTER TABLE ' . $this->driver->quoteIdentifier(
                            $this->prefix . $target_table->getName()
                        ) . implode(', ', $changes);

                    $create_and_alter_table_sql = [...$create_and_alter_table_sql, $alter_table];
                }
            }
        }

        // Results need to be in this order
        return [...$drop_foreign_key_sql, ...$create_and_alter_table_sql, ...$create_foreign_key_sql];
    }

    /**
     * @param string $table_name
     *
     * @return bool
     */
    public function tableExists(string $table_name): bool
    {
        return in_array($this->prefix . $table_name, $this->driver->listTables(), true);
    }

    /**
     * @param string $table_name
     * @param string $column_name
     *
     * @return bool
     */
    public function columnExists(string $table_name, string $column_name): bool
    {
        return in_array($column_name, $this->driver->listColumns($this->prefix . $table_name), true);
    }
}
