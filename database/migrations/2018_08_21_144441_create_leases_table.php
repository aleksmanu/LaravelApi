<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\Modules\Lease\Models\Lease::getTableName(), function (Blueprint $table) {

            $table->increments('id');
            $table->integer('lease_type_id')->unsigned()->nullable();
            $table->integer('break_party_option_id')->unsigned()->nullable();
            $table->integer('rent_frequency_id')->unsigned()->nullable();
            $table->integer('review_type_id')->unsigned()->nullable();
            $table->integer('unit_id')->unsigned();
            $table->integer('review_status_id')->unsigned();
            $table->integer('locked_by_user_id')->unsigned()->nullable();

            $table->string('yardi_import_unit_ref')->nullable();
            $table->string('yardi_tenant_ref', 16);
            $table->integer('break_notice_days')->nullable();
            $table->decimal('annual_rent_vat_rate')->nullable();
            $table->decimal('annual_service_charge_vat_rate')->nullable();
            $table->boolean('live');
            $table->boolean('approved')->nullable();
            $table->date('approved_at')->nullable();
            $table->string('approved_initials', 5)->nullable();
            $table->date('held_at')->nullable();
            $table->string('held_initials', 5)->nullable();
            $table->date('next_review_at')->nullable();
            $table->date('expiry_at')->nullable();
            $table->date('commencement_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lease_type_id')->references('id')->on(\App\Modules\Lease\Models\LeaseType::getTableName());
            $table->foreign('break_party_option_id')->references('id')->on(\App\Modules\Lease\Models\BreakPartyOption::getTableName());
            $table->foreign('rent_frequency_id')->references('id')->on(\App\Modules\Lease\Models\RentFrequency::getTableName());
            $table->foreign('review_type_id')->references('id')->on(\App\Modules\Lease\Models\ReviewType::getTableName());
            $table->foreign('unit_id')->references('id')->on(\App\Modules\Property\Models\Unit::getTableName());
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
        Schema::dropIfExists(\App\Modules\Lease\Models\Lease::getTableName());
    }
}
