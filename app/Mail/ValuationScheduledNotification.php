<?php

namespace App\Mail;

use App\Models\Valuation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ValuationScheduledNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Valuation $valuation;

    /**
     * Create a new message instance.
     */
    public function __construct(Valuation $valuation)
    {
        $this->valuation = $valuation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your valuation is scheduled - ' . ($this->valuation->property_address ?? 'Abodeology'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.valuation-scheduled',
            with: [
                'valuation' => $this->valuation,
                'seller' => $this->valuation->seller,
                'agent' => $this->valuation->agent,
                'loginUrl' => route('login'),
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

