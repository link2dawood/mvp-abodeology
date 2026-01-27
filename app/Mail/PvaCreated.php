<?php

namespace App\Mail;

use App\Constants\EmailActions;
use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PvaCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $pva;
    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct(User $pva, string $password)
    {
        $this->pva = $pva;
        $this->password = $password;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $defaultSubject = 'Welcome to Abodeology - Your PVA Account';

        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'pva' => $this->pva,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::PVA_CREATED, $data);

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
            'pva' => $this->pva,
            'password' => $this->password,
            'loginUrl' => route('login'),
        ];

        $template = $templateService->getTemplateForAction(EmailActions::PVA_CREATED, $data);

        if ($template && $template->template_type === 'override') {
            return new Content(
                htmlString: $templateService->renderTemplate($template, $data),
            );
        }

        return new Content(
            view: 'emails.pva-created',
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
