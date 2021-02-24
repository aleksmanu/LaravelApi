<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Modules\Workorder\Models\ExpenditureType;

class AddExpenditureTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable(ExpenditureType::getTableName())) {
            Schema::create(
                ExpenditureType::getTableName(),
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('name', 64);
                    $table->string('code', 16);
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
        Schema::dropIfExists(ExpenditureType::getTableName());
    }
}
