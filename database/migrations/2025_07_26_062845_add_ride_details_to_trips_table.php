<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            // The frontend sends a driver_id, but an admin assigns it. Let's make it nullable.
            // Assuming you have a 'users' table for drivers.
            $table->foreignId('driver_id')->nullable()->after('customer_id')->constrained('users');

            // Rename 'dropoff_location' to 'drop_location' for consistency with the frontend
            $table->renameColumn('dropoff_location', 'drop_location');

            // Add coordinate columns
            $table->decimal('pickup_lat', 10, 7)->after('pickup_location');
            $table->decimal('pickup_lng', 10, 7)->after('pickup_lat');
            $table->decimal('drop_lat', 10, 7)->after('drop_location');
            $table->decimal('drop_lng', 10, 7)->after('drop_lat');

            // Let's also make scheduled_at nullable if rides can be created for "now"
            $table->dateTime('scheduled_at')->nullable()->change();

            // Remove columns from the old controller logic that we aren't using yet
            $table->dropColumn(['trip_type']);
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');
            $table->renameColumn('drop_location', 'dropoff_location');
            $table->dropColumn(['pickup_lat', 'pickup_lng', 'drop_lat', 'drop_lng']);
            $table->dateTime('scheduled_at')->nullable(false)->change();
            $table->string('trip_type'); // Add back the column if you rollback
        });
    }
};