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

class IntegerColumn extends AbstractColumn implements ColumnInterface
{
    public function __construct(
        string $name,
        public readonly int $bits,
        public bool $unsigned = false,
        public bool $auto_increment = false,
    ) {
        parent::__construct($name);
    }

    /**
     * @param bool $auto_increment
     *
     * @return $this
     */
    public function autoIncrement(bool $auto_increment = true): self
    {
        $this->auto_increment = $auto_increment;

        return $this;
    }

    /**
     * @param bool $unsigned
     *
     * @return $this
     */
    public function unsigned(bool $unsigned = true): self
    {
        $this->unsigned = $unsigned;

        return $this;
    }
}
