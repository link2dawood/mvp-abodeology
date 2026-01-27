<?php

namespace App\Mail;

use App\Constants\EmailActions;
use App\Models\Viewing;
use App\Models\Property;
use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ViewingAssigned extends Mailable
{
    use Queueable, SerializesModels;

    public $viewing;
    public $property;
    public $pva;

    /**
     * Create a new message instance.
     */
    public function __construct(Viewing $viewing, Property $property, User $pva)
    {
        $this->viewing = $viewing;
        $this->property = $property;
        $this->pva = $pva;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $defaultSubject = 'New Viewing Assigned - ' . $this->property->address;

        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'viewing' => $this->viewing,
            'property' => $this->property,
            'pva' => $this->pva,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::VIEWING_ASSIGNED, $data);

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
            'viewing' => $this->viewing,
            'property' => $this->property,
            'pva' => $this->pva,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::VIEWING_ASSIGNED, $data);

        if ($template && $template->template_type === 'override') {
            return new Content(
                htmlString: $templateService->renderTemplate($template, $data),
            );
        }

        return new Content(
            view: 'emails.viewing-assigned',
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
