<?php
namespace App\Modules\Auth\Tests;

use App\Modules\Common\Classes\EndpointTest;
use App\Modules\Auth\Database\Seeds\RoleAndPermissionSeeder;

class RolesTest extends EndpointTest
{
    public function tesDoesDeveloperHaveAllPermissions()
    {
        $crudAndRoModels = array_merge(RoleAndPermissionSeeder::CRUD_MODELS, RoleAndPermissionSeeder::READ_ONLY_MODELS);
        foreach ($crudAndRoModels as $className) {
            foreach (RoleAndPermissionSeeder::CRUD_OPERATIONS as $operation) {
                $this->assertTrue($this->dev_user->can($operation, $className));
            }
        }
    }

    public function testDoesAdminHaveAllPermissions()
    {
        $crudAndRoModels = array_merge(RoleAndPermissionSeeder::CRUD_MODELS, RoleAndPermissionSeeder::READ_ONLY_MODELS);
        foreach ($crudAndRoModels as $className) {
            foreach (RoleAndPermissionSeeder::CRUD_OPERATIONS as $operation) {
                $this->assertTrue($this->admin_user->can($operation, $className));
            }
        }
    }

    public function testDoesAuthoriserHaveCorrectPermissions()
    {
        foreach (RoleAndPermissionSeeder::CRUD_MODELS as $className) {
            $this->assertTrue($this->authoriser_user->can('index', $className));
            $this->assertTrue($this->authoriser_user->can('create', $className));
            $this->assertTrue($this->authoriser_user->can('read', $className));
            $this->assertTrue($this->authoriser_user->can('update', $className));
            $this->assertFalse($this->authoriser_user->can('delete', $className));
        }

        foreach (RoleAndPermissionSeeder::READ_ONLY_MODELS as $className) {
            $this->assertTrue($this->authoriser_user->can('index', $className));
            $this->assertFalse($this->authoriser_user->can('create', $className));
            $this->assertTrue($this->authoriser_user->can('read', $className));
            $this->assertFalse($this->authoriser_user->can('update', $className));
            $this->assertFalse($this->authoriser_user->can('delete', $className));
        }
    }

    public function testDoesEditorHaveCorrectPermissions()
    {
        foreach (RoleAndPermissionSeeder::CRUD_MODELS as $className) {
            $this->assertTrue($this->editor_user->can('index', $className));
            $this->assertTrue($this->editor_user->can('create', $className));
            $this->assertTrue($this->editor_user->can('read', $className));
            $this->assertTrue($this->editor_user->can('update', $className));
            $this->assertFalse($this->editor_user->can('delete', $className));
        }

        foreach (RoleAndPermissionSeeder::READ_ONLY_MODELS as $className) {
            $this->assertTrue($this->editor_user->can('index', $className));
            $this->assertFalse($this->editor_user->can('create', $className));
            $this->assertTrue($this->editor_user->can('read', $className));
            $this->assertFalse($this->editor_user->can('update', $className));
            $this->assertFalse($this->editor_user->can('delete', $className));
        }
    }

    public function testDoesSlaveHaveNoPermissions()
    {
        $crudAndRoModels = array_merge(RoleAndPermissionSeeder::CRUD_MODELS, RoleAndPermissionSeeder::READ_ONLY_MODELS);
        foreach ($crudAndRoModels as $className) {
            foreach (RoleAndPermissionSeeder::CRUD_OPERATIONS as $operation) {
                $this->assertFalse($this->slave_user->can($operation, $className));
            }
        }
    }
}
