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
    public function set_title(string $title)
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
    public function set_copyright(string $copyright)
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

        Route::prefix($route)->group(function(){
            Route::middleware(['web', 'auth'])->group(function(){
                Route::get('/', [AdminxController::class, 'index']);
            });
        });
    }
}
