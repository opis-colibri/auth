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

use Opis\Colibri\Module\Auth\Entities\{AnonymousUser, UserEntity};
use function Opis\Colibri\{entity, entityManager, session, uuid4};

class Session
{
    const USER_KEY = 'authenticated_user';
    const SIGN_OUT_KEY = 'sign_out_key';

    private Realm $realm;
    private AnonymousUser $anonymous;
    private ?User $user = null;

    public function __construct(Realm $realm)
    {
        $this->realm = $realm;
        $this->anonymous = new AnonymousUser($realm);
    }

    public function authenticate(User $user, UserCredentials $credentials): bool
    {
        if ($user->isAnonymous() ||
            $user->realmId() !== $this->realm->id() ||
            !$user instanceof UserEntity
        ) {
            return false;
        }

        if ($credentials->validate($user)) {
            $user->setLastLogin(new \DateTimeImmutable());
            entityManager()->save($user);
            session()->set(self::USER_KEY, $user->id());
            session()->set(self::SIGN_OUT_KEY, uuid4());
            $this->user = $user;
            return true;
        }

        return false;
    }

    public function authenticateWithPassword(User $user, string $password): bool
    {
        return $this->authenticate($user, new Credentials\PasswordCredentials($password));
    }

    public function signOut(User $user, string $key): bool
    {
        if ($user->isAnonymous() ||
            $user->realmId() !== $this->realm->id() ||
            session()->get(self::USER_KEY) !== $user->id()
        ) {
            return false;
        }

        if (session()->get(self::SIGN_OUT_KEY) !== $key) {
            return false;
        }

        $this->user = null;
        return session()->destroy();
    }

    public function getSignOutKey(User $user): string
    {
        if ($user->isAnonymous() ||
            $user->realmId() !== $this->realm->id() ||
            session()->get(self::USER_KEY) !== $user->id()
        ) {
            return '';
        }

        return session()->get(self::SIGN_OUT_KEY);
    }

    public function currentUser(): User
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $session = session($this->realm->sessionName());

        if ($session->has(self::USER_KEY)) {
            $user = entity(UserEntity::class)
                ->where('realm_id')->is($this->realm->id())
                ->find($session->get(self::USER_KEY));

            if ($user !== null) {
                return $this->user = $user;
            } else {
                $session->delete(self::USER_KEY);
            }
        }

        return $this->anonymous;
    }
}