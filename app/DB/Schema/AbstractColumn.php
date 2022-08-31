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

use Fisharebest\Webtrees\DB\Expression;

/**
 * Common methods for all column types.
 */
abstract class AbstractColumn
{
    public string $comment = '';

    public int|float|string|Expression|null $default = null;

    public bool $nullable = false;

    public bool $invisible = false;

    /**
     * @param string $name
     */
    public function __construct(private readonly string $name)
    {
    }

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function comment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @param int|float|string|Expression|null $default
     *
     * @return $this
     */
    public function default(int|float|string|Expression|null $default): static
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @param bool $nullable
     *
     * @return $this
     */
    public function nullable(bool $nullable = true): static
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * @param bool $invisible
     *
     * @return $this
     */
    public function invisible(bool $invisible = true): static
    {
        $this->invisible = $invisible;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @return int|string|Expression|null
     */
    public function getDefault(): int|string|Expression|null
    {
        return $this->default;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isInvisible(): bool
    {
        return $this->invisible;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }
}
