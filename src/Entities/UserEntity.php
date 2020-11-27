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

namespace Opis\Colibri\Module\Auth\Entities;

use DateTimeInterface;
use Opis\ORM\{Core\DataMapper, Entity, EntityMapper, MappableEntity};
use Opis\Colibri\Module\Auth\{Realm, Role, User};
use function Opis\Colibri\config;

class UserEntity extends Entity implements MappableEntity, User
{
    /**
     * @inheritDoc
     */
    public function id(): string
    {
        return $this->orm()->getColumn('id');
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->orm()->getColumn('name');
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): User
    {
        $this->orm()->setColumn('name', $name);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function email(): string
    {
        return $this->orm()->getColumn('email');
    }

    /**
     * @inheritDoc
     */
    public function setEmail(string $email): User
    {
        $this->orm()->setColumn('email', $email);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function password(): ?string
    {
        return $this->orm()->getColumn('password');
    }

    /**
     * @inheritDoc
     */
    public function setPassword(string $password = null): User
    {
        $this->orm()->setColumn('password', $password);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registrationDate(): DateTimeInterface
    {
        return $this->orm()->getColumn('registration_date');
    }

    /**
     * @inheritDoc
     */
    public function setRegistrationDate(DateTimeInterface $date): User
    {
        $this->orm()->setColumn('registration_date', $date);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function lastLogin(): ?DateTimeInterface
    {
        return $this->orm()->getColumn('last_login');
    }

    /**
     * @inheritDoc
     */
    public function setLastLogin(DateTimeInterface $date): User
    {
        $this->orm()->setColumn('last_login', $date);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return $this->orm()->getColumn('is_active');
    }

    /**
     * @inheritDoc
     */
    public function isAnonymous(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function setIsActive(bool $value): User
    {
        $this->orm()->setColumn('is_active', $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function realmId(): string
    {
        return $this->orm()->getColumn('realm_id');
    }

    /**
     * @inheritDoc
     */
    public function realm(): Realm
    {
        return $this->orm()->getRelated('realm');
    }

    /**
     * @inheritDoc
     */
    public function setRealm(Realm $value): User
    {
        $this->orm()->setRelated('realm', $value);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function roles(): array
    {
        return $this->orm()->getColumn('roles');
    }

    /**
     * @inheritDoc
     */
    public function setRoles(array $roles): User
    {
        $this->orm()->setColumn('roles', $roles);
        return $this;
    }


    /**
     * @inheritDoc
     */
    public static function mapEntity(EntityMapper $mapper): void
    {
        $mapper->entityName('user');
        $mapper->table(config()->read('opis-colibri.auth.users-table', 'users'));

        $mapper->cast([
            'is_active' => 'boolean',
            'registration_date' => 'date',
            'last_login' => '?date',
            'roles' => 'json-assoc',
        ]);

        $mapper->setter('roles', static function(array $roles, DataMapper $orm) {
            if (null === $realm = Realm::get($orm->getColumn('realm_id'))) {
                throw new \RuntimeException("You must set the realm first");
            }

            $list = [];

            foreach ($roles as $role) {
                if ($role instanceof Role && $realm->hasRole($role->id())) {
                    $list[] = $role->id();
                }
            }

            return $list;
        });

        $mapper->getter('roles', static function(array $roles, DataMapper $orm) {
            $list = [];
            $realm = Realm::get($orm->getColumn('realm_id'));
            foreach ($roles as $role) {
                if (null !== $role = $realm->getRole($role)) {
                    $list[] = $role;
                }
            }
            return $list;
        });

        $mapper->relation('realm')->hasOne(Realm::class);
    }
}