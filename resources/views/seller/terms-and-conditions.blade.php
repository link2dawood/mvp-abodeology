<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions - {{ $property->address ?? 'Property' }}</title>
    <style>
        @page {
            margin: 20mm 15mm;
            size: A4 portrait;
        }
        
        @page:first {
            margin: 0;
        }
        
        @page:not(:first) {
            @top-right {
                content: "abodeology®";
                font-size: 14pt;
                color: #2CB8B4;
                font-family: "Times New Roman", Times, serif;
            }
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
            padding: 0;
            margin: 0;
        }
        
        .header-logo {
            text-align: right;
            font-size: 14pt;
            color: #2CB8B4;
            font-family: "Times New Roman", Times, serif;
            margin-bottom: 10mm;
        }
        
        h1 {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            margin: 15mm 0 12mm 0;
            color: #000;
            page-break-after: avoid;
        }
        
        h2 {
            font-size: 12pt;
            font-weight: bold;
            margin: 10pt 0 6pt 0;
            color: #000;
            page-break-after: avoid;
        }
        
        h3 {
            font-size: 11pt;
            font-weight: bold;
            margin: 8pt 0 4pt 0;
            color: #000;
            page-break-after: avoid;
        }
        
        p {
            margin: 5pt 0;
            text-align: justify;
            line-height: 1.5;
        }
        
        .agreement-header {
            margin-bottom: 10pt;
        }
        
        .agreement-header p {
            margin: 4pt 0;
            text-align: left;
        }
        
        .field-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200pt;
            margin-left: 5pt;
        }
        
        .field-line.short {
            min-width: 80pt;
        }
        
        .field-line.long {
            min-width: 300pt;
        }
        
        .important-notice {
            font-weight: bold;
            margin: 10pt 0;
            padding: 8pt;
            background: #f0f0f0;
        }
        
        .compliance-list {
            margin: 8pt 0;
        }
        
        .compliance-list p {
            margin: 4pt 0;
            text-align: left;
        }
        
        ul, ol {
            margin: 6pt 0 6pt 20pt;
        }
        
        li {
            margin: 4pt 0;
        }
        
        .section {
            margin: 12pt 0;
            page-break-inside: avoid;
        }
        
        .subsection {
            margin: 6pt 0 6pt 15pt;
        }
        
        .signature-section {
            margin: 20pt 0;
        }
        
        .signature-field {
            margin: 10pt 0;
        }
        
        .signature-field label {
            display: inline-block;
            min-width: 150pt;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 200pt;
            margin-left: 5pt;
        }
        
        .signature-line.short {
            min-width: 80pt;
        }
        
        .cancellation-form {
            margin-top: 20pt;
            border: 1px solid #000;
            padding: 15pt;
            page-break-inside: avoid;
        }
        
        .cancellation-form h2 {
            margin-top: 0;
        }
        
        .blank-line {
            border-bottom: 1px solid #000;
            min-height: 15pt;
            margin: 5pt 0;
        }
        
        .bold {
            font-weight: bold;
        }
        
        .italic {
            font-style: italic;
        }
        
        .small-text {
            font-size: 9pt;
        }
        .container{
            padding:40px;
        }
        
        .cover-page {
            width: 100%;
            height: 275mm;
            background-color: #000;
            color: #fff;
            position: relative;
            page-break-after: always;
            padding: 40px;
            box-sizing: border-box;
            margin: 0;
        }
        
        .cover-logo {
            text-align: right;
            font-size: 24pt;
            color: #2CB8B4;
            font-family: Arial, Helvetica, sans-serif;
            margin-bottom: 0;
            margin-top: 0;
            position: relative;
            z-index: 1;
        }
        
        .cover-title {
            font-size: 24pt;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            color: #fff;
            font-family: Arial, Helvetica, sans-serif;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: calc(100% - 80px);
            padding: 0;
            margin: 0;
        }
        
        .cover-footer {
            font-size: 10pt;
            color: #fff !important;
            font-family: Arial, Helvetica, sans-serif;
            text-align: left;
            position: absolute;
            bottom: 40px;
            left: 40px;
            margin: 0;
            z-index: 1;
        }
    </style>
</head>
<body>
    <!-- Cover Page -->
    <div class="cover-page" >
        <div class="cover-logo" style="margin-right:70px;">abodeology®</div>
        <div class="cover-title">TERMS OF AGREEMENT</div>
        <div class="cover-footer">ABODEOLOGY LIMITED 71-75, Shelton Street, Covent Garden, London, WC2H 9JQ</div>
    </div>
    <div class="container">
    <div class="header-logo">abodeology®</div>
    <h1>TERMS OF AGREEMENT</h1>
    <h2>ESTATE AGENCY AGREEMENT</h2>
    
    <div class="agreement-header">
        <p><strong>Agent:</strong> Abodeology, acting through the authorised branch/representative below</p>
        <p><strong>Branch/Office:</strong> Main</p>
        <p><strong>Branch Address:</strong> 71-75, Shelton Street, Covent Garden, London, WC2H 9JQ</p>
        <p><strong>Seller(s):</strong> <span class="field-line long">{{ $instruction->seller_names ?? $property->seller->name ?? '' }}</span></p>
        <p><strong>Property Address:</strong> <span class="field-line long">{{ $instruction->property_address ?? $property->address ?? '' }}</span></p>
        <p><strong>Initial Asking Price:</strong> £<span class="field-line">@if($property->asking_price){{ number_format($property->asking_price, 0) }}@endif</span></p>
        <p class="small-text">(Marketing figure only — not a valuation or guarantee.)</p>
        <p><strong>Commission Fee:</strong> <span class="field-line short">{{ number_format($instruction->fee_percentage ?? 1.5, 2) }}</span> % of the final sale price (plus VAT)</p>
        <p class="small-text">(Final fee = Sold Price x % Fee + VAT)</p>
        <p><strong>Fee Payable Upon Completion:</strong> Yes</p>
        <p class="small-text">Fees are due upon legal completion and payable to Abodeology HQ or its nominated collection entity, regardless of which branch handled the sale.</p>
    </div>
    
    <div class="">
        <p><strong>IMPORTANT NOTICE</strong></p>
        <p>This is a legally binding contract. Independent legal advice is recommended.</p>
    </div>
    
    <div class="compliance-list">
        <p>Abodeology complies with the:</p>
        <p>a) Estate Agents Act 1979</p>
        <p>b) Consumer Protection Regulations</p>
        <p>c) UK GDPR</p>
        <p>d) AML Regulations</p>
        <p>e) Equality Act</p>
        <p>f) Trading Standards Material Information Standards</p>
        <p>g) Consumer Contracts Regulations.</p>
    </div>
    
    <p style="margin-top: 12pt;">The Seller confirms all information provided about the property is accurate, complete and not misleading.</p>
    
    <div class="section">
        <h2>1 Definitions</h2>
        <p><strong>1.1</strong> "Abodeology", "we", "us" or "the Agent" means Abodeology Ltd, a company registered in England and Wales under company number <strong>14506067</strong>. Its registered office is located at 71-75 Shelton Street, Covent Garden, London, WC2H 9JQ.</p>
        <p><strong>1.2</strong> "Seller", "you" or "Client" means the person(s) named as the Seller in this Agreement.</p>
    </div>
    
    <div class="section">
        <h2>2 TYPE OF AGENCY (select one)</h2>
        
        <div class="subsection">
            <h3>2.1 Sole Agency</h3>
            <p>The Seller will pay the commission fee if:</p>
            <p>a) Abodeology introduces a buyer during the sole agency period who later completes; or</p>
            <p>b) Any buyer introduced by Abodeology during the period purchases within 6 months of termination; or</p>
            <p>c) Another agent introduces a buyer during the sole agency period.</p>
            <p><strong>Termination:</strong> 5 days' written notice.</p>
            <p><strong>Double Commission Warning:</strong> Instructing multiple agents during this period may result in liability to pay more than one fee.</p>
        </div>
        
        <div class="subsection">
            <h3>2.2 Sole Selling Rights</h3>
            <p>The Seller will pay the fee if contracts are exchanged:</p>
            <p>a) with any buyer during the period, regardless of who introduced them; or</p>
            <p>b) after the period, with a buyer introduced or negotiated with during the period.</p>
            <p><strong>Termination:</strong> 5 days' written notice.</p>
        </div>
        
        <div class="subsection">
            <h3>2.3 Multiple Agency</h3>
            <p>a) Only the agent who introduces the successful buyer earns the fee.</p>
            <p>b) Abodeology may charge a higher percentage for multiple agency.</p>
            <p><strong>Termination:</strong> 5 days' written notice.</p>
        </div>
    </div>
    
    <div class="section">
        <h2>3 MULTI-BRANCH & SUB-AGENCY NETWORK</h2>
        <p>The Seller acknowledges that Abodeology may market the property through:</p>
        <p>a) Any Abodeology branch</p>
        <p>b) Partner or licensee offices</p>
        <p>c) Self-employed agents</p>
        <p>d) Sub-agents or network affiliates</p>
        <p>All such parties act as authorised representatives of Abodeology. The contract remains strictly between the Seller and Abodeology.</p>
    </div>
    
    <div class="section">
        <h2>4 FOR SALE BOARDS</h2>
        <p>The Seller authorises Abodeology to erect and maintain a compliant For Sale board. Abodeology is not liable for third-party interference or damage.</p>
    </div>
    
    <div class="section">
        <h2>5 FEES & PAYMENT</h2>
        <div class="subsection">
            <h3>5.1 Fee Structure</h3>
            <p>Percentage fee only: <span class="field-line short">{{ number_format($instruction->fee_percentage ?? 1.5, 2) }}</span> % of final sale price achieved (plus VAT).</p>
        </div>
        <div class="subsection">
            <h3>5.2 Payment</h3>
            <p>Fees become due upon completion. All Sellers are jointly and severally liable.</p>
            <p><strong>4c. Ready, Willing & Able Buyer (CCR)</strong></p>
            <p>If the Seller requests immediate marketing within the 14-day cooling-off period, they may become liable for the full fee should Abodeology introduce a buyer who is ready, willing and able to proceed.</p>
        </div>
    </div>
    
    <div class="section">
        <h2>6 ADDITIONAL COSTS</h2>
        <p>Abodeology will not charge additional costs unless agreed in writing in advance. Optional services such as EPCs, premium marketing or third-party packages will be itemised where applicable.</p>
    </div>
    
    <div class="section">
        <h2>7 OFFERS</h2>
        <p>Abodeology will:</p>
        <p>• record all offers</p>
        <p>• communicate all offers promptly to the Seller</p>
        <p>• provide an offer log on request</p>
        <p>• verify the buyer's ability to proceed where possible</p>
        <p>No discrimination will occur under the Equality Act 2010.</p>
    </div>
    
    <div class="section">
        <h2>8 KEYS & ACCESS</h2>
        <p>Where Abodeology holds keys:</p>
        <p>• Viewings will be accompanied unless agreed otherwise</p>
        <p>• Keys will be securely coded</p>
        <p>• Surveyor/valuer access requires Seller consent</p>
        <p>• Any Abodeology branch/partner agent may conduct viewings</p>
    </div>
    
    <div class="section">
        <h2>9 DISCLOSURE OF PERSONAL INTEREST</h2>
        <p>Required by the Estate Agents Act 1979.</p>
        <p>Does any Abodeology representative have a personal interest in this property?</p>
        <p>Yes / No</p>
        <p>If yes, provide details: <span class="field-line long"></span></p>
    </div>
    
    <div class="section">
        <h2>10 ACCURACY & MATERIAL INFORMATION</h2>
        <p>The Seller agrees to:</p>
        <p>a) provide full, accurate and non-misleading information.</p>
        <p>b) disclose all material information, including:</p>
        <p style="margin-left: 20pt;">structural defects, disputes, notices, rights of way, planning issues,</p>
        <p style="margin-left: 20pt;">lease details, tenure, restrictions, title irregularities, unauthorised works,</p>
        <p style="margin-left: 20pt;">etc.</p>
        <p>c) notify Abodeology immediately of any changes.</p>
        <p>Marketing content will be approved by the Seller prior to publication. The Seller is legally responsible for the accuracy of their information.</p>
    </div>
    
    <div class="section">
        <h2>11 MARKETING MATERIALS</h2>
        <p>The Seller authorises Abodeology to create and use:</p>
        <p>• Photography</p>
        <p>• Video tours</p>
        <p>• 360 walkthroughs</p>
        <p>• Drone footage</p>
        <p>• Floorplans</p>
        <p>• EPCs</p>
        <p>• Social media and online advertising</p>
        <p>• Portal listings</p>
        <p>• Print or digital brochures</p>
        <p style="margin-top: 8pt;">All materials remain the intellectual property of Abodeology or its suppliers.</p>
        <p>Abodeology may retain archived materials for compliance, training or auditing.</p>
    </div>
    
    <div class="section">
        <h2>12 ABODEOLOGY® HOMECHECK - SERVICE DEFINITION</h2>
        <p>The "Abodeology HomeCheck" is a presentation-focused data-collection service only.</p>
        <p>Representatives collect basic visual information using third-party software, such as photographs and simple moisture readings.</p>
        <p>The data is analysed by an AI system and reviewed by an independent third-party assessment provider.</p>
        <p>The report's purpose is <strong>designed solely to give homeowners ideas on how to present their home for sale and to support general home preparation.</strong></p>
        
        <p style="margin-top: 8pt;"><strong>The HomeCheck report:</strong></p>
        <p>a) <strong>is not a survey</strong></p>
        <p>b) <strong>is not a valuation</strong></p>
        <p>c) <strong>is not a structural or condition report</strong></p>
        <p>d) <strong>must not be used for purchasing decisions</strong></p>
        
        <p style="margin-top: 8pt;">All assessments, observations and conclusions in the report are produced by the third-party provider.</p>
        <p><strong>Abodeology does not inspect, analyse, assess or verify the condition of the property and accepts no liability for the content of the report.</strong></p>
    </div>
    
    <div class="section">
        <h2>13 DATA PROTECTION (GDPR)</h2>
        <p>Abodeology processes data for:</p>
        <p>• contract fulfilment</p>
        <p>• AML checks</p>
        <p>• communication</p>
        <p>• compliance</p>
        <p>• marketing (where permitted)</p>
        <p>Data may be shared with authorised branches/partners and third-party processors where necessary.</p>
        <p>The Seller may request access or correction of their data.</p>
    </div>
    
    <div class="section">
        <h2>14 ANTI-MONEY LAUNDERING</h2>
        <p>Before marketing begins, Abodeology must:</p>
        <p>• verify the identity of all sellers</p>
        <p>• verify beneficial ownership</p>
        <p>• obtain source of funds where required</p>
        <p>Failure to provide documents delays marketing.</p>
        <p>Abodeology may file a Suspicious Activity Report without informing the Seller.</p>
    </div>
    
    <div class="section">
        <h2>15 CONSUMER CONTRACTS REGULATIONS (CCR)</h2>
        <p>Where signed off-premises or electronically, the Seller has a 14-day cancellation right.</p>
        <p>If the Seller permits immediate marketing, they acknowledge potential fee liability if Abodeology introduces a ready, willing and able buyer during the cooling-off period.</p>
    </div>
    
    <div class="section">
        <h2>16 MINIMUM AGREEMENT TERM</h2>
        <p>Minimum agency term: <span class="field-line short"></span> weeks</p>
        <p>After this period, the agreement continues on a rolling basis unless terminated with 5 days' written notice.</p>
    </div>
    
    <div class="section">
        <h2>17 LIMITATION OF LIABILITY</h2>
        <p>Abodeology is not liable for:</p>
        <p>a) financial loss</p>
        <p>b) market changes</p>
        <p>c) failed sales</p>
        <p>d) third-party delays</p>
        <p>e) indirect or consequential losses</p>
        <p>Nothing limits liability for fraud or negligence causing personal injury.</p>
    </div>
    
    <div class="section">
        <h2>18 COMPLAINTS & REDRESS</h2>
        <p>Abodeology is a member of: The Property Ombudsman (TPOS)</p>
        <p>A written complaints procedure is available on request.</p>
    </div>
    
    <div class="section">
        <h2>19 GOVERNING LAW</h2>
        <p>This Agreement is governed by the laws of England and Wales.</p>
        <p>Electronic signatures are valid.</p>
    </div>
    
    <div class="section signature-section">
        <h2>20 SIGNATURES</h2>
        
        <div class="signature-field">
            <label>Agent (Branch/Representative):</label>
            <span class="signature-line long">@if($property->assigned_agent_id && $property->assignedAgent){{ $property->assignedAgent->name }}@endif</span>
        </div>
        
        <div class="signature-field">
            <label>Date:</label>
            <span class="signature-line short">{{ date('d/m/Y') }}</span>
        </div>
        
        <div class="signature-field" style="margin-top: 15pt;">
            <label>Seller:</label>
            <span class="signature-line long">{{ $instruction->seller_names ?? $property->seller->name ?? '' }}</span>
        </div>
        
        <div class="signature-field">
            <label>Date:</label>
            <span class="signature-line short">{{ date('d/m/Y') }}</span>
        </div>
        
        <div class="signature-field" style="margin-top: 15pt;">
            <label>Seller (if joint):</label>
            <span class="signature-line long"></span>
        </div>
        
        <div class="signature-field">
            <label>Date:</label>
            <span class="signature-line short"></span>
        </div>
    </div>
    
    <div class="">
        <h2>STATUTORY CANCELLATION FORM (CCR)</h2>
        
        <div class="signature-field">
            <label>To:</label>
            <span class="signature-line long"></span>
        </div>
        
        <p style="margin: 10pt 0;">I/We hereby cancel the Estate Agency Agreement for:</p>
        
        <div class="signature-field">
            <label>Property Address:</label>
            <span class="signature-line long">{{ $instruction->property_address ?? $property->address ?? '' }}</span>
        </div>
        
        <div class="signature-field" style="margin-top: 15pt;">
            <label>Signed:</label>
            <span class="signature-line long">{{ $instruction->seller_names ?? $property->seller->name ?? '' }}</span>
        </div>
        
        <div class="signature-field">
            <label>Name:</label>
            <span class="signature-line long">{{ $instruction->seller_names ?? $property->seller->name ?? '' }}</span>
        </div>
        
        <div class="signature-field">
            <label>Date:</label>
            <span class="signature-line short">{{ date('d/m/Y') }}</span>
        </div>
    </div>

    <div class="">
        <h2>Digital Signature</h2>
        
        
        <div class="signature-field" style="margin-top: 15pt;">
            <label>Seller 1</label>
            <span class="signature-line long">{{@$instruction->seller1_name ?? ''}}</span>
        </div>
        
        <div class="signature-field">
            <label>Digital Signature:</label>
            <span class="signature-line long">{{@$instruction->seller1_signature ?? ''}}</span>
        </div>
        
        <div class="signature-field">
            <label>Date:</label>
            <span class="signature-line short">{{@$instruction->seller1_date ?? ''}}</span>
        </div>



        <div class="signature-field" style="margin-top: 15pt;">
            <label>Seller 2</label>
            <span class="signature-line long">{{@$instruction->seller2_name ?? ''}}</span>
        </div>
        
        <div class="signature-field">
            <label>Digital Signature:</label>
            <span class="signature-line long">{{@$instruction->seller2_signature ?? ''}}</span>
        </div>
        
        <div class="signature-field">
            <label>Date:</label>
            <span class="signature-line short">{{@$instruction->seller2_date ?? ''}}</span>
        </div>



    </div>

    </div>
    

</body>
</html>
