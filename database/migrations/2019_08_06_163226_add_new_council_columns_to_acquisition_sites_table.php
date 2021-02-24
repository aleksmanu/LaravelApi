<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewCouncilColumnsToAcquisitionSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acquisition_sites', function (Blueprint $table) {
            $table->string('client_ref')->nullable();
            $table->string('council_contact_name')->nullable();
            $table->string('council_tel')->nullable();
            $table->string('council_email')->nullable();
            $table->string('council_address')->nullable();
            $table->string('network_planner')->nullable();
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
            $table->dropColumn('client_ref');
            $table->dropColumn('council_contact_name');
            $table->dropColumn('council_tel');
            $table->dropColumn('council_email');
            $table->dropColumn('council_address');
            $table->dropColumn('network_planner');
        });
    }
}
