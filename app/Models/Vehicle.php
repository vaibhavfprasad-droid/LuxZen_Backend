<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Trip;
use App\Models\VehicleDocument;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'driver_id',
        'model',
        'type',
        'number_plate',
        'status',
    ];

    /**
     * Get the driver (user) associated with the vehicle.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get the trips for this vehicle.
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'vehicle_id');
    }

    /**
     * Get the documents related to this vehicle.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }
}
