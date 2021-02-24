<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_columns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('report_id');
            $table->boolean('preview');
            $table->string('name');
            $table->string('attribute');
            $table->integer('arrangement');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_columns');
    }
}
