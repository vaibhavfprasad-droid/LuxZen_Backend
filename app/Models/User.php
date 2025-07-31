<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Trip;
use App\Models\Vehicle;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // Removed 'password' => 'hashed' to handle password hashing manually
    ];

    /**
     * Defines the relationship for trips where this user is the **customer**.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tripsAsCustomer(): HasMany
    {
        return $this->hasMany(Trip::class, 'customer_id');
    }

    /**
     * Defines the relationship for trips where this user is the **driver**.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tripsAsDriver(): HasMany
    {
        return $this->hasMany(Trip::class, 'driver_id');
    }

    /**
     * Defines the relationship for the vehicle assigned to this user (driver).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vehicle(): HasOne
    {
        return $this->hasOne(Vehicle::class, 'driver_id');
    }
}
