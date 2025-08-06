<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;    // For customer and driver
use App\Http\Resources\VehicleResource; // For vehicle

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
            'customer_id'       => $this->customer_id,  // Include customer_id
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

            'otp'               => $this->otp,
            'completion_otp'    => $this->completion_otp,

            // Related customer data
            'customer'          => new UserResource($this->whenLoaded('customer')),

            // Conditionally include driver and vehicle details if trip status is driver_assigned or ongoing
            'driver'            => $this->when(
                in_array($this->status, ['driver_assigned', 'ongoing']),
                fn() => new UserResource($this->whenLoaded('driver'))
            ),

            'vehicle'           => $this->when(
                in_array($this->status, ['driver_assigned', 'ongoing']),
                fn() => new VehicleResource($this->whenLoaded('vehicle'))
            ),
        ];
    }
}
