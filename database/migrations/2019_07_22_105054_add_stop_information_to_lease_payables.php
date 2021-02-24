<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStopInformationToLeasePayables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lease_payables', function (Blueprint $table) {
            $table->string('stop_by')->nullable();
            $table->string('stop_reason')->nullable();
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
            $table->dropColumn(['stop_by', 'stop_reason']);
        });
    }
}
