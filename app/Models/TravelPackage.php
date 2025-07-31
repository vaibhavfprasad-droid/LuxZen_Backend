<!-- // app/Models/TravelPackage.php -->
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute; // Import this
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage; // Import this

class TravelPackage extends Model
{
    use HasFactory;

    // ... (existing fillable and casts properties) ...

    /**
     * The accessors to append to the model's array form.
     * This makes sure 'full_image_url' is always included in API responses.
     * @var array
     */
    protected $appends = ['full_image_url'];


    /**
     * Accessor for the full image URL.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function fullImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->image_url
                ? Storage::disk('public')->url($this->image_url)
                : null,
        );
    }

    // ... (existing relationships) ...
}