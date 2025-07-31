// database/migrations/xxxx_xx_xx_xxxxxx_create_travel_packages_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('destination');
            $table->text('description');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('duration_days');
            $table->decimal('price', 10, 2); // e.g., 99999999.99
            $table->string('image_url')->nullable(); // URL to a cover image
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_packages');
    }
};