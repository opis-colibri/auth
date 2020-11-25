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
use function Opis\Colibri\{app, config, entityManager, schema};

class Installer extends BaseInstaller
{
    public function install()
    {
        $users_table = config()->read('opis-colibri.auth.users-table', 'users');
        $realms_table = config()->read('opis-colibri.auth.realms-table', 'realms');

        schema()->create($realms_table, function (CreateTable $table) {
            $table->string('id', 32)->notNull()->primary();
            $table->string('name')->notNull();
            $table->string('description', 512);
            $table->string('session_name');
            $table->string('collector_class')->notNull();
        });

        schema()->create($users_table, function (CreateTable $table) {
            $table->fixed('id', 32)->notNull()->primary();
            $table->fixed('realm_id', 32)->notNull();
            $table->string('name')->notNull();
            $table->string('email')->notNull()->index();
            $table->string('password');
            $table->dateTime('registration_date')->notNull();
            $table->dateTime('last_login');
            $table->boolean('is_active')->notNull()->defaultValue(false);
            $table->binary('roles')->notNull();

            $table->unique(['realm_id', 'email']);

            $table->foreign('realm_id')
                ->references('realms', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        /** @var Realm $realm */
        $realm = entityManager()->create(Realm::class);

        $realm
            ->setId('default')
            ->setName('Default')
            ->setDescription('Default realm')
            ->setSessionName(null)
            ->setCollectorClass(RoleCollector::class);

        entityManager()->save($realm);
    }

    public function enable()
    {
        app()->getCollector()->register(RoleCollector::class, 'Role collector');
    }

    public function disable()
    {
        app()->getCollector()->unregister(RoleCollector::class);
    }

    public function uninstall()
    {
        $users_table = config()->read('opis-colibri.auth.users-table', 'users');
        $realms_table = config()->read('opis-colibri.auth.realms-table', 'realms');

        schema()->drop($users_table);
        schema()->drop($realms_table);
    }
}