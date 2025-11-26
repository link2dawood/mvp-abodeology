<?php

namespace App\Mail;

use App\Models\Offer;
use App\Models\Property;
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
        return new Envelope(
            subject: 'Memorandum of Sale - ' . $this->property->address,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.memorandum-of-sale',
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
