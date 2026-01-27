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
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class MemorandumOfSale extends Mailable
{
    use Queueable, SerializesModels;

    public $offer;
    public $property;
    public $memorandumPath;
    public $recipientType; // 'seller' or 'buyer'

    /**
     * Create a new message instance.
     */
    public function __construct(Offer $offer, Property $property, string $memorandumPath, string $recipientType = 'seller')
    {
        $this->offer = $offer;
        $this->property = $property;
        $this->memorandumPath = $memorandumPath;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $defaultSubject = 'Memorandum of Sale - ' . $this->property->address;

        /** @var EmailTemplateService $templateService */
        $templateService = app(EmailTemplateService::class);

        $data = [
            'offer' => $this->offer,
            'property' => $this->property,
            'memorandumPath' => $this->memorandumPath,
            'recipientType' => $this->recipientType,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::MEMORANDUM_OF_SALE, $data);

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
            'memorandumPath' => $this->memorandumPath,
            'recipientType' => $this->recipientType,
        ];

        $template = $templateService->getTemplateForAction(EmailActions::MEMORANDUM_OF_SALE, $data);

        if ($template && $template->template_type === 'override') {
            return new Content(
                htmlString: $templateService->renderTemplate($template, $data),
            );
        }

        return new Content(
            view: 'emails.memorandum-of-sale',
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
        // Determine storage disk (S3 if configured, otherwise public)
        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
        
        if ($this->memorandumPath && Storage::disk($disk)->exists($this->memorandumPath)) {
            return [
                Attachment::fromStorageDisk($disk, $this->memorandumPath)
                    ->as('memorandum-of-sale.pdf'),
            ];
        }

        return [];
    }
}
