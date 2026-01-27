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

class OfferAmountReleased extends Mailable
{
    use Queueable, SerializesModels;

    public $offer;
    public $property;

    /**
     * Create a new message instance.
     */
    public function __construct(Offer $offer, Property $property)
    {
        $this->offer = $offer;
        $this->property = $property;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $defaultSubject = 'Offer Amount Released - £' . number_format($this->offer->offer_amount, 0) . ' - ' . $this->property->address;

        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'offer' => $this->offer,
            'property' => $this->property,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::OFFER_AMOUNT_RELEASED, $data);

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
        ];

        $template = $templateService->getTemplateForAction(EmailActions::OFFER_AMOUNT_RELEASED, $data);

        if ($template && $template->template_type === 'override') {
            return new Content(
                htmlString: $templateService->renderTemplate($template, $data),
            );
        }

        return new Content(
            view: 'emails.offer-amount-released',
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
