<?php

namespace App\Modules\Client\Tests\Feature;

use App\Modules\Account\Models\AccountType;
use App\Modules\Common\Classes\EndpointTest;

class AccountTypeEndpointTest extends EndpointTest
{
    public function testCanUsersIndexAccountTypes()
    {
        $this->assertGetCountForAllUserTypes('/api/account/account-types/', AccountType::query());
    }

    public function testCanUsersReadAccountTypes()
    {
        $existing_id = AccountType::first()->id;
        $this->assertGetForAllUserTypes('/api/account/account-types/' . $existing_id);
        $this->apiAs($this->dev_user, 'GET', '/api/account/account-types/-1', [], [])->assertStatus(404);
    }
}
