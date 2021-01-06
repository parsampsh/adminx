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
| `per_page` | int | Table rows per page |
| `only_top_pagination` | bool | Only show top pagination |
| `only_bottom_pagination` | bool | Only show bottom pagination |

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

### `fields_values`
This option helps you to change default original value of a column in table(only for show).

```php
$admin->add_model(\App\Models\User::class, [
    // ...
    'fields_values' => [
        'email' => (function($row){
            // show a link to email instead of email string
            // this will be used only in table
            return '<a href="mailto:' . $row->email . '">' . $row->email . '</a>';
        }),
    ]
    // ...
]);
```

### `filter_data`
This option can customize table rows. This should be a Closure and gets the Query Builder, then you can customize the query and return changed query.

```php
$admin->add_model(\App\Models\User::class, [
    // ...
    'filter_data' => (function($query){
        return $query->orderBy('name', 'asc')->where('is_enable', 1); // or other conditions
    }),
    // ...
]);
```

### `virtual_fields`
This option can create some **Virtual fields**. This helps to add some options to the table which does not exists really in database.

For example, we have Posts table and posts have Comments. We want to show count of comments in a column.

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    'virtual_fields' => [
        'Posts Count' => (function($post){
            return $post->comments()->count();
            // or use html tags
            return '<a>' . $post->comments()->count() . '</a>';
        }),
    ],
    // ...
]);
```

---

[Previous: Frontend layout customization](03_frontend_layout_customization.md) |
[Next: Access API](05_access_api.md)
