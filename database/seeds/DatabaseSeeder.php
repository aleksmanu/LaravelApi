<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * @throws Exception
     * @throws Throwable
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        $this->command->info('POPULATING DATABASE...');
        $time_start = microtime(true);

        DB::transaction(function () {
            /** -- DO NOT MOVE THIS DECLARATION SMART GUY --
             * Multiplier for ClientAccount, Portfolio and HeadLease seeds
             * Affects the number of objects generated to decrease/increase seeding time and/or data complexity
             * Increase in small increments, as this increases seeding time exponentially (5 more ClientAccounts can result
             *                                                          in hundreds, even thousands of records down the line)
             * @return int
             */
            function getModelMagnitudeFactor(): int
            {
                return 1;
            }

            $this->call([
                DevSeeder::class,
                \App\Modules\Auth\Database\Seeds\UserSeeder::class,
                \App\Modules\Common\Database\Seeds\AddressSeeder::class,
                \App\Modules\Client\Database\Seeds\ClientDataSeeder::class,
                \App\Modules\Property\Database\Seeds\PropertySeeder::class,
                \App\Modules\Lease\Database\Seeds\TenantAndLeaseSeeder::class,
                //\App\Modules\Edits\Database\Seeds\EditsSeeder::class,
                \App\Modules\Common\Database\Seeds\NoteSeeder::class,
                \App\Modules\Workorder\Database\Seeds\WorkOrderSeeder::class,
                \App\Modules\Lease\Database\Seeds\LeasePayableSeeder::class,
                \App\Modules\Lease\Database\Seeds\LeaseChargeSeeder::class,
                \App\Modules\Lease\Database\Seeds\LeaseBreakSeeder::class
            ]);
        });

        $time_end = microtime(true);
        $execution_time = ($time_end - $time_start);
        $this->command->info("EXECUTION TIME: " . $execution_time . " seconds.");
    }
}
