<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEditBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edit_batches', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('edit_batch_type_id')->unsigned();
            $table->integer('created_by_user_id')->unsigned();
            $table->integer('reviewed_by_user_id')->unsigned()->nullable();
            $table->string('entity_type');
            $table->integer('entity_id');
            $table->string('name');
            $table->timestamps();
            $table->dateTime('status_changed_at');
            $table->timestamp('reviewed_at')->nullable();

            $table->foreign('edit_batch_type_id')->references('id')->on('edit_batch_types');
            $table->foreign('created_by_user_id')->references('id')->on('users');
            $table->foreign('reviewed_by_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('edit_batches');
    }
}
