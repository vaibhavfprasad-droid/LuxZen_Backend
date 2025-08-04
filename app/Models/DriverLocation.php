<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DriverLocation extends Model
{
    public $timestamps = false; // Since you handle timestamps manually

    protected $fillable = [
        'driver_id',
        'latitude',
        'longitude',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
