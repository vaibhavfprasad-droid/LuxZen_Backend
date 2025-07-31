<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // --- Relationships ---
    public function driver() {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function trips() {
        return $this->hasMany(Trip::class);
    }

    public function documents() {
        return $this->hasMany(VehicleDocument::class);
    }
}