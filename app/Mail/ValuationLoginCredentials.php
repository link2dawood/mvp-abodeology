<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Valuation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ValuationLoginCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $valuation;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $password, Valuation $valuation)
    {
        $this->user = $user;
        $this->password = $password;
        $this->valuation = $valuation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Abodeology - Your Login Credentials',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.valuation-login-credentials',
            with: [
                'user' => $this->user,
                'password' => $this->password,
                'valuation' => $this->valuation,
                'loginUrl' => route('login'),
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
