# General configuration
Adminx has some general configurations.

## Admin panel title
You can set title of your admin panel.

```php
$admin = new \Adminx\Core;

$admin->set_title('My admin panel');

// this methods returns the title
$admin->get_title();
```

Now, you can see this title in the admin panel.

<img src="/doc/images/title.png" />

## The footer copyright message
You can set the footer copyright message.

```php
$admin->set_copyright('All rights reserved.');

// this methods returns the copyright
$admin->get_copyright();
```

Now, you can see this copyright message at the footer of the admin panel.

<img src="/doc/images/copyright.png" />

## Access middleware
One important question is **Who can access to the admin panel?**

To determine that which users can access to the admin panel, you should use `set_middleware` method.

```php
$admin->set_middleware(function(){
    // check some conditions and return a boolean
    return true;
});

$admin->set_middleware(function($user){
    // the logged in user is in $user. we can check conditions on this
    // for example
    return $user->access_level === 'manager' || $user->access_level === 'admin';
});
```

This method gets a `Closure` and runs that and passes the current user as an argument to that(you can don't set the argument and that is optional), then if return value of function is `true`, means access is allowed for that user.

By default, if you don't set this middleware, default value is `true`.

Also you can check this middleware manually:

```php
$result = $admin->run_middleware();
var_dump($result); // true or false
```

## Logout url of user
The Adminx panel, has a logout button. you can set the link of that button to a route you determined for user to logout.

```php
$admin->set_logout('/user/logout');

// you can get the link with this method
$admin->get_logout();
```

Default value for this item is `/auth/logout`.

<img src="/doc/images/logout-btn.png" />
<img src="/doc/images/logout-window.png" />

## User info
There is user information at the Adminx panel top right: Username and image.

You can set values of them by using `set_userinfo` method.

```php
$admin->set_userinfo(function($user){
    return [
        'username' => $user->name,
        'image' => '/link/to/users/images/' . $user->img,
    ];
});
```

This method gets a closure and passes the user object to that(`$user` argument is optional) and this closure should return a dictonary contains two keys: `username` and `image`. The `username` will be shown as username of the user and `image` will be used as user profile image address.

Also you can get user info by using `get_userinfo` method.

```php
$user_info = $admin->get_userinfo();
var_dump($user_info); // {'username' => '...', 'image' => '...'}
```

<img src="/doc/images/userinfo.png" />

## Super user
There is an important thing in admin panels: The manager or Super user.

Super user is an user who can do everything!

To determine that who is super user in Adminx, you should use `super_user` method:

```php
$admin->super_user(function($user){
    return (bool) $user->is_manager();
});
```

This method gets a closure and that closure should recive user object and return a boolean.
If true is returned, means that user is Super user.

## An example

```php
$admin = new \Adminx\Core;

$admin
    ->set_title('My admin panel')
    ->set_copyright('All rights reserved')
    ->set_logout('/auth/logout')
    ->set_userinfo(function($user){
        return [
            'username' => $user->username
        ];
    })
    ->register('/admin');

```

---

[Next: Menu links and pages](01_menu_links_and_pages.md)
