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

---

[Previous: Frontend layout customization](03_frontend_layout_customization.md)
