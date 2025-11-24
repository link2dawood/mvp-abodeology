<?php

namespace App\Mail;

use App\Models\Offer;
use App\Models\Property;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOfferNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $offer;
    public $property;
    public $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(Offer $offer, Property $property, User $recipient)
    {
        $this->offer = $offer;
        $this->property = $property;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Offer Received - £' . number_format($this->offer->offer_amount, 0) . ' - ' . $this->property->address,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-offer-notification',
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
