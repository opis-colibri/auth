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

use Opis\ORM\{Entity, EntityMapper, MappableEntity};
use Opis\Colibri\Module\Auth\Entities\UserEntity;
use Opis\Colibri\Serializable\Collection;
use function Opis\Colibri\{collect, config, entity};

class Realm extends Entity implements MappableEntity
{
    private ?Session $userSession = null;
    private ?array $roles = null;

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

    public function collectorClass(): string
    {
        return $this->orm()->getColumn('collector_class');
    }

    public function setCollectorClass(string $value): self
    {
        $this->orm()->setColumn('collector_class', $value);
        return $this;
    }

    public function session(): Session
    {
        if ($this->userSession === null) {
            $this->userSession = new Session($this);
        }

        return $this->userSession;
    }

    public function roles(): array
    {
        if ($this->roles !== null) {
            return $this->roles;
        }

        $roles = [];
        /** @var Collection $list */
        $list = collect($this->collectorClass());

        foreach ($list->getEntries() as $key => $info) {
            $roles[$key] = new Role($key, $info['name'], $info['description']);
        }

        return $this->roles = $roles;
    }

    public function getRole(string $id): ?Role
    {
        return $this->roles()[$id] ?? null;
    }

    public function hasRole(string $id): bool
    {
        return ($this->roles()[$id] ?? null) !== null;
    }

    public static function get(string $id = 'default'): ?Realm
    {
        static $cache = [];

        if (array_key_exists($id, $cache)) {
            return $cache[$id];
        }

        $cache[$id] = $realm = entity(Realm::class)->find($id);

        return $realm;
    }

    /**
     * @inheritDoc
     */
    public static function mapEntity(EntityMapper $mapper): void
    {
        $mapper->entityName('realm');
        $mapper->table(config()->read('opis-colibri.auth.realms-table', 'realms'));

        $mapper->relation('users')->hasMany(UserEntity::class);
    }
}