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

class OfferDecisionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $offer;
    public $property;
    public $buyer;
    public $decision;

    /**
     * Create a new message instance.
     */
    public function __construct(Offer $offer, Property $property, User $buyer, string $decision)
    {
        $this->offer = $offer;
        $this->property = $property;
        $this->buyer = $buyer;
        $this->decision = $decision;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $defaultSubject = match($this->decision) {
            'accepted' => 'Great News! Your Offer Has Been Accepted',
            'declined' => 'Offer Response - ' . $this->property->address,
            'counter' => 'Counter-Offer Discussion Request - ' . $this->property->address,
            default => 'Offer Response - ' . $this->property->address,
        };

        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'offer' => $this->offer,
            'property' => $this->property,
            'buyer' => $this->buyer,
            'decision' => $this->decision,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::OFFER_DECISION, $data);

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
            'buyer' => $this->buyer,
            'decision' => $this->decision,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::OFFER_DECISION, $data);

        if ($template && $template->template_type === 'override') {
            return new Content(
                htmlString: $templateService->renderTemplate($template, $data),
            );
        }

        return new Content(
            view: 'emails.offer-decision-notification',
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
