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

class NewOfferNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $offer;
    public $property;
    public $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(Offer $offer, Property $property, User $recipient)
    {
        $this->offer = $offer;
        $this->property = $property;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        // Default subject logic (backwards compatible)
        if (in_array($this->recipient->role, ['seller', 'both']) && !$this->offer->released_to_seller) {
            $defaultSubject = 'New Offer Received - ' . $this->property->address;
        } else {
            $defaultSubject = 'New Offer Received - £' . number_format($this->offer->offer_amount, 0) . ' - ' . $this->property->address;
        }

        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'offer' => $this->offer,
            'property' => $this->property,
            'recipient' => $this->recipient,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::NEW_OFFER, $data);

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
            'recipient' => $this->recipient,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::NEW_OFFER, $data);

        if ($template && $template->template_type === 'override') {
            return new Content(
                htmlString: $templateService->renderTemplate($template, $data),
            );
        }

        return new Content(
            view: 'emails.new-offer-notification',
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
