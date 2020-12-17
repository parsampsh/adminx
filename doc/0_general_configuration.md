# General configuration
Adminx has some general configurations.

### Admin panel title
You can set title of your admin panel.

```php
$admin = new \Adminx\Core;

$admin->set_title('My admin panel');

// this methods returns the title
$admin->get_title();
```

now, you can see this title in the admin panel.

### The Footer Copyright message
You can set the footer copyright message.

```php
$admin->set_copyright('All rights reserved.');

// this methods returns the copyright
$admin->get_copyright();
```

now, you can see this copyright message at the footer of admin panel.

### Access middleware
The important question is that **Who can access to the admin panel?**

to set that which users can access to the admin panel, you can use `set_middleware` method.

```php
$admin->set_middleware(function(){
    // check some conditions and return a boolean
    return true;
});

$admin->set_middleware(function($user){
    // the logged in user is in $user. we can check conditions on thsi
    // for example
    return $user->access_level === 'manager' || $user->access_level === 'admin';
});
```

This method gets a `Closure` and runs that and passes the current user as argument to that(you can don't set the argument and that is optional), then, if return value of function is `true`, means access is allowed for that user.

By default, if you don't set this middleware, default value is `true`.

also you can check this middleware manually:

```php
$result = $admin->run_middleware();
var_dump($result); // true or false
```
