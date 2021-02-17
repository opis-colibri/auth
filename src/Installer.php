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

use Opis\Colibri\Installer as ModuleInstaller;
use Opis\Database\Schema\Blueprint;
use function Opis\Colibri\{config, schema};

class Installer extends ModuleInstaller
{
    public function install()
    {
        $realm_table = config()->read('opis-colibri.auth.realm-table', 'realms');
        $user_table = config()->read('opis-colibri.auth.user-table', 'users');

        schema()->create($realm_table, function (Blueprint $table) {
            $table->string('id', 32)->notNull()->primary();
            $table->string('name')->notNull();
            $table->string('description')->notNull();
            $table->string('session_name');
            $table->string('permission_collector')->notNull();
            $table->string('role_collector')->notNull();
            $table->string('role_permission_collector')->notNull();
        });

        schema()->create($user_table, function (Blueprint $table) use ($realm_table) {
            $table->fixed('id', 32)->notNull()->primary();
            $table->string('realm_id', 32)->notNull()->index();
            $table->string('name')->notNull();
            $table->string('email')->notNull()->unique();
            $table->string('password')->defaultValue(null);
            $table->dateTime('registration_date')->notNull();
            $table->dateTime('last_login')->defaultValue(null);
            $table->boolean('is_active')->notNull()->defaultValue(false);
            $table->binary('roles')->notNull();

            $table->foreign('realm_id')
                ->references($realm_table)
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    public function uninstall()
    {
        $realm_table = config()->read('opis-colibri.auth.realm-table', 'realms');
        $user_table = config()->read('opis-colibri.auth.user-table', 'users');

        schema()->drop($user_table);
        schema()->drop($realm_table);
    }
}