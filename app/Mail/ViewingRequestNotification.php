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

class ViewingRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $viewing;
    public $property;
    public $buyer;

    /**
     * Create a new message instance.
     */
    public function __construct(Viewing $viewing, Property $property, User $buyer)
    {
        $this->viewing = $viewing;
        $this->property = $property;
        $this->buyer = $buyer;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Viewing Request - ' . $this->property->address,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.viewing-request-notification',
            with: [
                'viewing' => $this->viewing,
                'property' => $this->property,
                'buyer' => $this->buyer,
                'dashboardUrl' => route('pva.viewings.index'),
                'viewingUrl' => route('pva.viewings.show', $this->viewing->id),
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

