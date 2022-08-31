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

/**
 *
 */
class Table
{
    /** @var array<ColumnInterface> */
    private array $columns;

    /** @var array<Index> */
    private array $indexes;

    /** @var array<UniqueIndex> */
    private array $unique_indexes;

    /** @var array<PrimaryKey> */
    private array $primary_keys;

    /** @var array<ForeignKey> */
    private array $foreign_keys;


    /**
     * @param string                                                         $name
     * @param array<ColumnInterface|Index|UniqueIndex|PrimaryKey|ForeignKey> $components
     */
    public function __construct(private readonly string $name, array $components = [])
    {
        $this->columns        = array_filter($components, static fn ($component): bool => $component instanceof ColumnInterface);
        $this->indexes        = array_filter($components, static fn ($component): bool => $component instanceof Index);
        $this->unique_indexes = array_filter($components, static fn ($component): bool => $component instanceof UniqueIndex);
        $this->primary_keys   = array_filter($components, static fn ($component): bool => $component instanceof PrimaryKey);
        $this->foreign_keys   = array_filter($components, static fn ($component): bool => $component instanceof ForeignKey);
    }

    /**
     * @param ColumnInterface $column
     *
     * @return $this
     */
    public function addColumn(ColumnInterface $column): static
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<ColumnInterface>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array<Index>
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * @return array<UniqueIndex>
     */
    public function getUniqueIndexes(): array
    {
        return $this->unique_indexes;
    }

    /**
     * @return array<ForeignKey>
     */
    public function getForeignKeys(): array
    {
        return $this->foreign_keys;
    }

    /**
     * @return array<PrimaryKey>
     */
    public function getPrimaryKeys(): array
    {
        return $this->primary_keys;
    }
}
