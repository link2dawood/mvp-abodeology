<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyInstruction;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class TermsAndConditionsPDFService
{
    /**
     * Generate HTML preview for property.
     *
     * @param Property $property
     * @param PropertyInstruction|object $instruction
     * @return string HTML content
     */
    public function generateHTML(Property $property, $instruction): string
    {
        return View::make('seller.terms-and-conditions', [
            'property' => $property,
            'instruction' => $instruction
        ])->render();
    }
    
    /**
     * Generate PDF from HTML using DomPDF.
     *
     * @param Property $property
     * @param PropertyInstruction|object $instruction
     * @return string Path to generated PDF
     */
    public function generatePDFFromHTML(Property $property, $instruction): string
    {
        try {
            // Generate HTML from Blade template
            $html = $this->generateHTML($property, $instruction);
            
            // Configure DomPDF options
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Helvetica');
            $options->set('chroot', public_path());
            
            // Create DomPDF instance
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            // Get PDF content
            $pdfContent = $dompdf->output();
            
            // Save the PDF
            $fileName = 'terms-and-conditions-' . $property->id . '-' . time() . '.pdf';
            $directory = 'terms-and-conditions';
            $filePath = $directory . '/' . $fileName;
            
            // Determine storage disk
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
            
            // Ensure directory exists (only for local storage)
            if ($disk !== 's3') {
                Storage::disk($disk)->makeDirectory($directory);
            }
            
            // Save PDF
            Storage::disk($disk)->put($filePath, $pdfContent);
            
            Log::info('Terms and Conditions PDF generated from HTML for property ID: ' . $property->id);
            
            return $filePath;
            
        } catch (\Exception $e) {
            Log::error('Terms and Conditions PDF generation from HTML error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * OLD METHOD - Generate PDF from HTML using FPDI (kept for reference).
     * This method is no longer used but kept in case needed.
     *
     * @param Property $property
     * @param PropertyInstruction|object $instruction
     * @return string Path to generated PDF
     */
    public function generatePDFFromHTML_OLD(Property $property, $instruction): string
    {
        try {
            $pdf = new Fpdi();
            $pdf->SetAutoPageBreak(false);
            
            // PAGE 1
            $pdf->AddPage();
            $this->addLogo($pdf);
            
            // Title
            $pdf->SetFont('Helvetica', 'B', 16);
            $pdf->SetXY(0, 70);
            $pdf->Cell(0, 10, 'ESTATE AGENCY AGREEMENT', 0, 1, 'C');
            
            $y = 95;
            $pdf->SetFont('Helvetica', '', 11);
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Agent: Abodeology, acting through the authorised branch/representative below', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Branch/Office: Main', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Branch Address: 71-75, Shelton Street, Covent Garden, London, WC2H 9JQ', 0, 1);
            $y += 6;
            
            $sellerNames = $instruction->seller_names ?? $property->seller->name ?? '';
            $pdf->SetXY(15, $y);
            $pdf->Cell(50, 6, 'Seller(s):', 0, 0);
            $pdf->Line(65, $y + 5, 195, $y + 5);
            if ($sellerNames) {
                $pdf->SetXY(67, $y);
                $pdf->Cell(0, 6, $sellerNames, 0, 1);
            }
            $y += 6;
            
            $propertyAddress = $instruction->property_address ?? $property->address ?? '';
            $pdf->SetXY(15, $y);
            $pdf->Cell(50, 6, 'Property Address:', 0, 0);
            $pdf->Line(65, $y + 5, 195, $y + 5);
            if ($propertyAddress) {
                $pdf->SetXY(67, $y);
                $pdf->MultiCell(128, 6, $propertyAddress, 0, 'L');
                $y += 10;
            } else {
                $y += 6;
            }
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(50, 6, 'Initial Asking Price: £', 0, 0);
            $pdf->Line(65, $y + 5, 120, $y + 5);
            if ($property->asking_price) {
                $pdf->SetXY(67, $y);
                $pdf->Cell(0, 6, number_format($property->asking_price, 0), 0, 1);
            }
            $y += 6;
            
            $pdf->SetFont('Helvetica', '', 9);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 5, '(Marketing figure only — not a valuation or guarantee.)', 0, 1);
            $y += 6;
            
            $feePercentage = $instruction->fee_percentage ?? 1.5;
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(50, 6, 'Commission Fee:', 0, 0);
            $pdf->Line(65, $y + 5, 85, $y + 5);
            $pdf->SetXY(67, $y);
            $pdf->Cell(0, 6, number_format($feePercentage, 2) . ' % of the final sale price (plus VAT)', 0, 1);
            $y += 6;
            
            $pdf->SetFont('Helvetica', '', 9);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 5, '(Final fee = Sold Price x % Fee + VAT)', 0, 1);
            $y += 6;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Fee Payable Upon Completion: Yes', 0, 1);
            $y += 6;
            
            $pdf->SetFont('Helvetica', '', 9);
            $pdf->SetXY(15, $y);
            $pdf->MultiCell(180, 5, 'Fees are due upon legal completion and payable to Abodeology HQ or its nominated collection entity, regardless of which branch handled the sale.', 0, 'L');
            $y += 12;
            
            // Important Notice
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'IMPORTANT NOTICE', 0, 1);
            $y += 6;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'This is a legally binding contract. Independent legal advice is recommended.', 0, 1);
            $y += 8;
            
            // Compliance list
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Abodeology complies with the:', 0, 1);
            $y += 6;
            
            $compliance = ['a) Estate Agents Act 1979', 'b) Consumer Protection Regulations', 'c) UK GDPR', 'd) AML Regulations', 'e) Equality Act', 'f) Trading Standards Material Information Standards', 'g) Consumer Contracts Regulations.'];
            foreach ($compliance as $item) {
                $pdf->SetXY(15, $y);
                $pdf->Cell(0, 6, $item, 0, 1);
                $y += 6;
            }
            
            $y += 4;
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'The Seller confirms all information provided about the property is accurate, complete and not misleading.', 0, 1);
            
            // PAGE 2
            $y = $this->checkPageBreak($pdf, $y, 50);
            if ($y == 30) {
                $pdf->AddPage();
                $this->addLogo($pdf);
            }
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '1 Definitions', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->MultiCell(180, 6, '1.1 "Abodeology", "we", "us" or "the Agent" means Abodeology Ltd, a company registered in England and Wales under company number 14506067. Its registered office is located at 71-75 Shelton Street, Covent Garden, London, WC2H 9JQ.', 0, 'L');
            $y += 12;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, '1.2 "Seller", "you" or "Client" means the person(s) named as the Seller in this Agreement.', 0, 1);
            $y += 10;
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '2 TYPE OF AGENCY (select one)', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 7, '2.1 Sole Agency', 0, 1);
            $y += 7;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'The Seller will pay the commission fee if:', 0, 1);
            $y += 6;
            
            $pdf->SetXY(20, $y);
            $pdf->Cell(0, 6, 'a) Abodeology introduces a buyer during the sole agency period who later completes; or', 0, 1);
            $y += 6;
            
            $pdf->SetXY(20, $y);
            $pdf->Cell(0, 6, 'b) Any buyer introduced by Abodeology during the period purchases within 6 months of termination; or', 0, 1);
            $y += 6;
            
            $pdf->SetXY(20, $y);
            $pdf->Cell(0, 6, 'c) Another agent introduces a buyer during the sole agency period.', 0, 1);
            $y += 6;
            
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Termination: 5 days\' written notice.', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->MultiCell(180, 6, 'Double Commission Warning: Instructing multiple agents during this period may result in liability to pay more than one fee.', 0, 'L');
            $y += 10;
            
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 7, '2.2 Sole Selling Rights', 0, 1);
            $y += 7;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'The Seller will pay the fee if contracts are exchanged:', 0, 1);
            $y += 6;
            
            $pdf->SetXY(20, $y);
            $pdf->Cell(0, 6, 'a) with any buyer during the period, regardless of who introduced them; or', 0, 1);
            $y += 6;
            
            $pdf->SetXY(20, $y);
            $pdf->Cell(0, 6, 'b) after the period, with a buyer introduced or negotiated with during the period.', 0, 1);
            $y += 6;
            
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Termination: 5 days\' written notice.', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 7, '2.3 Multiple Agency', 0, 1);
            $y += 7;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(20, $y);
            $pdf->Cell(0, 6, 'a) Only the agent who introduces the successful buyer earns the fee.', 0, 1);
            $y += 6;
            
            $pdf->SetXY(20, $y);
            $pdf->Cell(0, 6, 'b) Abodeology may charge a higher percentage for multiple agency.', 0, 1);
            $y += 6;
            
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Termination: 5 days\' written notice.', 0, 1);
            $y += 10;
            
            // PAGE 3
            $y = $this->checkPageBreak($pdf, $y, 50);
            if ($y == 30) {
                $pdf->AddPage();
                $this->addLogo($pdf);
            }
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '3 MULTI-BRANCH & SUB-AGENCY NETWORK', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'The Seller acknowledges that Abodeology may market the property through:', 0, 1);
            $y += 6;
            
            $networkItems = ['a) Any Abodeology branch', 'b) Partner or licensee offices', 'c) Self-employed agents', 'd) Sub-agents or network affiliates'];
            foreach ($networkItems as $item) {
                $pdf->SetXY(15, $y);
                $pdf->Cell(0, 6, $item, 0, 1);
                $y += 6;
            }
            
            $pdf->SetXY(15, $y);
            $pdf->MultiCell(180, 6, 'All such parties act as authorised representatives of Abodeology. The contract remains strictly between the Seller and Abodeology.', 0, 'L');
            $y += 12;
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '4 FOR SALE BOARDS', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->MultiCell(180, 6, 'The Seller authorises Abodeology to erect and maintain a compliant For Sale board. Abodeology is not liable for third-party interference or damage.', 0, 'L');
            $y += 10;
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '5 FEES & PAYMENT', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 7, '5.1 Fee Structure', 0, 1);
            $y += 7;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(60, 6, 'Percentage fee only:', 0, 0);
            $pdf->Line(75, $y + 5, 95, $y + 5);
            $pdf->SetXY(77, $y);
            $pdf->Cell(0, 6, number_format($feePercentage, 2) . ' % of final sale price achieved (plus VAT).', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 7, '5.2 Payment', 0, 1);
            $y += 7;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Fees become due upon completion. All Sellers are jointly and severally liable.', 0, 1);
            $y += 6;
            
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, '4c. Ready, Willing & Able Buyer (CCR)', 0, 1);
            $y += 6;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->MultiCell(180, 6, 'If the Seller requests immediate marketing within the 14-day cooling-off period, they may become liable for the full fee should Abodeology introduce a buyer who is ready, willing and able to proceed.', 0, 'L');
            $y += 12;
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '6 ADDITIONAL COSTS', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->MultiCell(180, 6, 'Abodeology will not charge additional costs unless agreed in writing in advance. Optional services such as EPCs, premium marketing or third-party packages will be itemised where applicable.', 0, 'L');
            $y += 12;
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '7 OFFERS', 0, 1);
            $y += 8;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Abodeology will:', 0, 1);
            $y += 6;
            
            $offerItems = ['• record all offers', '• communicate all offers promptly to the Seller', '• provide an offer log on request', '• verify the buyer\'s ability to proceed where possible'];
            foreach ($offerItems as $item) {
                $pdf->SetXY(15, $y);
                $pdf->Cell(0, 6, $item, 0, 1);
                $y += 6;
            }
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'No discrimination will occur under the Equality Act 2010.', 0, 1);
            $y += 10;
            
            // PAGE 4
            $y = $this->checkPageBreak($pdf, $y, 50);
            if ($y == 30) {
                $pdf->AddPage();
                $this->addLogo($pdf);
            }
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '8 KEYS & ACCESS', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Where Abodeology holds keys:', 0, 1);
            $y += 6;
            
            $keysItems = ['• Viewings will be accompanied unless agreed otherwise', '• Keys will be securely coded', '• Surveyor/valuer access requires Seller consent', '• Any Abodeology branch/partner agent may conduct viewings'];
            foreach ($keysItems as $item) {
                $pdf->SetXY(15, $y);
                $pdf->Cell(0, 6, $item, 0, 1);
                $y += 6;
            }
            
            $y += 4;
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '9 DISCLOSURE OF PERSONAL INTEREST', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Required by the Estate Agents Act 1979.', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Does any Abodeology representative have a personal interest in this property?', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Yes / No', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(50, 6, 'If yes, provide details:', 0, 0);
            $pdf->Line(65, $y + 5, 195, $y + 5);
            $y += 10;
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '10 ACCURACY & MATERIAL INFORMATION', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'The Seller agrees to:', 0, 1);
            $y += 6;
            
            $pdf->SetXY(20, $y);
            $pdf->Cell(0, 6, 'a) provide full, accurate and non-misleading information.', 0, 1);
            $y += 6;
            
            $pdf->SetXY(20, $y);
            $pdf->Cell(0, 6, 'b) disclose all material information, including:', 0, 1);
            $y += 6;
            
            $pdf->SetXY(35, $y);
            $pdf->Cell(0, 6, 'structural defects, disputes, notices, rights of way, planning issues,', 0, 1);
            $y += 6;
            
            $pdf->SetXY(35, $y);
            $pdf->Cell(0, 6, 'lease details, tenure, restrictions, title irregularities, unauthorised works,', 0, 1);
            $y += 6;
            
            $pdf->SetXY(35, $y);
            $pdf->Cell(0, 6, 'etc.', 0, 1);
            $y += 6;
            
            $pdf->SetXY(20, $y);
            $pdf->Cell(0, 6, 'c) notify Abodeology immediately of any changes.', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->MultiCell(180, 6, 'Marketing content will be approved by the Seller prior to publication. The Seller is legally responsible for the accuracy of their information.', 0, 'L');
            $y += 12;
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '11 MARKETING MATERIALS', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'The Seller authorises Abodeology to create and use:', 0, 1);
            $y += 6;
            
            $marketingItems = ['• Photography', '• Video tours', '• 360 walkthroughs', '• Drone footage', '• Floorplans', '• EPCs', '• Social media and online advertising', '• Portal listings', '• Print or digital brochures'];
            foreach ($marketingItems as $item) {
                $pdf->SetXY(15, $y);
                $pdf->Cell(0, 6, $item, 0, 1);
                $y += 6;
            }
            
            $y += 4;
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'All materials remain the intellectual property of Abodeology or its suppliers.', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Abodeology may retain archived materials for compliance, training or auditing.', 0, 1);
            $y += 10;
            
            // PAGE 5
            $y = $this->checkPageBreak($pdf, $y, 50);
            if ($y == 30) {
                $pdf->AddPage();
                $this->addLogo($pdf);
            }
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '12 ABODEOLOGY® HOMECHECK - SERVICE DEFINITION', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'The "Abodeology HomeCheck" is a presentation-focused data-collection service only.', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->MultiCell(180, 6, 'Representatives collect basic visual information using third-party software, such as photographs and simple moisture readings.', 0, 'L');
            $y += 10;
            
            $pdf->SetXY(15, $y);
            $pdf->MultiCell(180, 6, 'The data is analysed by an AI system and reviewed by an independent third-party assessment provider.', 0, 'L');
            $y += 10;
            
            $pdf->SetXY(15, $y);
            $pdf->MultiCell(180, 6, 'The report\'s purpose is designed solely to give homeowners ideas on how to present their home for sale and to support general home preparation.', 0, 'L');
            $y += 10;
            
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'The HomeCheck report:', 0, 1);
            $y += 6;
            
            $pdf->SetFont('Helvetica', '', 11);
            $homecheckItems = ['a) is not a survey', 'b) is not a valuation', 'c) is not a structural or condition report', 'd) must not be used for purchasing decisions'];
            foreach ($homecheckItems as $item) {
                $pdf->SetXY(20, $y);
                $pdf->Cell(0, 6, $item, 0, 1);
                $y += 6;
            }
            
            $y += 4;
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'All assessments, observations and conclusions in the report are produced by the third-party provider.', 0, 1);
            $y += 6;
            
            $pdf->SetFont('Helvetica', 'B', 11);
            $pdf->SetXY(15, $y);
            $pdf->MultiCell(180, 6, 'Abodeology does not inspect, analyse, assess or verify the condition of the property and accepts no liability for the content of the report.', 0, 'L');
            $y += 12;
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '13 DATA PROTECTION (GDPR)', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Abodeology processes data for:', 0, 1);
            $y += 6;
            
            $dataItems = ['• contract fulfilment', '• AML checks', '• communication', '• compliance', '• marketing (where permitted)'];
            foreach ($dataItems as $item) {
                $pdf->SetXY(15, $y);
                $pdf->Cell(0, 6, $item, 0, 1);
                $y += 6;
            }
            
            $y += 4;
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Data may be shared with authorised branches/partners and third-party processors where necessary.', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'The Seller may request access or correction of their data.', 0, 1);
            $y += 10;
            
            // PAGE 6
            $y = $this->checkPageBreak($pdf, $y, 50);
            if ($y == 30) {
                $pdf->AddPage();
                $this->addLogo($pdf);
            }
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '14 ANTI-MONEY LAUNDERING', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Before marketing begins, Abodeology must:', 0, 1);
            $y += 6;
            
            $amlItems = ['• verify the identity of all sellers', '• verify beneficial ownership', '• obtain source of funds where required'];
            foreach ($amlItems as $item) {
                $pdf->SetXY(15, $y);
                $pdf->Cell(0, 6, $item, 0, 1);
                $y += 6;
            }
            
            $y += 4;
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Failure to provide documents delays marketing.', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Abodeology may file a Suspicious Activity Report without informing the Seller.', 0, 1);
            $y += 10;
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '15 CONSUMER CONTRACTS REGULATIONS (CCR)', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Where signed off-premises or electronically, the Seller has a 14-day cancellation right.', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->MultiCell(180, 6, 'If the Seller permits immediate marketing, they acknowledge potential fee liability if Abodeology introduces a ready, willing and able buyer during the cooling-off period.', 0, 'L');
            $y += 12;
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '16 MINIMUM AGREEMENT TERM', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(50, 6, 'Minimum agency term:', 0, 0);
            $pdf->Line(65, $y + 5, 95, $y + 5);
            $pdf->SetXY(67, $y);
            $pdf->Cell(0, 6, ' weeks', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'After this period, the agreement continues on a rolling basis unless terminated with 5 days\' written notice.', 0, 1);
            $y += 10;
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '17 LIMITATION OF LIABILITY', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Abodeology is not liable for:', 0, 1);
            $y += 6;
            
            $liabilityItems = ['a) financial loss', 'b) market changes', 'c) failed sales', 'd) third-party delays', 'e) indirect or consequential losses'];
            foreach ($liabilityItems as $item) {
                $pdf->SetXY(20, $y);
                $pdf->Cell(0, 6, $item, 0, 1);
                $y += 6;
            }
            
            $y += 4;
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Nothing limits liability for fraud or negligence causing personal injury.', 0, 1);
            $y += 10;
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '18 COMPLAINTS & REDRESS', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Abodeology is a member of: The Property Ombudsman (TPOS)', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'A written complaints procedure is available on request.', 0, 1);
            $y += 10;
            
            // PAGE 7
            $y = $this->checkPageBreak($pdf, $y, 50);
            if ($y == 30) {
                $pdf->AddPage();
                $this->addLogo($pdf);
            }
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '19 GOVERNING LAW', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'This Agreement is governed by the laws of England and Wales.', 0, 1);
            $y += 6;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 6, 'Electronic signatures are valid.', 0, 1);
            $y += 12;
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y);
            $pdf->Cell(0, 8, '20 SIGNATURES', 0, 1);
            $y += 8;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(15, $y);
            $pdf->Cell(70, 6, 'Agent (Branch/Representative):', 0, 0);
            $pdf->Line(85, $y + 5, 195, $y + 5);
            $y += 10;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(70, 6, 'Date:', 0, 0);
            $pdf->Line(85, $y + 5, 120, $y + 5);
            $y += 12;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(70, 6, 'Seller:', 0, 0);
            $pdf->Line(85, $y + 5, 195, $y + 5);
            $y += 10;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(70, 6, 'Date:', 0, 0);
            $pdf->Line(85, $y + 5, 120, $y + 5);
            $y += 12;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(70, 6, 'Seller (if joint):', 0, 0);
            $pdf->Line(85, $y + 5, 195, $y + 5);
            $y += 10;
            
            $pdf->SetXY(15, $y);
            $pdf->Cell(70, 6, 'Date:', 0, 0);
            $pdf->Line(85, $y + 5, 120, $y + 5);
            $y += 10;
            
            // PAGE 8
            $y = $this->checkPageBreak($pdf, $y, 50);
            if ($y == 30) {
                $pdf->AddPage();
                $this->addLogo($pdf);
            }
            
            // Draw border for cancellation form
            $pdf->Rect(15, $y, 180, 200);
            
            $pdf->SetFont('Helvetica', 'B', 12);
            $pdf->SetXY(15, $y + 5);
            $pdf->Cell(0, 8, 'STATUTORY CANCELLATION FORM (CCR)', 0, 1);
            $y += 15;
            
            $pdf->SetFont('Helvetica', '', 11);
            $pdf->SetXY(20, $y);
            $pdf->Cell(20, 6, 'To:', 0, 0);
            $pdf->Line(40, $y + 5, 190, $y + 5);
            $y += 15;
            
            $pdf->SetXY(20, $y);
            $pdf->Cell(0, 6, 'I/We hereby cancel the Estate Agency Agreement for:', 0, 1);
            $y += 12;
            
            $pdf->SetXY(20, $y);
            $pdf->Cell(50, 6, 'Property Address:', 0, 0);
            $pdf->Line(70, $y + 5, 190, $y + 5);
            $y += 20;
            
            $pdf->SetXY(20, $y);
            $pdf->Cell(30, 6, 'Signed:', 0, 0);
            $pdf->Line(50, $y + 5, 190, $y + 5);
            $y += 12;
            
            $pdf->SetXY(20, $y);
            $pdf->Cell(30, 6, 'Name:', 0, 0);
            $pdf->Line(50, $y + 5, 190, $y + 5);
            $y += 12;
            
            $pdf->SetXY(20, $y);
            $pdf->Cell(30, 6, 'Date:', 0, 0);
            $pdf->Line(50, $y + 5, 120, $y + 5);
            
            // Save the PDF
            $fileName = 'terms-and-conditions-' . $property->id . '-' . time() . '.pdf';
            $directory = 'terms-and-conditions';
            $filePath = $directory . '/' . $fileName;
            
            // Determine storage disk
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
            
            // Ensure directory exists (only for local storage)
            if ($disk !== 's3') {
                Storage::disk($disk)->makeDirectory($directory);
            }
            
            // Output PDF to string
            $pdfContent = $pdf->Output('', 'S');
            
            // Save PDF
            Storage::disk($disk)->put($filePath, $pdfContent);
            
            Log::info('Terms and Conditions PDF generated for property ID: ' . $property->id);
            
            return $filePath;
            
        } catch (\Exception $e) {
            Log::error('Terms and Conditions PDF generation error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Add logo to PDF page.
     *
     * @param Fpdi $pdf
     */
    protected function addLogo(Fpdi $pdf): void
    {
        $pdf->SetFont('Helvetica', '', 14);
        $pdf->SetTextColor(44, 184, 180);
        $pdf->SetXY(175, 15);
        $pdf->Cell(0, 8, 'abodeology®', 0, 1, 'R');
        $pdf->SetTextColor(0, 0, 0);
    }
    
    /**
     * Check if new page is needed and add if necessary.
     *
     * @param Fpdi $pdf
     * @param float $y Current Y position
     * @param float $requiredHeight Required height for next content
     * @return float New Y position
     */
    protected function checkPageBreak(Fpdi $pdf, float $y, float $requiredHeight = 20): float
    {
        if ($y + $requiredHeight > 280) {
            $pdf->AddPage();
            $this->addLogo($pdf);
            return 30;
        }
        return $y;
    }
    /**
     * Generate filled PDF with property data and signature fields.
     *
     * @param Property $property
     * @param PropertyInstruction|object $instruction
     * @return string Path to generated PDF
     */
    public function generateForProperty(Property $property, $instruction): string
    {
        try {
            $templatePath = public_path('terms-and-conditions.pdf');
            
            if (!file_exists($templatePath)) {
                throw new \Exception('PDF template not found at: ' . $templatePath);
            }

            // Initialize FPDI
            $pdf = new Fpdi();
            
            // Import the template PDF
            $pageCount = $pdf->setSourceFile($templatePath);
            
            // Add all pages from template
            for ($i = 1; $i <= $pageCount; $i++) {
                $pdf->AddPage();
                $tplId = $pdf->importPage($i);
                $pdf->useTemplate($tplId);
                
                // Fill fields on each page based on page number
                $this->fillPageFields($pdf, $i, $property, $instruction);
            }
            
            // Add signature page at the end
            $this->addSignaturePage($pdf, $property, $instruction);
            
            // Save the PDF
            $fileName = 'terms-and-conditions-' . $property->id . '-' . time() . '.pdf';
            $directory = 'terms-and-conditions';
            $filePath = $directory . '/' . $fileName;
            
            // Determine storage disk
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
            
            // Ensure directory exists (only for local storage)
            if ($disk !== 's3') {
                Storage::disk($disk)->makeDirectory($directory);
            }
            
            // Output PDF to string
            $pdfContent = $pdf->Output('', 'S');
            
            // Save PDF
            Storage::disk($disk)->put($filePath, $pdfContent);
            
            Log::info('Terms and Conditions PDF generated for property ID: ' . $property->id);
            
            return $filePath;
            
        } catch (\Exception $e) {
            Log::error('Terms and Conditions PDF generation error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Fill fields on a specific page.
     *
     * @param Fpdi $pdf
     * @param int $pageNumber
     * @param Property $property
     * @param PropertyInstruction|object $instruction
     */
    protected function fillPageFields(Fpdi $pdf, int $pageNumber, Property $property, $instruction): void
    {
        $seller = $property->seller;
        
        // Set font for filling
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        
        // Page 2 typically contains the main agreement details
        // Note: Coordinates need to be adjusted based on your actual PDF template layout
        if ($pageNumber == 2) {
            // Seller name(s) - adjust X, Y coordinates to match your PDF
            $sellerNames = $instruction->seller_names ?? $seller->name ?? '';
            if ($sellerNames) {
                $pdf->SetXY(50, 80); // Adjust these coordinates
                $pdf->Cell(0, 10, $sellerNames, 0, 1);
            }
            
            // Property address
            $propertyAddress = $instruction->property_address ?? $property->address ?? '';
            if ($propertyAddress) {
                $pdf->SetXY(50, 95); // Adjust these coordinates
                // Handle long addresses by wrapping
                $pdf->MultiCell(150, 5, $propertyAddress, 0, 'L');
            }
            
            // Initial asking price
            if ($property->asking_price) {
                $askingPrice = '£' . number_format($property->asking_price, 0);
                $pdf->SetXY(50, 110); // Adjust these coordinates
                $pdf->Cell(0, 10, $askingPrice, 0, 1);
            }
            
            // Commission fee
            $feePercentage = $instruction->fee_percentage ?? 1.5;
            $pdf->SetXY(50, 125); // Adjust these coordinates
            $pdf->Cell(0, 10, number_format($feePercentage, 2) . '%', 0, 1);
        }
        
        // Add more page-specific field filling as needed
        // You can add conditions for other pages here
    }
    
    /**
     * Add signature page at the end.
     *
     * @param Fpdi $pdf
     * @param Property $property
     * @param PropertyInstruction|object $instruction
     */
    protected function addSignaturePage(Fpdi $pdf, Property $property, $instruction): void
    {
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('Helvetica', 'B', 14);
        $pdf->SetTextColor(0, 0, 0);
        
        // Title
        $pdf->SetXY(20, 30);
        $pdf->Cell(0, 10, 'DIGITAL SIGNATURES', 0, 1, 'C');
        
        // Set smaller font for content
        $pdf->SetFont('Helvetica', '', 12);
        
        // Seller 1 signature section
        $pdf->SetXY(20, 60);
        $pdf->Cell(0, 10, 'Seller 1:', 0, 1);
        
        $pdf->SetXY(20, 75);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(0, 10, 'Full Name:', 0, 1);
        $pdf->Line(20, 85, 100, 85); // Signature line
        
        $pdf->SetXY(20, 95);
        $pdf->Cell(0, 10, 'Digital Signature (type your full name):', 0, 1);
        $pdf->Line(20, 105, 100, 105); // Signature line
        
        $pdf->SetXY(20, 115);
        $pdf->Cell(0, 10, 'Date:', 0, 1);
        $pdf->Line(20, 125, 100, 125); // Date line
        
        // Seller 2 signature section (optional)
        $pdf->SetXY(20, 145);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 10, 'Seller 2 (if joint):', 0, 1);
        
        $pdf->SetXY(20, 160);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(0, 10, 'Full Name:', 0, 1);
        $pdf->Line(20, 170, 100, 170); // Signature line
        
        $pdf->SetXY(20, 180);
        $pdf->Cell(0, 10, 'Digital Signature (type your full name):', 0, 1);
        $pdf->Line(20, 190, 100, 190); // Signature line
        
        $pdf->SetXY(20, 200);
        $pdf->Cell(0, 10, 'Date:', 0, 1);
        $pdf->Line(20, 210, 100, 210); // Date line
        
        // Agent signature section
        $pdf->SetXY(120, 60);
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 10, 'Agent (Branch/Representative):', 0, 1);
        
        $pdf->SetXY(120, 75);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->Cell(0, 10, 'Name:', 0, 1);
        $pdf->Line(120, 85, 200, 85); // Signature line
        
        $pdf->SetXY(120, 95);
        $pdf->Cell(0, 10, 'Date:', 0, 1);
        $pdf->Line(120, 105, 200, 105); // Date line
    }
    
    /**
     * Get the PDF URL for a property.
     *
     * @param Property $property
     * @param PropertyInstruction|object $instruction
     * @return string
     */
    public function getPDFUrl(Property $property, $instruction): string
    {
        // Check if PDF already exists
        $directory = 'terms-and-conditions';
        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
        
        // Look for existing PDF for this property
        $existingFiles = Storage::disk($disk)->files($directory);
        $propertyFile = null;
        
        foreach ($existingFiles as $file) {
            if (strpos($file, 'terms-and-conditions-' . $property->id . '-') === strlen($directory) + 1) {
                $propertyFile = $file;
                break;
            }
        }
        
        // If exists, return URL
        if ($propertyFile) {
            if ($disk === 's3') {
                return Storage::disk($disk)->temporaryUrl($propertyFile, now()->addHours(1));
            }
            // For public disk, use asset() to generate correct URL
            return asset('storage/' . $propertyFile);
        }
        
        // Generate new PDF
        $filePath = $this->generateForProperty($property, $instruction);
        
        if ($disk === 's3') {
            return Storage::disk($disk)->temporaryUrl($filePath, now()->addHours(1));
        }
        
        // For public disk, use asset() to generate correct URL
        return asset('storage/' . $filePath);
    }
}
