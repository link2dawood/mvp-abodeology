<?php

namespace App\Mail;

use App\Models\Valuation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ValuationRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $valuation;

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
            subject: 'New Valuation Appointment Request - ' . $this->valuation->property_address,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.valuation-request-notification',
            with: [
                'valuation' => $this->valuation,
                'seller' => $this->valuation->seller,
                'dashboardUrl' => route('admin.dashboard'),
            ],
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
