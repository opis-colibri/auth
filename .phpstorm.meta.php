<?php
namespace PHPSTORM_META {
    override(\Opis\Colibri\Module\Auth\UserSession::currentUser(0), type(0));
    override(\Opis\Colibri\Core\ItemCollector::collect(0), map([
        '\Opis\Colibri\Module\Auth\Collectors\RealmCollector' => \Opis\Colibri\Serializable\Collection::class,
        '\Opis\Colibri\Module\Auth\Collectors\RoleCollector' => \Opis\Colibri\Serializable\Collection::class,
        '\Opis\Colibri\Module\Auth\Collectors\PermissionCollector' => \Opis\Colibri\Serializable\Collection::class,
        '\Opis\Colibri\Module\Auth\Collectors\RolePermissionsCollector' => \Opis\Colibri\Serializable\Collection::class,
    ]));
    override(\Opis\Colibri\collect(0), map([
        '\Opis\Colibri\Module\Auth\Collectors\RealmCollector' => \Opis\Colibri\Serializable\Collection::class,
        '\Opis\Colibri\Module\Auth\Collectors\RoleCollector' => \Opis\Colibri\Serializable\Collection::class,
        '\Opis\Colibri\Module\Auth\Collectors\PermissionCollector' => \Opis\Colibri\Serializable\Collection::class,
        '\Opis\Colibri\Module\Auth\Collectors\RolePermissionsCollector' => \Opis\Colibri\Serializable\Collection::class,
    ]));
}
