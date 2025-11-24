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

class OfferDecisionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $offer;
    public $property;
    public $buyer;
    public $decision;

    /**
     * Create a new message instance.
     */
    public function __construct(Offer $offer, Property $property, User $buyer, string $decision)
    {
        $this->offer = $offer;
        $this->property = $property;
        $this->buyer = $buyer;
        $this->decision = $decision;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->decision) {
            'accepted' => 'Great News! Your Offer Has Been Accepted',
            'declined' => 'Offer Response - ' . $this->property->address,
            'counter' => 'Counter-Offer Discussion Request - ' . $this->property->address,
            default => 'Offer Response - ' . $this->property->address,
        };

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
            view: 'emails.offer-decision-notification',
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
