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

use DateTimeInterface;
use Opis\ORM\{DataMapper, Entity, EntityMapper, MappableEntity};
use function Opis\Colibri\{config, uuid4, entity};

class User extends Entity implements MappableEntity
{
    /** @var Permission[]|null */
    private ?array $permissions = null;

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->orm()->getColumn('id');
    }

    public function realmName(): string
    {
        return $this->orm()->getColumn('realm');
    }

    public function setRealmName(string $value): self
    {
        $this->orm()->setColumn('realm', $value);
        return $this;
    }

    public function realm(): Realm
    {
        return Realm::get($this->realmName());
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->orm()->getColumn('name');
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->orm()->setColumn('name', $name);
        return $this;
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->orm()->getColumn('email');
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->orm()->setColumn('email', $email);
        return $this;
    }

    /**
     * @return string|null
     */
    public function password(): ?string
    {
        return $this->orm()->getColumn('password');
    }

    /**
     * @param string|null $password
     * @return $this
     */
    public function setPassword(?string $password = null): self
    {
        $this->orm()->setColumn('password', $password);
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function registrationDate(): DateTimeInterface
    {
        return $this->orm()->getColumn('registration_date');
    }

    /**
     * @param DateTimeInterface $date
     * @return $this
     */
    public function setRegistrationDate(DateTimeInterface $date): self
    {
        $this->orm()->setColumn('registration_date', $date);
        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function lastLogin(): ?DateTimeInterface
    {
        return $this->orm()->getColumn('last_login');
    }

    /**
     * @param DateTimeInterface $date
     * @return $this
     */
    public function setLastLogin(DateTimeInterface $date): self
    {
        $this->orm()->setColumn('last_login', $date);
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->orm()->getColumn('is_active');
    }

    public function data(): ?object
    {
        return $this->orm()->getColumn('data');
    }

    public function setData(?object $data): self
    {
        $this->orm()->setColumn('data', 'data');
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setIsActive(bool $value): self
    {
        $this->orm()->setColumn('is_active', $value);
        return $this;
    }

    /**
     * @return Role[]
     */
    public function roles(): array
    {
        return $this->orm()->getColumn('roles');
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->orm()->setColumn('roles', $roles);
        return $this;
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

        foreach ($this->roles() as $role) {
            foreach ($role->permissions() as $permission) {
                $permissions[] = $permission;
            }
        }

        return $this->permissions = $permissions;
    }

    /**
     * @param string[]|Permission[] $permissions
     * @return bool
     */
    public function hasPermissions(array $permissions): bool
    {
        foreach ($this->permissions() as $user_permission) {
            foreach ($permissions as $permission) {
                if ($user_permission->name() !== (string) $permission) {
                    return false;
                }
            }
        }

        return true;
    }

    public static function get(string $id): ?User
    {
        return entity(self::class)->find($id);
    }

    public function getByEmail(string $email, string $realm = 'default'): ?User
    {
        return entity(self::class)
                ->where('realm')->is($realm)
                ->where('email')->is($email)
                ->get();
    }

    /**
     * @inheritDoc
     */
    public static function mapEntity(EntityMapper $mapper): void
    {
        $mapper->entityName('user');
        $mapper->table(config()->read('opis-colibri.auth.users-table', 'users'));
        $mapper->primaryKeyGenerator(static fn () => uuid4(''));
        $mapper->cast([
            'is_active' => 'boolean',
            'registration_date' => 'date',
            'last_login' => '?date',
            'roles' => 'json-assoc',
            'data' => '?json',
        ]);

        $mapper->setter('password', static function (string $password) {
            return password_hash($password, PASSWORD_DEFAULT);
        });

        $mapper->setter('roles', static function(array $roles /*, DataMapper $orm*/) {
            $list = [];

            foreach ($roles as $role) {
                $list[] = @(string) $role;
            }

            return $list;
        });

        $mapper->getter('roles', static function(array $roles , DataMapper $orm) {
            $realm = Realm::get($orm->getColumn('realm'));

            $list = [];

            $roleCollection = $realm->roles();

            foreach ($roles as $roleId) {
                if (isset($roleCollection[$roleId])) {
                    $list[] = $roleCollection[$roleId];
                }
            }

            return $list;
        });
    }
}