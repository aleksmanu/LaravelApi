<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
        });

        /**
         *          ATTENTION SCRUB!
         *      THESE ARE HARDCODED AND IDs ARE REFERENCED IN THE FRONT-END CLIENT
         *         IF YOU
         */

        DB::table('document_levels')->insert([
                ['name' => 'Client'],
                ['name' => 'Property'],
                ['name' => 'Unit'],
                ['name' => 'Lease'],
                ['name' => 'Lease Payable'],
                ['name' => 'Acquisition']
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_levels');
    }
}
