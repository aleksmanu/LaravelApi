<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Modules\Workorder\Models\Quote;
use App\Modules\Workorder\Models\Supplier;
use App\Modules\Workorder\Models\ExpenditureType;
use App\Modules\Property\Models\Property;
use App\Modules\Property\Models\Unit;
use App\Modules\Auth\Models\User;

class AddQuoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable(Quote::getTableName())) {
            Schema::create(
                Quote::getTableName(),
                function (Blueprint $table) {
                    $table->increments('id');
                    $table->unsignedInteger('property_id');
                    $table->unsignedInteger('unit_id')->nullable();
                    $table->unsignedInteger('supplier_id');
                    $table->unsignedInteger('expenditure_type_id');
                    $table->unsignedInteger('locked_by_id')->nullable();
                    $table->integer('value')->default(0);
                    $table->string('work_description');
                    $table->string('critical_information')->nullable();
                    $table->string('contact_details')->nullable();
                    $table->dateTime('booked_at')->nullable();
                    $table->dateTime('due_at')->nullable();
                    $table->timestamps();
                    $table->string('locked_note')->nullable();

                    $table->foreign('property_id')->references('id')->on(Property::getTableName());
                    $table->foreign('supplier_id')->references('id')->on(Supplier::getTableName());
                    $table->foreign('expenditure_type_id')->references('id')->on(ExpenditureType::getTableName());
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
        Schema::dropIfExists(Quote::getTableName());
    }
}
