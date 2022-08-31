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

use Fisharebest\Webtrees\DB\Schema\ColumnInterface;
use Fisharebest\Webtrees\DB\Schema\ForeignKey;
use Fisharebest\Webtrees\DB\Schema\Index;
use Fisharebest\Webtrees\DB\Schema\PrimaryKey;
use Fisharebest\Webtrees\DB\Schema\Schema;
use Fisharebest\Webtrees\DB\Schema\Table;
use Fisharebest\Webtrees\DB\Schema\UniqueIndex;

interface DriverInterface
{
    /**
     * Create an array of DDL statements to update the current database to match the specification
     *
     * @param Schema $schema
     *
     * @return array<string>
     */
    public function diffSchema(Schema $schema): array;

    /**
     * @return array<string>
     */
    public function listTables(): array;

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    public function listColumns(string $table_name): array;

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    public function listPrimaryKeys(string $table_name): array;

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    public function listUniqueIndexes(string $table_name): array;

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    public function listIndexes(string $table_name): array;

    /**
     * @param string $table_name
     *
     * @return array<string>
     */
    public function listForeignKeys(string $table_name): array;

    /**
     * @return Schema
     */
    public function introspectSchema(): Schema;

    /**
     * @param string $table_name
     *
     * @return Table
     */
    public function introspectTable(string $table_name): Table;

    /**
     * @param string $table_name
     * @param string $column_name
     *
     * @return ColumnInterface
     */
    public function introspectColumn(string $table_name, string $column_name): ColumnInterface;

    /**
     * @param string $table_name
     * @param string $key_name
     *
     * @return PrimaryKey
     */
    public function introspectPrimaryKey(string $table_name, string $key_name): PrimaryKey;

    /**
     * @param string $table_name
     * @param string $key_name
     *
     * @return UniqueIndex
     */
    public function introspectUniqueIndex(string $table_name, string $key_name): UniqueIndex;

    /**
     * @param string $table_name
     * @param string $key_name
     *
     * @return Index
     */
    public function introspectIndex(string $table_name, string $key_name): Index;

    /**
     * @param string $table_name
     * @param string $key_name
     *
     * @return ForeignKey
     */
    public function introspectForeignKey(string $table_name, string $key_name): ForeignKey;
}
