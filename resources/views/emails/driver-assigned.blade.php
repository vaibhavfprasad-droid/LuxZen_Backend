<!DOCTYPE html>
<html>
<head>
    <title>Your Driver is Assigned</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        h1 { color: #444; }
        .details { background-color: #f9f9f9; padding: 15px; border-radius: 4px; }
        .footer { margin-top: 20px; font-size: 0.9em; text-align: center; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Great News, {{ $trip->customer->name }}!</h1>
        <p>A driver has been assigned for your upcoming trip.</p>

        <div class="details">
            <h3>Trip Details</h3>
            <p><strong>Trip ID:</strong> #{{ $trip->id }}</p>
            <p><strong>Scheduled For:</strong> {{ $trip->scheduled_at->format('F d, Y \a\t h:i A') }}</p>
            <p><strong>Pickup:</strong> {{ $trip->pickup_location }}</p>
            <p><strong>Drop-off:</strong> {{ $trip->dropoff_location }}</p>
            <hr>
            <h3>Your Driver</h3>
            <p><strong>Name:</strong> {{ $trip->driver->name }}</p>
            <p><strong>Vehicle:</strong> {{ $trip->vehicle->model }} ({{ $trip->vehicle->number_plate }})</p>
        </div>

        <p>Please be ready at your pickup location at the scheduled time. You can view your full trip history in our app.</p>
        <p>Thank you for choosing Luxzen!</p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} Luxzen. All rights reserved.</p>
    </div>
</body>
</html>