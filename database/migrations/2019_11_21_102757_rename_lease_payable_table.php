<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class RenameLeasePayableTable extends Migration
{
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        // Change lease payables into leases
        Schema::dropIfExists('leases');
        Schema::rename('lease_payables', 'leases');

        //Change lease payable types into lease types
        Schema::dropIfExists('lease_types');
        Schema::rename('lease_payable_types', 'lease_types');

        Schema::table('leases', function (Blueprint $table) {
            $table->boolean('payable')->default(true);
            $table->renameColumn('lease_payable_type_id', 'type_id');
        });

        DB::update("
            update lease_breaks
            set lease_breaks.entity_type = ?
            where lease_breaks.entity_type = ?;
        ", ['App\\Modules\\Lease\\Models\\Lease', 'App\\Modules\\Leasepayable\\Models\\LeasePayable']);

        DB::update('
            update lease_charges
            set lease_charges.entity_type = ?
            where lease_charges.entity_type = ?;
        ', ['App\\Modules\\Lease\\Models\\Lease', 'App\\Modules\\Leasepayable\\Models\\LeasePayable']);

        DB::Statement("
            update charge_history
            set charge_history.entity_type = ?
            where charge_history.entity_type = ?;
        ", ['App\\Modules\\Lease\\Models\\Lease', 'App\\Modules\\Leasepayable\\Models\\LeasePayable']);

        DB::update("
            update lease_reviews
            set lease_reviews.entity_type = ?
            where lease_reviews.entity_type = ?;
        ", ['App\\Modules\\Lease\\Models\\Lease', 'App\\Modules\\Leasepayable\\Models\\LeasePayable']);

        DB::update("
            update notes
            set notes.entity_type = ?
            where notes.entity_type = ?;
        ", ['App\\Modules\\Lease\\Models\\Lease', 'App\\Modules\\Leasepayable\\Models\\LeasePayable']);

        DB::update("
            update documents
            set documents.attachable_type = ?
            where documents.attachable_type = ?;
        ", ['App\\Modules\\Lease\\Models\\Lease', 'App\\Modules\\Leasepayable\\Models\\LeasePayable']);

        Schema::enableForeignKeyConstraints();
    }

    public function down()
    {
        dd('You cannot rollback this migration');
    }
}
