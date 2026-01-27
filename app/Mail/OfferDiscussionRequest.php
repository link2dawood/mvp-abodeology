<?php

namespace App\Mail;

use App\Constants\EmailActions;
use App\Models\Offer;
use App\Models\Property;
use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferDiscussionRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $offer;
    public $property;
    public $seller;
    public $requestData;

    /**
     * Create a new message instance.
     */
    public function __construct(Offer $offer, Property $property, User $seller, array $requestData)
    {
        $this->offer = $offer;
        $this->property = $property;
        $this->seller = $seller;
        $this->requestData = $requestData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $urgency = $this->requestData['urgency'] ?? 'normal';
        $urgencyText = [
            'normal' => 'Normal Priority',
            'urgent' => 'URGENT',
            'asap' => 'ASAP - Immediate'
        ][$urgency] ?? 'Normal Priority';

        $defaultSubject = '[' . $urgencyText . '] Seller Discussion Request - Offer on ' . $this->property->address;

        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'offer' => $this->offer,
            'property' => $this->property,
            'seller' => $this->seller,
            'requestData' => $this->requestData,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::OFFER_DISCUSSION_REQUEST, $data);

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
            'seller' => $this->seller,
            'requestData' => $this->requestData,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::OFFER_DISCUSSION_REQUEST, $data);

        if ($template && $template->template_type === 'override') {
            return new Content(
                htmlString: $templateService->renderTemplate($template, $data),
            );
        }

        return new Content(
            view: 'emails.offer-discussion-request',
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

