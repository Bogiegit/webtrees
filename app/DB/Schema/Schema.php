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

namespace Fisharebest\Webtrees\DB\Schema;

use function strcmp;
use function uasort;

/**
 * A collection tables, and factory methods for creating them.
 */
class Schema
{
    /** @var array<Table> */
    private array $tables = [];

    /**
     * @param array<Table> $tables
     */
    public function __construct(array $tables = []) {
        foreach ($tables as $table) {
            $this->tables[$table->getName()] = $table;
        }

        ksort($this->tables);
    }

    /**
     * @return array<Table>
     */
    public function getTables(): array
    {
        return $this->tables;
    }

    /**
     * @param string $table_name
     *
     * @return Table|null
     */
    public function getTable(string $table_name): ?Table
    {
        foreach ($this->tables as $table) {
            if ($table->getName() === $table_name) {
                return $table;
            }
        }

        return null;
    }

    /**
     * @param string                                                         $name
     * @param array<ColumnInterface|PrimaryKey|UniqueIndex|Index|ForeignKey> $components
     *
     * @return Table
     */
    public static function table(string $name, array $components): Table
    {
        return new Table(name: $name, components: $components);
    }

    /**
     * @param string $name
     *
     * @return IntegerColumn
     */
    public static function bigInteger(string $name): IntegerColumn
    {
        return new IntegerColumn(name: $name, bits: 64);
    }

    /**
     * @param string $name
     * @param int    $length
     *
     * @return BinaryColumn
     */
    public static function binary(string $name, int $length): BinaryColumn
    {
        return new BinaryColumn(name: $name, length: $length, varying: false);
    }

    /**
     * @param string $name
     * @param int    $bits
     *
     * @return IntegerColumn
     */
    public static function bit(string $name, int $bits = 1): IntegerColumn
    {
        return new IntegerColumn(name: $name, bits: $bits);
    }

    /**
     * @param string $name
     * @param int    $length
     *
     * @return BlobColumn
     */
    public static function blob(string $name, int $length = 4): BlobColumn
    {
        return new BlobColumn(name: $name, length: $length);
    }

    /**
     * @param string $name
     *
     * @return BooleanColumn
     */
    public static function boolean(string $name): BooleanColumn
    {
        return new BooleanColumn(name: $name);
    }

    /**
     * @param string $name
     * @param int    $length
     *
     * @return CharacterColumn
     */
    public static function char(string $name, int $length): CharacterColumn
    {
        return new CharacterColumn(name: $name, length: $length, varying: false, national: false);
    }

    /**
     * @param string $name
     *
     * @return DateColumn
     */
    public static function date(string $name): DateColumn
    {
        return new DateColumn(name: $name);
    }

    /**
     * @param string $name
     * @param int    $length
     *
     * @return DatetimeColumn
     */
    public static function datetime(string $name, int $length = 0): DatetimeColumn
    {
        return new DatetimeColumn(name: $name, length: $length);
    }

    /**
     * @param string $name
     * @param int    $precision
     * @param int    $scale
     *
     * @return DecimalColumn
     */
    public static function decimal(string $name, int $precision, int $scale): DecimalColumn
    {
        return new DecimalColumn(name: $name, precision: $precision, scale: $scale);
    }

    /**
     * @param string $name
     * @param int    $precision_bits
     *
     * @return FloatColumn
     */
    public static function double(string $name, int $precision_bits = 53): FloatColumn
    {
        return new FloatColumn(name: $name, precision_bits: $precision_bits);
    }

    /**
     * @param string        $name
     * @param array<string> $values
     *
     * @return EnumColumn
     */
    public static function enum(string $name, array $values): EnumColumn
    {
        return new EnumColumn(name: $name, values: $values);
    }

    /**
     * @param string $name
     * @param int    $precision_bits
     *
     * @return FloatColumn
     */
    public static function float(string $name, int $precision_bits = 23): FloatColumn
    {
        return new FloatColumn(name: $name, precision_bits: $precision_bits);
    }

    /**
     * @param string $name
     * @param int    $srid
     *
     * @return GeometryColumn
     */
    public static function geometry(string $name, int $srid = 0): GeometryColumn
    {
        return new GeometryColumn(name: $name, srid: $srid);
    }

    /**
     * @param string $name
     * @param int    $srid
     *
     * @return GeometrycollectionColumn
     */
    public static function geometrycollection(string $name, int $srid = 0): GeometrycollectionColumn
    {
        return new GeometrycollectionColumn(name: $name, srid: $srid);
    }

    /**
     * @param string $name
     * @param int    $bits
     *
     * @return IntegerColumn
     */
    public static function integer(string $name, int $bits = 32): IntegerColumn
    {
        return new IntegerColumn(name: $name, bits: $bits);
    }

    /**
     * @param string $name
     *
     * @return JsonColumn
     */
    public static function json(string $name): JsonColumn
    {
        return new JsonColumn(name: $name);
    }

    /**
     * @param string $name
     * @param int    $srid
     *
     * @return LinestringColumn
     */
    public static function linestring(string $name, int $srid = 0): LinestringColumn
    {
        return new LinestringColumn(name: $name, srid: $srid);
    }

    /**
     * @param string $name
     *
     * @return IntegerColumn
     */
    public static function mediumInteger(string $name): IntegerColumn
    {
        return new IntegerColumn(name: $name, bits: 24);
    }

    /**
     * @param string $name
     * @param int    $srid
     *
     * @return MultilinestringColumn
     */
    public static function multilinestring(string $name, int $srid = 0): MultilinestringColumn
    {
        return new MultilinestringColumn(name: $name, srid: $srid);
    }

    /**
     * @param string $name
     * @param int    $srid
     *
     * @return MultipointColumn
     */
    public static function multipoint(string $name, int $srid = 0): MultipointColumn
    {
        return new MultipointColumn(name: $name, srid: $srid);
    }

    /**
     * @param string $name
     * @param int    $srid
     *
     * @return MultipolygonColumn
     */
    public static function multipolygon(string $name, int $srid = 0): MultipolygonColumn
    {
        return new MultipolygonColumn(name: $name, srid: $srid);
    }

    /**
     * @param string $name
     * @param int    $length
     *
     * @return CharacterColumn
     */
    public static function nChar(string $name, int $length): CharacterColumn
    {
        return new CharacterColumn(name: $name, length: $length, varying: false, national: true);
    }

    /**
     * @param string $name
     * @param int    $length
     *
     * @return CharacterColumn
     */
    public static function nVarchar(string $name, int $length): CharacterColumn
    {
        return new CharacterColumn(name: $name, length: $length, varying: true, national: true);
    }

    /**
     * @param string $name
     * @param int    $srid
     *
     * @return PointColumn
     */
    public static function point(string $name, int $srid = 0): PointColumn
    {
        return new PointColumn(name: $name, srid: $srid);
    }

    /**
     * @param string $name
     * @param int    $srid
     *
     * @return PolygonColumn
     */
    public static function polygon(string $name, int $srid = 0): PolygonColumn
    {
        return new PolygonColumn(name: $name, srid: $srid);
    }

    /**
     * @param string        $name
     * @param array<string> $values
     *
     * @return SetColumn
     */
    public static function set(string $name, array $values): SetColumn
    {
        return new SetColumn(name: $name, values: $values);
    }

    /**
     * @param string $name
     *
     * @return IntegerColumn
     */
    public static function smallInteger(string $name): IntegerColumn
    {
        return new IntegerColumn(name: $name, bits: 16);
    }

    /**
     * @param string $name
     * @param int    $length
     *
     * @return TextColumn
     */
    public static function text(string $name, int $length = 4): TextColumn
    {
        return new TextColumn(name: $name, length: $length);
    }

    /**
     * @param string $name
     *
     * @return TimeColumn
     */
    public static function time(string $name): TimeColumn
    {
        return new TimeColumn(name: $name);
    }

    /**
     * @param string $name
     * @param int    $length
     *
     * @return TimestampColumn
     */
    public static function timestamp(string $name, int $length = 0): TimestampColumn
    {
        return new TimestampColumn(name: $name, precision: $length);
    }

    /**
     * @param string $name
     *
     * @return IntegerColumn
     */
    public static function tinyInteger(string $name): IntegerColumn
    {
        return new IntegerColumn(name: $name, bits: 8);
    }

    /**
     * @param string $name
     *
     * @return UuidColumn
     */
    public static function uuid(string $name): UuidColumn
    {
        return new UuidColumn(name: $name);
    }

    /**
     * @param string $name
     * @param int    $length
     *
     * @return BinaryColumn
     */
    public static function varBinary(string $name, int $length): BinaryColumn
    {
        return new BinaryColumn(name: $name, length: $length, varying: true);
    }

    /**
     * @param string $name
     * @param int    $length
     *
     * @return CharacterColumn
     */
    public static function varchar(string $name, int $length): CharacterColumn
    {
        return new CharacterColumn(name: $name, length: $length, varying: true, national: false);
    }

    /**
     * @param string $name
     *
     * @return YearColumn
     */
    public static function year(string $name): YearColumn
    {
        return new YearColumn(name: $name);
    }

    /**
     * @param string|array<string> $columns
     * @param string               $name
     *
     * @return PrimaryKey
     */
    public static function primaryKey(string|array $columns, string $name = ''): PrimaryKey
    {
        return new PrimaryKey(name: $name, columns: (array) $columns);
    }

    /**
     * @param string|array<string> $columns
     * @param string               $name
     *
     * @return Index
     */
    public static function index(string|array $columns, string $name = ''): Index
    {
        return new Index(name: $name, columns: (array) $columns);
    }

    /**
     * @param string|array<string> $columns
     * @param string               $name
     *
     * @return UniqueIndex
     */
    public static function uniqueIndex(string|array $columns, string $name = ''): UniqueIndex
    {
        return new UniqueIndex(name: $name, columns: (array) $columns);
    }

    /**
     * @param string|array<string> $local_columns
     * @param string               $foreign_table
     * @param string|array<string> $foreign_columns
     * @param string               $name
     *
     * @return ForeignKey
     */
    public static function foreignKey(string|array $local_columns, string $foreign_table, string|array $foreign_columns = null, string $name = ''): ForeignKey
    {
        // If the foreign columns have the same name, we don't need to specify them.
        $foreign_columns ??= $local_columns;

        $local_columns   = (array) $local_columns;
        $foreign_columns = (array) $foreign_columns;

        return new ForeignKey(local_columns: $local_columns, foreign_table: $foreign_table, foreign_columns: $foreign_columns, name: $name);
    }
}
