<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lease_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entity_id')->unsigned()->nullable();
            $table->string('entity_type', 150)->nullable();
            $table->integer('lease_charge_type');
            $table->string('pay_terms')->nullable();
            $table->integer('insurance')->nullable();

            $table->string('charge_from')->nullable();
            $table->date('start')->nullable();
            $table->integer('advance')->nullable();

            $table->boolean('li_charged')->nullable();
            $table->integer('li_grace')->nullable();
            $table->string('li_bank')->nullable();
            $table->integer('li_rate')->nullable();
            $table->integer('li_accrued')->nullable();
            $table->integer('li_min_rate')->nullable();
            $table->integer('li_max_rate')->nullable();

            $table->string('vat')->nullable();
            $table->string('frequency')->nullable();
            $table->decimal('annual', 15, 2)->nullable();
            $table->integer('period')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lease_charges');
    }
}
