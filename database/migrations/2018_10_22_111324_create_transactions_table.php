<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\Modules\Lease\Models\Transaction::getTableName(), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lease_id')->unsigned();
            $table->integer('transaction_type_id')->unsigned()->nullable();
            $table->integer('paid_status_id')->unsigned();
            $table->integer('yardi_transaction_ref');
            $table->string('invoice_number')->nullable();
            $table->decimal('amount', 9, 2)->nullable();
            $table->decimal('vat', 9, 2)->nullable();
            $table->decimal('gross', 9, 2)->nullable();
            $table->decimal('gross_received', 9, 2)->nullable();
            $table->date('due_at')->nullable();
            $table->date('paid_at')->nullable();
            $table->date('period_from')->nullable();
            $table->date('period_to')->nullable();
            $table->timestamps();

            $table->foreign('lease_id')->references('id')->on(\App\Modules\Lease\Models\Lease::getTableName());
            $table->foreign('transaction_type_id')
                ->references('id')
                ->on(\App\Modules\Lease\Models\TransactionType::getTableName());
            $table->foreign('paid_status_id')
                ->references('id')
                ->on(\App\Modules\Lease\Models\PaidStatus::getTableName());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(\App\Modules\Lease\Models\Transaction::getTableName());
    }
}
