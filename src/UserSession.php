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

use DateTimeImmutable;
use RuntimeException;
use Opis\Colibri\Collectors\SessionCollector;
use Opis\Colibri\Session\Session;
use function Opis\Colibri\{app, collect, entity, entityManager, session, uuid4};

final class UserSession
{
    const USER_KEY = 'authenticated_user';
    const SIGN_OUT_KEY = 'sign_out_key';

    private ?User $user = null;
    private ?string $sessionName;
    private ?Session $session = null;

    public function __construct(?string $sessionName = null)
    {
        $this->sessionName = $sessionName;
    }

    public function authenticate(User $user, UserCredentials $credentials): bool
    {
        if (!$user->isActive()) {
            return false;
        }

        $session = $this->session();

        if ($credentials->validate($user)) {
            $user->setLastLogin(new DateTimeImmutable());
            entityManager()->save($user);
            $session->set(self::USER_KEY, $user->id());
            $session->set(self::SIGN_OUT_KEY, uuid4());
            $this->user = $user;
            return true;
        }

        return false;
    }

    public function authenticateWithPassword(User $user, string $password): bool
    {
        return $this->authenticate($user, new Credentials\PasswordCredentials($password));
    }

    public function signOut(User $user, string $key, bool $destroy = true): bool
    {
        $session = $this->session();

        if (!$user->isActive() || $session->get(self::USER_KEY) !== $user->id()) {
            return false;
        }

        if ($session->get(self::SIGN_OUT_KEY) !== $key) {
            return false;
        }

        $this->user = null;
        $session->delete(self::USER_KEY);
        $session->delete(self::SIGN_OUT_KEY);

        return $destroy ? $session->destroy() : true;
    }

    public function getSignOutKey(User $user): string
    {
        $session = $this->session();

        if (!$user->isActive() || $session->get(self::USER_KEY) !== $user->id()) {
            return '';
        }

        return session()->get(self::SIGN_OUT_KEY);
    }

    public function currentUser($entity = User::class): ?User
    {
        $session = $this->session();

        if ($session->has(self::USER_KEY)) {
            if ($this->user !== null) {
                return $this->user;
            }

            $user = entity($entity)->find($session->get(self::USER_KEY));

            if ($user !== null) {
                return $this->user = $user;
            } else {
                $this->user = null;
                $session->delete(self::USER_KEY);
            }
        }

        return null;
    }

    private function session(): Session
    {
        if ($this->session !== null) {
            return $this->session;
        }

        if ($this->sessionName === null) {
            return $this->session = session();
        }

        $collector = collect(SessionCollector::class);

        if ($collector->has($this->sessionName)) {
            return $this->session = session($this->sessionName);
        }

        if (!preg_match('/^[a-z0-9-A-Z_-]*:[a-z0-9A-Z_-]+$/', $this->sessionName)) {
            throw new RuntimeException('Invalid session name ' . $this->sessionName);
        }

        [$name, ] = explode(':', $this->sessionName);

        $sessionInstance = session($name === '' ? null : $name);
        $handler = $sessionInstance->getHandler();
        $config = $sessionInstance->getConfig();
        $config['cookie_name'] = $this->sessionName;

        return $this->session = new Session(app()->getSessionCookieContainer(), $handler, $config);
    }
}