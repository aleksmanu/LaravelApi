<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLeasePayableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lease_payables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('unit_id')->unsigned();
            $table->foreign('unit_id')->references('id')->on('units');
            $table->integer('managing_agent_id')->unsigned()->nullable();
            $table->integer('landlord_id')->unsigned()->nullable();

            $table->string('cluttons_lease_ref');
            $table->string('client_lease_ref')->nullable();
            $table->string('live')->nullable();
            $table->date('stop')->nullable();
            $table->decimal('passing_rent', 15, 2)->nullable();
            $table->decimal('service_charge', 15, 2)->nullable();
            $table->decimal('rates_liability', 15, 2)->nullable();

            $table->date('agreement_date')->nullable();
            $table->date('lease_start')->nullable();
            $table->date('lease_end')->nullable();
            $table->date('next_rent_review')->nullable();
            $table->boolean('outside_54_act')->nullable();
            $table->boolean('holding')->nullable();

            $table->boolean('turnover_rent')->nullable();
            $table->integer('review_pattern')->nullable();
            $table->date('first_review')->nullable();
            $table->date('next_review')->nullable();
            $table->string('review_notes')->nullable();
            $table->boolean('review_initiable_by_tenant')->nullable();
            $table->boolean('time_sensitive')->nullable();
            $table->boolean('notice_required')->nullable();
            $table->boolean('upwards_review_only')->nullable();
            $table->boolean('interest_on_late_review')->nullable();
            $table->string('review_basis')->nullable();
            $table->integer('rent_grace')->nullable();
            $table->string('li_bank')->nullable();
            $table->decimal('li_change_base_interest', 15, 2)->nullable();

            $table->boolean('aga_required')->nullable();
            $table->string('keep_open_clause')->nullable();
            $table->string('assignment')->nullable();
            $table->string('assignment_comments')->nullable();
            $table->string('subletting')->nullable();
            $table->string('subletting_comments')->nullable();
            $table->string('user_clause')->nullable();
            $table->string('user_clause_comments')->nullable();
            $table->string('alterations')->nullable();
            $table->string('repair_obligation')->nullable();
            $table->text('repair_obligation_note')->nullable();
            $table->string('plate_glass_insurance')->nullable();
            $table->string('building_insurance')->nullable();

            $table->integer('e_decorations_freq')->nullable();
            $table->boolean('e_decorations_first')->nullable();
            $table->boolean('e_decorations_last')->nullable();
            $table->integer('i_decorations_freq')->nullable();
            $table->boolean('i_decorations_first')->nullable();
            $table->boolean('i_decorations_last')->nullable();

            $table->string('g_organisation')->nullable();
            $table->string('g_security')->nullable();
            $table->string('g_amount')->nullable();
            $table->date('g_expiry_date')->nullable();
            $table->string('g_contact_details')->nullable();
            $table->string('g_notes')->nullable();

            $table->string('lease_notes')->nullable();
            $table->string('mgt_remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lease_payables');
    }
}
