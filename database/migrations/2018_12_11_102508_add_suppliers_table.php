<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Modules\Workorder\Models\Supplier;

class AddSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable(Supplier::getTableName())) {
            Schema::create(
                Supplier::getTableName(),
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('name', 128);
                    $table->string('phone', 64)->nullable();
                    $table->string('email', 128)->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                }
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Supplier::getTableName());
    }
}
