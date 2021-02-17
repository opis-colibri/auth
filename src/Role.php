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

use Stringable;
use Opis\Colibri\Module\Auth\Collectors\RolePermissionsCollector;
use Opis\Colibri\Serializable\Collection;
use function Opis\Colibri\collect;

final class Role implements Stringable
{
    private string $name, $description;

    /** @var Permission[]|null */
    private ?array $permissions = null;

    private Realm $realm;

    public function __construct(Realm $realm, string $name, string $description)
    {
        $this->realm = $realm;
        $this->name = $name;
        $this->description = $description;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Permission[]
     */
    public function permissions(): array
    {
        if ($this->permissions !== null) {
            return $this->permissions;
        }

        $permissions = [];

        $permissionCollection = $this->realm->permissions();
        /** @var Collection $rolePermissions */
        $rolePermissions = collect(RolePermissionsCollector::class)->get($this->realm->name());

        if (null !== $list = $rolePermissions->get($this->name)) {
            foreach ($list as $permission) {
                if (isset($permissionCollection[$permission])) {
                    $permissions[] = $permissionCollection[$permission];
                }
            }
        }

        return $this->permissions = $permissions;
    }
}