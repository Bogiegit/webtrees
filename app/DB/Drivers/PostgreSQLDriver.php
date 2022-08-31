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
use Fisharebest\Webtrees\DB\Schema\Schema;

use function array_map;

/**
 * Driver for PostgreSQL.
 */
class PostgreSQLDriver extends AbstractDriver implements DriverInterface
{
    /**
     * @param string|null $schema_name
     *
     * @return Schema
     */
    public function diffSchema(string $schema_name = null): Schema
    {
        return new Schema();
    }

}
