# Plugins
Adminx has an useful plugin system. You can write your own plugin or use other plugins.

Plugin system is so simple, plugin is a class that can make effect on admin panel configurations.

For example:

```php
// define the plugin
class MyAdminxPlugin {
    public function run(\Adminx\Core $admin, array $options=[]) {
        // example
        $admin->add_page('My plugin page', 'my-plugin'...);
    }
}

$admin = new \Adminx\Core;

// add the plugin to your admin panel
$admin->add_plugin(MyAdminxPlugin::class); // then, plugin makes effects on your admin panel

$admin->register();
```

In the plugin defining, method `run` of plugin class will be ran and admin core object will be
passed to that. Then, plugin can make effects on admin panel in that method.

Also there is a second argument, `$options`. this argument contains user passed options:

```php
$admin->add_plugin(MyAdminxPlugin::class, [
    'option1' => 'value',
    'foo' => 'bar',
    // ...
]);
```

Like above example, second argument will be passed to `run` as user options.

---

[Previous: Log system](05_log_system.md) | [Next: Themes](07_themes.md)
