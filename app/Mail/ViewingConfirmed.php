<?php

namespace App\Mail;

use App\Models\Viewing;
use App\Models\Property;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ViewingConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $viewing;
    public $property;
    public $recipient;
    public $recipientRole; // 'buyer' or 'seller'

    /**
     * Create a new message instance.
     */
    public function __construct(Viewing $viewing, Property $property, User $recipient, string $recipientRole)
    {
        $this->viewing = $viewing;
        $this->property = $property;
        $this->recipient = $recipient;
        $this->recipientRole = $recipientRole;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->recipientRole === 'buyer'
            ? 'Viewing Confirmed - ' . $this->property->address
            : 'Viewing Confirmed for Your Property - ' . $this->property->address;
        
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.viewing-confirmed',
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
