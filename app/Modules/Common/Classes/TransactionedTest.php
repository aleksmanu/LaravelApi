<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/6/18
 * Time: 3:07 PM
 */

namespace App\Modules\Common\Classes;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TransactionedTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    public function tearDown(): void
    {
        DB::rollback();
        parent::tearDown();
    }
}
