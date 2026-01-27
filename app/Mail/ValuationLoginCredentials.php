<?php

namespace App\Mail;

use App\Constants\EmailActions;
use App\Models\User;
use App\Models\Valuation;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        $defaultSubject = 'Welcome to Abodeology - Your Login Credentials';

        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'user' => $this->user,
            'valuation' => $this->valuation,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::VALUATION_LOGIN_CREDENTIALS, $data);

        $subject = $template && $template->subject
            ? $templateService->renderSubject($template, $data)
            : $defaultSubject;

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'user' => $this->user,
            'password' => $this->password,
            'valuation' => $this->valuation,
            'loginUrl' => route('login'),
        ];

        $template = $templateService->getTemplateForAction(EmailActions::VALUATION_LOGIN_CREDENTIALS, $data);

        if ($template && $template->template_type === 'override') {
            return new Content(
                htmlString: $templateService->renderTemplate($template, $data),
            );
        }

        return new Content(
            view: 'emails.valuation-login-credentials',
            with: $data,
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
