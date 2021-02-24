<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('entity_id')->unsigned()->nullable();
            $table->string('entity_type', 150)->nullable();
            $table->integer('lease_charge_type_id');
            $table->string('supplier_number')->nullable();
            $table->string('supplier_id')->nullable();

            $table->string('status')->default('Unpaid');
            $table->date('paid_date')->nullable();
            $table->date('from')->nullable();
            $table->date('to')->nullable();

            $table->decimal('amount', 15, 2)->nullable();
            $table->decimal('vat', 15, 2)->nullable();

            $table->string('description')->nullable();
            $table->string('invoice_number')->nullable();

            $table->string('apb_normal')->nullable();
            $table->string('apb_prop_ref')->nullable();

            $table->string('o2_gl_code')->nullable();
            $table->string('02_lease_payable_ref')->nullable();

            $table->string('trans_id')->nullable();
            $table->string('ouc_code')->nullable();
            
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
        Schema::dropIfExists('supplier_transaction_file');
    }
}
