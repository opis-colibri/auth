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

interface User
{
    /**
     * User's id
     * @return string
     */
    public function id(): string;

    /**
     * Get user's human-readable name
     *
     * @return string
     */
    public function name(): string;

    /**
     * Set user's human-readable name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self;

    /**
     * Get user's email address
     *
     * @return string
     */
    public function email(): string;

    /**
     * Set user's unique email address
     *
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self;

    /**
     * Get user's password (hashed)
     *
     * @return null|string
     */
    public function password(): ?string;

    /**
     * Set user's password
     * @param string|null $password Password in plain text
     *
     * @return $this
     */
    public function setPassword(string $password = null): self;

    /**
     * Registration date
     *
     * @return DateTimeInterface
     */
    public function registrationDate(): DateTimeInterface;

    /**
     * @param DateTimeInterface $date
     *
     * @return $this
     */
    public function setRegistrationDate(DateTimeInterface $date): self;

    /**
     * User's last login time
     *
     * @return  DateTimeInterface|null
     */
    public function lastLogin(): ?DateTimeInterface;

    /**
     * @param DateTimeInterface $date
     *
     * @return $this
     */
    public function setLastLogin(DateTimeInterface $date): self;

    /**
     * Check if user is active
     *
     * @return  boolean
     */
    public function isActive(): bool;

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setIsActive(bool $value): self;

    /**
     * @return bool
     */
    public function isAnonymous(): bool;

    /**
     * @return string
     */
    public function realmId(): string;

    /**
     * Get user's realm
     *
     * @return Realm
     */
    public function realm(): Realm;

    /**
     * Set user's realm
     *
     * @param Realm $value
     * @return $this
     */
    public function setRealm(Realm $value): self;

    /**
     * User's roles
     *
     * @return Role[]
     */
    public function roles(): array;

    /**
     * Set user's roles
     *
     * @param Role[]|string[] $roles
     * @return $this
     */
    public function setRoles(array $roles): self;
}