<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IncreaseSizeOfLeaseNotesColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lease_payables', function (Blueprint $table) {
            $table->text('lease_notes')->nullable()->change();
            $table->text('mgt_remarks')->nullable()->change();
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
            $table->string('lease_notes')->nullable()->change();
            $table->string('mgt_remarks')->nullable()->change();
        });
    }
}
