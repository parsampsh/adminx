# Adminx\Access  

The adminx permission and group API

This Class Has many static methods to
check user permissions and groups  





## Methods

| Name | Description |
|------|-------------|
|[add_permission_for_group](#accessadd_permission_for_group)|Adds a permission to group|
|[add_permission_for_user](#accessadd_permission_for_user)|Adds a permission for user|
|[add_user_to_group](#accessadd_user_to_group)|Adds a user to a group|
|[remove_permission_from_group](#accessremove_permission_from_group)|Removes a permission from group|
|[remove_user_from_group](#accessremove_user_from_group)|Removes a user from a group|
|[user_has_permission](#accessuser_has_permission)|Checks a user has the permission|
|[user_is_in_group](#accessuser_is_in_group)|Checks user is in a group|




### Access::add_permission_for_group  

**Description**

```php
public static add_permission_for_group (\Group $group, string $permission, bool $flag)
```

Adds a permission to group 

 

**Parameters**

* `(\Group) $group`
* `(string) $permission`
* `(bool) $flag`

**Return Values**

`void`


<hr />


### Access::add_permission_for_user  

**Description**

```php
public static add_permission_for_user (\User $user, string $permission, bool $flag)
```

Adds a permission for user 

The $flag is a boolean. if this is true, means user has this permission  
and if this is false, means user Has NOT this permission 

**Parameters**

* `(\User) $user`
* `(string) $permission`
* `(bool) $flag`

**Return Values**

`bool`




<hr />


### Access::add_user_to_group  

**Description**

```php
public static add_user_to_group (\User $user, \Group $group)
```

Adds a user to a group 

 

**Parameters**

* `(\User) $user`
* `(\Group) $group`

**Return Values**

`bool`




<hr />


### Access::remove_permission_from_group  

**Description**

```php
public static remove_permission_from_group (\Group $group, string $permission)
```

Removes a permission from group 

 

**Parameters**

* `(\Group) $group`
* `(string) $permission`

**Return Values**

`bool`




<hr />


### Access::remove_user_from_group  

**Description**

```php
public static remove_user_from_group (\User $user, \Group $group)
```

Removes a user from a group 

 

**Parameters**

* `(\User) $user`
* `(\Group) $group`

**Return Values**

`bool`




<hr />


### Access::user_has_permission  

**Description**

```php
public static user_has_permission (\User $user, string $permission)
```

Checks a user has the permission 

 

**Parameters**

* `(\User) $user`
* `(string) $permission`

**Return Values**

`bool`




<hr />


### Access::user_is_in_group  

**Description**

```php
public static user_is_in_group (\User $user, \Group $group)
```

Checks user is in a group 

 

**Parameters**

* `(\User) $user`
* `(\Group) $group`

**Return Values**

`bool`




<hr />

