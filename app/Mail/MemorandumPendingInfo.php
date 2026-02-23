<?php

namespace App\Mail;

use App\Constants\EmailActions;
use App\Models\Offer;
use App\Models\Property;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemorandumPendingInfo extends Mailable
{
    use Queueable, SerializesModels;

    public $offer;
    public $property;
    public $role; // 'seller' or 'buyer'

    /**
     * Create a new message instance.
     */
    public function __construct(Offer $offer, Property $property, string $role)
    {
        $this->offer = $offer;
        $this->property = $property;
        $this->role = $role;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $defaultSubject = 'Action Required: Complete Your Information for Memorandum of Sale';

        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'offer' => $this->offer,
            'property' => $this->property,
            'role' => $this->role,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::MEMORANDUM_PENDING_INFO, $data);

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
            'offer' => $this->offer,
            'property' => $this->property,
            'role' => $this->role,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::MEMORANDUM_PENDING_INFO, $data);

        if ($template && $template->template_type === 'override') {
            return new Content(
                htmlString: $templateService->renderTemplate($template, $data),
            );
        }

        return new Content(
            view: 'emails.memorandum-pending-info',
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
