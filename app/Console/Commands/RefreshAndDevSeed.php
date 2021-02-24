<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Reports\Database\Seeds\ReportsSeeder;

class RefreshAndDevSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes database and runs dev seeder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('migration refresh started');
        \Artisan::call('migrate:fresh');
        $this->info('migration refresh completed');
        $this->info('dev seeder started');
        \Artisan::call('db:seed', ['--class' => 'DevSeeder']);
        $this->info('dev seeder completed');
        $this->info('reports seeder started');
        \Artisan::call('db:seed', ['--class' => ReportsSeeder::class]);
        $this->info('reports seeder completed');
    }
}
