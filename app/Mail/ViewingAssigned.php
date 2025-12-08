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

class ViewingAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $viewing;
    public $property;
    public $pva;

    /**
     * Create a new message instance.
     */
    public function __construct(Viewing $viewing, Property $property, User $pva)
    {
        $this->viewing = $viewing;
        $this->property = $property;
        $this->pva = $pva;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Viewing Assigned - ' . $this->property->address,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.viewing-assigned',
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
