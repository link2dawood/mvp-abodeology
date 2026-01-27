<?php

namespace App\Mail;

use App\Constants\EmailActions;
use App\Models\User;
use App\Models\Property;
use App\Models\PropertyInstruction;
use App\Services\EmailTemplateService;
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
        $defaultSubject = 'Welcome to Abodeology - Your Welcome Pack';

        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'user' => $this->user,
            'property' => $this->property,
            'instruction' => $this->instruction,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::WELCOME_PACK, $data);

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
            'property' => $this->property,
            'instruction' => $this->instruction,
            'sellerDashboardUrl' => route('seller.dashboard'),
        ];

        $template = $templateService->getTemplateForAction(EmailActions::WELCOME_PACK, $data);

        if ($template && $template->template_type === 'override') {
            return new Content(
                htmlString: $templateService->renderTemplate($template, $data),
            );
        }

        return new Content(
            view: 'emails.welcome-pack',
            with: $data,
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
