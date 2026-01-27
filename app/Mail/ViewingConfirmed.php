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

class ViewingConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $viewing;
    public $property;
    public $recipient;
    public $recipientRole; // 'buyer' or 'seller'

    /**
     * Create a new message instance.
     */
    public function __construct(Viewing $viewing, Property $property, User $recipient, string $recipientRole)
    {
        $this->viewing = $viewing;
        $this->property = $property;
        $this->recipient = $recipient;
        $this->recipientRole = $recipientRole;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $defaultSubject = $this->recipientRole === 'buyer'
            ? 'Viewing Confirmed - ' . $this->property->address
            : 'Viewing Confirmed for Your Property - ' . $this->property->address;

        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'viewing' => $this->viewing,
            'property' => $this->property,
            'recipient' => $this->recipient,
            'recipientRole' => $this->recipientRole,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::VIEWING_CONFIRMED, $data);

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
            'recipient' => $this->recipient,
            'recipientRole' => $this->recipientRole,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::VIEWING_CONFIRMED, $data);

        if ($template && $template->template_type === 'override') {
            return new Content(
                htmlString: $templateService->renderTemplate($template, $data),
            );
        }

        return new Content(
            view: 'emails.viewing-confirmed',
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
