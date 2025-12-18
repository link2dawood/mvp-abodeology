@extends('layouts.admin')

@section('title', 'Property Details')

@push('styles')
<style>
    h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 20px;
        color: var(--abodeology-teal);
    }

    .info-row {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid var(--line-grey);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        min-width: 200px;
        color: #666;
    }

    .info-value {
        flex: 1;
    }

    .status {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-draft { 
        background: #666; 
        color: #FFF; 
    }

    .status-property_details_captured { 
        background: #2CB8B4; 
        color: #FFF; 
    }
    .status-property_details_completed { 
        background: #2CB8B4; 
        color: #FFF; 
    }

    .status-awaiting_aml { 
        background: #ffc107; 
        color: #000; 
    }

    .status-signed { 
        background: #28a745; 
        color: #FFF; 
    }

    .status-live { 
        background: #2CB8B4; 
        color: #FFF; 
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        display: inline-block;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        margin-right: 10px;
        transition: background 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-main {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-main:hover {
        background: #25A29F;
    }

    .btn-secondary {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-secondary:hover {
        background: #25A29F;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2>Property Details</h2>
        </div>
        <div>
            <a href="{{ route('admin.valuations.index') }}" class="btn btn-secondary">Back to Valuations</a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <h3>Property Information</h3>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="status status-{{ $property->status }}">
                    {{ ucfirst(str_replace('_', ' ', $property->status)) }}
                </span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Address:</div>
            <div class="info-value">{{ $property->address }}</div>
        </div>
        @if($property->postcode)
        <div class="info-row">
            <div class="info-label">Postcode:</div>
            <div class="info-value">{{ $property->postcode }}</div>
        </div>
        @endif
        @if($property->property_type)
        <div class="info-row">
            <div class="info-label">Property Type:</div>
            <div class="info-value">{{ ucfirst(str_replace('_', ' ', $property->property_type)) }}</div>
        </div>
        @endif
        @if($property->bedrooms)
        <div class="info-row">
            <div class="info-label">Bedrooms:</div>
            <div class="info-value">{{ $property->bedrooms }}</div>
        </div>
        @endif
        @if($property->bathrooms)
        <div class="info-row">
            <div class="info-label">Bathrooms:</div>
            <div class="info-value">{{ $property->bathrooms }}</div>
        </div>
        @endif
        @if($property->reception_rooms)
        <div class="info-row">
            <div class="info-label">Reception Rooms:</div>
            <div class="info-value">{{ $property->reception_rooms }}</div>
        </div>
        @endif
        @if($property->outbuildings)
        <div class="info-row">
            <div class="info-label">Outbuildings:</div>
            <div class="info-value">{{ $property->outbuildings }}</div>
        </div>
        @endif
        @if($property->garden_details)
        <div class="info-row">
            <div class="info-label">Garden Details:</div>
            <div class="info-value">{{ $property->garden_details }}</div>
        </div>
        @endif
        @if($property->asking_price)
        <div class="info-row">
            <div class="info-label">Asking Price:</div>
            <div class="info-value">£{{ number_format($property->asking_price, 0) }}</div>
        </div>
        @endif
    </div>

    <div class="card">
        <h3>Seller Information</h3>
        <div class="info-row">
            <div class="info-label">Seller 1 (Primary):</div>
            <div class="info-value">{{ $property->seller->name ?? 'N/A' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $property->seller->email ?? 'N/A' }}</div>
        </div>
        @if($property->seller->phone)
        <div class="info-row">
            <div class="info-label">Phone:</div>
            <div class="info-value">{{ $property->seller->phone }}</div>
        </div>
        @endif
        
        @if($property->seller2_name || $property->seller2_email || $property->seller2_phone)
        <hr style="margin: 15px 0; border: none; border-top: 1px solid var(--line-grey);">
        <div class="info-row">
            <div class="info-label">Seller 2:</div>
            <div class="info-value">{{ $property->seller2_name ?? 'N/A' }}</div>
        </div>
        @if($property->seller2_email)
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $property->seller2_email }}</div>
        </div>
        @endif
        @if($property->seller2_phone)
        <div class="info-row">
            <div class="info-label">Phone:</div>
            <div class="info-value">{{ $property->seller2_phone }}</div>
        </div>
        @endif
        @endif
    </div>

    @if($property->instruction)
    <div class="card">
        <h3>Instruction Status</h3>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="status status-{{ $property->instruction->status }}">
                    {{ ucfirst($property->instruction->status) }}
                </span>
            </div>
        </div>
        @if($property->instruction->signed_at)
        <div class="info-row">
            <div class="info-label">Signed Date:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($property->instruction->signed_at)->format('l, F j, Y g:i A') }}</div>
        </div>
        @endif
        @if($property->instruction->seller1_name)
        <div class="info-row">
            <div class="info-label">Seller 1:</div>
            <div class="info-value">{{ $property->instruction->seller1_name }}</div>
        </div>
        @endif
        @if($property->instruction->seller2_name)
        <div class="info-row">
            <div class="info-label">Seller 2:</div>
            <div class="info-value">{{ $property->instruction->seller2_name }}</div>
        </div>
        @endif
    </div>
    @endif

    @if(in_array($property->status, ['property_details_captured', 'property_details_completed']) && (!$property->instruction || $property->instruction->status !== 'signed'))
    <div class="card" style="background: #E8F4F3;">
        <h3 style="color: var(--abodeology-teal); margin-top: 0;">Next Steps</h3>
        <p><strong>Ask the seller if they want to instruct now or later.</strong></p>
        
        @if(!$property->instruction || $property->instruction->status !== 'pending')
        <div style="margin-top: 20px;">
            <div style="margin-bottom: 20px;">
                <h4 style="color: var(--abodeology-teal); margin-bottom: 10px;">Sign Up Now</h4>
                <p style="font-size: 14px; color: #666; margin-bottom: 10px;">If the seller chooses to sign up immediately, send them an instruction request with a direct link to sign the Terms & Conditions.</p>
                <form action="{{ route('admin.properties.request-instruction', $property->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-main">Sign Up Now - Request Instruction</button>
                </form>
            </div>
            
            <div style="border-top: 1px solid #ccc; padding-top: 20px; margin-top: 20px;">
                <h4 style="color: var(--abodeology-teal); margin-bottom: 10px;">Sign Up Later</h4>
                <p style="font-size: 14px; color: #666; margin-bottom: 10px;">If the seller prefers to sign up later, send them a post-valuation follow-up email with an "Instruct Abodeology" button. They can click the button when they're ready.</p>
                <form action="{{ route('admin.properties.send-post-valuation-email', $property->id) }}" method="POST" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="btn" style="background: var(--abodeology-teal);">Sign Up Later - Send Post-Valuation Email</button>
                </form>
            </div>
        </div>
        @else
        <p style="color: #2CB8B4; font-weight: 600; margin-top: 15px;">
            ✓ Instruction request has been sent. Waiting for seller to sign.
        </p>
        <p style="font-size: 13px; color: #666; margin-top: 10px;">
            The seller has been notified and will receive an email with a link to sign the Terms & Conditions.
        </p>
        @endif
    </div>
    @endif

    @if($property->instruction && $property->instruction->status === 'signed')
    <div class="card" style="background: #d4edda;">
        <h3 style="color: #28a745; margin-top: 0;">✓ Instruction Signed</h3>
        <p>Congratulations! The seller has signed the Terms & Conditions. The Welcome Pack has been sent to the seller.</p>
        <p><strong>Signed Date:</strong> {{ \Carbon\Carbon::parse($property->instruction->signed_at)->format('l, F j, Y g:i A') }}</p>
        
        @php
            $activeHomeCheck = $property->homecheckReports->whereIn('status', ['pending', 'scheduled', 'in_progress'])->first();
            $completedHomeCheck = $property->homecheckReports->where('status', 'completed')->first();
            $homecheckData = $property->homecheckData ?? collect();
        @endphp

        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #c3e6cb;">
            <h4 style="color: #28a745; margin-bottom: 15px;">HomeCheck Status</h4>
            @if($completedHomeCheck)
                <p style="color: #28a745; font-weight: 600;">
                    ✓ HomeCheck Completed
                </p>
                <p style="font-size: 13px; color: #666; margin-top: 5px;">
                    <strong>Completed:</strong> {{ \Carbon\Carbon::parse($completedHomeCheck->completed_at)->format('l, F j, Y g:i A') }}
                    @if($completedHomeCheck->completed_by)
                        <br><strong>By:</strong> {{ $completedHomeCheck->completer->name ?? 'Agent' }}
                    @endif
                </p>
                @if($homecheckData->count() > 0)
                    <div style="margin-top: 15px; padding: 15px; background: #F9F9F9; border-radius: 6px;">
                        <h5 style="margin-top: 0; color: #1E1E1E;">Uploaded Data Summary</h5>
                        <p style="font-size: 13px; color: #666; margin-bottom: 10px;">
                            <strong>Total Images:</strong> {{ $homecheckData->count() }}
                            <br><strong>Rooms:</strong> {{ $homecheckData->groupBy('room_name')->count() }}
                            @if($homecheckData->whereNotNull('moisture_reading')->count() > 0)
                                <br><strong>Rooms with Moisture Readings:</strong> {{ $homecheckData->whereNotNull('moisture_reading')->groupBy('room_name')->count() }}
                            @endif
                        </p>
                        <details style="margin-top: 10px;">
                            <summary style="cursor: pointer; color: #2CB8B4; font-weight: 600; font-size: 13px;">View Room Details</summary>
                            <div style="margin-top: 10px;">
                                @foreach($homecheckData->groupBy('room_name') as $roomName => $roomImages)
                                    <div style="margin-bottom: 10px; padding: 10px; background: white; border-radius: 4px;">
                                        <strong>{{ $roomName }}</strong>
                                        <br><span style="font-size: 12px; color: #666;">
                                            {{ $roomImages->count() }} image(s)
                                            @if($roomImages->first()->moisture_reading)
                                                | Moisture: {{ $roomImages->first()->moisture_reading }}%
                                            @endif
                                            @if($roomImages->where('is_360', true)->count() > 0)
                                                | {{ $roomImages->where('is_360', true)->count() }} 360° image(s)
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    </div>
                @endif
            @elseif($activeHomeCheck)
                <p style="color: #ffc107; font-weight: 600;">
                    ⏳ HomeCheck {{ ucfirst(str_replace('_', ' ', $activeHomeCheck->status)) }}
                </p>
                <p style="font-size: 13px; color: #666; margin-top: 5px;">
                    @if($activeHomeCheck->scheduled_date)
                        <strong>Scheduled:</strong> {{ \Carbon\Carbon::parse($activeHomeCheck->scheduled_date)->format('l, F j, Y') }}
                    @endif
                    @if($activeHomeCheck->status === 'pending')
                        <br><a href="{{ route('admin.properties.complete-homecheck', $property->id) }}" class="btn btn-main" style="margin-top: 10px;">Start HomeCheck Upload</a>
                    @elseif($activeHomeCheck->status === 'scheduled')
                        <br><a href="{{ route('admin.properties.complete-homecheck', $property->id) }}" class="btn btn-main" style="margin-top: 10px;">Complete HomeCheck</a>
                    @elseif($activeHomeCheck->status === 'in_progress')
                        <br><a href="{{ route('admin.properties.complete-homecheck', $property->id) }}" class="btn btn-main" style="margin-top: 10px;">Continue HomeCheck Upload</a>
                    @endif
                </p>
            @else
                <p style="font-size: 14px; color: #666; margin-bottom: 10px;">
                    Schedule a HomeCheck appointment to capture 360° images and photos of the property.
                </p>
                <a href="{{ route('admin.properties.schedule-homecheck', $property->id) }}" class="btn btn-main">Schedule HomeCheck</a>
            @endif
        </div>
    </div>
    @endif

    @if($property->status === 'signed' || $property->status === 'draft')
        @php
            $hasPhotos = $property->photos && $property->photos->count() > 0;
            $hasFloorplan = $property->documents && $property->documents->where('document_type', 'floorplan')->count() > 0;
            $hasEPC = $property->documents && $property->documents->where('document_type', 'epc')->count() > 0;
        @endphp

        <div class="card" style="background: #E8F4F3;">
            <h3 style="color: var(--abodeology-teal); margin-top: 0;">Listing Preparation</h3>
            
            @if(!$hasPhotos || !$hasFloorplan || !$hasEPC)
                <p style="font-size: 14px; color: #666; margin-bottom: 15px;">
                    Upload photos, floorplan, and EPC to create a listing draft. Once ready, you can publish the listing to portals.
                </p>
                
                @if(!$hasPhotos)
                    <p style="color: #dc3545; font-size: 13px; margin-bottom: 8px;">✗ Photos required</p>
                @else
                    <p style="color: #28a745; font-size: 13px; margin-bottom: 8px;">✓ Photos uploaded ({{ $property->photos->count() }})</p>
                @endif
                
                @if(!$hasFloorplan)
                    <p style="color: #dc3545; font-size: 13px; margin-bottom: 8px;">✗ Floorplan (optional)</p>
                @else
                    <p style="color: #28a745; font-size: 13px; margin-bottom: 8px;">✓ Floorplan uploaded</p>
                @endif
                
                @if(!$hasEPC)
                    <p style="color: #dc3545; font-size: 13px; margin-bottom: 8px;">✗ EPC (optional)</p>
                @else
                    <p style="color: #28a745; font-size: 13px; margin-bottom: 8px;">✓ EPC uploaded</p>
                @endif
                
                <div style="margin-top: 20px;">
                    <a href="{{ route('admin.properties.listing-upload', $property->id) }}" class="btn btn-main">
                        {{ $hasPhotos ? 'Update Listing Materials' : 'Upload Listing Materials' }}
                    </a>
                </div>
            @else
                <p style="color: #28a745; font-weight: 600; margin-bottom: 15px;">
                    ✓ Listing draft ready! All materials have been uploaded.
                </p>
                <p style="font-size: 13px; color: #666; margin-bottom: 15px;">
                    You can now publish the listing to Rightmove and other portals.
                </p>
                <div style="margin-top: 20px;">
                    <form action="{{ route('admin.properties.publish', $property->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to publish this listing to all portals? This will set the status to \"Live on Market\".');">
                        @csrf
                        <button type="submit" class="btn btn-main">Publish Listing to Portals</button>
                    </form>
                    <a href="{{ route('admin.properties.generate-rtdf', $property->id) }}" class="btn" style="background: #28a745; color: #fff; margin-left: 10px;">Generate RTDF File</a>
                    <a href="{{ route('admin.properties.listing-upload', $property->id) }}" class="btn btn-secondary">Update Materials</a>
                </div>
            @endif
        </div>
    @endif

    @if($property->status === 'live')
        <div class="card" style="background: #d4edda;">
            <h3 style="color: #28a745; margin-top: 0;">✓ Listing Live on Market</h3>
            <p style="font-size: 14px; color: #666; margin-bottom: 15px;">
                This property is live and available for viewing requests.
            </p>
            <p style="font-size: 13px; color: #666;">
                <strong>Status:</strong> Live on Market<br>
                <strong>Published:</strong> {{ $property->updated_at->format('l, F j, Y g:i A') }}
            </p>
        </div>
    @endif

    <!-- OFFERS SECTION -->
    @if($property->offers && $property->offers->count() > 0)
        <div class="card">
            <h3 style="color: var(--abodeology-teal); margin-top: 0;">Offers Received ({{ $property->offers->count() }})</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                <thead>
                    <tr style="background: #F4F4F4;">
                        <th style="padding: 12px; border: 1px solid #dcdcdc; text-align: left;">Buyer</th>
                        <th style="padding: 12px; border: 1px solid #dcdcdc; text-align: left;">Offer Amount</th>
                        <th style="padding: 12px; border: 1px solid #dcdcdc; text-align: left;">Funding Type</th>
                        <th style="padding: 12px; border: 1px solid #dcdcdc; text-align: left;">Status</th>
                        <th style="padding: 12px; border: 1px solid #dcdcdc; text-align: left;">Submitted</th>
                        <th style="padding: 12px; border: 1px solid #dcdcdc; text-align: left;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($property->offers->sortByDesc('created_at') as $offer)
                        <tr>
                            <td style="padding: 12px; border: 1px solid #dcdcdc;">
                                <strong>{{ $offer->buyer->name ?? 'N/A' }}</strong>
                                @if($offer->buyer)
                                    <br><span style="font-size: 12px; color: #666;">{{ $offer->buyer->email }}</span>
                                    @if($offer->buyer->phone)
                                        <br><span style="font-size: 12px; color: #666;">{{ $offer->buyer->phone }}</span>
                                    @endif
                                @endif
                            </td>
                            <td style="padding: 12px; border: 1px solid #dcdcdc;">
                                <strong style="color: var(--abodeology-teal); font-size: 16px;">
                                    £{{ number_format($offer->offer_amount, 2) }}
                                </strong>
                                @if($offer->deposit_amount)
                                    <br><span style="font-size: 12px; color: #666;">Deposit: £{{ number_format($offer->deposit_amount, 2) }}</span>
                                @endif
                            </td>
                            <td style="padding: 12px; border: 1px solid #dcdcdc;">
                                {{ ucfirst(str_replace('_', ' ', $offer->funding_type ?? 'N/A')) }}
                                @if($offer->chain_position)
                                    <br><span style="font-size: 12px; color: #666;">{{ ucfirst(str_replace('-', ' ', $offer->chain_position)) }}</span>
                                @endif
                            </td>
                            <td style="padding: 12px; border: 1px solid #dcdcdc;">
                                @if($offer->status === 'pending')
                                    <span style="background: #ffc107; color: #000; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Pending</span>
                                @elseif($offer->status === 'accepted')
                                    <span style="background: #28a745; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Accepted</span>
                                @elseif($offer->status === 'declined')
                                    <span style="background: #dc3545; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Declined</span>
                                @elseif($offer->status === 'countered')
                                    <span style="background: #17a2b8; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Countered</span>
                                @else
                                    <span style="background: #6c757d; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">{{ ucfirst($offer->status) }}</span>
                                @endif
                            </td>
                            <td style="padding: 12px; border: 1px solid #dcdcdc; font-size: 13px; color: #666;">
                                {{ $offer->created_at->format('M j, Y') }}
                                <br>{{ $offer->created_at->format('g:i A') }}
                                @if($offer->released_to_seller && $offer->released_at)
                                    <br><span style="color: #28a745; font-size: 11px;">✓ Released {{ $offer->released_at->format('M j') }}</span>
                                @endif
                            </td>
                            <td style="padding: 12px; border: 1px solid #dcdcdc;">
                                @if($offer->status === 'pending')
                                    @if(!$offer->released_to_seller)
                                        <form action="{{ route('admin.offers.release', $offer->id) }}" method="POST" style="display: inline-block; margin-bottom: 5px;">
                                            @csrf
                                            <button type="submit" class="btn" style="background: #28a745; color: #fff; padding: 6px 12px; font-size: 13px; border: none; border-radius: 4px; cursor: pointer;">Release to Seller</button>
                                        </form>
                                        <br>
                                    @endif
                                    <a href="{{ route('seller.offer.decision.show', $offer->id) }}" class="btn btn-main" style="padding: 6px 12px; font-size: 13px;">Review Offer</a>
                                @elseif($offer->latestDecision)
                                    <div style="font-size: 12px;">
                                        <strong style="color: #1E1E1E;">Decision:</strong> {{ ucfirst($offer->latestDecision->decision) }}
                                        @if($offer->latestDecision->decision === 'counter' && $offer->latestDecision->counter_amount)
                                            <br><strong>Counter Amount:</strong> £{{ number_format($offer->latestDecision->counter_amount, 2) }}
                                        @endif
                                        @if($offer->latestDecision->comments)
                                            <br><strong>Comments:</strong> {{ Str::limit($offer->latestDecision->comments, 50) }}
                                        @endif
                                        @if($offer->latestDecision->decided_at)
                                            <br><strong>Date:</strong> {{ $offer->latestDecision->decided_at->format('M j, Y g:i A') }}
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @if($offer->conditions)
                            <tr>
                                <td colspan="6" style="padding: 8px 12px; border: 1px solid #dcdcdc; background: #F9F9F9; font-size: 13px;">
                                    <strong>Conditions:</strong> {{ $offer->conditions }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif($property->status === 'live')
        <div class="card" style="background: #F9F9F9;">
            <h3 style="color: var(--abodeology-teal); margin-top: 0;">Offers</h3>
            <p style="font-size: 14px; color: #666;">No offers received yet for this property.</p>
        </div>
    @endif
</div>
@endsection

