<?php

namespace App\Services;

use App\Models\Offer;
use App\Models\Property;
use App\Models\SalesProgression;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MemorandumOfSaleService
{
    /**
     * Generate and save Memorandum of Sale document.
     *
     * @param Offer $offer
     * @param Property $property
     * @param SalesProgression $salesProgression
     * @return string  Path to saved memorandum
     */
    public function generateAndSave(Offer $offer, Property $property, SalesProgression $salesProgression): string
    {
        try {
            // Generate HTML content for Memorandum of Sale
            $content = $this->generateContent($offer, $property, $salesProgression);

            // Save as HTML (can be converted to PDF later with a library like DomPDF or wkhtmltopdf)
            $fileName = 'memorandum-of-sale-' . $property->id . '-' . $offer->id . '-' . time() . '.html';
            $directory = 'memorandums-of-sale/' . $property->id;
            $filePath = $directory . '/' . $fileName;

            // Determine storage disk (S3 if configured, otherwise public)
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

            // Ensure directory exists (only for local storage, S3 doesn't need directories)
            if ($disk !== 's3') {
                Storage::disk($disk)->makeDirectory($directory);
            }

            // Save memorandum
            Storage::disk($disk)->put($filePath, $content);

            // Create PropertyDocument record
            \App\Models\PropertyDocument::create([
                'property_id' => $property->id,
                'document_type' => 'other',
                'file_path' => $filePath,
                'uploaded_at' => now(),
            ]);

            Log::info('Memorandum of Sale generated successfully for property ID: ' . $property->id);

            return $filePath;

        } catch (\Exception $e) {
            Log::error('Memorandum of Sale generation error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate Memorandum of Sale content (HTML).
     *
     * @param Offer $offer
     * @param Property $property
     * @param SalesProgression $salesProgression
     * @return string
     */
    protected function generateContent(Offer $offer, Property $property, SalesProgression $salesProgression): string
    {
        $seller = $property->seller;
        $buyer = $offer->buyer;

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Memorandum of Sale - ' . e($property->address) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #333; line-height: 1.6; }
        .header { background: #0F0F0F; color: #2CB8B4; padding: 20px; text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #2CB8B4; font-size: 28px; }
        .section { margin: 25px 0; padding: 20px; background: #F9F9F9; }
        .section h2 { color: #2CB8B4; margin-top: 0; border-bottom: 2px solid #2CB8B4; padding-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px; }
        .info-item { padding: 10px; background: white; border-radius: 4px; }
        .info-label { font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; }
        .info-value { font-size: 16px; color: #333; margin-top: 5px; }
        .signature-section { margin-top: 40px; padding: 20px; border-top: 2px solid #ddd; }
        .signature-box { display: inline-block; width: 300px; margin: 20px 20px 0 0; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th, table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        table th { background: #2CB8B4; color: white; }
        .total-row { font-weight: 600; background: #E8F4F3; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MEMORANDUM OF SALE</h1>
    </div>

    <div class="section">
        <h2>Property Details</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Property Address</div>
                <div class="info-value">' . e($property->address) . '</div>
            </div>
            <div class="info-item">
                <div class="info-label">Postcode</div>
                <div class="info-value">' . e($property->postcode ?? 'N/A') . '</div>
            </div>
            <div class="info-item">
                <div class="info-label">Property Type</div>
                <div class="info-value">' . ucfirst(str_replace('_', ' ', $property->property_type ?? 'N/A')) . '</div>
            </div>
            <div class="info-item">
                <div class="info-label">Bedrooms</div>
                <div class="info-value">' . ($property->bedrooms ?? 'N/A') . '</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Seller Information</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Seller Name</div>
                <div class="info-value">' . e($seller->name) . '</div>
            </div>
            <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value">' . e($seller->email) . '</div>
            </div>
            ' . ($seller->phone ? '<div class="info-item"><div class="info-label">Phone</div><div class="info-value">' . e($seller->phone) . '</div></div>' : '') . '
        </div>
        ' . ($property->solicitor_name ? '
        <div style="margin-top: 15px; padding: 15px; background: white; border-radius: 4px;">
            <div style="font-weight: 600; color: #2CB8B4; margin-bottom: 10px;">Solicitor Details</div>
            <div><strong>Name:</strong> ' . e($property->solicitor_name) . '</div>
            ' . ($property->solicitor_firm ? '<div><strong>Firm:</strong> ' . e($property->solicitor_firm) . '</div>' : '') . '
            ' . ($property->solicitor_email ? '<div><strong>Email:</strong> ' . e($property->solicitor_email) . '</div>' : '') . '
            ' . ($property->solicitor_phone ? '<div><strong>Phone:</strong> ' . e($property->solicitor_phone) . '</div>' : '') . '
        </div>' : '') . '
    </div>

    <div class="section">
        <h2>Buyer Information</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Buyer Name</div>
                <div class="info-value">' . e($buyer->name) . '</div>
            </div>
            <div class="info-item">
                <div class="info-label">Email</div>
                <div class="info-value">' . e($buyer->email) . '</div>
            </div>
            ' . ($buyer->phone ? '<div class="info-item"><div class="info-label">Phone</div><div class="info-value">' . e($buyer->phone) . '</div></div>' : '') . '
        </div>
    </div>

    <div class="section">
        <h2>Sale Details</h2>
        <table>
            <tr>
                <th>Item</th>
                <th>Details</th>
            </tr>
            <tr>
                <td><strong>Agreed Sale Price</strong></td>
                <td>£' . number_format($offer->offer_amount, 2) . '</td>
            </tr>
            ' . ($offer->deposit_amount ? '<tr><td><strong>Deposit Amount</strong></td><td>£' . number_format($offer->deposit_amount, 2) . '</td></tr>' : '') . '
            <tr>
                <td><strong>Funding Type</strong></td>
                <td>' . ucfirst(str_replace('_', ' ', $offer->funding_type ?? 'Not specified')) . '</td>
            </tr>
            <tr>
                <td><strong>Offer Date</strong></td>
                <td>' . $offer->created_at->format('l, F j, Y') . '</td>
            </tr>
            <tr>
                <td><strong>Accepted Date</strong></td>
                <td>' . Carbon::now()->format('l, F j, Y') . '</td>
            </tr>
            ' . ($offer->conditions ? '<tr><td><strong>Conditions</strong></td><td>' . nl2br(e($offer->conditions)) . '</td></tr>' : '') . '
        </table>
    </div>

    <div class="section">
        <h2>Important Notes</h2>
        <p>This Memorandum of Sale confirms that the seller and buyer have agreed on the sale of the above property at the agreed price.</p>
        <p><strong>This document does not constitute a legally binding contract.</strong> A formal contract will need to be drawn up by the solicitors.</p>
        <p>Both parties should now instruct their solicitors to proceed with the conveyancing process.</p>
    </div>

    <div class="signature-section">
        <div style="font-weight: 600; margin-bottom: 15px;">Issued by Abodeology®</div>
        <div style="font-size: 13px; color: #666;">
            <div>Date: ' . Carbon::now()->format('l, F j, Y g:i A') . '</div>
            <div style="margin-top: 10px;">This Memorandum of Sale has been automatically generated by the Abodeology platform.</div>
        </div>
    </div>

    <div class="footer">
        <p>© ' . date('Y') . ' Abodeology®. All rights reserved.</p>
        <p>This Memorandum of Sale is issued for information purposes and should be shared with both parties\' solicitors.</p>
    </div>
</body>
</html>';

        return $html;
    }
}
