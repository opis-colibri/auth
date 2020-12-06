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

namespace Opis\Colibri\Module\Auth\Collectors;

use Opis\Colibri\Collectors\BaseCollector;
use Opis\Colibri\Serializable\Collection;

/**
 * @method Collection data()
 */
class RoleCollector extends BaseCollector
{
    public function __construct()
    {
        parent::__construct(new Collection());
    }

    /**
     * @param string $name
     * @param string $description
     * @return $this
     */
    public function register(string $name, string $description): self
    {
        $this->data()->add($name, $description);
        return $this;
    }
}