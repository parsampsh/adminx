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
|`menu.dashboard`|text of manu dashboard link|`Dashboard`|

also you can use `get_word` and `get_all_words` methods to get words:

```php
$admin->get_word('key');
$admin->get_all_words(); // array
```

---

[Previous: Menu links and pages](01_menu_links_and_pages.md) | [Next: Frontend layout customization](03_frontend_layout_customization.md)
