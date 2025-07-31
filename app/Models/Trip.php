<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Invoice;

class Trip extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'customer_id',
        'driver_id',
        'vehicle_id',
        'pickup_location',
        'pickup_latitude',      // <-- added
        'pickup_longitude',     // <-- added
        'dropoff_location',
        'dropoff_latitude',     // <-- added
        'dropoff_longitude',    // <-- added
        'trip_type',
        'status',
        'fare',
        'scheduled_at',
        'started_at',
        'completed_at',
        'canceled_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_at'  => 'datetime',
        'started_at'    => 'datetime',
        'completed_at'  => 'datetime',
        'canceled_at'   => 'datetime',
        'fare'          => 'decimal:2',
        'pickup_latitude'   => 'float',  // <-- added
        'pickup_longitude'  => 'float',  // <-- added
        'dropoff_latitude'  => 'float',  // <-- added
        'dropoff_longitude' => 'float',  // <-- added
    ];

    /**
     * Get the customer (user) who booked the trip.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the driver (user) assigned to this trip.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get the vehicle assigned for this trip.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the invoice associated with this trip.
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
