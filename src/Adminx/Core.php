<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2021 parsa shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under GPL-v3.
 * For more information, please see the LICENSE file.
 */

namespace Adminx;

use Adminx\Controllers\AdminxController;
use Illuminate\Support\Facades\Route;

/**
 * The Adminx Core
 * 
 * This Class only keeps Admin panel configuration
 */
class Core
{
    /**
     * The current registered admin panel
     */
    public static $core;

    /**
     * Title of the admin panel
     */
    protected string $title = 'Adminx Panel';

    /**
     * Sets the title of admin panel
     * 
     * @param string $title
     * @return Core
     */
    public function set_title(string $title): Core
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Returns title of admin panel
     * 
     * @return string
     */
    public function get_title(): string
    {
        return $this->title;
    }

    /**
     * Copyright message of the admin panel
     */
    protected string $copyright = 'Copyright';

    /**
     * Sets the title of admin panel
     * 
     * @param string $copyright
     * @return Core
     */
    public function set_copyright(string $copyright): Core
    {
        $this->copyright = $copyright;
        return $this;
    }

    /**
     * Returns Copyright message of admin panel
     * 
     * @return string
     */
    public function get_copyright(): string
    {
        return $this->copyright;
    }

    /**
     * Register configured admin panel on routes
     * 
     * @param string $route
     */
    public function register(string $route='/admin')
    {
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
                Route::get('/', [AdminxController::class, 'index']);
                Route::get('/page/{slug}', [AdminxController::class, 'show_page']);
                Route::get('/model/{slug}', [AdminxController::class, 'model_index']);
            });
        });
    }

    /**
     * Admin panel access middleware closure
     */
    protected $middleware = null;

    /**
     * Sets the access middleware
     * 
     * @param \Closure $middleware
     * @return Core
     */
    public function set_middleware(\Closure $middleware): Core
    {
        $this->middleware = $middleware;
        return $this;
    }

    /**
     * Runs the access middleware and returns the result
     * 
     * @return bool
     */
    public function run_middleware(): bool
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
        return url($this->route_prefix . '/' . $url);
    }

    /**
     * Logout url for user
     */
    protected string $logout = '/auth/logout';

    /**
     * Sets the Logout url for user
     * 
     * @param string $logout
     * @return Core
     */
    public function set_logout(string $logout): Core
    {
        $this->logout = $logout;
        return $this;
    }

    /**
     * Returns Logout url for user
     * 
     * @return string
     */
    public function get_logout(): string
    {
        return $this->logout;
    }

    /**
     * Info of user
     */
    protected $userinfo = null;

    /**
     * Sets Info of user
     * 
     * @param \Closure $userinfo
     * @return Core
     */
    public function set_userinfo(\Closure $userinfo): Core
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
    public function get_userinfo(): array
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
    protected array $menu = [];

    /**
     * Adds a link item to menu
     * 
     * @param string $title
     * @param string $link
     * @param string $target
     * @param string $icon
     * @return Core
     */
    public function add_link(string $title, string $link, string $target='', string $icon=''): Core
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
    public function get_menu(): array
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
    public function add_page(string $title, string $slug, \Closure $action, string $icon, string $link_target=''): Core
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
    protected array $words = [];

    /**
     * Sets a word in frontend customization words
     * 
     * @param string $key
     * @param string $value
     * @return Core
     */
    public function set_word(string $key, string $value): Core
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
    public function get_word(string $key, string $default=''): string
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
    public function get_all_words(): array
    {
        return $this->words;
    }

    /**
     * The admin panel default layout view
     */
    protected string $layout = 'adminx.layouts.default';

    /**
     * Returns admin panel default layout view
     * 
     * @return string
     */
    public function get_layout(): string
    {
        return $this->layout;
    }

    /**
     * Returns admin panel default layout view
     * 
     * @param string $layout
     * @return Core
     */
    public function set_layout(string $layout): Core
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * Adds a model
     * 
     * @param string $model
     * @param array $config
     */
    public function add_model(string $model, array $config): Core
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
     * @return array[string]
     */
    public function get_model_columns(array $model_config): array
    {
        // load columns
        $tmp_model_object = new $model_config['model'];
        $columns = $tmp_model_object->getConnection()->getSchemaBuilder()->getColumnListing($tmp_model_object->getTable());

        // remove hidden fields
        $new_columns_list = [];
        foreach ($columns as $col) {
            if (!\in_array($col, $model_config['hidden_fields'])){
                array_push($new_columns_list, $col);
            }
        }

        return $new_columns_list;
    }
}
