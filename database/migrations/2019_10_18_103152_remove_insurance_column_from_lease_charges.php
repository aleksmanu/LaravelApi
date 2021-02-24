<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveInsuranceColumnFromLeaseCharges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lease_charges', function (Blueprint $table) {
            $table->dropColumn('advance');
            $table->dropColumn('insurance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lease_charges', function (Blueprint $table) {
            $table->integer('advance')->nullable();
            $table->integer('insurance')->nullable();
        });
    }
}
