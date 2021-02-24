<?php

namespace App\Modules\Account\Tests\Unit\Controllers\AccountController;

use App\Modules\Account\Http\Controllers\AccountController;
use App\Modules\Account\Models\AccountType;
use App\Modules\Auth\Models\Role;
use App\Modules\TestFramework\Classes\BaseTestClass;

class IndexTest extends BaseTestClass
{

    /**
     * @return AccountController|string
     */
    public function class()
    {
        return AccountController::class;
    }

    /**
     * @setUp
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user->assign(Role::DEVELOPER);
    }

    /**
     * @tearDown
     * @throws \Exception
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @see AccountController::index()
     * @test
     */
    public function test()
    {

        $request_data = $this->getRequestData();

        $expected = \DB::table('accounts')->where('account_type_id', $request_data['account_type_id'])
                                                ->whereNull('deleted_at')
                                                ->orderBy('name', 'asc')
                                                ->get();

        $result = $this->apiAs($this->user, 'GET', 'api/account/accounts', $request_data);

        $result->assertSuccessful();

        $this->assertSame(count($expected), count($result->json()));
    }

    /**
     * @return array
     */
    private function getRequestData()
    {
        return [
            'account_type_id' => AccountType::where('slug', '!=', AccountType::SYSTEM)->first()->id
        ];
    }
}
