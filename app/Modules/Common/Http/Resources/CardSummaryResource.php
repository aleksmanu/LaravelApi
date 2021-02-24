<?php

namespace App\Modules\Common\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CardSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            ['name' => 'TOTAL',         'value' => $this->total],
            ['name' => 'REVIEWED',      'value' => $this->reviewed],
            ['name' => 'PENDING',       'value' => $this->pending],
            ['name' => 'NOT REVIEWED',  'value' => $this->not_reviewed],
        ];
    }
}
