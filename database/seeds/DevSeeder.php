<?php

use Illuminate\Database\Seeder;
use App\Modules\Auth\Models\Role;
use App\Modules\Account\Models\AccountType;

class DevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Set up default account types
        $sysAccountTypeObj = \App\Modules\Account\Models\AccountType::create([
            'name' => 'System',
            'slug' => AccountType::SYSTEM
        ]);

        \App\Modules\Account\Models\AccountType::create([
            'name' => 'Client',
            'slug' => AccountType::CLIENT
        ]);

        $extAccountTypeObj = \App\Modules\Account\Models\AccountType::create([
            'name' => 'External',
            'slug' => AccountType::EXTERNAL
        ]);

        //Create a SYSTEM account to attach dev users to
        $sysAccountObj = $sysAccountTypeObj->accounts()->create(['name' => 'System']);
        //Create a EXTERNAL account to attach external users to
        $extAccountObj = $extAccountTypeObj->accounts()->create(['name' => 'External']);

        //Now some dev accounts
        $aron = $sysAccountObj->users()->create([
            'first_name' => 'Aron',
            'last_name'  => 'Herteli',
            'email'      => 'aronherteli@airiq.co.uk',
            'password'   => \Illuminate\Support\Facades\Hash::make('Test123'),
        ]);
        $aidan = $sysAccountObj->users()->create([
            'first_name' => 'Aidan',
            'last_name'  => 'Ward',
            'email'      => 'aidanward@airiq.co.uk',
            'password'   => \Illuminate\Support\Facades\Hash::make('Test123'),
        ]);
        $charlie = $sysAccountObj->users()->create([
            'first_name' => 'Charlie',
            'last_name'  => 'Asemota',
            'email'      => 'charlieasemota@airiq.co.uk',
            'password'   => \Illuminate\Support\Facades\Hash::make('Test123'),
        ]);
        $abbie = $sysAccountObj->users()->create([
            'first_name' => 'Abbie',
            'last_name'  => 'Meekin',
            'email'      => 'abbiemeekin@airiq.co.uk',
            'password'   => \Illuminate\Support\Facades\Hash::make('Test123'),
        ]);
        $dave = $sysAccountObj->users()->create([
            'first_name' => 'David',
            'last_name'  => 'Mulvey',
            'email'      => 'davidmulvey@airiq.co.uk',
            'password'   => \Illuminate\Support\Facades\Hash::make('Test123'),
        ]);

        $cluttons = $sysAccountObj->users()->create([
            'first_name' => 'James',
            'last_name'  => 'Warburton',
            'email'      => 'james.warburton@cluttons.com',
            'password'   => \Illuminate\Support\Facades\Hash::make('n3br4ska'),
        ]);

        $cluttons2 = $sysAccountObj->users()->create([
            'first_name' => 'James',
            'last_name'  => 'Warburton',
            'email'      => 'james.warburton2@cluttons.com',
            'password'   => \Illuminate\Support\Facades\Hash::make('n3br4ska'),
        ]);

        /**
         * Create default permissions and role_templates
         */
        $this->call([\App\Modules\Auth\Database\Seeds\RoleAndPermissionSeeder::class]);
        $aron->assign(Role::DEVELOPER);
        $aidan->assign(Role::DEVELOPER);
        $charlie->assign(Role::DEVELOPER);
        $dave->assign(Role::AUTHORISER);
        $abbie->assign(Role::AUTHORISER);

        $cluttons->assign(Role::AUTHORISER);
        $cluttons2->assign(Role::EDITOR);

        $this->call(\App\Modules\Edits\Database\Seeds\EditsDevSeeder::class);
    }
}
