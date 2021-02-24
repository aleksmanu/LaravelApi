<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReviewAndDemiseToLeasePayable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lease_payables', function (Blueprint $table) {
            $table->boolean('review')->default(false);
            $table->string('demise');
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
            $table->dropColumn('review');
            $table->dropColumn('demise');

        });
    }
}
