# The Adminx Permission access API
Adminx has a class named `Adminx\Access`. this class contains some static methods to handle users permissions.

### user_has_permission
this method checks user has permission or not, returns boolean.

```php
$user = \App\Models\User::find(1); // the user model
\Adminx\Access::user_has_permission($user, '<permission>');
// for example
\Adminx\Access::user_has_permission($user, 'Product.create');
```

### add_permission_for_user
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
