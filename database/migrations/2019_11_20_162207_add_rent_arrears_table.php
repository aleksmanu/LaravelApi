<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRentArrearsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arrears', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('charge_type_id')->unsigned()->nullable();
            $table->integer('lease_id')->unsigned()->nullable();
            
            $table->string('invoice_number')->nullable();
            $table->string('description')->nullable();

            $table->date('due_date');
            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();

            $table->float('net')->nullable();
            $table->float('vat')->nullable();
            $table->float('gross')->nullable();
            $table->float('outstanding')->nullable();
            $table->float('receipt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arrears');
    }
}
