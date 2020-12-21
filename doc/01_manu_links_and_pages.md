# Admin panel manu links and pages
you can add links and custom pages to the admin panel sidebar.

### Links
to add the links, use `add_link` method.

```php
$admin->add_link('Title of the link', '<link>');
// for example
$admin->add_link('Title of the link', 'https://example.com/foo/bar');
```

also you can set the target of the link. if you set `blank`, link will be opened in new tab. by default, link will be opened in current tab.

```php
$admin->add_link('Title of the link', 'https://example.com/foo/bar', 'blank');
```

also you can set icon of the item in menu using classes.

```php
$admin->add_link('Title of the link', 'https://example.com/foo/bar', 'blank', 'fa fa-user'); // fontawesome
```

- [Previous: General Configuration](00_general_configuration.md)

### Pages
You can add custom pages to your admin panel. they will be accessible from the menu.

```php
$admin->add_page('Title of the page in menu', 'the-slug-of-page-in-url', function($request){
    // this is your action
    // return value will be showed to user in the main content section of the view
    // also you can use $request variable
    return 'hello world! i am a simple page.';
}, 'fa fa-user(icons as class)', 'blank(target, default is none)');
```
