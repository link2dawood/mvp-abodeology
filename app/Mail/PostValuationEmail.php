<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PostValuationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $seller;
    public $property;

    /**
     * Create a new message instance.
     */
    public function __construct(User $seller, Property $property)
    {
        $this->seller = $seller;
        $this->property = $property;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Post-Valuation Follow-Up - ' . $this->property->address,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.post-valuation',
            with: [
                'seller' => $this->seller,
                'property' => $this->property,
                'instructUrl' => route('seller.instruct', $this->property->id),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
