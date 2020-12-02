<?php
/* ===========================================================================
 * Copyright 2020 Zindex Software
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================================ */

namespace Opis\Colibri\Module\Auth;

use Opis\Colibri\Installer as BaseInstaller;
use Opis\Database\Schema\CreateTable;
use function Opis\Colibri\{config, schema};

class Installer extends BaseInstaller
{
    public function install()
    {
        $users_table = config()->read('opis-colibri.auth.users-table', 'users');

        schema()->create($users_table, function (CreateTable $table) {
            $table->fixed('id', 32)->notNull()->primary();
            $table->string('name')->notNull();
            $table->string('email')->notNull()->unique();
            $table->string('password');
            $table->dateTime('registration_date')->notNull();
            $table->dateTime('last_login');
            $table->boolean('is_active')->notNull()->defaultValue(false);
            $table->binary('roles')->notNull();
        });
    }

    public function uninstall()
    {
        $users_table = config()->read('opis-colibri.auth.users-table', 'users');

        schema()->drop($users_table);
    }
}