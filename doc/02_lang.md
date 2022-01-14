# Frontend language customization
You can customize the language of admin panel.

This is so easy. You just need to use `setWord` method.

For example:

```php
$admin->setWord('menu.dashboard', 'Huvudsida');
$admin->setWord('foo', 'bar');
// ...
```

The first argument is the `key` and the second argument is the value. In the above example, `menu.dashboard` key points to admin panel menu home page link. In the above example, default value `Dashboard` will be changed to `Huvudsida` (a swedeish word).

All of keys:

|Key|Description|Default|
|---|-----------|-------|
|`logout.title`|Title of logout window|`Ready to leave?`|
|`logout.btn`|Text of logout button|`Logout`|
|`logout.message`|The message of logout window|`Select "Logout" below if you are ready to end your current session.`|
|`logout.cancel`|Text of logout cancel button|`Cancel`|
|`menu.dashboard`|Text of menu dashboard link|`Dashboard`|
|`btn.delete`|Text of model delete button|`Delete`|
|`tbl.action`|Title of actions column in models table|`Actions`|
|`btn.create`|Title of create button|`Create new {name}` (you should write exactly `{name}` for model name)|
|`btn.back`|Title of back button|`Back`|
|`btn.update`|Title of update button|`Update`|
|`btn.log`|Title of history button|`History`|
|`user.btn.log`|Title of history button in user menu|`Activity History`|

Also you can use `getWord` and `getAllWords` methods to get words:

```php
$admin->getWord('key');
$admin->getAllWords(); // array
```

## Right to left
You can set the admin panel layout to right to left mode.

```php
$admin->enableRtl(); // this method enables the rtl mode
```

Also you can check that is the rtl enabled:

```php
echo $admin->isRtl(); // returns boolean
```

Also you can disable the rtl mode by passing `false` to `enableRtl` method:

```php
$admin->enableRtl(false);
```

<img src="/doc/images/rtl.png" />

## Font
You can change the default font of Adminx default layouts.

You can use `setFont` method:

```php
$admin->setFont('/url/to/font');

// examples
$admin->setFont('/fonts/Some-font.ttf');
$admin->setFont('https://example.com/font.woff');
```

Also you can get the current font with `getFont` method:

```php
echo $admin->getFont();
```

---

[Previous: Menu links and pages](01_menu_links_and_pages.md) | [Next: Frontend layout customization](03_frontend_layout_customization.md)
