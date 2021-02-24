<?php
namespace App\Modules\Workorder\Providers;

use App\Modules\Workorder\Repositories\Interfaces\IQuoteRepository;
use App\Modules\Workorder\Repositories\Interfaces\IWorkOrderRepository;
use App\Modules\Workorder\Repositories\Interfaces\ISupplierRepository;
use App\Modules\Workorder\Repositories\Interfaces\IExpenditureTypeRepository;
use App\Modules\Workorder\Repositories\QuoteRepository;
use App\Modules\Workorder\Repositories\WorkOrderRepository;
use App\Modules\Workorder\Repositories\SupplierRepository;
use App\Modules\Workorder\Repositories\ExpenditureTypeRepository;
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
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/Lang', 'workorder');
        $this->loadViewsFrom(__DIR__ . '/../Resources/Views', 'workorder');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations', 'workorder');
        $this->loadConfigsFrom(__DIR__ . '/../config');
    }

    /**
     * Register the module services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->bind(IQuoteRepository::class, QuoteRepository::class);
        $this->app->bind(IWorkOrderRepository::class, WorkOrderRepository::class);
        $this->app->bind(ISupplierRepository::class, SupplierRepository::class);
        $this->app->bind(IExpenditureTypeRepository::class, ExpenditureTypeRepository::class);
    }
}
