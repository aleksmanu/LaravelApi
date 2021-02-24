<?php
namespace App\Modules\TestFramework\Classes;

use App\Modules\Account\Models\Account;
use App\Modules\Auth\Models\User;
use App\Modules\TestFramework\Classes\SeedClasses\UserSeed;
use Faker\Factory;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class BaseTestClass extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Factory
     */
    protected $faker;

    /**
     * @setUp
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        \DB::beginTransaction();

        $this->faker = Factory::create('en_GB');
        $this->user  = User::create(UserSeed::generate(Account::first()));
        $this->user->assign('developer');
    }

    /**
     * @tearDown
     * @throws \Exception
     */
    public function tearDown(): void
    {

        \DB::rollBack();

        parent::tearDown();
    }

    /**
     * @param User $user
     * @param $method
     * @param $uri
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function apiAs(User $user, $method, $uri, array $data = [], array $headers = [])
    {

        $headers = array_merge([
                                   'Authorization' => 'Bearer ' . JWTAuth::fromUser($user),
                               ], $headers);

        return $this->api($method, $uri, $data, $headers);
    }

    /**
     * @param $method
     * @param $uri
     * @param array $data
     * @param array $headers
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function api($method, $uri, array $data = [], array $headers = [])
    {

        return $this->json($method, $uri, $data, $headers);
    }

    /**
     * @return void
     */
    abstract public function test();
}
