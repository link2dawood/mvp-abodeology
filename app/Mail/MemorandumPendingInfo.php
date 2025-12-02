<?php

namespace App\Mail;

use App\Models\Offer;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemorandumPendingInfo extends Mailable
{
    use Queueable, SerializesModels;

    public $offer;
    public $property;
    public $role; // 'seller' or 'buyer'

    /**
     * Create a new message instance.
     */
    public function __construct(Offer $offer, Property $property, string $role)
    {
        $this->offer = $offer;
        $this->property = $property;
        $this->role = $role;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->role === 'seller' 
            ? 'Action Required: Complete Your Information for Memorandum of Sale'
            : 'Action Required: Complete Your Information for Memorandum of Sale';
        
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
            view: 'emails.memorandum-pending-info',
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
