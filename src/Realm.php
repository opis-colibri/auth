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
use Opis\Colibri\Module\Auth\Collectors\{PermissionCollector, RoleCollector, RealmCollector};
use function Opis\Colibri\{collect};

final class Realm
{
    private ?array $roles = null;
    private ?array $permissions = null;
    private ?UserSession $userSession = null;
    private string $name;
    private string $userClass;
    private ?string $sessionName;

    public function __construct(string $name, string $userClass, ?string $sessionName)
    {
        $this->name = $name;
        $this->userClass = $userClass;
        $this->sessionName = $sessionName;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function userClass(): string
    {
        return $this->userClass;
    }

    public function sessionName(): ?string
    {
        return $this->sessionName;
    }

    public function userSession(): UserSession
    {
        if ($this->userSession === null) {
            $this->userSession = new UserSession($this->userClass, $this->sessionName);
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
            $roleCollection = collect(RoleCollector::class)->get($this->name);

            if ($roleCollection === null) {
                return $this->roles = [];
            }

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
            $permissionCollection = collect(PermissionCollector::class)->get($this->name);

            $permissions = [];

            foreach ($permissionCollection->getEntries() as $name => $description) {
                $permissions[$name] = new Permission($name, $description);
            }

            $this->permissions = $permissions;
        }

        return $this->permissions;
    }

    public static function get(string $name = 'default'): Realm
    {
        static $realmCache = [];

        if (!isset($realmCache[$name])) {
            if (null === $realmInfo = collect(RealmCollector::class)->get($name)) {
                throw new RuntimeException("Invalid realm name ". $name);
            }
            $realmCache[$name] = new self($name, $realmInfo['userClass'], $realmInfo['sessionName']);
        }

        return $realmCache[$name];
    }
}