<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColumnsIndexInPortfoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->index('name', 'portfolios_name_index');
            $table->index('yardi_portfolio_ref', 'portfolios_yardi_portfolio_ref_index');
            $table->index('locked_at', 'portfolios_locked_at_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropIndex('portfolios_name_index');
            $table->dropIndex('portfolios_yardi_portfolio_ref_index');
            $table->dropIndex('portfolios_locked_at_index');
        });
    }
}
