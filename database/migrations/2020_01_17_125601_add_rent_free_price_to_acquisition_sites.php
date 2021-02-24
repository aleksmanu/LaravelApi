<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRentFreePriceToAcquisitionSites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('acquisition_sites', function(Blueprint $table) {
            $table->decimal('rent_free_price', 12, 2)->nullable();
        });
    }
}
