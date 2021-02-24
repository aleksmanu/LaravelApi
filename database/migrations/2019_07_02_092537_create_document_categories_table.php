<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
        });

        DB::table('document_categories')->insert([
            'id' => 1, // this is referenced in 'create_document_types_table'
            'name' => 'Acquisitions Documents'
        ]);

        DB::table('document_categories')->insert([
                ['name' => 'Legal'],
                ['name' => 'Contract'],
                ['name' => 'Plans'],
                ['name' => 'Health & Safety'],
                ['name' => 'Insurance'],
                ['name' => 'Service Charge'],
                ['name' => 'Valuation'],
                ['name' => 'Reports']
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
        Schema::dropIfExists('document_categories');
    }
}
