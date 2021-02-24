<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargeHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'charge_history',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('entity_id')->unsigned()->nullable();
                $table->string('entity_type', 150)->nullable();
                $table->integer('type_id')->unsigned();

                $table->decimal('amount', 15, 2);
                $table->string('reason')->nullable();
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charge_history');
    }
}
