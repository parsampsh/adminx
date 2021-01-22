# Frontend language customization
You can customize the language of admin panel.

This is too easy. You only need to use `set_word` method.

For example:

```php
$admin->set_word('menu.dashboard', 'Huvudsida');
$admin->set_word('foo', 'bar');
// ...
```

The first argument is `key` and the second argument is value. In the above example, `menu.dashboard` key points to admin panel menu home page link. In the above example, default value `Dashboard` will be changed to `Huvudsida`.

All of keys:

|Key|Description|Default|
|---|-----------|-------|
|`logout.title`|title of logout window|`Ready to leave?`|
|`logout.btn`|text of logout button|`Logout`|
|`logout.message`|the message of logout window|`Select "Logout" below if you are ready to end your current session.`|
|`logout.cancel`|text of logout cancel button|`Cancel`|
|`menu.dashboard`|text of menu dashboard link|`Dashboard`|
|`btn.delete`|text of model delete button|`Delete`|
|`tbl.action`|title of actions column in models table|`Actions`|
|`btn.create`|title of create button|`Create new {name}` (you should write exactly `{name}` for model name)|
|`btn.back`|title of back button|`Back`|
|`btn.update`|title of update button|`Update`|
|`btn.log`|title of history button|`History`|
|`user.btn.log`|title of history button in user menu|`Activity History`|

also you can use `get_word` and `get_all_words` methods to get words:

```php
$admin->get_word('key');
$admin->get_all_words(); // array
```

## Right to left
You can set the admin panel layout Right to left.

```php
$admin->enable_rtl(); // this method enables the rtl mode
```

also you can check is rtl enable:

```php
echo $admin->is_rtl(); // returns boolean
```

also you can disable the rtl mode by passing `false` to `enable_rtl` method:

```php
$admin->enable_rtl(false);
```

<img src="/doc/images/rtl.png" />

## Font
You can change default font of Adminx default layouts.

You can use `set_font` method:

```php
$admin->set_font('/url/to/font');

// examples
$admin->set_font('/fonts/Some-font.ttf');
$admin->set_font('https://example.com/font.woff');
```

Also you can get current font with `get_font`:

```php
echo $admin->get_font();
```

---

[Previous: Menu links and pages](01_menu_links_and_pages.md) | [Next: Frontend layout customization](03_frontend_layout_customization.md)
