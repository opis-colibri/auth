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

use Opis\Colibri\Collector as BaseCollector;
use Opis\Colibri\Collectors\ContractCollector;
use Opis\Colibri\Module\Auth\Collectors\PermissionCollector;
use Opis\Colibri\Module\Auth\Collectors\RoleCollector;
use Opis\Colibri\Module\Auth\Collectors\RolePermissionsCollector;

class Collector extends BaseCollector
{
    public function roles(RoleCollector $roles)
    {
        $roles
            ->register('authenticated', 'Authenticated user')
            ->register('administrator', 'Site administrator');
    }

    public function permissions(PermissionCollector $permissions)
    {
        $permissions->register('create-users', 'Create new users');
        $permissions->register('manage-users', 'Manage existing users');
        $permissions->register('delete-users', 'Delete existing users');
        $permissions->register('manage-own-user', 'Manage own user');
    }

    public function rolePermissions(RolePermissionsCollector $collector)
    {
        $collector->register('authenticated', [
            'manage-own-user',
        ]);

        $collector->register('administrator', [
            'create-users',
            'manage-users',
            'delete-users',
            'manage-own-user',
        ]);
    }

    public function contracts(ContractCollector $contract)
    {
        $contract->singleton(UserSession::class);
    }
}