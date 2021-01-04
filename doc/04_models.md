# Models
Adminx can handel CRUD(Create/Read/Update/Delete) actions for your database models fully automaticaly.

To Config your models in adminx, you can use `add_model` method:

```php
$admin->add_model(\App\Models\Product::class, [
    // config the model in here..
]);
```

This method gets two arguments: The model class name and configuration for that model.

look at this example:

```php
$admin->add_model(\App\Models\Product::class, [
    'title' => 'Products',
    'slug' => 'product',
    'icon' => 'fa fa-product-hunt',
    'middleware' => (function($user){
        return $user->can_access_products();
    }),
    // ...
]);
```

This feature adds a item to admin panel menu and users can manage that model by clicking that link.

All of config options:

|Name|Type|Description|
|----|----|-----------|
| `title` | string | This is a general title for the model |
| `slug` | string | Slug of model in url |
| `icon` | string | Icon of item in menu as class |
| `middleware` | Closure | This closure can recive `$user` and return that this user **Can access this model** |
| `target` | string | Target of link of model in menu |
| `no_table_footer` | bool | If this is true, table footer will not show |

### `hidden_fields`
This option makes some fields hidden in the table.

```php
$admin->add_model(\App\Models\User::class, [
    // ...
    'hidden_fields' => ['password'] // the `password` column will be hidden in the table
    // ...
]);
```

### `fields_titles`
This option changes title of a column in table. Default title is name of column.

```php
$admin->add_model(\App\Models\User::class, [
    // ...
    'fields_titles' => [
        'email' => 'The Email', // `The Email` will be showed as title of column `email` in table
    ]
    // ...
]);
```

---

[Previous: Frontend layout customization](03_frontend_layout_customization.md) |
[Next: Access API](05_access_api.md)
