<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\Modules\Property\Models\Property::getTableName(), function (Blueprint $table) {
            $table->increments('id');

            $table->integer('portfolio_id')->unsigned();
            $table->integer('property_manager_id')->unsigned();
            $table->integer('address_id')->unsigned();
            $table->integer('property_status_id')->unsigned();
            $table->integer('property_use_id')->unsigned()->nullable();
            $table->integer('property_tenure_id')->unsigned();
            $table->integer('location_type_id')->unsigned();
            $table->integer('property_category_id')->unsigned()->nullable();
            $table->integer('stop_posting_id')->unsigned();
            $table->integer('review_status_id')->unsigned();
            $table->integer('locked_by_user_id')->unsigned()->nullable();
            $table->integer('partner_id')->unsigned()->nullable();


            $table->string('name');
            $table->string('yardi_property_ref', 20);
            $table->string('yardi_alt_ref', 20)->nullable();
            $table->decimal('total_lettable_area', 15, 2)->nullable();
            $table->decimal('void_total_lettable_area', 15, 2)->nullable();
            $table->decimal('total_site_area', 15, 2)->nullable();
            $table->decimal('total_gross_internal_area', 15, 2)->nullable();
            $table->decimal('total_rateable_value', 15, 2)->nullable();
            $table->decimal('void_total_rateable_value', 15, 2)->nullable();
            $table->boolean('listed_building')->nullable();
            $table->boolean('live')->nullable();
            $table->boolean('conservation_area')->nullable();
            $table->boolean('air_conditioned')->nullable();
            $table->boolean('vat_registered')->nullable();
            $table->boolean('approved')->nullable();
            $table->date('approved_at')->nullable();
            $table->string('approved_initials', 5)->nullable();
            $table->date('held_at')->nullable();
            $table->string('held_initials', 5)->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('portfolio_id')->references('id')->on(\App\Modules\Client\Models\Portfolio::getTableName());
            $table->foreign('property_manager_id')->references('id')->on(\App\Modules\Property\Models\PropertyManager::getTableName());
            $table->foreign('address_id')->references('id')->on(\App\Modules\Common\Models\Address::getTableName());
            $table->foreign('property_status_id')->references('id')->on(\App\Modules\Property\Models\PropertyStatus::getTableName());
            $table->foreign('property_use_id')->references('id')->on(\App\Modules\Property\Models\PropertyUse::getTableName());
            $table->foreign('property_tenure_id')->references('id')->on(\App\Modules\Property\Models\PropertyTenure::getTableName());
            $table->foreign('location_type_id')->references('id')->on(\App\Modules\Property\Models\LocationType::getTableName());
            $table->foreign('property_category_id')->references('id')->on(\App\Modules\Property\Models\PropertyCategory::getTableName());
            $table->foreign('stop_posting_id')->references('id')->on(\App\Modules\Property\Models\StopPosting::getTableName());
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
        Schema::dropIfExists(\App\Modules\Property\Models\Property::getTableName());
    }
}
