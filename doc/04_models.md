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

<img src="/doc/images/model-table.png" />

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

### `custom_html` & `custom_html_bottom`
This options help you to show a custom html/text in the top and bottom of the table.

You should set a Closure as value for them. out put of the closure will be showd.

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    'custom_html' => (function(){
        return 'this is the custom TOP html';
        // or use view
        return view('some.view');
    }),

    'custom_html_bottom' => (function(){
        return 'this is the custom BOTTOM html';
        // or use view
        return view('some.view');
    }),
    // ...
]);
```

You can use this feature to create advance filter options for you data, etc.

TODO : add a image

### Searching
You can add a text search option for model.

for example:

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    'search' => (function($q, $word){
        // filter the query by searching in title
        // or any algorithm you want
        return $q->where('title', 'like', '%'.$word.'%');
    }),

    `search_hint` => 'Search by post title',
    // ...
]);
```

You should set `search` option to a closure. Then, a search box will be added to your model page. user can search with that box. when user are searching something, the query builder and searched word will be passed to your closure. then you can filter the query by searched word and return new query.

Also you can write a hint for search box by `search_hint` option(default value for this, is `Search here...`).

TODO : add a image

### Delete middleware
This option is for handling delete permission. by default, adminx checks user permission for delete the item. for example, user should have `user.delete` permission in database(You will learn about this is the **Access API** next part) to delete a item in users table. But also you can handle some exceptions by using this `delete_middleware` to customize this action permissions.

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    // this closure should return boolean
    'delete_middleware' => (function($user, $post){
        // for example:
        return $user->is_manager() || $post->user_id == $user->id;
        // if result id true, user can delete the item
    }),
    // ...
]);
```

### Create middleware
This option is like `delete_middleware` but for create action. by default, this permission is handled by adminx permission system but you can check some exceptions by this option.

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    // this closure should return boolean
    'create_middleware' => (function($user, $post){
        // for example:
        return $user->can_create_new_post_or_something_else();
    }),
    // ...
]);
```

The Create form:

TODO : add a image from create form

### Update middleware
This option is like `create_middle` but for update action. by default, this permission is handled by adminx permission system but you can check some exceptions by this option.

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    // this closure should return boolean
    'update_middleware' => (function($user, $post){
        // for example:
        return $user->id === $post->id;
    }),
    // ...
]);
```

### `readonly_fields`, `only_addable_fields`, `only_editable_fields`
The `readonly_fields` option declares some columns READONLY. the readonly column value cannot be set in create and update form.

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    'readonly_fields' => ['something'],
    // ...
]);
```

(By default, `created_at`, `updated_at` and `deleted_at` fields are readonly).

But sometimes you want to set some fields readonly ONLY for UPDATE action.
means you want to set value of this field in CREATE action, but this not be editable in update action.

To do this, you should add that field to `readonly_fields` AND `only_addable_fields`:

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    'readonly_fields' => ['something'],
    'only_addable_fields' => ['something'],
    // ...
]);
```

In the above example, value of column `something` can be set in CREATE action, but cannot be changed in UPDATE action.

Also `only_editable_fields` option is like `only_addable_fields` but for Update action.

If you want to set a field that to don't get value in Create action, but can be edited in Update action,
You should put that field in `readonly_fields` and `only_editable_fields`:

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    'readonly_fields' => ['something'],
    'only_editable_fields' => ['something'],
    // ...
]);
```

### `fields_comments`
This option sets comment for fields. this comment will be showed as placeholder in create and update forms.

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    'fields_comments' => ['body' => 'Content of the post'],
    // ...
]);
```

TODO : add a image

### Foreign Keys
Handling foreign keys is so easy in adminx.

You should set a option named `foreign_keys`.

`1 to N` example:

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    // for example, posts have a user_id:
    'foreign_keys' => [
        'user_id' => [
            // you should set model of foreign table with `model` option
            'model' => \App\Models\User::class,
            'list' => (function(){
                // in the `list` closure, you should return list of foreign table
                // (This will be used for Create ad Update forms)
                return \App\Models\User::all();
                // also you can optimize your query and select only tha thing you want to use
                return \App\Models\User::all(['id', 'username']);
            }),
            'title' => (function($row){
                // determine a title for item
		// remember, this output will be showed as html! if you want to make this secure, use `htmlspecialchars()`
                return $row->username;
            }),
        ]
    ],
    // ...
]);
```

In the above example, column `user_id` will be set as a foreign key and instead of `user_id`,
The title will be showed as value of column, and also in Create and Update forms, A select box
will be showed and user can select a item. (The 1 To N relationship)

TODO : add a image

### Foreign Keys: N to N Relations
Adminx can handle N to N relations between models.

You should use option `n2n`:

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    'n2n' => [
        [
            'name' => 'Categories', // name of this relation
            'model' => \App\Models\Category::class, // which model the Post model has n2n relation with
            'pivot' => \App\Models\PostCategory::class, // the pivot table
            'pivot_keys' => ['post_id', 'category_id'], // the pivot table keys. first is Post id key, second is Category id key
            'list' => (function(){
                // closure to load list of Categories
                return \App\Models\Category::all();
            }),
            'title' => (function($row){
                // determine title for each Post
                return $row->title;
            }),
        ],
        [
            // other relation...
        ],
    ],
    // ...
]);
```

Then, Adminx loads categories with `list` closure and shows that as a multiple select box
in Create/Update forms for Post model.

TODO : add a image

### `filter_create_data`, `filter_update_data`
`filter_create_data` option, is a option to customize user entered data for Create action.
By default, Adminx Validates user data, sets data on a model object, But you can customize the data(Optional).

For example:

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    'filter_create_data' => (function($row){
        // make changes on $row and return the $row
        // for example:
        $row->body .= 'something else';
        // return the row
        return $row;
    }),
    // ...
]);
```

Also `filter_update_data` is for Update form:

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    'filter_update_data' => (function($old, $row){
        // $old is the old row data and $row is new edited data!
        // make changes on $row and return the $row
        // for example:
        $row->body .= 'something else';
        // return the row
        return $row;
    }),
    // ...
]);
```

### `after_create_go_to`, `after_update_go_to`
This option is for customizing that after create action, user will be redirected to where.

Valid values:
- `table`: back to the table
- `stay`: stay at create page to create new
- `update`: go to update form of created item

Default is `update`.

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    'after_create_go_to' => 'table',
    'after_update_go_to' => 'update', // this is even like after_create_go_to but for update form
    // ...
]);
```

### Custom actions
There is a column named `actions` in model table. By default, there is delete and update buttons.

You can add some custom buttons to this column using custom actions system.

For example:

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    'actions' => [
        'my_custom_button' => [
            'title' => 'my button',
            'class' => '(html-classes) btn btn-primary',
            'middleware' => (function($user, $row){
                // gets $user and $row
                // you should determine show this button for $row for $user
                return $user->id === $row->user_id; // exmaple
            }),
            'run' => (function($post){
                // will be runed when button is pressed for $post
                // do something...
                
                // return the response
                // this response will be returned as http response
                // for example:
                return view('some.view');
                return redirect('/some/place');
                // ...
            }),
        ],
    ],
    // ...
]);
```

TODO : add a image

### `create_html`, `update_html`
This option can make a custom html inside create/update form.

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    'create_html' => (function(){
        return 'this is my custom html';
        // also you can use view
        return view('some.view');
    }),
    // ...
]);
```

Then, in create form, output of this closure will be showed in end of create form.
For example, You can make custom inputs for form and use them in `filter_create_data`. 

Also `update_html` is even like `create_html` but this closure can get `$row` user are editing:

```php
$admin->add_model(\App\Models\Post::class, [
    // ...
    'update_html' => (function($post){
        return 'this is my custom html';
        // also you can use view
        return view('some.view');
    }),
    // ...
]);
```

TODO : add a image

---

[Previous: Frontend layout customization](03_frontend_layout_customization.md) |
[Next: Log System](05_log_system.md)
