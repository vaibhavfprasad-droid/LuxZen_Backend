<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'pickup_location'   => $this->pickup_location,
            'pickup_latitude'   => $this->pickup_latitude,
            'pickup_longitude'  => $this->pickup_longitude,
            'dropoff_location'  => $this->dropoff_location,
            'dropoff_latitude'  => $this->dropoff_latitude,
            'dropoff_longitude' => $this->dropoff_longitude,
            'trip_type'         => $this->trip_type,
            'scheduled_at'      => $this->scheduled_at ? $this->scheduled_at->format('Y-m-d H:i:s') : null,
            'status'            => $this->status,
            'fare'              => $this->fare,
            'vehicle_id'        => $this->vehicle_id,
            // Optionally include related data:
            // 'vehicle'        => new VehicleResource($this->whenLoaded('vehicle')),
            // 'driver'         => new UserResource($this->whenLoaded('driver')),
            // ...
        ];
    }
}
