<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Modules\Workorder\Models\WorkOrder;
use App\Modules\Workorder\Models\Quote;
use App\Modules\Auth\Models\User;

class AddWorkOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable(WorkOrder::getTableName())) {
            Schema::create(
                WorkOrder::getTableName(),
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->unsignedInteger('quote_id')->unique();
                    $table->integer('value')->default(0);

                    $table->unsignedInteger('locked_by_id')->nullable();
                    $table->timestamp('locked_at')->nullable();
                    $table->string('locked_note')->nullable();

                    $table->unsignedInteger('completed_by_id')->nullable();
                    $table->timestamp('completed_at')->nullable();

                    $table->unsignedInteger('paid_by_id')->nullable();
                    $table->timestamp('paid_at')->nullable();
                    $table->timestamps();

                    $table->foreign('quote_id')->references('id')->on(Quote::getTableName());
                    $table->foreign('locked_by_id')->references('id')->on(User::getTableName());
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
        Schema::dropIfExists(WorkOrder::getTableName());
    }
}
