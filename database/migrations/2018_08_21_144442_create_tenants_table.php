<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\Modules\Lease\Models\Tenant::getTableName(), function (Blueprint $table) {

            $table->increments('id');
            $table->integer('tenant_status_id')->unsigned();
            $table->integer('lease_id')->unsigned();
            $table->integer('review_status_id')->unsigned();
            $table->integer('locked_by_user_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('yardi_tenant_ref', 16);
            $table->string('yardi_tenant_alt_ref', 16)->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_status_id')->references('id')->on(\App\Modules\Lease\Models\TenantStatus::getTableName());
            // $table->foreign('lease_id')->references('id')->on(\App\Modules\Lease\Models\Lease::getTableName());
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
        Schema::dropIfExists(\App\Modules\Lease\Models\Tenant::getTableName());
    }
}
