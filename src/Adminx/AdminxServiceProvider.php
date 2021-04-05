<?php

/*
 * This file is part of Adminx.
 *
 * Copyright 2020-2021 parsa shahmaleki <parsampsh@gmail.com>
 *
 * Adminx project is Licensed Under MIT.
 * For more information, please see the LICENSE file.
 */

namespace Adminx;

use Illuminate\Support\ServiceProvider;

/**
 * The Adminx Laravel Service Provider
 */
class AdminxServiceProvider extends ServiceProvider
{
    public function boot() {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        if ($this->app->runningInConsole()) {
            $this->mergePublic();
        }
    }

    private function mergePublic() {
        $this->publishes([__DIR__ . '/../../public' => public_path('/adminx-public')], 'adminx-public');
    }
}
