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

use DateTimeImmutable;
use DateTimeInterface;
use Opis\Colibri\Module\Auth\{Realm, User};

final class AnonymousUser implements User
{
    private Realm $realm;

    public function __construct(Realm $realm)
    {
        $this->realm = $realm;
    }

    /**
     * @inheritDoc
     */
    public function id(): string
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): User
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function email(): string
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function setEmail(string $email): User
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function password(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setPassword(string $password = null): User
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registrationDate(): DateTimeInterface
    {
        return new DateTimeImmutable();
    }

    /**
     * @inheritDoc
     */
    public function setRegistrationDate(DateTimeInterface $date): User
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function lastLogin(): ?DateTimeInterface
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setLastLogin(DateTimeInterface $date): User
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function setIsActive(bool $value): User
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isAnonymous(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function realmId(): string
    {
        return $this->realm->id();
    }


    /**
     * @inheritDoc
     */
    public function realm(): Realm
    {
        return $this->realm;
    }

    /**
     * @inheritDoc
     */
    public function setRealm(Realm $value): User
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function roles(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function setRoles(array $roles): self
    {
        return $this;
    }

}