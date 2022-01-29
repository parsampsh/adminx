# Admin panel menu links and pages
You can add links and custom pages in the admin panel sidebar.

## Links
To add the links, use `addLink` method.

```php
$admin->addLink('Title of the link', '<link>');
// for example
$admin->addLink('Title of the link', 'https://example.com/foo/bar');
```

Also you can set the target of the link. If you set it to `blank`, link will be opened in new tab. By default, link will be opened in the current tab.

```php
$admin->addLink('Title of the link', 'https://example.com/foo/bar', 'blank');
```

Also you can set icon of the item in menu using css classes.

```php
$admin->addLink('Title of the link', 'https://example.com/foo/bar', 'blank', 'fa fa-user'); // fontawesome
```

## Pages
You can add custom pages to your admin panel. They will be accessible from the menu.

```php
$admin->addPage('Title of the page in menu', 'the-slug-of-page-in-url', function($request){
    // this is your action
    // returned value will be shown to the user in the main content section of the template
    // also you can use $request variable
    return 'hello world! i am a simple page.';
    // or use a view
    return view('some.view');
}, 'fa fa-user(icons as class)', 'blank(target, default is none)');
```

<img src="/doc/images/page-in-menu.png" />

<img src="/doc/images/page.png" />

If you don't want to put response of your custom page inside of the base view template and you want to return it directly, you can use `Adminx\Views\NoBaseViewResponse` class to do it:

```php
$admin->addPage('Test', 'test', function($request){
    //return 'hello world! i am a simple page.';
    return new \Adminx\Views\NoBaseViewResponse('hello world! i am a simple page.');
    return new \Adminx\Views\NoBaseViewResponse(redirect('/somewhere'));
    //...
}, 'fa fa-user(icons as class)', 'blank(target, default is none)');
```

### Index page
To set the index page(http://localhost:yyyy/admin/), you should use `.` for slug option.

For example:

```php
$admin->addPage('Welcome', '.', function($request){
    return view('my.custom.index.page');
});
```

---

[Previous: General configuration](00_general_configuration.md) | [Next: Frontend language customization](02_lang.md)
