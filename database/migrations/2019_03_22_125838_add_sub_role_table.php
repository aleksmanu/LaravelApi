<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

//        Schema::create('sub_roles', function (Blueprint $table) {
//            $table->increments('id');
//            $table->string('name', 150);
//            $table->string('title')->nullable();
//            $table->integer('role_id')->unsigned();
//
//            $table->foreign('role_id')
//                  ->references('id')->on('roles');
//
//            $table->unique(
//                ['name', 'role_id'],
//                'roles_name_unique'
//            );
//        });
//
//        Schema::create('assigned_sub_roles', function (Blueprint $table) {
//            $table->integer('sub_role_id')->unsigned()->index();
//            $table->integer('entity_id')->unsigned();
//            $table->string('entity_type', 150);
//
//            $table->index(
//                ['entity_id', 'entity_type'],
//                'assigned_sub_roles_entity_index'
//            );
//
//            $table->foreign('sub_role_id')
//                  ->references('id')->on('sub_roles');
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::drop('sub_roles');
//        Schema::drop('assigned_sub_roles');
    }
}
