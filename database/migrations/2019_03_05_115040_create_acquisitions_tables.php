<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAcquisitionsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Creates Acquisitions, Workflows and Steps
         */
        Schema::create('acquisition_step_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order')->unsigned();
            $table->string('name');

            $table->timestamps();
            $table->softDeletes();

            $table->integer('account_id')->unsigned()->nullable();
            $table->foreign('account_id')->references('id')->on('accounts');
        });

        Schema::create('acquisition_acquisitions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->integer('account_id')->unsigned();
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->timestamp('commence_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('acquisition_pop_areas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('acquisition_id')->unsigned();
            $table->foreign('acquisition_id')->references('id')->on('acquisition_acquisitions');
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('acquisition_user', function (Blueprint $table) {
            $table->integer('acquisition_id')->unsigned();
            $table->foreign('acquisition_id')->references('id')->on('acquisition_acquisitions');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('acquisition_sites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference');

            $table->integer('pop_area_id')->unsigned()->nullable();
            $table->foreign('pop_area_id')->references('id')->on('acquisition_pop_areas');

            $table->string('unit')->nullable();
            $table->string('number')->nullable();
            $table->string('building')->nullable();
            $table->string('street')->nullable();
            $table->string('estate')->nullable();
            $table->string('suburb')->nullable();

            $table->string('town')->nullable();
            $table->string('county')->nullable();
            $table->string('country')->nullable();
            $table->string('postcode');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('surveyor_name');
            $table->string('agent_mobile');
            $table->string('agent_email');

            $table->text('landlord_address')->nullable();
            $table->string('landlord_name')->nullable();
            $table->string('landlord_telephone')->nullable();
            $table->string('landlord_email')->nullable();

            $table->text('landlord_agent_address')->nullable();
            $table->string('landlord_agent_name')->nullable();
            $table->string('landlord_agent_telephone')->nullable();
            $table->string('landlord_agent_email')->nullable();

            $table->text('landlord_solicitor_address')->nullable();
            $table->string('landlord_solicitor_name')->nullable();
            $table->string('landlord_solicitor_telephone')->nullable();
            $table->string('landlord_solicitor_email')->nullable();

            $table->dateTime('cancelled_at')->nullable();
            $table->integer('cancelled_by')->unsigned()->nullable();
            $table->text('cancel_reason')->nullable();

            $table->string('action')->nullable();
            $table->string('status')->nullable();
            $table->string('option')->nullable()->default('');
            $table->string('site_type')->nullable();
            $table->decimal('proposed_rent', 12, 2)->nullable();
            $table->decimal('agreed_rent', 12, 2)->nullable();
            $table->boolean('rent_free')->nullable();
            $table->string('flood_risk')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('acquisition_checklists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('is_template')->nullable();
            $table->integer('account_id')->unsigned()->nullable();
            $table->foreign('account_id')->references('id')->on('accounts');

            $table->integer('acquisition_site_id')->unsigned()->nullable();
            $table->foreign('acquisition_site_id')->references('id')->on('acquisition_sites');

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('acquisition_steps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('duration_days')->unsigned();

            $table->integer('order')->unsigned();
            $table->string('label');

            // Which phase does this step live in
            $table->integer('acquisition_step_group_id')->unsigned()->nullable();
            $table->foreign('acquisition_step_group_id')->references('id')->on('acquisition_step_groups');

            // BelongsTo one Workflow
            $table->integer('acquisition_checklist_id')->unsigned()->nullable();
            $table->foreign('acquisition_checklist_id')->references('id')->on('acquisition_checklists');

            $table->string('type')->nullable()->default('text:Note');
            $table->string('value')->nullable();

            $table->boolean('mandatory')->default(false);

            // Can only complete this step if another thing has been completed
            $table->integer('depends_on_step_order_number')->unsigned()->nullable();

            $table->integer('updated_by')->unsigned()->nullable();
            $table->foreign('updated_by')->references('id')->on('users');

            $table->integer('completed_by')->unsigned()->nullable();
            $table->foreign('completed_by')->references('id')->on('users');

            $table->integer('role_id')->unsigned()->nullable();
            $table->foreign('role_id')->references('id')->on('roles'); // Which users 'can' this be assigned to

            $table->integer('account_id')->unsigned()->nullable();
            $table->foreign('account_id')->references('id')->on('accounts');

            $table->dateTime('completed_on')->nullable();

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
        Schema::drop('acquisition_steps');
        Schema::drop('acquisition_step_groups');
        Schema::drop('acquisition_checklists');
        Schema::drop('acquisition_sites');
        Schema::drop('acquisition_pop_areas');
        Schema::drop('acquisition_acquisitions');
    }
}
