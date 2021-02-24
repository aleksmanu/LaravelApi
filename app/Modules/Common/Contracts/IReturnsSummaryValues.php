<?php
namespace App\Modules\Common\Contracts;

use App\Modules\Common\Http\Resources\CardSummaryResource;

interface IReturnsSummaryValues
{
    public function summarize(): CardSummaryResource;
}
