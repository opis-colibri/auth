<?php
/* ===========================================================================
 * Copyright 2021 Zindex Software
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

use RuntimeException;
use Opis\Colibri\Serializable\Collection;
use Opis\ORM\{Entity, EntityMapper, MappableEntity};
use function Opis\Colibri\{collect, config, entity};

final class Realm extends Entity implements MappableEntity
{
    private ?array $roles = null;
    private ?array $permissions = null;
    private ?UserSession $userSession = null;

    public function id(): string
    {
        return $this->orm()->getColumn('id');
    }

    public function setId(string $value): self
    {
        $this->orm()->setColumn('id', $value);
        return $this;
    }

    public function name(): string
    {
        return $this->orm()->getColumn('name');
    }

    public function setName(string $value): self
    {
        $this->orm()->setColumn('name', $value);
        return $this;
    }

    public function description(): string
    {
        return $this->orm()->getColumn('id');
    }

    public function setDescription(string $value): self
    {
        $this->orm()->setColumn('id', $value);
        return $this;
    }

    public function sessionName(): ?string
    {
        return $this->orm()->getColumn('session_name');
    }

    public function setSessionName(?string $value): self
    {
        $this->orm()->setColumn('session_name', $value);
        return $this;
    }

    public function permissionCollector(): string
    {
        return $this->orm()->getColumn('permission_collector');
    }

    public function setPermissionCollector(string $value): self
    {
        $this->orm()->setColumn('permission_collector', $value);
        return $this;
    }

    public function roleCollector(): string
    {
        return $this->orm()->getColumn('role_collector');
    }

    public function setRoleCollector(string $value): self
    {
        $this->orm()->setColumn('role_collector', $value);
        return $this;
    }

    public function rolePermissionCollector(): string
    {
        return $this->orm()->getColumn('role_permission_collector');
    }

    public function setRolePermissionCollector(string $value): self
    {
        $this->orm()->setColumn('role_permission_collector', $value);
        return $this;
    }

    public function userSession(): UserSession
    {
        if ($this->userSession === null) {
            $this->userSession = new UserSession($this->sessionName());
        }

        return $this->userSession;
    }

    /**
     * @return Role[]
     */
    public function roles(): array
    {
        if ($this->roles === null) {
            /** @var Collection $roleCollection */
            $roleCollection = collect($this->roleCollector());

            $roles = [];
            foreach ($roleCollection->getEntries() as $name => $description) {
                $roles[$name] = new Role($this, $name, $description);
            }

            $this->roles = $roles;
        }

        return $this->roles;
    }

    /**
     * @return Permission[]
     */
    public function permissions(): array
    {
        if ($this->permissions === null) {
            /** @var Collection $permissionCollection */
            $permissionCollection = collect($this->permissionCollector());

            $permissions = [];

            foreach ($permissionCollection->getEntries() as $name => $description) {
                $permissions[$name] = new Permission($name, $description);
            }

            $this->permissions = $permissions;
        }

        return $this->permissions;
    }

    public static function get(string $id): Realm
    {
        static $realmCache = [];

        if (isset($realmCache[$id])) {
            return $realmCache[$id];
        }

        $realm = entity(self::class)->find($id);

        if (!$realm) {
            throw new RuntimeException("Invalid realm id ". $id);
        }

        $realmCache[$id] = $realm;

        return $realm;
    }

    /**
     * @inheritDoc
     */
    public static function mapEntity(EntityMapper $mapper): void
    {
        $mapper->entityName('realm');
        $mapper->table(config()->read('opis-colibri.auth.realms-table', 'realms'));
        $mapper->cast([
            'session_name' => '?string'
        ]);

        $mapper->relation('users')->hasMany(User::class);
    }
}