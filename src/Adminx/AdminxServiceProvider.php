<?php

namespace Adminx;

<?php

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
