<?php

namespace App\Modules\Auth\Database\Seeds;

use App\Modules\Auth\Models\Permission;
use App\Modules\Auth\Models\RoleTemplate;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $p0 = Permission::create([
            'name' => 'User.Index',
            'slug' => 'user_index',
            'is_system' => true,
        ]);

        $p1 = Permission::create([
            'name' => 'User.Create',
            'slug' => 'user_create',
            'is_system' => true,
        ]);

        $p2 = Permission::create([
            'name' => 'User.Read',
            'slug' => 'user_read',
            'is_system' => true,
        ]);

        $p3 = Permission::create([
            'name' => 'User.Update',
            'slug' => 'user_update',
            'is_system' => true,
        ]);

        $p4 = Permission::create([
            'name' => 'User.Delete',
            'slug' => 'user_delete',
            'is_system' => true,
        ]);

        $p5 = Permission::create([
            'name' => 'Client.Create',
            'slug' => 'client_create',
            'is_system' => false,
        ]);

        $p6 = Permission::create([
            'name' => 'Client.Read',
            'slug' => 'client_read',
            'is_system' => false,
        ]);

        $testRole1 = RoleTemplate::create(['name' => 'Example Role Template']);
        $testRole1->permissions()->attach($p0);
        $testRole1->permissions()->attach($p1);
        $testRole1->permissions()->attach($p2);
        $testRole1->permissions()->attach($p3);
        $testRole1->permissions()->attach($p4);

        $testRole2 = RoleTemplate::create(['name' => 'Manage Clients (2nd example)']);
        $testRole2->permissions()->attach($p5);
        $testRole2->permissions()->attach($p6);
    }
}
