<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Property;
use App\Models\PropertyInstruction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomePack extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $property;
    public $instruction;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Property $property, PropertyInstruction $instruction)
    {
        $this->user = $user;
        $this->property = $property;
        $this->instruction = $instruction;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Abodeology - Your Welcome Pack',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-pack',
            with: [
                'user' => $this->user,
                'property' => $this->property,
                'instruction' => $this->instruction,
                'sellerDashboardUrl' => route('seller.dashboard'),
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
