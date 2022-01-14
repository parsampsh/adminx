# The Adminx Permission access API
Adminx has a class named `Adminx\Access`. this class contains some static methods to handle users permissions.

## userHasPermission
This method checks user has permission or not, returns boolean.

```php
$user = \App\Models\User::find(1); // the user model
\Adminx\Access::userHasPermission($user, '<permission>');
// for example
\Adminx\Access::userHasPermission($user, 'Product.create');
```

## addPermissionForUser
This methods adds a permission for an user.

```php
$user = \App\Models\User::find(1); // the user model
\Adminx\Access::addPermissionForUser($user, '<permission>');
```

Now, user has `<permission>` permission.

Also you can pass a boolean as permission flag. if this boolean is false, means user should have NOT this permission.

```php
$user = \App\Models\User::find(1); // the user model
\Adminx\Access::addPermissionForUser($user, '<permission>', false);
```

## addUserToGroup
This method adds the user to a group.

```php
$user = User::find(1);
$group = \Adminx\Models\Group::find($x);
\Adminx\Access::addUserToGroup($user, $group);
```

Now, the user is added to that group and has all of the group permissions.

## userIsInGroup
This method checks an user is in a group.

```php
$user = User::find(1);
$group = \Adminx\Models\Group::find($x);
$result = \Adminx\Access::userIsInGroup($user, $group); // true or false
```

The output is a boolean.

## removeUserFromGroup
This method removes an user from a group.

```php
$user = User::find(1);
$group = \Adminx\Models\Group::find($x);
\Adminx\Access::removeUserFromGroup($user, $group);
```

Now, user is removed from that group.

## addPermissionForGroup
This method adds a permssion for group.

```php
$group = \Adminx\Models\Group::find($x);
\Adminx\Access::addPermissionForGroup($group, 'the-permission');
```

Now, that group has `the-permission`.

Also you can use flag argument to Deny the permission for the group users:

```php
$group = \Adminx\Models\Group::find($x);
\Adminx\Access::addPermissionForGroup($group, 'the-permission', false);
```

## removePermissionFromGroup
This method removes a permssion from group.

```php
$group = \Adminx\Models\Group::find($x);
\Adminx\Access::removePermissionFromGroup($group, 'the-permission');
```

Now, that group has NOT `the-permission`.

---

[Previous: Plugin system](06_plugins.md)
