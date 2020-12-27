# Adminx\Core  

The Adminx Core

This Class only keeps Admin panel configuration  





## Methods

| Name | Description |
|------|-------------|
|[add_link](#coreadd_link)|Adds a link item to menu|
|[add_model](#coreadd_model)|Adds a model|
|[add_page](#coreadd_page)|Adds a page to the menu|
|[get_all_words](#coreget_all_words)|Returns all of localization words|
|[get_copyright](#coreget_copyright)|Returns Copyright message of admin panel|
|[get_layout](#coreget_layout)|Returns admin panel default layout view|
|[get_logout](#coreget_logout)|Returns Logout url for user|
|[get_menu](#coreget_menu)|Returns the menu list|
|[get_title](#coreget_title)|Returns title of admin panel|
|[get_userinfo](#coreget_userinfo)|Returns Info of user|
|[get_word](#coreget_word)|Returns a localization word value|
|[register](#coreregister)|Register configured admin panel on routes|
|[run_middleware](#corerun_middleware)|Runs the access middleware and returns the result|
|[set_copyright](#coreset_copyright)|Sets the title of admin panel|
|[set_layout](#coreset_layout)|Returns admin panel default layout view|
|[set_logout](#coreset_logout)|Sets the Logout url for user|
|[set_middleware](#coreset_middleware)|Sets the access middleware|
|[set_title](#coreset_title)|Sets the title of admin panel|
|[set_userinfo](#coreset_userinfo)|Sets Info of user|
|[set_word](#coreset_word)|Sets a word in frontend customization words|
|[url](#coreurl)|Admin panel url|




### Core::add_link  

**Description**

```php
public add_link (string $title, string $link, string $target, string $icon)
```

Adds a link item to menu 

 

**Parameters**

* `(string) $title`
* `(string) $link`
* `(string) $target`
* `(string) $icon`

**Return Values**

`\Core`




<hr />


### Core::add_model  

**Description**

```php
public add_model (string $model, array $config)
```

Adds a model 

 

**Parameters**

* `(string) $model`
* `(array) $config`

**Return Values**

`void`


<hr />


### Core::add_page  

**Description**

```php
public add_page (string $title, string $slug, \Closure $action, string $icon, string $link_target)
```

Adds a page to the menu 

 

**Parameters**

* `(string) $title`
* `(string) $slug`
* `(\Closure) $action`
* `(string) $icon`
* `(string) $link_target`

**Return Values**

`\Core`




<hr />


### Core::get_all_words  

**Description**

```php
public get_all_words (void)
```

Returns all of localization words 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`array`




<hr />


### Core::get_copyright  

**Description**

```php
public get_copyright (void)
```

Returns Copyright message of admin panel 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`




<hr />


### Core::get_layout  

**Description**

```php
public get_layout (void)
```

Returns admin panel default layout view 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`




<hr />


### Core::get_logout  

**Description**

```php
public get_logout (void)
```

Returns Logout url for user 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`




<hr />


### Core::get_menu  

**Description**

```php
public get_menu (void)
```

Returns the menu list 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`array`




<hr />


### Core::get_title  

**Description**

```php
public get_title (void)
```

Returns title of admin panel 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`string`




<hr />


### Core::get_userinfo  

**Description**

```php
public get_userinfo (void)
```

Returns Info of user 

Returned data structure: ['username' => '...', 'image' => '...'] 

**Parameters**

`This function has no parameters.`

**Return Values**

`array`




<hr />


### Core::get_word  

**Description**

```php
public get_word (string $key, string $default)
```

Returns a localization word value 

 

**Parameters**

* `(string) $key`
* `(string) $default`

**Return Values**

`string`




<hr />


### Core::register  

**Description**

```php
public register (string $route)
```

Register configured admin panel on routes 

 

**Parameters**

* `(string) $route`

**Return Values**

`void`


<hr />


### Core::run_middleware  

**Description**

```php
public run_middleware (void)
```

Runs the access middleware and returns the result 

 

**Parameters**

`This function has no parameters.`

**Return Values**

`bool`




<hr />


### Core::set_copyright  

**Description**

```php
public set_copyright (string $copyright)
```

Sets the title of admin panel 

 

**Parameters**

* `(string) $copyright`

**Return Values**

`\Core`




<hr />


### Core::set_layout  

**Description**

```php
public set_layout (string $layout)
```

Returns admin panel default layout view 

 

**Parameters**

* `(string) $layout`

**Return Values**

`\Core`




<hr />


### Core::set_logout  

**Description**

```php
public set_logout (string $logout)
```

Sets the Logout url for user 

 

**Parameters**

* `(string) $logout`

**Return Values**

`\Core`




<hr />


### Core::set_middleware  

**Description**

```php
public set_middleware (\Closure $middleware)
```

Sets the access middleware 

 

**Parameters**

* `(\Closure) $middleware`

**Return Values**

`\Core`




<hr />


### Core::set_title  

**Description**

```php
public set_title (string $title)
```

Sets the title of admin panel 

 

**Parameters**

* `(string) $title`

**Return Values**

`\Core`




<hr />


### Core::set_userinfo  

**Description**

```php
public set_userinfo (\Closure $userinfo)
```

Sets Info of user 

 

**Parameters**

* `(\Closure) $userinfo`

**Return Values**

`\Core`




<hr />


### Core::set_word  

**Description**

```php
public set_word (string $key, string $value)
```

Sets a word in frontend customization words 

 

**Parameters**

* `(string) $key`
* `(string) $value`

**Return Values**

`\Core`




<hr />


### Core::url  

**Description**

```php
public url (string $url)
```

Admin panel url 

 

**Parameters**

* `(string) $url`

**Return Values**

`string`




<hr />

