Auth
------

## Realms

You can create a new realm by adding a new entry in the `realms` table.

```php
$realm = entityManager()->create(Realm::class);

$realm
    ->setId('my-realm')
    ->setName('My realm')
    ->setDescription('This is my realm')
    ->setSessionName('my-realm-session')
    ->setCollectorClass(MyRealmRoleCollector::class);

entityManager()->save($realm);
```

Every realm must have a role collector that extends from `Opis\Colibri\Module\Auth\Collectors\BaseRoleCollector`.

```php
class MyRealmRoleCollector extends BaseRoleCollector
{
    // Don't forget to register your role collector
}
```

This will enable modules to collect roles for your realm.

```php
class Collector extends BaseCollector 
{
    public function myRealmRoles(MyRealmRoleCollector $roles)
    {
        $roles
            ->register('some-role', 'Some role', 'Description of this role')
            ->register('other-role', 'Other role', 'Description of this role');
    }
}
```

### Default realm

The module provides a default `default` realm that uses the default session storage and the 
`Opis\Colibri\Module\Auth\Collectors\RoleCollector` class to collect roles.

### Realm instances

You can retrieve an instance of a specific realm by using the `get` method.

```php
$realm = Realm::get('my-realm');
// Get default realm
$realm = Realm::get();
```