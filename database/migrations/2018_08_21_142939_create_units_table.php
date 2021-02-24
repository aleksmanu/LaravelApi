<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(\App\Modules\Property\Models\Unit::getTableName(), function (Blueprint $table) {

            $table->increments('id');
            $table->string('yardi_import_ref')->unique()->nullable();
            $table->string('yardi_property_unit_ref')->nullable();
            $table->string('yardi_unit_ref');
            $table->integer('property_id')->unsigned();
            $table->integer('property_manager_id')->unsigned();
            $table->integer('measurement_unit_id')->unsigned();
            $table->integer('review_status_id')->unsigned();
            $table->integer('locked_by_user_id')->unsigned()->nullable();
            $table->string('demise')->nullable();
            $table->string('unit')->nullable();
            $table->string('name')->nullable();
            $table->decimal('measurement_value', 12, 2)->nullable();
            $table->boolean('approved')->nullable();
            $table->date('approved_at')->nullable();
            $table->string('approved_initials', 5)->nullable();
            $table->date('held_at')->nullable();
            $table->string('held_initials', 5)->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('property_id')->references('id')->on(\App\Modules\Property\Models\Property::getTableName());
            $table->foreign('property_manager_id')->references('id')->on(\App\Modules\Property\Models\PropertyManager::getTableName());
            $table->foreign('measurement_unit_id')->references('id')->on(\App\Modules\Property\Models\MeasurementUnit::getTableName());
            $table->foreign('locked_by_user_id')->references('id')->on(\App\Modules\Auth\Models\User::getTableName());
            $table->foreign('review_status_id')->references('id')->on('review_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(\App\Modules\Property\Models\Unit::getTableName());
    }
}
