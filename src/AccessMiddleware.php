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

use Opis\Routing\Middleware;
use Opis\Http\{Request, Response};
use function Opis\Colibri\{httpError, make};

class AccessMiddleware extends Middleware
{
    public function __invoke(Request $request, array $permissions = []): Response
    {
        $isJsonRequest = $this->isJsonRequest($request);
        $user = make(UserSession::class)->currentUser();

        if ($user === null) {
            return httpError(401, $isJsonRequest ? [] : null);
        }

        if ($user->isOwner()) {
            return $this->next();
        }

        if (!$user->isActive() || !$user->hasPermissions($permissions)) {
            return httpError(403, $isJsonRequest ? [] : null);
        }

        return $this->next();
    }

    private function isJsonRequest(Request $request): bool
    {
        $contentType = $request->getHeader('Content-Type', 'text/html');
        $contentType = strtolower(trim(explode(';', $contentType)[0]));
        return $contentType === 'application/json';
    }
}