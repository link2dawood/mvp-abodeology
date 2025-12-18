<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PropertyStatusChangedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $property;
    public $status;
    public $message;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Property $property, string $status, string $message)
    {
        $this->user = $user;
        $this->property = $property;
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->status) {
            'sold' => 'Property Has Been Sold',
            'withdrawn' => 'Property Has Been Withdrawn',
            'sstc' => 'Property Status Updated - Sold Subject to Contract',
            default => 'Property Status Changed',
        };

        return new Envelope(
            subject: $subject . ' - ' . $this->property->address,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.property-status-changed',
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
