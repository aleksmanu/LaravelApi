<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlanningFieldsToAcquisitionSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acquisition_sites', function (Blueprint $table) {
            $table->string('planning_type')->nullable();
            $table->string('planning_application_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('acquisition_sites', function (Blueprint $table) {
            $table->dropColumn('planning_type');
            $table->dropColumn('planning_application_number');
        });
    }
}
