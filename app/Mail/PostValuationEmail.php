<?php

namespace App\Mail;

use App\Constants\EmailActions;
use App\Models\User;
use App\Models\Property;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PostValuationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $seller;
    public $property;

    /**
     * Create a new message instance.
     */
    public function __construct(User $seller, Property $property)
    {
        $this->seller = $seller;
        $this->property = $property;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $defaultSubject = 'Post-Valuation Follow-Up - ' . $this->property->address;

        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'seller' => $this->seller,
            'property' => $this->property,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::POST_VALUATION, $data);

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
            'seller' => $this->seller,
            'property' => $this->property,
            'instructUrl' => route('seller.instruct', $this->property->id),
        ];

        $template = $templateService->getTemplateForAction(EmailActions::POST_VALUATION, $data);

        if ($template && $template->template_type === 'override') {
            return new Content(
                htmlString: $templateService->renderTemplate($template, $data),
            );
        }

        return new Content(
            view: 'emails.post-valuation',
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
