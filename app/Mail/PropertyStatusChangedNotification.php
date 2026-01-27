<?php

namespace App\Mail;

use App\Constants\EmailActions;
use App\Models\User;
use App\Models\Property;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PropertyStatusChangedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $property;
    public $status;
    public $message;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Property $property, string $status, string $message)
    {
        $this->user = $user;
        $this->property = $property;
        $this->status = $status;
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $defaultSubject = match($this->status) {
            'sold' => 'Property Has Been Sold',
            'withdrawn' => 'Property Has Been Withdrawn',
            'sstc' => 'Property Status Updated - Sold Subject to Contract',
            default => 'Property Status Changed',
        };

        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'user' => $this->user,
            'property' => $this->property,
            'status' => $this->status,
            'message' => $this->message,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::PROPERTY_STATUS_CHANGED, $data);

        $subject = $template && $template->subject
            ? $templateService->renderSubject($template, $data)
            : $defaultSubject;

        return new Envelope(
            subject: $subject . ' - ' . $this->property->address,
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
            'status' => $this->status,
            'message' => $this->message,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::PROPERTY_STATUS_CHANGED, $data);

        if ($template && $template->template_type === 'override') {
            return new Content(
                htmlString: $templateService->renderTemplate($template, $data),
            );
        }

        return new Content(
            view: 'emails.property-status-changed',
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
