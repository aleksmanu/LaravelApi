<?php

namespace App\Modules\Common\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DashboardPropertyResource extends JsonResource
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
            'id' => $this->id,
            'address' => [
                'latitude' => $this->address->latitude,
                'longitude' => $this->address->longitude,
            ],
        ];
    }
}
