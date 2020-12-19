<?php

namespace Adminx;

use Adminx\Controllers\AdminxController;
use Illuminate\Support\Facades\Route;

/**
 * The Adminx Core
 */
class Core
{
    /**
     * Title of the admin panel
     */
    protected string $title = 'Adminx Panel';

    /**
     * Sets the title of admin panel
     */
    public function set_title($title)
    {
        $this->title = $title;
    }

    /**
     * Returns title of admin panel
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Copyright message of the admin panel
     */
    protected string $copyright = 'Copyright';

    /**
     * Sets the title of admin panel
     */
    public function set_copyright($copyright)
    {
        $this->copyright = $copyright;
    }

    /**
     * Returns Copyright message of admin panel
     */
    public function get_copyright()
    {
        return $this->copyright;
    }

    public static $core;

    /**
     * Register configured admin panel on routes
     */
    public function register($route='/admin')
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
            });
            //Route::get('/')
        });
    }

    /**
     * Admin panel access middleware closure
     */
    protected $middleware = null;

    /**
     * Sets the access middleware
     */
    public function set_middleware(\Closure $middleware)
    {
        $this->middleware = $middleware;
    }

    /**
     * Runs the access middleware and returns the result
     */
    public function run_middleware(): bool
    {
        if ($this->middleware === null) {
            return true;
        }

        $tmp = $this->middleware;
        return (bool) \call_user_func_array($tmp, [auth()->user()]);
    }

    /**
     * Admin panel url
     */
    public function url($url='/')
    {
        return url($this->route_prefix . '/' . $url);
    }

    /**
     * Logout url for user
     */
    protected string $logout = '/auth/logout';

    /**
     * Sets the Logout url for user
     */
    public function set_logout($logout)
    {
        $this->logout = $logout;
    }

    /**
     * Returns Logout url for user
     */
    public function get_logout()
    {
        return $this->logout;
    }

    /**
     * Info of user
     */
    protected $userinfo = null;

    /**
     * Sets Info of user
     */
    public function set_userinfo(\Closure $userinfo)
    {
        $this->userinfo = $userinfo;
    }

    /**
     * Returns Info of user
     */
    public function get_userinfo()
    {
        if ($this->userinfo === null) {
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
}
