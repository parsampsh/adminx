<?php

/*
 * This file is part of Adminx.
 *   Copyright 2020 parsa shahmaleki <parsampsh@gmail.com>
 * Licensed Under GPL-v3
 * For more information, please view the LICENSE file
 */

namespace Adminx;

use Illuminate\Support\ServiceProvider;

class AdminxServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
    }
}
