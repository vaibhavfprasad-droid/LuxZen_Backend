<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Trip; // Import the Trip model

class DriverAssignedNotification extends Mailable
{
    use Queueable, SerializesModels;

    // Make the trip property public so it's automatically available in the view
    public Trip $trip;

    /**
     * Create a new message instance.
     */
    public function __construct(Trip $trip)
    {
        // Accept the Trip object when creating a new email
        $this->trip = $trip;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Driver has been Assigned!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Tell Laravel to use a Blade view for the email's content
        return new Content(
            view: 'emails.driver-assigned',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}