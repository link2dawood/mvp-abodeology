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

class InstructionRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $seller;
    public $property;
    public $instruction;

    /**
     * Create a new message instance.
     */
    public function __construct(User $seller, Property $property, PropertyInstruction $instruction)
    {
        $this->seller = $seller;
        $this->property = $property;
        $this->instruction = $instruction;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Action Required: Sign Your Terms & Conditions - ' . $this->property->address,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.instruction-request-notification',
            with: [
                'seller' => $this->seller,
                'property' => $this->property,
                'instruction' => $this->instruction,
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
