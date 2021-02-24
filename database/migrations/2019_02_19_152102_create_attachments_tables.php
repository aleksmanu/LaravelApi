<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttachmentsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        DB::table('categories')->insert(['name' => 'Acquisitions Documents']);

        Schema::create('uploads', function (Blueprint $table) {
            //$table->uuid('id');
            //$table->primary('id');
            $table->increments('id');
            $table->string('filename');
            $table->string('reference')->nullable();
            $table->string('mime_type');

            $table->integer('user_id')->unsigned();
            $table->integer('category_id')->unsigned()->nullable();

            $table->string('attachable_type');
            $table->integer('attachable_id')->unsigned();

            $table->foreign('category_id')->references('id')->on('categories');
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
        Schema::dropIfExists('uploads');
        Schema::dropIfExists('categories');
    }
}
