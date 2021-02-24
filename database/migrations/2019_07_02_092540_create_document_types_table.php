<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->integer('document_category_id')->unsigned();

            $table->foreign('document_category_id')->references('id')->on('document_categories');
        });

        DB::table('document_types')->insert([
            [
                'id' => 1, // this is referenced in the front-end, sliding-step-completor
                'document_category_id' => 1, // this is declared in 'create_document_categories_table'
                'name' => 'Survey'
            ],
            [
                'id' => 2, // this is referenced in the front-end, sliding-step-completor
                'document_category_id' => 1, // this is declared in 'create_document_categories_table'
                'name' => 'Miscellaneous'
            ],
            [
                'id' => 3, // this is referenced in the front-end, sliding-step-completor
                'document_category_id' => 1, // this is declared in 'create_document_categories_table'
                'name' => 'Design'
            ],
            [
                'id' => 4, // this is referenced in the front-end, sliding-step-completor
                'document_category_id' => 1, // this is declared in 'create_document_categories_table'
                'name' => 'Planning'
            ],
            [
                'id' => 5, // this is referenced in the front-end, sliding-step-completor
                'document_category_id' => 1, // this is declared in 'create_document_categories_table'
                'name' => 'Legals'
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_types');
    }
}
