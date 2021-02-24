<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('edit_batch_id')->unsigned();
            $table->integer('edit_status_id')->unsigned();
            $table->string('field');
            $table->string('previous_value')->nullable();
            $table->string('value')->nullable();
            $table->string('foreign_entity')->nullable();
            $table->timestamps();

            $table->foreign('edit_batch_id')->references('id')->on('edit_batches');
            $table->foreign('edit_status_id')->references('id')->on('edit_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('edits');
    }
}
