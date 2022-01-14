# Plugins
Adminx has an useful plugin system. You can write your own plugin or use other's plugins.

Plugin system is so simple, plugin is a class that can make effects on admin panel configurations.

For example:

```php
// define a plugin
class MyAdminxPlugin implements \Adminx\Plugins\IPlugin {
    public function run(\Adminx\Core $admin, array $options=[]) {
        // example
        $admin->addPage('My plugin page', 'my-plugin'...);
    }
}

$admin = new \Adminx\Core;

// add the plugin to your admin panel
$admin->addPlugin(new MyAdminxPlugin); // then, plugin makes effects on your admin panel

$admin->register();
```

In the plugin defining, method `run` of plugin class will be ran and admin core object will be
passed to that. Then, plugin can make effects on admin panel inside of that method.

Also there is a second argument, `$options`. This argument contains user passed options:

```php
$admin->addPlugin(new MyAdminxPlugin, [
    'option1' => 'value',
    'foo' => 'bar',
    // ...
]);
```

The plugin class has to implement interface `\Adminx\Plugins\IPlugin`.

When you wanna add a plugin to your admin panel, you must pass a object from the plugin class to `addPlugin` method:

```php
$admin->addPlugin(new MyTestPlugin);

// also you can add additional options
$admin->addPlugin(new MyTestPlugin, ['foo' => 'bar']);
```

Like above example, second argument will be passed to `run` as user options.

---

[Previous: Log system](05_log_system.md) | [Next: Themes](07_themes.md)
