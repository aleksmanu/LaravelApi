<?php
/**
 * Created by PhpStorm.
 * User: aron
 * Date: 9/4/18
 * Time: 1:38 PM
 */

namespace App\Modules\Common\Traits;

trait TestsCardSummaryData
{
    public function assertCardSummaryStructure($response)
    {
        return $response->assertJsonStructure([
            'data' => [
                ['name', 'value'],
                ['name', 'value'],
                ['name', 'value'],
                ['name', 'value']
            ]
        ]);
    }
}
