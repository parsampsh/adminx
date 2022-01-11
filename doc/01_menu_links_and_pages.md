# Admin panel menu links and pages
You can add links and custom pages in the admin panel sidebar.

## Links
To add the links, use `add_link` method.

```php
$admin->add_link('Title of the link', '<link>');
// for example
$admin->add_link('Title of the link', 'https://example.com/foo/bar');
```

Also you can set the target of the link. If you set it to `blank`, link will be opened in new tab. By default, link will be opened in the current tab.

```php
$admin->add_link('Title of the link', 'https://example.com/foo/bar', 'blank');
```

Also you can set icon of the item in menu using css classes.

```php
$admin->add_link('Title of the link', 'https://example.com/foo/bar', 'blank', 'fa fa-user'); // fontawesome
```

## Pages
You can add custom pages to your admin panel. They will be accessible from the menu.

```php
$admin->add_page('Title of the page in menu', 'the-slug-of-page-in-url', function($request){
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

### Index page
To set the index page(http://localhost:yyyy/admin/), you should use `.` for slug option.

For example:

```php
$admin->add_page('Welcome', '.', function($request){
    return view('my.custom.index.page');
});
```

---

[Previous: General configuration](00_general_configuration.md) | [Next: Frontend language customization](02_lang.md)
