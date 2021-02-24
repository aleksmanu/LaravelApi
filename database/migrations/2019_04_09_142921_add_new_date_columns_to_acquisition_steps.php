<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewDateColumnsToAcquisitionSteps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acquisition_steps', function ($table) {
            $table->datetime('target_date')->nullable();
            $table->datetime('forecast_for')->nullable();
            $table->datetime('start_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acquisition_steps', function ($table) {
            $table->dropColumn('target_date');
            $table->dropColumn('forecast_for');
            $table->dropColumn('start_on');
        });
    }
}
