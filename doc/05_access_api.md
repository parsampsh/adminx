# The Adminx Permission access API
Adminx has a class named `Adminx\Access`. this class contains some static methods to handle users permissions.

## user_has_permission
this method checks user has permission or not, returns boolean.

```php
$user = \App\Models\User::find(1); // the user model
\Adminx\Access::user_has_permission($user, '<permission>');
// for example
\Adminx\Access::user_has_permission($user, 'Product.create');
```

## add_permission_for_user
this methods adds a permission for a user.

```php
$user = \App\Models\User::find(1); // the user model
\Adminx\Access::add_permission_for_user($user, '<permission>');
```

now, user has `<permission>` permission.

also you can pass a boolean as permission flag. if this boolean is false, means user should have NOT this permission.

```php
$user = \App\Models\User::find(1); // the user model
\Adminx\Access::add_permission_for_user($user, '<permission>', false);
```

## add_user_to_group
this method adds the user to a group.

```php
$user = User::find(1);
$group = \Adminx\Models\Group::find($x);
\Adminx\Access::add_user_to_group($user, $group);
```

now, the user is added to that group and has all of the group permissions.

## user_is_in_group
this method checks a user is in a group.

```php
$user = User::find(1);
$group = \Adminx\Models\Group::find($x);
$result = \Adminx\Access::user_is_in_group($user, $group); // true or false
```

the output is a boolean.

## remove_user_from_group
this method removes a user from a group.

```php
$user = User::find(1);
$group = \Adminx\Models\Group::find($x);
\Adminx\Access::remove_user_from_group($user, $group);
```

now, user is removed from that group.

## add_permission_for_group
this method adds a permssion for group.

```php
$group = \Adminx\Models\Group::find($x);
\Adminx\Access::add_permission_for_group($group, 'the-permission');
```

now, that group has `the-permission`.

also you can use flag argument to Deny the permission for the group users:

```php
$group = \Adminx\Models\Group::find($x);
\Adminx\Access::add_permission_for_group($group, 'the-permission', false);
```

## remove_permission_from_group
this method removes a permssion from group.

```php
$group = \Adminx\Models\Group::find($x);
\Adminx\Access::remove_permission_from_group($group, 'the-permission');
```

now, that group has NOT `the-permission`.

---

[Previous: Models](04_models.md)
