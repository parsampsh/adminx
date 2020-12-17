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
