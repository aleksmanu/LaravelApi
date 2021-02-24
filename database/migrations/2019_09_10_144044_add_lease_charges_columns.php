<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLeaseChargesColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lease_charges', function (Blueprint $table) {
            $table->boolean('stop_automatic_demands')->default(false);
            $table->date('freq_next')->nullable();
            $table->date('payment_by')->nullable();
            $table->date('end')->nullable();
            $table->date('commencement')->nullable();
            $table->string('pay_method')->nullable();
            $table->string('supplier_ref')->nullable();
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
            $table->dropColumn('stop_automatic_demands');
            $table->dropColumn('freq_next');
            $table->dropColumn('payment_by');
            $table->dropColumn('end');
            $table->dropColumn('commencement');
            $table->dropColumn('pay_method');
            $table->dropColumn('supplier_ref');
        });
    }
}
