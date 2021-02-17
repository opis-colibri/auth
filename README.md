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
    ->setRoleCollector(MyRealmRoleCollector::class)
    ->setPermissionCollector(MyRealmPermissionCollector::class)
    ->setRolePermissionCollector(MyRealmRolePermissionCollector::class);

entityManager()->save($realm);
```

Every realm must define its own collectors.

```php
class MyRealmRoleCollector extends RoleCollector
{
    // Don't forget to register your collector
}

class MyRealmPermissionCollector extends PermissionCollector
{
    // Don't forget to register your collector
}

class MyRealmRolePermissionCollector extends RolePermissionCollector
{
    // Don't forget to register your collector
}
```

This will enable modules to collect roles and permissions for your realm.

```php
class Collector extends BaseCollector 
{
    public function myRealmRoles(MyRealmRoleCollector $roles)
    {
        $roles
            ->register('some-role', 'Description of this role')
            ->register('other-role', 'Description of this role');
    }
    
    public function myRealmPermissions(MyRealmPermissionCollector $permisisons)
    {
        $permisisons
            ->register('some-permission', 'Description of this permission')
            ->register('other-permission', 'Description of this permission');
    }
    
    public function myRealmRolePermissions(MyRealmRolePermissionCollector $rolePermission)
    {
        $rolePermission
            ->register('some-role', ['some-permission', 'other-permission'])
            ->register('other-role', ['other-permission']);
    }
}
```

### Realm instances

You can retrieve an instance of a specific realm by using the `get` method.

```php
$realm = Realm::get('my-realm');
```