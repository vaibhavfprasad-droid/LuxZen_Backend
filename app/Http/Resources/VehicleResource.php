<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'model'        => $this->model,
            'type'         => $this->type,
            'number_plate' => $this->number_plate,
            'status'       => $this->status,
        ];
    }
}
