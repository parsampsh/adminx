<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2022 Parsa Shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

namespace Adminx;

use Adminx\AdminxController;
use Illuminate\Support\Facades\Route;
use Adminx\Plugins\IPlugin;

/**
 * Adminx Core object
 *
 * This class is used to define admin panel configurations and register it
 *
 * @package Adminx
 */
class Core
{
    public string $adminx_version = '0.1';

    /**
     * The current registered admin panel
     */
    public static ?Core $core;

    function __construct() {
        // config Adminx Group model
        $admin = $this;
        $this->addModel(\Adminx\Models\Group::class, [
            'title' => 'Groups',
            'slug' => 'AdminxGroup',
            'icon' => 'fa fa-object-group',
            'create_html' => (function() use ($admin){
                $permissions = [];
        
                foreach ($admin->getMenu() as $item) {
                    if ($item['type'] === 'model') {
                        array_push($permissions, $item['config']['slug'] . '.create');
                        array_push($permissions, $item['config']['slug'] . '.update');
                        array_push($permissions, $item['config']['slug'] . '.delete');
                    }
                }
        
                $output = "
                Permissions:
                <select name='permissions[]' multiple='multiple' class='select2-box form-control'>
                ";
                
                foreach ($permissions as $value) {
                    $output .= '<option value="' . $value .'">' . $value .'</option>';
                }
        
                $output .= "
                </select>
                <br />
                <br />
                ";
        
                return $output;
            }),
        
            'update_html' => (function($group) use ($admin){
                $permissions = [];
        
                foreach ($admin->getMenu() as $item) {
                    if ($item['type'] === 'model') {
                        array_push($permissions, $item['config']['slug'] . '.create');
                        array_push($permissions, $item['config']['slug'] . '.update');
                        array_push($permissions, $item['config']['slug'] . '.delete');
                    }
                }
        
                $output = "
                Permissions:
                <select name='permissions[]' multiple='multiple' class='select2-box form-control'>
                ";
                
                foreach ($permissions as $value) {
                    $selected = '';
                    if ($group->permissions()->where('permission', $value)->count() > 0) {
                        $selected = ' selected';
                    }
                    $output .= '<option' . $selected . ' value="' . $value .'">' . $value .'</option>';
                }
        
                $output .= "
                </select>
                <br />
                <br />
                ";
        
                return $output;
            }),
        
            'filter_create_data' => (function($group){
                $permissions = request()->input('permissions');
        
                $group->save();
        
                if (is_array($permissions)) {
                    foreach ($permissions as $permission) {
                        $p = new \Adminx\Models\GroupPermission;
                        $p->permission = $permission;
                        $p->adminx_group_id = $group->id;
                        $p->flag = 1;
                        $p->save();
                    }
                }
        
                return $group;
            }),
        
            'filter_update_data' => (function($group){
                $permissions = request()->input('permissions');
        
                $group->save();
        
                if (is_array($permissions)) {
                    $group->permissions()->delete();
        
                    foreach ($permissions as $permission) {
                        $p = new \Adminx\Models\GroupPermission;
                        $p->permission = $permission;
                        $p->adminx_group_id = $group->id;
                        $p->flag = 1;
                        $p->save();
                    }
                }

                return $group;
            }),

            'virtual_fields' => [
                'Permissions' => (function($group){
                    $permissions = $group->permissions;

                    $output = '';

                    $i = 0;
                    foreach ($permissions as $permission) {
                        $output .= htmlspecialchars($permission->permission);
                        if ($i < count($permissions)-1) {
                            $output .= ', ';
                        }
                        $i++;
                    }

                    return $output;
                }),
            ],
        ]);
    }

    /**
     * Title of the admin panel
     */
    private string $title = 'Adminx Panel';

    /**
     * Sets the title of admin panel
     * 
     * @param string $title
     * @return Core
     */
    public function setTitle(string $title): Core
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Returns title of admin panel
     * 
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Copyright message of the admin panel
     */
    private string $copyright = 'Copyright';

    /**
     * Sets the title of admin panel
     * 
     * @param string $copyright
     * @return Core
     */
    public function setCopyright(string $copyright): Core
    {
        $this->copyright = $copyright;
        return $this;
    }

    /**
     * Returns Copyright message of admin panel
     * 
     * @return string
     */
    public function getCopyright(): string
    {
        return $this->copyright;
    }

    /**
     * The Default route prefix for admin panel routes
     */
    protected string $route_prefix = '/';

    /**
     * Register configured admin panel on routes
     * 
     * @param string $route
     */
    public function register(string $route='/admin')
    {
        // add Adminx Log model
        $admin = $this;
        $this->addModel(\Adminx\Models\Log::class, [
            'slug' => 'AdminxLog',
            'title' => 'Logs',
            'icon' => 'fa fa-history',
            'hidden_fields' => ['updated_at'],
            'fields_titles' => [
                'created_at' => 'At',
                'user_id' => 'User',
                'item_id' => 'Item',
                'model' => 'Table',
                'action' => 'Action',
                'message' => 'Message',
            ],
            'fields_values' => [
                'model' => (function($log) use ($admin) {
                    return '<a href="' . $admin->url('/model/' . $log->model) . '">' . $log->model . '</a> <a href="?filter_model=' . $log->model . '">(Filter)</a>';
                }),
                'item_id' => (function($log) use ($admin) {
                    return '<a href="' . $admin->url('/model/' . $log->model . '/update/' . $log->item_id) . '?back=' . request()->fullUrl() . '">' . $log->item_id . '</a> <a href="?filter_item=' . $log->item_id . '">(Filter)</a>';
                }),
                'user_id' => (function($log) use ($admin) {
                    return '<a href="' . $admin->url('/model/User/update/' . $log->user_id) . '?back=' . request()->fullUrl() . '">' . $log->user_id . '</a> <a href="?filter_user=' . $log->user_id . '">(Filter)</a>';
                }),
            ],
            'custom_html' => (function() use ($admin){
                $output = '
                Filter Model:
                <form action="'. request()->fullUrl() .'">
                <select name="filter_model" class="select2-box form-control">
                <option value="">(None)</option>
                ';
                foreach($admin->getMenu() as $item) {
                    if ($item['type'] === 'model') {
                        if ($item['config']['slug'] !== 'AdminxLog') {
                            $selected = '';
                            if (request()->get('filter_model') === $item['config']['slug']) {
                                $selected = 'selected';
                            }
                            $output .= '<option '.$selected.' value="'. $item['config']['slug'] .'">' . $item['config']['slug'] . '</option>';
                        }
                    }
                }
                $output .= '
                </select>
                <br />
                <br />
                <input type="submit" value="Filter" class="btn btn-success" />
                </form>
                <br />
                <a class="btn btn-success" href="'. request()->url() .'">Remove filters</a>
                <br />
                <br />
                ';
                return $output;
            }),
            'create_middleware' => (function(){return false;}),
            'update_middleware' => (function(){return false;}),
            'delete_middleware' => (function(){return false;}),
            'filter_data' => (function($q) use ($admin) {
                $q = $q->orderBy('created_at', 'desc');
                if (!$admin->checkSuperUser(auth()->user())) {
                    $q = $q->where('user_id', auth()->user()->id);
                }
                if (request()->get('filter_model') !== null) {
                    $q->where('model', request()->get('filter_model'));
                }
                if (request()->get('filter_user') !== null) {
                    $q->where('user_id', request()->get('filter_user'));
                }
                if (request()->get('filter_item') !== null) {
                    $q->where('item_id', request()->get('filter_item'));
                }
                return $q;
            }),
        ]);

        $this->route_prefix = $route;

        static::$core = clone $this;

        // register views
        $paths = config('view.paths');
        $tmp_path = __DIR__ . '/Views';
        array_push($paths, realpath($tmp_path));
        config(["view.paths" => $paths]);

        // register routes
        Route::prefix($route)->group(function () {
            Route::middleware(['web', 'auth'])->group(function () {
                Route::any('/', [AdminxController::class, 'index']);
                Route::any('/page/{slug}', [AdminxController::class, 'showPage']);
                Route::get('/model/{slug}', [AdminxController::class, 'modelIndex']);
                Route::post('/model/{slug}', [AdminxController::class, 'modelIndex']);
                Route::delete('/model/{slug}', [AdminxController::class, 'modelDelete']);
                Route::get('/model/{slug}/create', [AdminxController::class, 'modelCreate']);
                Route::post('/model/{slug}/create', [AdminxController::class, 'modelCreate']);
                Route::get('/model/{slug}/update/{id}', [AdminxController::class, 'modelUpdate']);
                Route::put('/model/{slug}/update/{id}', [AdminxController::class, 'modelUpdate']);
            });
        });
    }

    /**
     * Admin panel access middleware closure
     */
    private $middleware = null;

    /**
     * Sets the access middleware
     * 
     * @param \Closure $middleware
     * @return Core
     */
    public function setMiddleware(\Closure $middleware): Core
    {
        $this->middleware = $middleware;
        return $this;
    }

    /**
     * Runs the access middleware and returns the result
     * 
     * @return bool
     */
    public function runMiddleware(): bool
    {
        if (!is_callable($this->middleware)) {
            return true;
        }

        $tmp = $this->middleware;
        return (bool) \call_user_func_array($tmp, [auth()->user()]);
    }

    /**
     * Admin panel url
     * 
     * @param string $url
     * @return string
     */
    public function url(string $url='/'): string
    {
        $output = url($this->route_prefix . '/' . $url);
        $output = str_replace('//', '/', $output);
        $output = str_replace('http:/', 'http://', $output);
        $output = str_replace('https:/', 'https://', $output);
        return $output;
    }

    /**
     * Logout url for user
     */
    private string $logout = '/auth/logout';

    /**
     * Sets the Logout url for user
     * 
     * @param string $logout
     * @return Core
     */
    public function setLogout(string $logout): Core
    {
        $this->logout = $logout;
        return $this;
    }

    /**
     * Returns Logout url for user
     * 
     * @return string
     */
    public function getLogout(): string
    {
        return $this->logout;
    }

    /**
     * Info of user
     */
    private $userinfo = null;

    /**
     * Sets Info of user
     * 
     * @param \Closure $userinfo
     * @return Core
     */
    public function setUserinfo(\Closure $userinfo): Core
    {
        $this->userinfo = $userinfo;
        return $this;
    }

    /**
     * Returns Info of user
     * 
     * Returned data structure: ['username' => '...', 'image' => '...']
     * 
     * @return array
     */
    public function getUserinfo(): array
    {
        if (!is_callable($this->userinfo)) {
            return ['username' => 'unset', 'image' => 'unset'];
        }
        $tmp = $this->userinfo;
        $info = call_user_func_array($tmp, [auth()->user()]);
        $result = ['username' => 'unset', 'image' => 'unset'];
        if (is_array($info)) {
            if (isset($info['username'])) {
                $result['username'] = $info['username'];
            }
            if (isset($info['image'])) {
                $result['image'] = $info['image'];
            }
        }
        return $result;
    }

    /**
     * The admin panel menu items
     *
     * Contains models, links and custom pages
     */
    private array $menu = [];

    /**
     * Adds a link item to menu
     * 
     * @param string $title
     * @param string $link
     * @param string $target
     * @param string $icon
     * @return Core
     */
    public function addLink(string $title, string $link, string $target='', string $icon=''): Core
    {
        array_push($this->menu, [
            'type' => 'link',
            'title' => $title,
            'link' => $link,
            'target' => $target,
            'icon' => $icon,
        ]);
        return $this;
    }

    /**
     * Returns the menu list
     * 
     * @return array
     */
    public function getMenu(): array
    {
        return $this->menu;
    }

    /**
     * Adds a page to the menu
     * 
     * @param string $title
     * @param string $slug
     * @param \Closure $action
     * @param string $icon
     * @param string $link_target
     * @return Core
     */
    public function addPage(string $title, string $slug, \Closure $action, string $icon='', string $link_target=''): Core
    {
        array_push($this->menu, [
            'type' => 'page',
            'title' => $title,
            'slug' => $slug,
            'target' => $link_target,
            'icon' => $icon,
            'action' => $action,
        ]);
        return $this;
    }

    /**
     * The frontend localization words
     */
    private array $words = [];

    /**
     * Sets a word in frontend customization words
     * 
     * @param string $key
     * @param string $value
     * @return Core
     */
    public function setWord(string $key, string $value): Core
    {
        $this->words[$key] = $value;
        return $this;
    }

    /**
     * Returns a localization word value
     * 
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getWord(string $key, string $default=''): string
    {
        if(isset($this->words[$key]))
        {
            return $this->words[$key];
        }
        return $default;
    }

    /**
     * Returns all of localization words
     * 
     * @return array
     */
    public function getAllWords(): array
    {
        return $this->words;
    }

    /**
     * The admin panel default layout view
     */
    private string $layout = 'adminx.layouts.default';

    /**
     * Returns admin panel default layout view
     * 
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * Sets admin panel default layout view
     * 
     * @param string $layout
     * @return Core
     */
    public function setLayout(string $layout): Core
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Is Right to left
     */
    private bool $rtl = false;

    /**
     * Returns is right to left
     * 
     * @return bool
     */
    public function isRtl(): bool
    {
        return $this->rtl;
    }

    /**
     * Enables the right to left
     * 
     * @param bool $rtl
     * @return Core
     */
    public function enableRtl(bool $rtl=true): Core
    {
        $this->rtl = $rtl;
        return $this;
    }

    /**
     * Adds a model
     * 
     * @param string $model
     * @param array $config
     */
    public function addModel(string $model, array $config): Core
    {
        $config['model'] = $model;
        if(!isset($config['title']) || !\is_string($config['title']))
        {
            $config['title'] = $config['model'];
        }
        if(!isset($config['icon']) || !\is_string($config['icon']))
        {
            $config['icon'] = '';
        }
        if(!isset($config['slug']) || !\is_string($config['slug']))
        {
            $config['slug'] = str_replace('\\', '-', $config['model']);
        }
        if(!isset($config['target']) || !\is_string($config['target']))
        {
            $config['target'] = '';
        }
        if(!isset($config['middleware']) || !\is_callable($config['middleware']))
        {
            $config['middleware'] = (function(){ return true; });
        }
        if(!isset($config['hidden_fields']))
        {
            $config['hidden_fields'] = [];
        }
        if(!isset($config['fields_titles']))
        {
            $config['fields_titles'] = [];
        }
        if(!isset($config['no_table_footer']))
        {
            $config['no_table_footer'] = false;
        }
        if(!isset($config['per_page']))
        {
            $config['per_page'] = 30;
        }
        if(!isset($config['fields_values']))
        {
            $config['fields_values'] = [];
        }
        if(!isset($config['filter_data']) || !is_callable($config['filter_data']))
        {
            $config['filter_data'] = (function($query){
                return $query;
            });
        }
        if(!isset($config['virtual_fields']) || !is_array($config['virtual_fields']))
        {
            $config['virtual_fields'] = [];
        }
        if(!isset($config['only_top_pagination']))
        {
            $config['only_top_pagination'] = false;
        }
        if(!isset($config['only_bottom_pagination']))
        {
            $config['only_bottom_pagination'] = false;
        }
        if(!isset($config['custom_html']) || !is_callable($config['custom_html']))
        {
            $config['custom_html'] = (function(){
                return '';
            });
        }
        if(!isset($config['custom_html_bottom']) || !is_callable($config['custom_html_bottom']))
        {
            $config['custom_html_bottom'] = (function(){
                return '';
            });
        }
        if(!isset($config['search']) || !is_callable($config['search']))
        {
            $config['search'] = null;
        }
        if(!isset($config['search_hint']))
        {
            $config['search_hint'] = 'Search here...';
        }
        if(!isset($config['delete_middleware']) || !is_callable($config['delete_middleware']))
        {
            $config['delete_middleware'] = (function(){
                return true;
            });
        }
        if(!isset($config['create_middleware']) || !is_callable($config['create_middleware']))
        {
            $config['create_middleware'] = (function(){
                return true;
            });
        }
        if(!isset($config['readonly_fields']) || !is_array($config['readonly_fields']))
        {
            $config['readonly_fields'] = ['created_at', 'updated_at', 'deleted_at'];
        }
        if(!isset($config['only_addable_fields']) || !is_array($config['only_addable_fields']))
        {
            $config['only_addable_fields'] = [];
        }
        if(!isset($config['only_editable_fields']) || !is_array($config['only_editable_fields']))
        {
            $config['only_editable_fields'] = [];
        }
        if(!isset($config['fields_comments']) || !is_array($config['fields_comments']))
        {
            $config['fields_comments'] = [];
        }
        if(!isset($config['foreign_keys']) || !is_array($config['foreign_keys']))
        {
            $config['foreign_keys'] = [];
        }
        if(!isset($config['filter_create_data']) || !is_callable($config['filter_create_data']))
        {
            $config['filter_create_data'] = (function($row){
                return $row;
            });
        }
        if(!isset($config['filter_update_data']) || !is_callable($config['filter_update_data']))
        {
            $config['filter_update_data'] = (function($old_row, $row){
                return $row;
            });
        }
        if(!isset($config['after_create_go_to']))
        {
            $config['after_create_go_to'] = 'update';
        }
        if(!isset($config['after_update_go_to']))
        {
            $config['after_update_go_to'] = 'update';
        }
        if(!isset($config['actions']) || !is_array($config['actions']))
        {
            $config['actions'] = [];
        }
        if(!isset($config['create_html']) || !is_callable($config['create_html']))
        {
            $config['create_html'] = (function(){
                return '';
            });
        }
        if(!isset($config['update_html']) || !is_callable($config['update_html']))
        {
            $config['update_html'] = (function(){
                return '';
            });
        }
        if(!isset($config['update_middleware']) || !is_callable($config['update_middleware']))
        {
            $config['update_middleware'] = (function(){
                return true;
            });
        }
        if(!isset($config['n2n']) || !is_array($config['n2n']))
        {
            $config['n2n'] = [];
        }

        // handle User model customizations
        if ($config['model'] === \App\Models\User::class) {
            $config['slug'] = 'User';

            // add Groups n2n relation
            array_push($config['n2n'], [
                'name' => 'User Groups',
                'model' => \Adminx\Models\Group::class,
                'pivot' => \Adminx\Models\UserGroup::class,
                'pivot_keys' => ['user_id', 'adminx_group_id'],
                'title' => (function($group){ return $group->name; }),
                'list' => (function(){ return \Adminx\Models\Group::all(); }),
            ]);
        }

        array_push($this->menu, [
            'type' => 'model',
            'config' => $config,
        ]);
        return $this;
    }

    /**
     * Loads the model columns from database by config
     * 
     * @param array $model_config
     * @param bool $remove_hidden_columns (Do remove hidden fields from list or not)
     * @return array[string]
     */
    public function getModelColumns(array $model_config, bool $remove_hidden_columns=true): array
    {
        // load columns
        $tmp_model_object = new $model_config['model'];
        $columns = $tmp_model_object->getConnection()->getSchemaBuilder()->getColumnListing($tmp_model_object->getTable());

        // remove hidden fields
        if ($remove_hidden_columns) {
            $new_columns_list = [];
            foreach ($columns as $col) {
                if (!\in_array($col, $model_config['hidden_fields'])) {
                    array_push($new_columns_list, $col);
                }
            }
        } else {
            $new_columns_list = $columns;
        }

        return $new_columns_list;
    }

    private $superUser_closure = null;

    /**
     * Sets a closure to determine super user
     *
     * @param \Closure $closure
     * @return Core
     */
    public function superUser(\Closure $closure)
    {
        $this->superUser_closure = $closure;
        return $this;
    }

    /**
     * Checks user is super user
     *
     * @param \App\Models\User $user
     */
    public function checkSuperUser(\App\Models\User $user)
    {
        $func = $this->superUser_closure;

        if ($func === null){
            return false;
        }

        return (bool) call_user_func_array($func, [$user]);
    }

    /**
     * The added plugins to the admin panel will be stored at this array
     * This array is key=>value
     * The key is the plugin class name and value will be an object from that
     *
     * @var array[IPlugin]
     */
    protected array $plugins = [];

    /**
     * Adds a plugin
     *
     * @param string $class
     * @param array $options
     */
    public function addPlugin(IPlugin $plugin, array $options=[])
    {
        // here we use plugin class name as an unique identifier for the plugins
        $this->plugins[$plugin::class] = $plugin;
        call_user_func_array([$this->plugins[$plugin::class], 'run'], [$this, $options]);
        return $this;
    }

    /**
     * The frontend font
     *
     * @var string
     */
    private string $font = '';

    /**
     * Sets the frontend font
     *
     * @param string $font
     * @return $this
     */
    public function setFont(string $font)
    {
        $this->font = $font;
        return $this;
    }

    /**
     * Returns the frontend font
     *
     * @return string
     */
    public function getFont(): string
    {
        return $this->font;
    }
}
