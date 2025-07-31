<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('trips', function (Blueprint $table) {
        $table->id();
        $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null');
        $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
        $table->text('pickup_location');
        $table->text('dropoff_location');
        $table->enum('trip_type', ['point_to_point', 'rental'])->default('point_to_point');
        $table->enum('status', ['pending', 'driver_assigned', 'ongoing', 'completed', 'canceled'])->default('pending');
        $table->decimal('fare', 8, 2)->nullable();
        $table->dateTime('scheduled_at');
        $table->dateTime('started_at')->nullable();
        $table->dateTime('completed_at')->nullable();
        $table->dateTime('canceled_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};