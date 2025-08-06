<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DriverLocation extends Model
{
    use HasFactory;

    // Disable timestamps since you handle created_at manually
    public $timestamps = false;

    // Mass assignable attributes
    protected $fillable = [
        'driver_id',
        'latitude',
        'longitude',
        'created_at',
    ];

    // Cast attributes to proper types
    protected $casts = [
        'created_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Get the driver (user) associated with this location.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
