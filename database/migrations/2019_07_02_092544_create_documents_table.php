<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('filename');
            $table->string('reference')->nullable();
            $table->string('mime_type');

            $table->integer('user_id')->unsigned();
            $table->integer('document_type_id')->unsigned();

            $table->string('attachable_type');
            $table->integer('attachable_id')->unsigned();

            $table->date('date');
            $table->string('parties');
            $table->string('comments')->nullable();

            $table->foreign('document_type_id')->references('id')->on('document_types')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
