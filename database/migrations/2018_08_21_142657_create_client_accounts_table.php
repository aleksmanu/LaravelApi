<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\Modules\Client\Models\ClientAccount::getTableName(), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->unsigned();
            $table->integer('organisation_type_id')->unsigned()->nullable();
            $table->integer('address_id')->unsigned()->nullable();
            $table->integer('property_manager_id')->unsigned();
            $table->integer('client_account_status_id')->unsigned();
            $table->integer('review_status_id')->unsigned();
            $table->integer('locked_by_user_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('yardi_client_ref', 20);
            $table->string('yardi_alt_ref', 20)->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on(\App\Modules\Account\Models\Account::getTableName());
            $table->foreign('organisation_type_id')->references('id')->on(\App\Modules\Client\Models\OrganisationType::getTableName());
            $table->foreign('address_id')->references('id')->on(\App\Modules\Common\Models\Address::getTableName());
            $table->foreign('property_manager_id')->references('id')->on(\App\Modules\Property\Models\PropertyManager::getTableName());
            $table->foreign('client_account_status_id')->references('id')->on(\App\Modules\Client\Models\ClientAccountStatus::getTableName());
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
        Schema::dropIfExists(\App\Modules\Client\Models\ClientAccount::getTableName());
    }
}
