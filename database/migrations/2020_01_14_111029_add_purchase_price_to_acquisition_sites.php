<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPurchasePriceToAcquisitionSites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acquisition_sites', function (Blueprint $table) {
            $table->decimal('purchase_price', 12, 2)->nullable();
        });
    }
}
