<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyTransactionsTableForPayables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('lease_type', 150)->nullable();
            $table->renameColumn('transaction_type_id', 'lease_charge_type_id');

            $table->string('supplier_number')->nullable();
            $table->string('supplier_name')->nullable();
            $table->string('ouc_code')->nullable();
            $table->string('apb_normal')->nullable();
            $table->string('o2_gl_code')->nullable();
            $table->string('apb_property_reference')->nullable();
            $table->string('o2_lease_payable_reference')->nullable();
            $table->string('description')->nullable();

            $table->dropForeign('transactions_lease_id_foreign');
            $table->dropForeign('transactions_transaction_type_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('lease_type');
            $table->renameColumn('lease_charge_type_id', 'transaction_type_id');

            $table->dropColumn('supplier_number');
            $table->dropColumn('supplier_name');
            $table->dropColumn('ouc_code');
            $table->dropColumn('apb_normal');
            $table->dropColumn('o2_gl_code');
            $table->dropColumn('apb_property_reference');
            $table->dropColumn('o2_lease_payable_reference');
            $table->dropColumn('description');
        });
    }
}
