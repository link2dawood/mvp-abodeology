<?php

namespace App\Mail;

use App\Models\Offer;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferAmountReleased extends Mailable
{
    use Queueable, SerializesModels;

    public $offer;
    public $property;

    /**
     * Create a new message instance.
     */
    public function __construct(Offer $offer, Property $property)
    {
        $this->offer = $offer;
        $this->property = $property;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Offer Amount Released - £' . number_format($this->offer->offer_amount, 0) . ' - ' . $this->property->address,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.offer-amount-released',
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
