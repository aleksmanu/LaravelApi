<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLeaseTypeIdColumnToLeasePayables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lease_payables', function (Blueprint $table) {
            $table->integer('lease_payable_type_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lease_payables', function (Blueprint $table) {
            $table->dropColumn('lease_payable_type_id');
        });
    }
}
