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
use Opis\Colibri\Module\Auth\Collectors\{PermissionCollector, RealmCollector, RoleCollector, RolePermissionsCollector};
use Opis\Database\Schema\Blueprint;
use function Opis\Colibri\{config, registerCollector, schema, unregisterCollector};

class Installer extends ModuleInstaller
{
    public function install()
    {
        $user_table = config()->read('opis-colibri.auth.user-table', 'users');

        schema()->create($user_table, function (Blueprint $table) {
            $table->fixed('id', 32)->notNull()->primary();
            $table->string('realm', 32)->defaultValue('default')->index();
            $table->string('name')->notNull();
            $table->string('email')->notNull();
            $table->string('password')->defaultValue(null);
            $table->dateTime('registration_date')->notNull();
            $table->dateTime('last_login')->defaultValue(null);
            $table->boolean('is_active')->notNull()->defaultValue(false);
            $table->binary('roles')->notNull();
            $table->binary('data')->size('big');

            $table->unique(['realm', 'email']);
        });
    }

    public function enable()
    {
        registerCollector(RealmCollector::class, 'Collect realms');
        registerCollector(RoleCollector::class, 'Collect roles');
        registerCollector(PermissionCollector::class, 'Collect permissions');
        registerCollector(RolePermissionsCollector::class, 'Collect role permissions');
    }

    public function disable()
    {
        unregisterCollector(RealmCollector::class);
        unregisterCollector(RoleCollector::class);
        unregisterCollector(PermissionCollector::class);
        unregisterCollector(RolePermissionsCollector::class);
    }


    public function uninstall()
    {
        $user_table = config()->read('opis-colibri.auth.user-table', 'users');

        schema()->drop($user_table);
    }
}