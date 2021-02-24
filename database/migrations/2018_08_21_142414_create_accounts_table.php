<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\Modules\Account\Models\Account::getTableName(), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_type_id')->unsigned();
            //$table->string('name')->unique();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_type_id')->references('id')->on(\App\Modules\Account\Models\AccountType::getTableName());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(\App\Modules\Account\Models\Account::getTableName());
    }
}
