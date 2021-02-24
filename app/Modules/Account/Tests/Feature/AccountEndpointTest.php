<?php

namespace App\Modules\Account\Tests\Feature;

use App\Modules\Account\Models\Account;
use App\Modules\Account\Models\AccountType;
use App\Modules\Common\Classes\EndpointTest;

class AccountEndpointTest extends EndpointTest
{
    public function testCanUsersIndexAccounts()
    {
        $this->assertGetCountForAllUserTypes('/api/account/accounts', Account::query());
    }

    public function testCanUsersFilterAccounts()
    {
        $some_account_type_id = AccountType::first()->id;

        $result = $this->apiAs($this->dev_user, 'GET', '/api/account/accounts/', [
            'account_type_id'   => $some_account_type_id
        ], []);

        $result->assertSuccessful();
        foreach ($result->json() as $account) {
            $this->assertEquals($some_account_type_id, $account['account_type_id']);
        }
    }

    public function testCanUsersReadAccounts()
    {
        $existing_id = Account::first()->id;
        $this->assertGetForAllUserTypes('/api/account/accounts/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/account/accounts/-1', [], [])->assertStatus(404);
    }
}
