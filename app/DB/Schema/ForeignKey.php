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

class ForeignKey
{
    private ReferentialAction $on_delete = ReferentialAction::NO_ACTION;
    private ReferentialAction $on_update = ReferentialAction::NO_ACTION;

    /**
     * @param array<string> $local_columns
     * @param string        $foreign_table
     * @param array<string> $foreign_columns
     */
    public function __construct(
        public readonly array $local_columns,
        public readonly string $foreign_table,
        public readonly array $foreign_columns,
        public readonly string $name
    ) {
    }

    /**
     * @param ReferentialAction $on_delete
     *
     * @return $this
     */
    public function onDelete(ReferentialAction $on_delete): static
    {
        $this->on_delete = $on_delete;

        return $this;
    }

    /**
     * @return $this
     */
    public function onDeleteCascade(): static
    {
        return $this->onDelete(ReferentialAction::CASCADE);
    }

    /**
     * @return $this
     */
    public function onDeleteSetNull(): static
    {
        return $this->onDelete(ReferentialAction::SET_NULL);
    }

    /**
     * @param ReferentialAction $on_update
     *
     * @return $this
     */
    public function onUpdate(ReferentialAction $on_update): static
    {
        $this->on_update = $on_update;

        return $this;
    }

    /**
     * @return $this
     */
    public function onUpdateCascade(): static
    {
        return $this->onDelete(ReferentialAction::CASCADE);
    }
}
