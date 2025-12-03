@extends('layouts.seller')

@section('title', 'Seller Dashboard')

@push('styles')
<style>
    body {
        margin: 0;
        background: #f7f7f7;
        font-family: "Inter", Arial, sans-serif;
        color: #111;
    }

    .container {
        max-width: 1050px;
        margin: 40px auto;
        padding: 0 20px;
    }

    h2 {
        font-size: 24px;
        margin-bottom: 14px;
        font-weight: 600;
    }

    /* SECTION CARD */
    .section {
        background: #fff;
        border-radius: 12px;
        padding: 28px;
        margin-bottom: 30px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        cursor: move;
        transition: all 0.3s ease;
        position: relative;
    }

    .section.dragging {
        opacity: 0.5;
        transform: scale(0.98);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .section.drag-over {
        border: 2px dashed #32b3ac;
        background: #f8fdfd;
    }

    .drag-handle {
        position: absolute;
        top: 15px;
        right: 15px;
        color: #999;
        cursor: move;
        font-size: 16px;
    }

    .drag-handle:hover {
        color: #32b3ac;
    }

    /* PROPERTY SUMMARY BOX */
    .property-box {
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .property-photo {
        width: 210px;
        height: 150px;
        background: #ddd;
        border-radius: 10px;
        background-size: cover;
        background-position: center;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        background: #32b3ac;
        color: #fff;
        border-radius: 50px;
        font-size: 13px;
        margin-top: 8px;
        font-weight: 600;
    }

    .status-badge.live {
        background: #32b3ac;
    }

    .status-badge.sstc {
        background: #e67c22;
    }

    .cta-button {
        background: #32b3ac;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        color: #fff;
        text-decoration: none;
        display: inline-block;
        margin-top: 16px;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .cta-button:hover {
        background: #25A29F;
    }

    .view-button {
        background: #f0f0f0;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 600;
        color: #333;
        text-decoration: none;
        display: inline-block;
        margin-top: 16px;
        border: 1px solid #ddd;
        cursor: pointer;
        font-size: 14px;
    }

    .view-button:hover {
        background: #e0e0e0;
    }

    /* TABLES */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
    }

    th, td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
    }

    th {
        background: #fafafa;
        font-weight: 600;
        text-align: left;
    }

    /* TIMELINE */
    .timeline {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        position: relative;
    }

    .timeline::before {
        content: "";
        position: absolute;
        top: 18px;
        left: 0;
        width: 100%;
        height: 3px;
        background: #e0e0e0;
    }

    .milestone {
        position: relative;
        text-align: center;
        width: 25%;
        font-size: 13px;
        font-weight: 600;
    }

    .milestone span {
        background: #fff;
        padding: 5px 10px;
        border-radius: 6px;
    }

    .milestone .dot {
        width: 16px;
        height: 16px;
        background: #32b3ac;
        border-radius: 50%;
        margin: 0 auto 6px auto;
    }

    /* HOMECHECK SECURITY STYLES */
    .homecheck-security {
        background: #fff6d6;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
        font-size: 13px;
        color: #7a6300;
    }

    .security-notice {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .security-notice::before {
        content: "🔒";
        font-size: 16px;
    }

    /* Customization notice */
    .customization-notice {
        text-align: center;
        padding: 10px;
        background: #f0f8f0;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #2d5a2d;
    }

    /* MODAL STYLES */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 90%;
        max-width: 800px;
        max-height: 90vh;
        overflow: auto;
        position: relative;
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-body {
        padding: 20px;
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
    }

    .close-modal:hover {
        color: #000;
    }

    /* Prevent text selection and right-click */
    .protected-content {
        user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
    }

    .protected-content img {
        pointer-events: none;
    }

    /* Customization notice */
    .customization-notice {
        text-align: center;
        padding: 10px;
        background: #f0f8f0;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #2d5a2d;
    }

    /* Offer action buttons */
    .offer-actions {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .offer-btn {
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 12px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .offer-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .offer-btn.accept {
        background: #32b3ac;
        color: white;
    }

    .offer-btn.decline {
        background: #ff6b6b;
        color: white;
    }

    .offer-btn.counter {
        background: #e67c22;
        color: white;
    }

    .offer-btn.discuss {
        background: #6c757d;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container">
    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="customization-notice">
        Drag and drop sections to customize your dashboard layout
    </div>

    <!-- DRAGGABLE SECTIONS CONTAINER -->
    <div id="dashboardSections">
        @php
            $primaryProperty = $properties->whereIn('status', ['live', 'sstc'])->first() ?? $properties->first();
            $primaryPropertyPhoto = $primaryProperty && $primaryProperty->photos && $primaryProperty->photos->count() > 0 
                ? \Storage::url($primaryProperty->photos->first()->image_path) 
                : 'data:image/svg+xml;base64,' . base64_encode('<svg width="350" height="250" xmlns="http://www.w3.org/2000/svg"><rect width="350" height="250" fill="#E8F4F3"/><text x="50%" y="50%" font-family="Arial, sans-serif" font-size="18" fill="#2CB8B4" text-anchor="middle" dominant-baseline="middle" font-weight="600">Abodeology</text></svg>');
        @endphp

        <!-- PROPERTY SUMMARY -->
        @if($primaryProperty)
        <div class="section" data-section="property">
            <div class="drag-handle">⣿⣿</div>
            <h2>Your Property</h2>
            <div class="property-box">
                <div class="property-photo" style="background-image:url('{{ $primaryPropertyPhoto }}');"></div>
                <div>
                    <h3 style="margin:0; font-size:22px;">{{ $primaryProperty->address }}</h3>
                    @if($primaryProperty->postcode)
                        <p style="margin: 4px 0; color: #666; font-size: 14px;">{{ $primaryProperty->postcode }}</p>
                    @endif
                    <div class="status-badge {{ $primaryProperty->status }}">{{ ucfirst($primaryProperty->status === 'sstc' ? 'Sold Subject to Contract' : 'Live on Market') }}</div>
                    <p style="margin-top:12px; color:#555;">
                        @if($primaryProperty->status === 'live')
                            Your property is now fully live across all major portals including Rightmove, Zoopla, OnTheMarket and Abodeology.co.uk.
                        @elseif($primaryProperty->status === 'sstc')
                            Your property is sold subject to contract. Sales progression is underway.
                        @endif
                    </p>
                    <a class="cta-button" href="{{ route('seller.properties.show', $primaryProperty->id) }}">View Property Details</a>
                </div>
            </div>
        </div>
        @endif

        <!-- SALES PROGRESSION -->
        @if(isset($salesProgression) && $salesProgression->count() > 0)
        <div class="section" data-section="sales">
            <div class="drag-handle">⣿⣿</div>
            <h2>Sales Progression</h2>
            <div class="timeline">
                <div class="milestone">
                    <div class="dot"></div>
                    <span>Offer Accepted</span>
                </div>
                <div class="milestone">
                    <div class="dot"></div>
                    <span>Searches</span>
                </div>
                <div class="milestone">
                    <div class="dot"></div>
                    <span>Exchange</span>
                </div>
                <div class="milestone">
                    <div class="dot"></div>
                    <span>Completion</span>
                </div>
            </div>
        </div>
        @endif

        <!-- VIEWINGS -->
        @if(isset($upcomingViewings) && $upcomingViewings->count() > 0)
        <div class="section" data-section="viewings">
            <div class="drag-handle">⣿⣿</div>
            <h2>Viewings</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Buyer Name</th>
                        <th>Status</th>
                        <th>Feedback</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcomingViewings->take(5) as $viewing)
                    <tr>
                        <td>{{ $viewing->viewing_date ? \Carbon\Carbon::parse($viewing->viewing_date)->format('d M Y - H:i') : 'N/A' }}</td>
                        <td>{{ $viewing->buyer->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($viewing->status ?? 'Scheduled') }}</td>
                        <td>
                            @if($viewing->feedback && $viewing->feedback->buyer_feedback)
                                "{{ Str::limit($viewing->feedback->buyer_feedback, 50) }}"
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- OFFERS SECTION - ENHANCED WITH ALL ACTIONS -->
        @if(isset($offers) && $offers->count() > 0)
        <div class="section" data-section="offers">
            <div class="drag-handle">⣿⣿</div>
            <h2>Offers</h2>
            <table>
                <thead>
                    <tr>
                        <th>Buyer</th>
                        <th>Offer Amount</th>
                        <th>Status</th>
                        <th>Proof of Funds</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offers as $offer)
                    <tr>
                        <td>
                            <strong>{{ $offer->buyer->name ?? 'N/A' }}</strong><br>
                            <small style="color: #666;">
                                @if($offer->chain_position)
                                    {{ ucfirst(str_replace('-', ' ', $offer->chain_position)) }}
                                @endif
                                @if($offer->funding_type === 'cash')
                                    • No chain
                                @elseif($offer->funding_type === 'mortgage')
                                    • Mortgage
                                @endif
                            </small>
                        </td>
                        <td>
                            @if($offer->released_to_seller)
                                <strong>£{{ number_format($offer->offer_amount ?? 0, 0) }}</strong><br>
                                @if($offer->property && $offer->property->asking_price)
                                    @php
                                        $difference = $offer->offer_amount - $offer->property->asking_price;
                                    @endphp
                                    <small style="color: #666;">
                                        @if($difference > 0)
                                            £{{ number_format($difference, 0) }} over asking
                                        @elseif($difference < 0)
                                            £{{ number_format(abs($difference), 0) }} below asking
                                        @else
                                            At asking price
                                        @endif
                                    </small>
                                @endif
                            @else
                                <span style="color: #666; font-style: italic;">Amount withheld pending agent review</span>
                            @endif
                        </td>
                        <td>
                            <span style="color: #e67c22; font-weight: 600;">
                                {{ ucfirst($offer->status) }}
                            </span><br>
                            <small style="color: #666;">Received: {{ $offer->created_at->format('d M Y') }}</small>
                        </td>
                        <td>
                            @if($offer->funding_type === 'cash')
                                <span style="color: #32b3ac; font-weight: 600;">✓ Cash Buyer</span><br>
                                <small style="color: #666;">Funds immediately available</small>
                            @else
                                <span style="color: #e67c22; font-weight: 600;">Awaiting</span><br>
                                <small style="color: #666;">Proof of funds requested</small>
                            @endif
                        </td>
                        <td>
                            <div class="offer-actions">
                                @if($offer->status === 'pending')
                                    <form action="{{ route('seller.offer.decision.update', $offer->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="decision" value="accepted">
                                        <button type="submit" class="offer-btn accept">Accept Offer</button>
                                    </form>
                                    <form action="{{ route('seller.offer.decision.update', $offer->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="decision" value="declined">
                                        <button type="submit" class="offer-btn decline" onclick="return confirm('Are you sure you want to decline this offer?');">Decline Offer</button>
                                    </form>
                                    <button type="button" class="offer-btn counter" onclick="openCounterOfferModal('{{ $offer->buyer->name ?? 'Buyer' }}', {{ $offer->offer_amount }}, {{ $offer->id }})">Counter Offer</button>
                                    <button type="button" class="offer-btn discuss" onclick="openDiscussModal('{{ $offer->buyer->name ?? 'Buyer' }}', {{ $offer->id }})">Discuss with Agent</button>
                                @else
                                    <a href="{{ route('seller.offer.decision.show', $offer->id) }}" class="offer-btn" style="background: #6c757d; color: white; text-decoration: none; display: inline-block;">View Details</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- OFFER ACTION MODALS -->
            <div id="counterOfferModal" class="modal">
                <div class="modal-content" style="max-width: 500px;">
                    <div class="modal-header">
                        <h3 style="margin:0;">Make Counter Offer</h3>
                        <button class="close-modal" onclick="closeCounterOfferModal()">×</button>
                    </div>
                    <div class="modal-body">
                        <form id="counterOfferForm" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="decision" value="counter">
                            <div style="margin-bottom: 20px;">
                                <p>Making counter offer to: <strong id="counterBuyerName"></strong></p>
                                <p>Current offer: <strong id="currentOfferAmount"></strong></p>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Your Counter Offer Amount:</label>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="font-size: 18px; font-weight: 600;">£</span>
                                    <input type="number" name="counter_amount" id="counterOfferInput" style="flex: 1; padding: 10px; border: 2px solid #32b3ac; border-radius: 6px; font-size: 16px; font-weight: 600;" placeholder="Enter amount" required>
                                </div>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Message to Buyer (Optional):</label>
                                <textarea name="notes" id="counterOfferMessage" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; resize: vertical;" placeholder="Add any conditions or comments..." rows="3"></textarea>
                            </div>
                            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                <button type="button" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer;" onclick="closeCounterOfferModal()">Cancel</button>
                                <button type="submit" style="background: #32b3ac; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">Submit Counter Offer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="discussModal" class="modal">
                <div class="modal-content" style="max-width: 500px;">
                    <div class="modal-header">
                        <h3 style="margin:0;">Discuss with Agent</h3>
                        <button class="close-modal" onclick="closeDiscussModal()">×</button>
                    </div>
                    <div class="modal-body">
                        <form id="discussForm" method="POST" action="{{ route('seller.offer.discuss') }}">
                            @csrf
                            <input type="hidden" name="offer_id" id="discussOfferId">
                            <div style="margin-bottom: 20px;">
                                <p>Requesting call about offer from: <strong id="discussBuyerName"></strong></p>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Preferred Contact Method:</label>
                                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                                    <label style="display: flex; align-items: center; gap: 5px;">
                                        <input type="radio" name="contact_method" value="call" checked> Phone Call
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 5px;">
                                        <input type="radio" name="contact_method" value="video"> Video Call
                                    </label>
                                </div>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Urgency:</label>
                                <select name="urgency" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;">
                                    <option value="normal">Normal - Within 24 hours</option>
                                    <option value="urgent">Urgent - Today if possible</option>
                                    <option value="asap">ASAP - Immediate callback requested</option>
                                </select>
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Discussion Points:</label>
                                <textarea name="discussion_points" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; resize: vertical;" placeholder="What would you like to discuss? (buyer position, negotiation strategy, timing, etc.)" rows="4"></textarea>
                            </div>
                            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                <button type="button" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer;" onclick="closeDiscussModal()">Cancel</button>
                                <button type="submit" style="background: #32b3ac; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600;">Request Call</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <h4 style="margin: 0 0 10px 0; font-size: 14px;">Offer Actions Guide</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13px;">
                    <div><span style="color: #32b3ac; font-weight: 600;">Accept Offer</span> - Formally accept this offer</div>
                    <div><span style="color: #ff6b6b; font-weight: 600;">Decline Offer</span> - Politely decline this offer</div>
                    <div><span style="color: #e67c22; font-weight: 600;">Counter Offer</span> - Propose different terms</div>
                    <div><span style="color: #6c757d; font-weight: 600;">Discuss with Agent</span> - Get expert advice</div>
                </div>
            </div>

            <div style="margin-top: 15px; padding: 12px; background: #fff6d6; border-radius: 6px;">
                <small style="color: #7a6300;">
                    <strong>Tip:</strong> Use "Discuss with Agent" if you're unsure about any offer. Our team can provide expert advice on buyer credibility, market position, and negotiation strategy.
                </small>
            </div>
        </div>
        @endif

        <!-- HOMECHECK REPORT - ONLINE VIEW ONLY -->
        @if($primaryProperty && $primaryProperty->homecheckReports && $primaryProperty->homecheckReports->where('status', 'completed')->count() > 0)
        @php
            $completedHomecheck = $primaryProperty->homecheckReports->where('status', 'completed')->first();
        @endphp
        <div class="section" data-section="homecheck">
            <div class="drag-handle">⣿⣿</div>
            <h2>Abodeology HomeCheck Report</h2>
            <div class="homecheck-security">
                <div class="security-notice">
                    <strong>Secure Online Viewing</strong>
                </div>
                <p>Your HomeCheck Report is available for online viewing only. This protects the proprietary analysis and ensures you receive the most current version with any updates.</p>
            </div>
            <p style="color:#555;">
                Your personalised HomeCheck Report provides room-by-room analysis, improvement suggestions, and insights to help maximise your sale price and buyer appeal.
            </p>
            <button class="view-button" onclick="openHomeCheckModal()">View HomeCheck Report Online</button>
            <div style="margin-top: 10px; font-size: 13px; color: #666;">
                Online view only - Download disabled to protect proprietary content
            </div>
        </div>
        @endif
    </div>
</div>

<!-- HOMECHECK REPORT MODAL (Online View Only) -->
@if($primaryProperty && $completedHomecheck)
<div id="homecheckModal" class="modal">
    <div class="modal-content protected-content">
        <div class="modal-header">
            <h3 style="margin:0;">Abodeology HomeCheck Report</h3>
            <button class="close-modal" onclick="closeHomeCheckModal()">×</button>
        </div>
        <div class="modal-body">
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <strong>Property:</strong> {{ $primaryProperty->address }}<br>
                @if($primaryProperty->postcode)
                    <strong>Postcode:</strong> {{ $primaryProperty->postcode }}<br>
                @endif
                <strong>Report Date:</strong> {{ $completedHomecheck->completed_at ? \Carbon\Carbon::parse($completedHomecheck->completed_at)->format('d F Y') : 'N/A' }}<br>
                <strong>Prepared by:</strong> Abodeology Property Consultants
            </div>
            
            @if($primaryProperty->homecheckData && $primaryProperty->homecheckData->count() > 0)
                @php
                    $rooms = $primaryProperty->homecheckData->groupBy('room_name');
                @endphp
                @foreach($rooms as $roomName => $roomData)
                <div style="margin: 25px 0; padding: 20px; border: 1px solid #eee; border-radius: 8px;">
                    <h4 style="margin-top:0; color: #32b3ac;">{{ ucfirst($roomName) }} Analysis</h4>
                    @if($roomData->first()->ai_analysis)
                        <p>{{ $roomData->first()->ai_analysis }}</p>
                    @else
                        <p>Analysis for this room is being generated. Please check back shortly.</p>
                    @endif
                    
                    @if($roomData->first()->homecheck_score)
                    <div style="background: #fff6d6; padding: 12px; border-radius: 6px; margin: 15px 0;">
                        <strong>HOMECHECK SCORE: {{ $roomData->first()->homecheck_score }}/10</strong><br>
                        <em>{{ $roomData->first()->recommendations ?? 'Analysis in progress' }}</em>
                    </div>
                    @endif
                    
                    @if($roomData->first()->recommendations)
                    <h5>Recommended Improvements:</h5>
                    <ul>
                        @foreach(explode("\n", $roomData->first()->recommendations) as $recommendation)
                            @if(trim($recommendation))
                                <li>{{ trim($recommendation) }}</li>
                            @endif
                        @endforeach
                    </ul>
                    @endif
                </div>
                @endforeach
            @else
                <div style="padding: 20px; text-align: center; color: #666;">
                    <p>HomeCheck report is being generated. Please check back shortly.</p>
                </div>
            @endif

            <div style="text-align: center; margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <p style="margin:0; color: #666; font-size: 13px;">
                    This report is for online viewing only. Downloading, printing, or screenshotting is disabled.<br>
                    © {{ date('Y') }} Abodeology®. All rights reserved. Proprietary content.
                </p>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Drag and Drop functionality
    document.addEventListener('DOMContentLoaded', function() {
        const dashboard = document.getElementById('dashboardSections');
        let draggedItem = null;

        // Load saved order from localStorage
        loadDashboardOrder();

        // Add event listeners for drag and drop
        const sections = document.querySelectorAll('.section');
        sections.forEach(section => {
            section.setAttribute('draggable', 'true');

            section.addEventListener('dragstart', function(e) {
                draggedItem = this;
                setTimeout(() => this.classList.add('dragging'), 0);
            });

            section.addEventListener('dragend', function() {
                this.classList.remove('dragging');
                saveDashboardOrder();
            });

            section.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('drag-over');
            });

            section.addEventListener('dragleave', function() {
                this.classList.remove('drag-over');
            });

            section.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');

                if (draggedItem && draggedItem !== this) {
                    const allSections = Array.from(dashboard.children);
                    const thisIndex = allSections.indexOf(this);
                    const draggedIndex = allSections.indexOf(draggedItem);

                    if (draggedIndex < thisIndex) {
                        this.parentNode.insertBefore(draggedItem, this.nextSibling);
                    } else {
                        this.parentNode.insertBefore(draggedItem, this);
                    }

                    saveDashboardOrder();
                }
            });
        });

        function saveDashboardOrder() {
            const sections = Array.from(dashboard.children);
            const order = sections.map(section => section.getAttribute('data-section'));
            localStorage.setItem('dashboardOrder', JSON.stringify(order));
        }

        function loadDashboardOrder() {
            const savedOrder = localStorage.getItem('dashboardOrder');
            if (savedOrder) {
                const order = JSON.parse(savedOrder);
                const sections = Array.from(dashboard.children);

                // Reorder sections based on saved order
                order.forEach(sectionId => {
                    const section = document.querySelector(`[data-section="${sectionId}"]`);
                    if (section) {
                        dashboard.appendChild(section);
                    }
                });
            }
        }
    });

    // Counter Offer Modal Functions
    function openCounterOfferModal(buyerName, currentAmount, offerId) {
        document.getElementById('counterBuyerName').textContent = buyerName;
        document.getElementById('currentOfferAmount').textContent = '£' + currentAmount.toLocaleString();
        document.getElementById('counterOfferForm').action = '{{ route("seller.offer.decision.update", ":id") }}'.replace(':id', offerId);
        document.getElementById('counterOfferModal').style.display = 'flex';
    }

    function closeCounterOfferModal() {
        document.getElementById('counterOfferModal').style.display = 'none';
        document.getElementById('counterOfferInput').value = '';
        document.getElementById('counterOfferMessage').value = '';
    }

    // Discuss Modal Functions
    function openDiscussModal(buyerName, offerId) {
        document.getElementById('discussBuyerName').textContent = buyerName;
        document.getElementById('discussOfferId').value = offerId;
        document.getElementById('discussModal').style.display = 'flex';
    }

    function closeDiscussModal() {
        document.getElementById('discussModal').style.display = 'none';
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        const counterModal = document.getElementById('counterOfferModal');
        const discussModal = document.getElementById('discussModal');
        
        if (event.target === counterModal) {
            closeCounterOfferModal();
        }
        if (event.target === discussModal) {
            closeDiscussModal();
        }
    }

    // HomeCheck Modal Functions
    function openHomeCheckModal() {
        const modal = document.getElementById('homecheckModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.addEventListener('contextmenu', disableRightClick);
        }
    }

    function closeHomeCheckModal() {
        const modal = document.getElementById('homecheckModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    function disableRightClick(e) {
        e.preventDefault();
        return false;
    }

    // Close HomeCheck modal when clicking outside content
    window.onclick = function(event) {
        const homecheckModal = document.getElementById('homecheckModal');
        if (homecheckModal && event.target === homecheckModal) {
            closeHomeCheckModal();
        }
        
        const counterModal = document.getElementById('counterOfferModal');
        const discussModal = document.getElementById('discussModal');
        
        if (event.target === counterModal) {
            closeCounterOfferModal();
        }
        if (event.target === discussModal) {
            closeDiscussModal();
        }
    }

    // Disable keyboard shortcuts for saving
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'p')) {
            e.preventDefault();
        }
    });
</script>
@endpush

