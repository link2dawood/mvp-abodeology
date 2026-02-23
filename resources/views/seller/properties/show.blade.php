@extends('layouts.seller')

@section('title', 'Property Details')

@push('styles')
<style>
    .container {
        max-width: 1100px;
        margin: 30px auto;
        padding: 20px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    h2 {
        border-bottom: 2px solid #000000;
        padding-bottom: 8px;
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }

    .btn {
        background: #2CB8B4;
        color: #ffffff;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 4px;
        display: inline-block;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background 0.3s ease;
        margin-right: 10px;
    }

    .btn:hover {
        background: #25A29F;
    }

    .btn-primary {
        background: #2CB8B4;
    }

    .card {
        border: 1px solid #dcdcdc;
        padding: 20px;
        margin: 20px 0;
        border-radius: 4px;
        background: #fff;
    }

    .property-header {
        margin-bottom: 20px;
    }

    .property-address {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #1E1E1E;
    }

    .property-postcode {
        color: #666;
        font-size: 16px;
        margin-bottom: 15px;
    }

    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        background: #000000;
        color: #fff;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .status-badge.draft {
        background: #666;
    }

    .status-badge.property_details_captured,
    .status-badge.property_details_completed {
        background: #2CB8B4;
    }

    .status-badge.awaiting_aml {
        background: #ffc107;
        color: #000;
    }

    .status-badge.signed {
        background: #28a745;
    }

    .status-badge.live {
        background: #2CB8B4;
    }

    .status-badge.sold {
        background: #28a745;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .info-item {
        padding: 15px;
        background: #F4F4F4;
        border-radius: 4px;
    }

    .info-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: #1E1E1E;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin: 30px 0 15px 0;
        padding-bottom: 8px;
        border-bottom: 1px solid #dcdcdc;
    }

    .action-buttons {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #dcdcdc;
    }

    .celebration-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 15, 15, 0.55);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 3000;
        padding: 20px;
    }

    .celebration-modal {
        position: relative;
        width: 100%;
        max-width: 520px;
        background: #fff;
        border-radius: 14px;
        border: 1px solid #eaeaea;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.24);
        overflow: hidden;
        text-align: center;
    }

    .celebration-top {
        background: #0f0f0f;
        padding: 18px;
    }

    .celebration-top img {
        width: 150px;
        height: auto;
        object-fit: contain;
    }

    .celebration-body {
        padding: 28px 26px 24px;
    }

    .celebration-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(44, 184, 180, 0.14);
        color: #14807b;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    .celebration-title {
        margin: 0 0 8px;
        font-size: 30px;
        line-height: 1.1;
    }

    .celebration-text {
        margin: 0 auto 18px;
        color: #4b5563;
        max-width: 420px;
    }

    .celebration-close {
        background: #2CB8B4;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 11px 20px;
        font-weight: 700;
        cursor: pointer;
    }

    .celebration-close:hover {
        background: #25A29F;
    }

    #celebration-confetti {
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 2990;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="page-header">
        <h2>Property Details</h2>
        <a href="{{ route('seller.properties.index') }}" class="btn">← Back to Properties</a>
    </div>

    @php
        $successMessage = session('success');
        $shouldCelebrate = $successMessage && str_contains(strtolower($successMessage), 'instruction has been signed successfully');
    @endphp

    @if($successMessage)
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ $successMessage }}
        </div>
    @endif

    @if($shouldCelebrate)
        <canvas id="celebration-confetti"></canvas>
        <div id="celebration-overlay" class="celebration-overlay">
            <div class="celebration-modal">
                <div class="celebration-top">
                    <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo">
                </div>
                <div class="celebration-body">
                    <span class="celebration-badge">Milestone unlocked</span>
                    <h3 class="celebration-title">Congratulations! 🎉</h3>
                    <p class="celebration-text">
                        Your instruction is now signed and your property is progressing to the next stage.
                    </p>
                    <button id="celebration-close" class="celebration-close" type="button">Awesome, let’s go!</button>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="property-header">
            <div class="property-address">{{ $property->address }}</div>
            @if($property->postcode)
                <div class="property-postcode">{{ $property->postcode }}</div>
            @endif
            <span class="status-badge {{ $property->status }}">
                {{ $property->status_text ?? ucfirst($property->status) }}
            </span>
        </div>

        <div class="info-grid">
            @if($property->asking_price)
                <div class="info-item">
                    <div class="info-label">Asking Price</div>
                    <div class="info-value">£{{ number_format($property->asking_price, 0) }}</div>
                </div>
            @endif

            @if($property->property_type)
                <div class="info-item">
                    <div class="info-label">Property Type</div>
                    <div class="info-value">{{ ucfirst(str_replace('_', ' ', $property->property_type)) }}</div>
                </div>
            @endif

            @if($property->bedrooms)
                <div class="info-item">
                    <div class="info-label">Bedrooms</div>
                    <div class="info-value">{{ $property->bedrooms }}</div>
                </div>
            @endif

            @if($property->bathrooms)
                <div class="info-item">
                    <div class="info-label">Bathrooms</div>
                    <div class="info-value">{{ $property->bathrooms }}</div>
                </div>
            @endif

            @if($property->reception_rooms)
                <div class="info-item">
                    <div class="info-label">Reception Rooms</div>
                    <div class="info-value">{{ $property->reception_rooms }}</div>
                </div>
            @endif

            @if($property->tenure)
                <div class="info-item">
                    <div class="info-label">Tenure</div>
                    <div class="info-value">{{ ucfirst(str_replace('_', ' ', $property->tenure)) }}</div>
                </div>
            @endif

            @if($property->parking)
                <div class="info-item">
                    <div class="info-label">Parking</div>
                    <div class="info-value">{{ ucfirst(str_replace('_', ' ', $property->parking)) }}</div>
                </div>
            @endif
        </div>

        @if($property->outbuildings || $property->garden_details)
            <div class="section-title">Outdoor Features</div>
            <div class="info-grid">
                @if($property->outbuildings)
                    <div class="info-item">
                        <div class="info-label">Outbuildings</div>
                        <div class="info-value">{{ $property->outbuildings }}</div>
                    </div>
                @endif

                @if($property->garden_details)
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <div class="info-label">Garden Details</div>
                        <div class="info-value" style="white-space: pre-wrap;">{{ $property->garden_details }}</div>
                    </div>
                @endif
            </div>
        @endif

        @if($property->lease_years_remaining || $property->ground_rent || $property->service_charge)
            <div class="section-title">Leasehold Information</div>
            <div class="info-grid">
                @if($property->lease_years_remaining)
                    <div class="info-item">
                        <div class="info-label">Lease Years Remaining</div>
                        <div class="info-value">{{ $property->lease_years_remaining }} years</div>
                    </div>
                @endif

                @if($property->ground_rent)
                    <div class="info-item">
                        <div class="info-label">Ground Rent</div>
                        <div class="info-value">£{{ number_format($property->ground_rent, 2) }} per year</div>
                    </div>
                @endif

                @if($property->service_charge)
                    <div class="info-item">
                        <div class="info-label">Service Charge</div>
                        <div class="info-value">£{{ number_format($property->service_charge, 2) }} per year</div>
                    </div>
                @endif

                @if($property->managing_agent)
                    <div class="info-item">
                        <div class="info-label">Managing Agent</div>
                        <div class="info-value">{{ $property->managing_agent }}</div>
                    </div>
                @endif
            </div>
        @endif

        <div class="action-buttons">
            @if($property->status === 'draft')
                <a href="{{ route('seller.onboarding', $property->id) }}" class="btn btn-primary">Start Onboarding</a>
            @elseif(in_array($property->status, ['property_details_captured', 'property_details_completed']))
                @if($property->instruction && $property->instruction->status === 'pending')
                    <a href="{{ route('seller.instruct', $property->id) }}" class="btn btn-primary">Sign Terms & Conditions</a>
                    <p style="color: #2CB8B4; margin-top: 10px;">✓ Instruction request received. Please sign the Terms & Conditions to proceed.</p>
                @elseif(!$property->instruction || $property->instruction->status !== 'signed')
                    <p style="color: #666; margin-top: 10px;">Waiting for instruction request from your agent...</p>
                @endif
            @elseif($property->status === 'awaiting_aml')
                <div style="background: #fff3cd; padding: 20px; margin-top: 20px; border-radius: 4px;">
                    <h4 style="color: #856404; margin-top: 0; font-size: 18px;">⚠️ Action Required: Upload AML Documents</h4>
                    <p style="margin: 10px 0; color: #856404; font-weight: 600;">Your Terms & Conditions have been signed successfully!</p>
                    <p style="margin: 10px 0; color: #856404;">To proceed, please upload your AML documents (Photo ID + Proof of Address).</p>
                    <div style="margin-top: 15px;">
                        <a href="{{ route('seller.aml.upload', $property->id) }}" class="btn btn-primary" style="background: #ffc107; color: #000; border: none;">Upload AML Documents Now</a>
                    </div>
                    <p style="margin: 15px 0 0 0; font-size: 13px; color: #666;">
                        <strong>Required Documents:</strong><br>
                        • Photo ID (Passport, Driving License, or National ID)<br>
                        • Proof of Address (Utility bill, Bank statement, or Council tax bill dated within last 3 months)
                    </p>
                </div>
            @elseif($property->status === 'signed')
                <p style="color: #28a745; font-weight: 600; margin-top: 10px;">✓ Terms & Conditions signed. Welcome Pack sent!</p>
                <div style="margin-top: 20px; padding: 15px; background: #E8F4F3; border-radius: 4px;">
                    <h4 style="color: #2CB8B4; margin-top: 0;">Next Steps</h4>
                    <div style="margin-top: 15px;">
                        @php
                            $hasAmlDocs = isset($amlCheck) && $amlCheck->id_document && $amlCheck->proof_of_address;
                            $amlStatus = isset($amlCheck) ? $amlCheck->verification_status : 'pending';
                            $hasSolicitorDetails = $property->solicitor_details_completed;
                        @endphp
                        
                        @if(!$hasAmlDocs)
                            <div style="margin-bottom: 15px;">
                                <p style="margin: 5px 0;">
                                    <span style="color: #dc3545;">✗</span> <strong>AML Documents Required:</strong> Please upload your ID and Proof of Address.
                                </p>
                                <a href="{{ route('seller.aml.upload', $property->id) }}" class="btn btn-primary" style="margin-top: 5px;">Upload AML Documents</a>
                            </div>
                        @elseif($amlStatus === 'pending')
                            <p style="margin: 5px 0; color: #ffc107;">
                                <span style="color: #ffc107;">⏳</span> <strong>AML Documents:</strong> Under review.
                            </p>
                        @elseif($amlStatus === 'verified')
                            <p style="margin: 5px 0; color: #28a745;">
                                <span style="color: #28a745;">✓</span> <strong>AML Documents:</strong> Verified
                            </p>
                        @endif

                        @if(!$hasSolicitorDetails)
                            <div style="margin-bottom: 15px;">
                                <p style="margin: 5px 0;">
                                    <span style="color: #dc3545;">✗</span> <strong>Solicitor Details Required:</strong> Please provide your solicitor's contact information.
                                </p>
                                <a href="{{ route('seller.solicitor.details', $property->id) }}" class="btn btn-primary" style="margin-top: 5px;">Provide Solicitor Details</a>
                            </div>
                        @else
                            <p style="margin: 5px 0; color: #28a745;">
                                <span style="color: #28a745;">✓</span> <strong>Solicitor Details:</strong> Completed
                            </p>
                            @if($property->solicitor_name)
                                <p style="margin: 5px 0; font-size: 14px; color: #666;">
                                    {{ $property->solicitor_name }}, {{ $property->solicitor_firm }}
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            @elseif($property->status === 'live')
                <a href="{{ route('buyer.viewing.request', $property->id) }}" class="btn">View Live Listing</a>
            @endif
            <a href="{{ route('seller.properties.index') }}" class="btn" style="background: #2CB8B4;">Back to Properties</a>
        </div>
    </div>

    @if($property->offers && $property->offers->count() > 0)
        <div class="section-title">Offers ({{ $property->offers->count() }})</div>
        <div class="card">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <th style="padding: 12px; border-bottom: 1px solid #dcdcdc; text-align: left;">Buyer</th>
                    <th style="padding: 12px; border-bottom: 1px solid #dcdcdc; text-align: left;">Amount</th>
                    <th style="padding: 12px; border-bottom: 1px solid #dcdcdc; text-align: left;">Status</th>
                    <th style="padding: 12px; border-bottom: 1px solid #dcdcdc; text-align: left;">Action</th>
                </tr>
                @foreach($property->offers as $offer)
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #dcdcdc;">{{ $offer->buyer->name ?? 'N/A' }}</td>
                        <td style="padding: 12px; border-bottom: 1px solid #dcdcdc;">
                            @if($offer->released_to_seller)
                                £{{ number_format($offer->offer_amount ?? 0, 0) }}
                            @else
                                <span style="color: #666; font-style: italic;">Amount withheld</span>
                            @endif
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #dcdcdc;">{{ ucfirst($offer->status ?? 'Pending') }}</td>
                        <td style="padding: 12px; border-bottom: 1px solid #dcdcdc;">
                            @if($offer->status === 'pending')
                                <a href="{{ route('seller.offer.decision.show', $offer->id) }}" class="btn" style="padding: 6px 12px; font-size: 13px;">Review Offer</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    @php
        $instruction = $property->instruction;
        $hasSignedInstruction = $instruction && $instruction->status === 'signed';
        $activeHomeCheck = $property->homecheckReports->whereIn('status', ['pending', 'scheduled', 'in_progress'])->first();
        $completedHomeCheck = $property->homecheckReports->where('status', 'completed')->first();
        $homeCheckReport = $completedHomeCheck ?? $activeHomeCheck;
    @endphp

    @if($hasSignedInstruction && $homeCheckReport)
        <div class="section-title">HomeCheck Report</div>
        <div class="card">
            @if($completedHomeCheck && $completedHomeCheck->report_path)
                <div style="margin-bottom: 15px;">
                    <p><strong>Report Generated:</strong> {{ $completedHomeCheck->completed_at ? \Carbon\Carbon::parse($completedHomeCheck->completed_at)->format('l, F j, Y g:i A') : 'N/A' }}</p>
                    @if($completedHomeCheck->completed_by)
                        <p><strong>Completed By:</strong> {{ $completedHomeCheck->completer->name ?? 'Agent' }}</p>
                    @endif
                </div>
                <a href="{{ route('seller.homecheck.report', $property->id) }}" target="_blank" class="btn btn-primary">View HomeCheck Report</a>
                <p style="font-size: 13px; color: #666; margin-top: 10px;">
                    This AI-generated report analyzes your property's condition based on the HomeCheck images.
                </p>
            @elseif($activeHomeCheck)
                <div style="margin-bottom: 15px;">
                    <p><strong>Status:</strong> 
                        @if($activeHomeCheck->status === 'in_progress')
                            Report being generated
                        @elseif($activeHomeCheck->status === 'scheduled')
                            Scheduled for {{ $activeHomeCheck->scheduled_date ? \Carbon\Carbon::parse($activeHomeCheck->scheduled_date)->format('l, F j, Y') : 'TBD' }}
                        @else
                            Pending
                        @endif
                    </p>
                    @if($activeHomeCheck->scheduled_by)
                        <p><strong>Scheduled By:</strong> {{ $activeHomeCheck->scheduler->name ?? 'Agent' }}</p>
                    @endif
                </div>
                <p style="font-size: 13px; color: #666;">
                    Your HomeCheck report is being prepared. You will have immediate access once it's completed. This technical assessment will help inform your property presentation strategy before marketing begins.
                </p>
            @endif
        </div>
    @endif
</div>
@endsection

@if($shouldCelebrate)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('celebration-overlay');
    const closeBtn = document.getElementById('celebration-close');
    const canvas = document.getElementById('celebration-confetti');
    if (!overlay || !canvas) return;

    const ctx = canvas.getContext('2d');
    const colors = ['#2CB8B4', '#0F0F0F', '#FFD54F', '#F97316', '#22C55E', '#ffffff'];
    let particles = [];
    let rafId = null;
    let running = true;

    function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }

    function createParticle(x, y) {
        return {
            x,
            y,
            r: Math.random() * 5 + 3,
            c: colors[Math.floor(Math.random() * colors.length)],
            vx: (Math.random() - 0.5) * 8,
            vy: Math.random() * -9 - 3,
            g: Math.random() * 0.2 + 0.12,
            a: 1,
            rot: Math.random() * Math.PI * 2,
            vr: (Math.random() - 0.5) * 0.2
        };
    }

    function burst() {
        const cx = canvas.width / 2;
        const cy = canvas.height * 0.28;
        for (let i = 0; i < 180; i++) {
            particles.push(createParticle(cx + (Math.random() - 0.5) * 120, cy + (Math.random() - 0.5) * 40));
        }
    }

    function draw() {
        if (!running) return;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particles = particles.filter(p => p.a > 0);

        particles.forEach(p => {
            p.vy += p.g;
            p.x += p.vx;
            p.y += p.vy;
            p.rot += p.vr;
            p.a -= 0.008;

            ctx.save();
            ctx.globalAlpha = Math.max(p.a, 0);
            ctx.translate(p.x, p.y);
            ctx.rotate(p.rot);
            ctx.fillStyle = p.c;
            ctx.fillRect(-p.r, -p.r, p.r * 2, p.r * 2);
            ctx.restore();
        });

        rafId = requestAnimationFrame(draw);
    }

    function closeCelebration() {
        overlay.remove();
        running = false;
        if (rafId) cancelAnimationFrame(rafId);
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        setTimeout(() => canvas.remove(), 120);
    }

    resize();
    window.addEventListener('resize', resize);
    burst();
    draw();

    closeBtn?.addEventListener('click', closeCelebration);
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) closeCelebration();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeCelebration();
    }, { once: true });

    setTimeout(closeCelebration, 8000);
});
</script>
@endpush
@endif

