<?php
namespace App\Modules\Client\Providers;

use App\Modules\Client\Models\ClientAccount;
use App\Modules\Client\Observers\ClientAccountObserver;
use Caffeinated\Modules\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the module services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__.'/../Resources/Lang', 'client');
        $this->loadViewsFrom(__DIR__.'/../Resources/Views', 'client');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations', 'client');
        $this->loadConfigsFrom(__DIR__.'/../config');
        ClientAccount::observe(ClientAccountObserver::class);
    }

    /**
     * Register the module services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }
}
