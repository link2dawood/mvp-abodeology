@extends('layouts.seller')

@section('title', 'Seller Dashboard')

@push('styles')
<style>
    body {
        font-family: Arial, sans-serif;
        background: #ffffff;
        margin: 0;
        padding: 0;
        color: #000000;
    }

    .container {
        max-width: 1100px;
        margin: 30px auto;
        padding: 20px;
    }

    h2 {
        border-bottom: 2px solid #000000;
        padding-bottom: 8px;
        margin-top: 40px;
        font-size: 24px;
        font-weight: 600;
    }

    .card {
        border: 1px solid #dcdcdc;
        padding: 20px;
        margin: 15px 0;
        border-radius: 3px;
    }

    .card strong {
        font-weight: 600;
    }

    .btn {
        background: #000000;
        color: #ffffff;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 2px;
        margin-right: 10px;
        display: inline-block;
        font-size: 14px;
        margin-top: 15px;
        transition: opacity 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn:hover {
        opacity: 0.85;
    }

    .status {
        padding: 8px 12px;
        background: #000000;
        color: #fff;
        display: inline-block;
        margin-bottom: 15px;
        border-radius: 2px;
        font-size: 14px;
        font-weight: 600;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    table th, table td {
        padding: 12px;
        border-bottom: 1px solid #dcdcdc;
        text-align: left;
        font-size: 14px;
    }

    table th {
        background: #f5f5f5;
        font-weight: 600;
    }

    table tr:last-child td {
        border-bottom: none;
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        .container {
            padding: 15px;
            margin: 20px auto;
        }

        h2 {
            font-size: 20px;
            margin-top: 30px;
        }

        .card {
            padding: 15px;
            margin: 12px 0;
        }

        .btn {
            width: 100%;
            margin: 8px 0;
            text-align: center;
            padding: 12px 20px;
        }

        table {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table th,
        table td {
            padding: 8px;
            font-size: 13px;
            white-space: nowrap;
        }

        .card > div[style*="display: flex"] {
            flex-direction: column;
        }

        .card > div[style*="display: flex"] > div:last-child {
            margin-top: 15px;
        }

        .card > div[style*="display: flex"] > div:last-child .btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .container {
            padding: 12px;
            margin: 15px auto;
        }

        h2 {
            font-size: 18px;
        }

        .card {
            padding: 12px;
        }

        table th,
        table td {
            padding: 6px;
            font-size: 12px;
        }

        .status {
            font-size: 12px;
            padding: 6px 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    @if(isset($valuations) && $valuations->count() > 0)
        <h2 style="margin-top: 0;">Upcoming Valuations</h2>
        <div style="margin-bottom: 30px;">
            @foreach($valuations as $valuation)
                @if($valuation->valuation_date && $valuation->valuation_date >= now()->toDateString())
                    <div class="card" style="margin-bottom: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div style="flex: 1;">
                                <strong style="font-size: 16px;">{{ $valuation->property_address }}</strong>
                                @if($valuation->postcode)
                                    <div style="color: #666; font-size: 14px; margin-top: 5px;">{{ $valuation->postcode }}</div>
                                @endif
                                <div style="margin-top: 10px; font-size: 14px;">
                                    <strong>Valuation Date:</strong> 
                                    {{ \Carbon\Carbon::parse($valuation->valuation_date)->format('l, F j, Y') }}
                                    @if($valuation->valuation_time)
                                        @ {{ \Carbon\Carbon::parse($valuation->valuation_time)->format('g:i A') }}
                                    @endif
                                </div>
                                @if($valuation->status)
                                    <div style="margin-top: 8px;">
                                        <span class="status">Status: {{ ucfirst($valuation->status) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; border: none; padding: 0;">Your Properties</h2>
        <a href="{{ route('seller.properties.create') }}" class="btn" style="margin: 0;">+ Create New Property</a>
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

    @if(isset($properties) && $properties->count() > 0)
        <div style="margin-bottom: 30px;">
            @foreach($properties as $prop)
                <div class="card" style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div style="flex: 1;">
                            <strong style="font-size: 16px;">{{ $prop->address }}</strong>
                            @if($prop->postcode)
                                <div style="color: #666; font-size: 14px; margin-top: 5px;">{{ $prop->postcode }}</div>
                            @endif
                            <div style="margin-top: 10px;">
                                <span class="status">Status: {{ $prop->status_text ?? 'Draft' }}</span>
                                @if($prop->asking_price)
                                    <span style="margin-left: 15px; font-size: 14px;">Asking Price: £{{ number_format($prop->asking_price, 0) }}</span>
                                @endif
                            </div>
                            @if($prop->valuation && ($prop->valuation->valuation_date || $prop->valuation->valuation_time))
                                <div style="margin-top: 10px; font-size: 14px; color: #666;">
                                    <strong>Valuation:</strong>
                                    @if($prop->valuation->valuation_date)
                                        {{ \Carbon\Carbon::parse($prop->valuation->valuation_date)->format('l, F j, Y') }}
                                    @endif
                                    @if($prop->valuation->valuation_time)
                                        @ {{ \Carbon\Carbon::parse($prop->valuation->valuation_time)->format('g:i A') }}
                                    @endif
                                </div>
                            @endif
                            @if($prop->status === 'awaiting_aml')
                                <div style="margin-top: 10px; padding: 10px; background: #fff3cd; border-left: 3px solid #ffc107; border-radius: 3px;">
                                    <p style="margin: 0 0 8px 0; font-size: 13px; color: #856404; font-weight: 600;">⚠️ Action Required: Upload AML Documents</p>
                                    <p style="margin: 0; font-size: 12px; color: #856404;">Please upload your ID and Proof of Address to proceed.</p>
                                    <a href="{{ route('seller.aml.upload', $prop->id) }}" class="btn" style="background: #ffc107; color: #000; margin-top: 8px; padding: 8px 16px; font-size: 13px;">Upload Now</a>
                                </div>
                            @elseif($prop->status === 'signed')
                                @php
                                    $hasAmlDocs = isset($amlCheck) && $amlCheck->id_document && $amlCheck->proof_of_address;
                                    $hasSolicitorDetails = $prop->solicitor_details_completed;
                                @endphp
                                @if(!$hasAmlDocs || !$hasSolicitorDetails)
                                    <div style="margin-top: 10px; padding: 10px; background: #E8F4F3; border-left: 3px solid #2CB8B4; border-radius: 3px;">
                                        <p style="margin: 0; font-size: 13px; color: #1E1E1E;"><strong>Action Required:</strong></p>
                                        <ul style="margin: 5px 0 0 0; padding-left: 20px; font-size: 13px; color: #1E1E1E;">
                                            @if(!$hasAmlDocs)
                                                <li>Upload AML documents (ID + Proof of Address)</li>
                                            @endif
                                            @if(!$hasSolicitorDetails)
                                                <li>Provide solicitor details</li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('seller.properties.show', $prop->id) }}" class="btn" style="margin: 5px 0;">View Details</a>
                            @if($prop->status === 'draft')
                                <a href="{{ route('seller.onboarding', $prop->id) }}" class="btn" style="margin: 5px 0; background: #2CB8B4;">Start Onboarding</a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if(isset($property) && $property)
            <h2>Property Overview</h2>
            <div class="card">
                <strong>Address:</strong> {{ $property->address }}<br><br>
                <div class="status">Status: {{ $property->status_text ?? 'Draft' }}</div>
                @if($property->asking_price)
                    <div style="margin-top: 10px;"><strong>Asking Price:</strong> £{{ number_format($property->asking_price, 0) }}</div>
                @endif
                <br>
                <a href="{{ route('seller.properties.show', $property->id) }}" class="btn">View Full Details</a>
                @if($property->status === 'live')
                    <a href="#" class="btn">View Live Listing</a>
                @endif
            </div>
        @endif
    @else
        <div class="card">
            <p style="margin: 0 0 20px 0; color: #666;">You haven't created any properties yet.</p>
            <a href="{{ route('seller.properties.create') }}" class="btn">Create Your First Property</a>
        </div>
    @endif

    @if(isset($property) && $property)
        <h2>HomeCheck</h2>
        <div class="card">
            @php
                $hasMaterialInfo = $property->materialInformation ? true : false;
                $homecheckCompleted = $hasMaterialInfo && $property->homecheckData && $property->homecheckData->count() > 0;
            @endphp
            <div class="status">HomeCheck: {{ $homecheckCompleted ? 'Completed' : 'Pending' }}</div>
            @if($homecheckCompleted)
                <a href="#" class="btn">View HomeCheck Report</a>
                <a href="{{ route('seller.homecheck.upload', $property->id) }}" class="btn">Upload Additional Images</a>
            @elseif($property->status !== 'draft')
                <a href="{{ route('seller.homecheck.upload', $property->id) }}" class="btn">Complete HomeCheck</a>
            @else
                <p style="color: #666; margin-top: 10px;">Please complete onboarding first.</p>
            @endif
        </div>

        <h2>Upcoming Viewings</h2>
        <div class="card">
            <table>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Property</th>
                    <th>Buyer</th>
                    <th>Status</th>
                </tr>
                @forelse($upcomingViewings ?? [] as $viewing)
                    <tr>
                        <td>{{ $viewing->viewing_date ? $viewing->viewing_date->format('d M Y') : 'N/A' }}</td>
                        <td>{{ $viewing->viewing_date ? $viewing->viewing_date->format('H:i') : 'N/A' }}</td>
                        <td>{{ $viewing->property->address ?? 'N/A' }}</td>
                        <td>{{ $viewing->buyer->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($viewing->status ?? 'Booked') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #999;">No upcoming viewings</td>
                    </tr>
                @endforelse
            </table>
        </div>

        <h2>Pending Offers</h2>
        <div class="card">
            <table>
                <tr>
                    <th>Property</th>
                    <th>Buyer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @forelse($offers ?? [] as $offer)
                    <tr>
                        <td>{{ $offer->property->address ?? 'N/A' }}</td>
                        <td>{{ $offer->buyer->name ?? 'N/A' }}</td>
                        <td>£{{ number_format($offer->offer_amount ?? 0, 0) }}</td>
                        <td>{{ ucfirst($offer->status ?? 'Pending') }}</td>
                        <td>
                            @if($offer->status === 'pending')
                                <a href="{{ route('seller.offer.decision', $offer->id) }}" class="btn" style="padding: 6px 12px; font-size: 13px;">Review</a>
                            @else
                                <span style="color: #666; font-size: 13px;">{{ ucfirst($offer->status) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #999;">No pending offers</td>
                    </tr>
                @endforelse
            </table>
        </div>

        <h2>Documents</h2>
        <div class="card">
            <a href="#" class="btn">Download Terms & Conditions</a>
            @if($property->status === 'sstc' || $property->status === 'sold')
                <a href="#" class="btn">Download Memorandum of Sale</a>
            @endif
        </div>
    @else
        <div style="margin-top: 40px;">
            <h2>Get Started</h2>
            <div class="card">
                <p style="margin: 0 0 15px 0; color: #666;">Create your first property to start the selling process.</p>
                <a href="{{ route('seller.properties.create') }}" class="btn">Create Your First Property</a>
            </div>
        </div>
    @endif
</div>
@endsection
