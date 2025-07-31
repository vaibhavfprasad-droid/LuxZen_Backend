<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Trip;
use App\Models\Invoice;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- Create Users ---
        // We use variables to capture the created models so we can use their IDs later.

        // Create the Admin user (will have id=1)
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@luxzen.com',
            'password' => Hash::make('password'),
            'type' => 'executive',
            'status' => 'active'
        ]);

        // Create a Customer user (will have id=2)
        $customer = User::create([
            'name' => 'John Doe',
            'email' => 'customer@test.com',
            'password' => Hash::make('password'),
            'type' => 'customer',
            'status' => 'active'
        ]);

        // Create a Driver user (will have id=3)
        $driver = User::create([
            'name' => 'Jane Smith',
            'email' => 'driver@test.com',
            'password' => Hash::make('password'),
            'type' => 'driver',
            'status' => 'active'
        ]);
        
        $this->command->info('Users created successfully.');


        // --- Create Vehicles and Assign to Driver ---

        // Create a vehicle and assign it to the driver we just created
        $vehicle = Vehicle::create([
            'driver_id' => $driver->id,
            'model' => 'Toyota Camry',
            'type' => 'Sedan',
            'number_plate' => 'ABC-123'
        ]);

        // Create a second, unassigned vehicle
        Vehicle::create([
            'model' => 'Honda Civic',
            'type' => 'Sedan',
            'number_plate' => 'XYZ-789',
            'status' => 'in_maintenance'
        ]);

        $this->command->info('Vehicles created successfully.');


        // --- Create Trips ---

        // Create a pending trip for the customer we created
        $pendingTrip = Trip::create([
            'customer_id' => $customer->id,
            'pickup_location' => '123 Main St, Cityville',
            'dropoff_location' => '456 Oak Ave, Townburg',
            'scheduled_at' => now()->addHours(3)
        ]);

        // Create a completed trip with an assigned driver and vehicle
        $completedTrip = Trip::create([
            'customer_id' => $customer->id,
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'pickup_location' => '789 Pine Ln',
            'dropoff_location' => '101 Maple Rd',
            'scheduled_at' => now()->subDays(2),
            'status' => 'completed',
            'completed_at' => now()->subDays(2)->addHours(1),
            'fare' => 45.50
        ]);
        
        $this->command->info('Trips created successfully.');


        // --- Create an Invoice for the completed trip ---
        Invoice::create([
            'trip_id' => $completedTrip->id,
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-' . $completedTrip->id,
            'amount' => $completedTrip->fare,
            'issued_at' => $completedTrip->completed_at,
            'status' => 'paid',
            'paid_at' => $completedTrip->completed_at,
        ]);

        $this->command->info('Invoice created successfully.');
        $this->command->info('Test data seeding complete!');
    }
}