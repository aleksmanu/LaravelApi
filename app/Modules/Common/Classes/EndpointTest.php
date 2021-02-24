<?php
namespace App\Modules\Common\Classes;

use App\Modules\Auth\Models\Role;
use App\Modules\Auth\Models\User;
use App\Modules\Common\Traits\TestsAuthenticatedRoute;
use Faker\Factory;

class EndpointTest extends TransactionedTest
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    use TestsAuthenticatedRoute;

    public $dev_user;
    public $admin_user;
    public $authoriser_user;
    public $editor_user;
    public $slave_user;

    /**
     * EndpointTest constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->faker = Factory::create();
    }

    public function assertGetForAllUserTypes($uri)
    {
        $this->apiAs($this->dev_user, 'GET', $uri, [], [])
            ->assertOk();
        $this->apiAs($this->admin_user, 'GET', $uri, [], [])
            ->assertOk();
        $this->apiAs($this->authoriser_user, 'GET', $uri, [], [])
            ->assertOk();
        $this->apiAs($this->editor_user, 'GET', $uri, [], [])
            ->assertOk();
        $this->apiAs($this->slave_user, 'GET', $uri, [], [])
            ->assertForbidden();
    }

    public function assertGetCountForAllUserTypes($uri, $equivalent_query = null)
    {
        $response_dev = $this->apiAs($this->dev_user, 'GET', $uri, [], []);
        $response_dev->assertOk();

        $response_adm = $this->apiAs($this->admin_user, 'GET', $uri, [], []);
        $response_adm->assertOk();

        $response_auth = $this->apiAs($this->authoriser_user, 'GET', $uri, [], []);
        $response_auth->assertOk();

        $response_edit = $this->apiAs($this->editor_user, 'GET', $uri, [], []);
        $response_edit->assertOk();

        $response_slave = $this->apiAs($this->slave_user, 'GET', $uri, [], []);
        $response_slave->assertForbidden();

        if ($equivalent_query) {
            $expected_count = min($equivalent_query->count(), +config('misc.api.maximumResponseSize'));
            $this->assertSame($expected_count, count($response_dev->json()));
            $this->assertSame($expected_count, count($response_adm->json()));
            $this->assertSame($expected_count, count($response_auth->json()));
            $this->assertSame($expected_count, count($response_edit->json()));
        }
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->dev_user = factory(User::class)->create();
        $this->admin_user = factory(User::class)->create();
        $this->authoriser_user = factory(User::class)->create();
        $this->editor_user = factory(User::class)->create();
        $this->slave_user = factory(User::class)->create();

        $this->dev_user->assign(Role::DEVELOPER);
        $this->admin_user->assign(Role::ADMIN);
        $this->authoriser_user->assign(Role::AUTHORISER);
        $this->editor_user->assign(Role::EDITOR);
        // If we don't assign anything to the slave, the user index query will fail on joining roles
        $this->slave_user->assign('temp_slave_role');
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
