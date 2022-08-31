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

use Exception;
use Fisharebest\Webtrees\DB\Schema\ReferentialAction;
use Fisharebest\Webtrees\DB\Schema\Schema;

/**
 * Definitions for the webtrees database.
 */
class WebtreesSchema
{
    /**
     * @return void
     */
    public function historicSchemaVersions(): void
    {
        switch ('webtrees_schema') {
            case 1: // webtrees 1.0.0 - 1.0.3
            case 2: // webtrees 1.0.4
            case 3:
            case 4: // webtrees 1.0.5
            case 5: // webtrees 1.0.6
            case 6:
            case 7:
            case 8:
            case 9: // webtrees 1.1.0 - 1.1.1
            case 10: // webtrees 1.1.2
            case 11: // webtrees 1.2.0
            case 12: // webtrees 1.2.1 - 1.2.3
            case 13:
            case 14:
            case 15: // webtrees 1.2.4 - 1.2.5
            case 16: // webtrees 1.2.7
            case 17:
            case 18: // webtrees 1.3.0
            case 19: // webtrees 1.3.1
            case 20: // webtrees 1.3.2
            case 21:
            case 22:
            case 23: // webtrees 1.4.0 - 1.4.1
            case 24:
            case 25: // webtrees 1.4.2 - 1.4.4, 1.5.0
            case 26: // webtrees 1.4.5 - 1.4.6
            case 27: // webtrees 1.5.1 - 1.6.0
            case 28:
            case 29: // webtrees 1.6.1 - 1.6.2
            case 30:
            case 31: // webtrees 1.7.0 - 1.7.1
            case 32: // webtrees 1.7.2
            case 33:
            case 34: // webtrees 1.7.3 - 1.7.4
            case 35:
            case 36: // webtrees 1.7.5 - 1.7.7
            case 37: // webtrees 1.7.8 - 2.0.0
            case 38:
            case 39:
            case 40: // webtrees 2.0.1 - 2.1.15
        }
    }

    /**
     * @return Schema
     */
    public static function schema(): Schema
    {
        $block = Schema::table('block', [
            Schema::integer('block_id')->autoIncrement(),
            Schema::integer('gedcom_id')->nullable(),
            Schema::integer('user_id')->nullable(),
            Schema::varchar('xref', 20)->nullable(),
            Schema::enum('location', ['main', 'side'])->nullable(),
            Schema::integer('block_order'),
            Schema::nVarchar('module_name', 32),
            Schema::primaryKey('block_id'),
            Schema::foreignKey('gedcom_id', 'gedcom')->onDeleteCascade()->onUpdateCascade(),
            Schema::foreignKey('user_id', 'user')->onDeleteCascade()->onUpdateCascade(),
            Schema::foreignKey('module_name', 'module')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $block_setting = Schema::table('block_setting', [
           Schema::integer('block_id'),
           Schema::varchar('setting_name', 32),
           Schema::text('setting_value'),
           Schema::primaryKey(['block_id', 'setting_name']),
           Schema::foreignKey('block_id', 'block')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $change = Schema::table('change', [
            Schema::integer('change_id')->autoIncrement(),
            Schema::timestamp('change_time')->defaultCurrentTimestamp(),
            Schema::varchar('status', 8)->default('pending'),
            Schema::integer('gedcom_id'),
            Schema::varchar('xref', 20),
            Schema::text('old_gedcom'),
            Schema::text('new_gedcom'),
            Schema::integer('user_id'),
            Schema::primaryKey('change_id'),
            Schema::index(['gedcom_id', 'status', 'xref']),
            Schema::foreignKey('gedcom_id', 'gedcom')->onDeleteCascade()->onUpdateCascade(),
            Schema::foreignKey('user_id', 'user')->onDeleteSetNull()->onUpdateCascade(),
        ]);

        $dates = Schema::table('dates', [
            Schema::smallInteger('d_day'),
            Schema::char('d_month', 5),
            Schema::smallInteger('d_mon'),
            Schema::smallInteger('d_year'),
            Schema::mediumInteger('d_julianday1'),
            Schema::mediumInteger('d_julianday2'),
            Schema::varchar('d_fact', 15),
            Schema::varchar('d_gid', 20),
            Schema::integer('d_file'),
            Schema::varchar('d_type', 13),
            Schema::index(['d_day']),
            Schema::index(['d_month']),
            Schema::index(['d_mon']),
            Schema::index(['d_year']),
            Schema::index(['d_julianday1']),
            Schema::index(['d_julianday2']),
            Schema::index(['d_gid']),
            Schema::index(['d_file']),
            Schema::index(['d_type']),
            Schema::index(['d_fact', 'd_gid']),
        ]);

        $default_resn = Schema::table('default_resn', [
            Schema::integer('default_resn_id')->autoIncrement(),
            Schema::integer('gedcom_id'),
            Schema::varchar('xref', 20)->nullable(),
            Schema::varchar('tag_type', 15)->nullable(),
            Schema::varchar('resn', 12),
            Schema::nVarchar('comment', 255)->nullable(),
            Schema::timestamp('updated')->defaultCurrentTimestamp(),
            Schema::primaryKey('default_resn_id'),
            Schema::index(['gedcom_id', 'xref', 'tag_type']),
            Schema::foreignKey('gedcom_id', 'gedcom')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $families = Schema::table('families', [
            Schema::varchar('f_id', 20),
            Schema::integer('f_file'),
            Schema::varchar('f_husb', 20)->nullable(),
            Schema::varchar('f_wife', 20)->nullable(),
            Schema::text('f_gedcom'),
            Schema::integer('f_numchil'),
            Schema::primaryKey(['f_id', 'f_file']),
            Schema::uniqueIndex(['f_file', 'f_id']),
            Schema::index('f_husb'),
            Schema::index('f_wife'),
        ]);

        $favorite = Schema::table('favorite', [
            Schema::integer('favorite_id')->autoIncrement(),
            Schema::integer('user_id')->nullable(),
            Schema::integer('gedcom_id'),
            Schema::varchar('xref', 20)->nullable(),
            Schema::varchar('favorite_type', 4),
            Schema::nVarchar('url', 255)->nullable(),
            Schema::nVarchar('title', 255)->nullable(),
            Schema::nVarchar('note', 1000)->nullable(),
            Schema::primaryKey('favorite_id'),
            Schema::index(['user_id']),
            Schema::index(['gedcom_id', 'user_id']),
            Schema::foreignKey('user_id', 'user')->onDeleteCascade()->onUpdateCascade(),
            Schema::foreignKey('gedcom_id', 'gedcom')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $gedcom = Schema::table('gedcom', [
            Schema::integer('gedcom_id')->autoIncrement(),
            Schema::nVarchar('gedcom_name', 255),
            Schema::integer('sort_order')->default(0),
            Schema::primaryKey('gedcom_id'),
            Schema::uniqueIndex('gedcom_name'),
            Schema::index('sort_order'),
        ]);

        $gedcom_chunk = Schema::table('gedcom_chunk', [
            Schema::integer('gedcom_chunk_id')->autoIncrement(),
            Schema::integer('gedcom_id'),
            Schema::text('chunk_data'),
            Schema::tinyInteger('imported')->default(0),
            Schema::primaryKey('gedcom_chunk_id'),
            Schema::index(['gedcom_id', 'imported']),
            Schema::foreignKey('gedcom_id', 'gedcom')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $gedcom_setting = Schema::table('gedcom_setting', [
            Schema::integer('gedcom_id'),
            Schema::nVarchar('setting_name', 32),
            Schema::nVarchar('setting_value', 255),
            Schema::primaryKey(['gedcom_id', 'setting_name']),
            Schema::foreignKey('gedcom_id', 'gedcom')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $hit_counter = Schema::table('hit_counter', [
            Schema::integer('gedcom_id'),
            Schema::varchar('page_name', 32),
            Schema::varchar('page_parameter', 20),
            Schema::integer('page_count'),
            Schema::primaryKey(['gedcom_id', 'page_name', 'page_parameter']),
            Schema::foreignKey('gedcom_id', 'gedcom')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $individuals = Schema::table('individuals', [
            Schema::varchar('i_id', 20),
            Schema::integer('i_file'),
            Schema::nVarchar('i_rin', 20),
            Schema::varchar('i_sex', 1),
            Schema::text('i_gedcom'),
            Schema::primaryKey(['i_id', 'i_file']),
            Schema::uniqueIndex(['i_file', 'i_id']),
        ]);

        $link = Schema::table('link', [
            Schema::integer('l_file'),
            Schema::varchar('l_from', 20),
            Schema::varchar('l_type', 15),
            Schema::varchar('l_to', 20),
            Schema::primaryKey(['l_from', 'l_file', 'l_tyoe', 'l_to']),
            Schema::uniqueIndex(['l_to', 'l_file', 'l_type', 'l_from']),
        ]);

        $log = Schema::table('log', [
            Schema::integer('log_id')->autoIncrement(),
            Schema::timestamp('log_time')->defaultCurrentTimestamp(),
            Schema::varchar('log_type', 6),
            Schema::text('log_message'),
            Schema::varchar('ip_address', 45),
            Schema::integer('user_id')->nullable(),
            Schema::integer('gedcom_id')->nullable(),
            Schema::primaryKey('log_id'),
            Schema::index('log_time'),
            Schema::index('log_type'),
            Schema::index('ip_address'),
            Schema::index('user_id'),
            Schema::index('gedcom_id'),
            Schema::foreignKey('gedcom_id', 'gedcom')->onDeleteCascade()->onUpdateCascade(),
            Schema::foreignKey('user_id', 'user')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $media = Schema::table('media', [
            Schema::varchar('m_id', 20),
            Schema::integer('m_file'),
            Schema::text('m_gedcom'),
            Schema::primaryKey(['m_id', 'm_file']),
            Schema::uniqueIndex(['m_file', 'm_id']),
        ]);

        $media_file = Schema::table('media_file', [
            Schema::integer('id')->autoIncrement(),
            Schema::varchar('m_id', 20),
            Schema::integer('m_file'),
            Schema::nVarchar('multimedia_file_refn', 246),
            Schema::nVarchar('multimedia_format', 4),
            Schema::nVarchar('source_media_type', 15),
            Schema::nVarchar('descriptive_title', 248),
            Schema::primaryKey('id'),
            Schema::index(['m_id', 'm_file']),
            Schema::index(['m_file', 'm_id']),
            Schema::index(['m_file', 'multimedia_file_refn']),
            Schema::index(['m_file', 'multimedia_format']),
            Schema::index(['m_file', 'source_media_type']),
            Schema::index(['m_file', 'descriptive_title']),
        ]);

        $message = Schema::table('message', [
            Schema::integer('message_id')->autoIncrement(),
            Schema::nVarchar('sender', 64),
            Schema::varchar('ip_address', 45),
            Schema::integer('user_id'),
            Schema::nVarchar('subject', 255),
            Schema::text('body'),
            Schema::timestamp('created')->defaultCurrentTimestamp(),
            Schema::primaryKey('message_id'),
            Schema::index('user_id'),
            Schema::foreignKey('user_id', 'user')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $module = Schema::table('module', [
            Schema::nVarchar('module_name', 32),
            Schema::varchar('status', 8),
            Schema::integer('tab_order')->nullable(),
            Schema::integer('menu_order')->nullable(),
            Schema::integer('sidebar_order')->nullable(),
            Schema::integer('footer_order')->nullable(),
            Schema::primaryKey('module_name'),
        ]);

        $module_privacy = Schema::table('module_privacy', [
            Schema::integer('id')->autoIncrement(),
            Schema::nVarchar('module_name', 32),
            Schema::integer('gedcom_id'),
            Schema::varchar('interface', 255),
            Schema::tinyInteger('access_level'),
            Schema::primaryKey('id'),
            Schema::uniqueIndex(['gedcom_id', 'module_name', 'interface']),
            Schema::uniqueIndex(['module_name', 'gedcom_id', 'interface']),
            Schema::foreignKey('gedcom_id', 'gedcom')->onDeleteCascade()->onUpdateCascade(),
            Schema::foreignKey('module_name', 'module')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $module_setting = Schema::table('module_setting', [
            Schema::nVarchar('module_name', 32),
            Schema::nVarchar('setting_name', 32),
            Schema::text('setting_value'),
            Schema::primaryKey(['module_name', 'setting_name']),
            Schema::foreignKey('module_name', 'module')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $name = Schema::table('name', [
            Schema::integer('n_file'),
            Schema::varchar('n_id', 20),
            Schema::integer('n_num'),
            Schema::varchar('n_type', 15),
            Schema::nVarchar('n_sort', 255),
            Schema::nVarchar('n_full', 255),
            Schema::nVarchar('n_surname', 255),
            Schema::nVarchar('n_surn', 255),
            Schema::nVarchar('n_givn', 255),
            Schema::varchar('n_soundex_givn_std', 255),
            Schema::varchar('n_soundex_surn_std', 255),
            Schema::varchar('n_soundex_givn_dm', 255),
            Schema::varchar('n_soundex_surn_dm', 255),
            Schema::primaryKey(['n_id', 'n_file', 'n_num']),
            Schema::index(['n_full', 'n_id', 'n_file']),
            Schema::index(['n_surn', 'n_file', 'n_type', 'n_id']),
            Schema::index(['n_givn', 'n_file', 'n_type', 'n_id']),
        ]);

        $news = Schema::table('news', [
            Schema::integer('news_id')->autoIncrement(),
            Schema::integer('user_id')->nullable(),
            Schema::integer('gedcom_id')->nullable(),
            Schema::varchar('subject', 255),
            Schema::text('body'),
            Schema::timestamp('updated')->defaultCurrentTimestamp(),
            Schema::primaryKey('news_id'),
            Schema::index(['user_id', 'updated']),
            Schema::index(['gedcom_id', 'updated']),
            Schema::foreignKey('gedcom_id', 'gedcom')->onDeleteCascade()->onUpdateCascade(),
            Schema::foreignKey('user_id', 'user')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $other = Schema::table('other', [
            Schema::varchar('o_id', 20),
            Schema::integer('o_file'),
            Schema::varchar('o_type', 15),
            Schema::text('o_gedcom'),
            Schema::primaryKey(['o_id', 'o_file']),
            Schema::uniqueIndex(['o_file', 'o_id']),
        ]);

        $places = Schema::table('places', [
            Schema::integer('p_id')->autoIncrement(),
            Schema::nVarchar('p_place', 150),
            Schema::integer('p_parent_id')->nullable(),
            Schema::integer('p_file'),
            Schema::text('p_std_soundex'),
            Schema::text('p_dm_soundex'),
            Schema::primaryKey('p_id'),
            Schema::uniqueIndex(['p_parent_id', 'p_file', 'p_place']),
            Schema::index(['p_file', 'p_place']),
        ]);

        $placelinks = Schema::table('placelinks', [
            Schema::integer('pl_p_id'),
            Schema::varchar('pl_gid', 20),
            Schema::integer('pl_file'),
            Schema::primaryKey(['pl_p_id', 'pl_gid', 'pl_file']),
            Schema::index('pl_gid'),
            Schema::index('pl_file'),
        ]);

        $place_location = Schema::table('place_location', [
            Schema::integer('id')->autoIncrement(),
            Schema::integer('parent_id')->nullable(),
            Schema::nVarchar('place', 120),
            Schema::float('latitude')->nullable(),
            Schema::float('longitude')->nullable(),
            Schema::primaryKey('id'),
            Schema::uniqueIndex(['parent_id', 'place']),
            Schema::uniqueIndex(['place', 'parent_id']),
            Schema::index('latitude'),
            Schema::index('longitude'),
            Schema::foreignKey('parent_id', 'place_location', 'id')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $session = Schema::table('session', [
            Schema::varchar('session_id', 32),
            Schema::timestamp('session_time')->defaultCurrentTimestamp(),
            Schema::integer('user_id'),
            Schema::varchar('ip_address', 45),
            Schema::text('session_data'),
            Schema::primaryKey('session_id'),
            Schema::index(['session_time']),
            Schema::index(['user_id', 'ip_address']),
        ]);

        $site_setting = Schema::table('site_setting', [
            Schema::nVarchar('setting_name', 32),
            Schema::nVarchar('setting_value', 2000),
            Schema::primaryKey('setting_name'),
        ]);

        $sources = Schema::table('sources', [
            Schema::varchar('s_id', 20),
            Schema::integer('s_file'),
            Schema::nVarchar('s_name', 255),
            Schema::text('s_gedcom'),
            Schema::primaryKey(['s_id', 's_file']),
            Schema::uniqueIndex(['s_file', 's_id']),
            Schema::index(['s_name', 's_file']),
        ]);

        $user_gedcom_setting = Schema::table('user_gedcom_setting', [
            Schema::integer('user_id'),
            Schema::integer('gedcom_id'),
            Schema::nVarchar('setting_name', 32),
            Schema::nVarchar('setting_value', 255),
            Schema::primaryKey(['user_id', 'gedcom_id', 'setting_name']),
            Schema::index('gedcom_id'),
            Schema::foreignKey('gedcom_id', 'gedcom')->onDeleteCascade()->onUpdateCascade(),
            Schema::foreignKey('user_id', 'user')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $user = Schema::table('user', [
            Schema::integer('user_id')->autoIncrement(),
            Schema::nVarchar('user_name', 32),
            Schema::nVarchar('real_name', 64),
            Schema::nVarchar('email', 64),
            Schema::nVarchar('password', 128),
            Schema::primaryKey('user_id'),
            Schema::uniqueIndex('user_name'),
            Schema::uniqueIndex('email'),
        ]);

        $user_setting = Schema::table('user_setting', [
            Schema::integer('user_id'),
            Schema::nVarchar('setting_name', 32),
            Schema::nVarchar('setting_value', 255),
            Schema::primaryKey(['user_id', 'setting_name']),
            Schema::foreignKey('user_id', 'user')->onDeleteCascade()->onUpdateCascade(),
        ]);

        $foo = Schema::table('foo', [
            Schema::integer('id'),
            Schema::char('f', 255),
            Schema::nChar('g', 255),
            Schema::primaryKey('id'),
            Schema::index('f'),
            Schema::uniqueIndex('g'),
        ]);

        return new Schema([
            $foo,
            $block,
            $block_setting,
            $change,
            $dates,
            $default_resn,
            $families,
            $favorite,
            $gedcom,
            $gedcom_chunk,
            $gedcom_setting,
            $hit_counter,
            $individuals,
            $link,
            $log,
            $media,
            $media_file,
            $message,
            $module,
            $module_privacy,
            $module_setting,
            $name,
            $news,
            $other,
            $place_location,
            $placelinks,
            $places,
            $session,
            $site_setting,
            $sources,
            $user,
            $user_gedcom_setting,
            $user_setting,
        ]);
    }
}
