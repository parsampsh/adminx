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
     * The current registered admin panel
     */
    public static $core;

    /**
     * Title of the admin panel
     */
    protected string $title = 'Adminx Panel';

    /**
     * Sets the title of admin panel
     */
    public function set_title(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Returns title of admin panel
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
     */
    public function set_copyright(string $copyright)
    {
        $this->copyright = $copyright;
        return $this;
    }

    /**
     * Returns Copyright message of admin panel
     */
    public function get_copyright(): string
    {
        return $this->copyright;
    }

    /**
     * Register configured admin panel on routes
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
            });
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
        return $this;
    }

    /**
     * Runs the access middleware and returns the result
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
     */
    public function set_logout(string $logout)
    {
        $this->logout = $logout;
        return $this;
    }

    /**
     * Returns Logout url for user
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
     */
    public function set_userinfo(\Closure $userinfo)
    {
        $this->userinfo = $userinfo;
        return $this;
    }

    /**
     * Returns Info of user
     */
    public function get_userinfo()
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
     */
    public function add_link(string $title, string $link, string $target='', string $icon='')
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
     */
    public function get_menu(): array
    {
        return $this->menu;
    }

    /**
     * Adds a page to the menu
     */
    public function add_page(string $title, string $slug, \Closure $action, string $icon, string $link_target='')
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
}
