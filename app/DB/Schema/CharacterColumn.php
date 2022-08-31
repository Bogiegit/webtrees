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
 * Definition of a CHAR or VARCHAR column.
 */
class CharacterColumn extends AbstractColumn implements ColumnInterface
{
    public ?string $collation;

    /**
     * @param string $name
     * @param int    $length
     * @param bool   $varying
     * @param bool   $national
     */
    public function __construct(string $name, public readonly int $length, public readonly bool $varying, public readonly bool $national)
    {
        parent::__construct($name);
    }

    /**
     * @param string|null $collation
     *
     * @return $this
     */
    public function collation(?string $collation): static
    {
        $this->collation = $collation;

        return $this;
    }
}
