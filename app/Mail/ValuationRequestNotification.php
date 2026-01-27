<?php

namespace App\Mail;

use App\Constants\EmailActions;
use App\Models\Valuation;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ValuationRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $valuation;

    /**
     * Create a new message instance.
     */
    public function __construct(Valuation $valuation)
    {
        $this->valuation = $valuation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $defaultSubject = 'New Valuation Appointment Request - ' . $this->valuation->property_address;

        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'valuation' => $this->valuation,
            'seller' => $this->valuation->seller,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::VALUATION_REQUEST, $data);

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
            'valuation' => $this->valuation,
            'seller' => $this->valuation->seller,
            'dashboardUrl' => route('admin.dashboard'),
        ];

        $template = $templateService->getTemplateForAction(EmailActions::VALUATION_REQUEST, $data);

        if ($template && $template->template_type === 'override') {
            return new Content(
                htmlString: $templateService->renderTemplate($template, $data),
            );
        }

        return new Content(
            view: 'emails.valuation-request-notification',
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
