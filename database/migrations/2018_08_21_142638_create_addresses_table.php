<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\Modules\Common\Models\Address::getTableName(), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('county_id')->unsigned()->nullable();
            $table->integer('country_id')->unsigned()->nullable();
            $table->integer('review_status_id')->unsigned();
            $table->integer('locked_by_user_id')->unsigned()->nullable();
            $table->string('unit')->nullable();
            $table->string('building')->nullable();
            $table->string('number', 255)->nullable();
            $table->string('street')->nullable();
            $table->string('estate')->nullable();
            $table->string('suburb')->nullable();
            $table->string('town')->nullable();
            $table->string('postcode', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('county_id')->references('id')->on(\App\Modules\Common\Models\County::getTableName());
            $table->foreign('country_id')->references('id')->on(\App\Modules\Common\Models\Country::getTableName());
            $table->foreign('locked_by_user_id')->references('id')->on(\App\Modules\Auth\Models\User::getTableName());
            $table->foreign('review_status_id')->references('id')->on('review_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(\App\Modules\Common\Models\Address::getTableName());
    }
}
