<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnsIndexInPropertyStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('property_statuses', function (Blueprint $table) {
            $table->index('slug', 'property_statuses_slug_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_statuses', function (Blueprint $table) {
            $table->dropIndex('property_statuses_slug_index');
        });
    }
}
