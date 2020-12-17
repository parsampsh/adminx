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
