<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBreakDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lease_breaks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('break_party_option_id')->unsigned();
            $table->integer('entity_id')->unsigned()->nullable();
            $table->string('entity_type', 150)->nullable();
            $table->string('type');
            $table->date('date');
            $table->integer('min_notice')->nullable();
            $table->boolean('penalty')->nullable();
            $table->string('penalty_incentive')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('break_party_option_id')->references('id')->on('break_party_options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lease_breaks');
    }
}
