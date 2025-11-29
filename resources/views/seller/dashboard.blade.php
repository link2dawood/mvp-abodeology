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
        background: #2CB8B4;
        color: #ffffff;
        padding: 10px 20px;
        text-decoration: none;
        border-radius: 2px;
        margin-right: 10px;
        display: inline-block;
        font-size: 14px;
        margin-top: 15px;
        transition: background 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn:hover {
        background: #25A29F;
    }

    .status {
        padding: 8px 12px;
        background: #2CB8B4;
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
                    <a href="{{ route('buyer.viewing.request', $property->id) }}" class="btn">View Live Listing</a>
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
        <!-- MATERIAL INFORMATION -->
        @if(isset($materialInfo) && $materialInfo->count() > 0)
            <h2>Material Information</h2>
            <div class="card">
                <table>
                    <tr>
                        <th>Property</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Action</th>
                    </tr>
                    @foreach($materialInfo as $material)
                        <tr>
                            <td>{{ $material->property->address ?? 'N/A' }}</td>
                            <td>
                                @if($material->completed)
                                    <span style="background: #28a745; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Completed</span>
                                @else
                                    <span style="background: #ffc107; color: #000; padding: 4px 8px; border-radius: 4px; font-size: 12px;">In Progress</span>
                                @endif
                            </td>
                            <td>{{ $material->updated_at->format('M j, Y') }}</td>
                            <td>
                                <a href="{{ route('seller.properties.show', $material->property_id) }}" class="btn" style="padding: 6px 12px; font-size: 13px;">View</a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        <!-- HOMECHECK -->
        <h2>HomeCheck Reports</h2>
        <div class="card">
            @if(isset($homecheckReports) && $homecheckReports->count() > 0)
                <table>
                    <tr>
                        <th>Property</th>
                        <th>Status</th>
                        <th>Rooms</th>
                        <th>Images</th>
                        <th>Completed</th>
                        <th>Action</th>
                    </tr>
                    @foreach($homecheckReports as $report)
                        <tr>
                            <td>{{ $report->property->address ?? 'N/A' }}</td>
                            <td>
                                @if($report->status === 'completed')
                                    <span style="background: #28a745; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Completed</span>
                                @elseif($report->status === 'in_progress')
                                    <span style="background: #ffc107; color: #000; padding: 4px 8px; border-radius: 4px; font-size: 12px;">In Progress</span>
                                @else
                                    <span style="background: #6c757d; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ ucfirst($report->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($report->homecheckData)
                                    {{ $report->homecheckData->groupBy('room_name')->count() }} rooms
                                @else
                                    0 rooms
                                @endif
                            </td>
                            <td>
                                @if($report->homecheckData)
                                    {{ $report->homecheckData->count() }} images
                                @else
                                    0 images
                                @endif
                            </td>
                            <td>
                                @if($report->completed_at)
                                    {{ $report->completed_at->format('M j, Y') }}
                                @else
                                    <span style="color: #999;">Not completed</span>
                                @endif
                            </td>
                            <td>
                                @if($report->status === 'completed')
                                    <a href="{{ route('seller.homecheck.report', $report->property_id) }}" target="_blank" class="btn" style="padding: 6px 12px; font-size: 13px;">View Report</a>
                                @else
                                    <a href="{{ route('seller.homecheck.upload', $report->property_id) }}" class="btn" style="padding: 6px 12px; font-size: 13px;">Continue</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p style="text-align: center; color: #999; padding: 20px;">No HomeCheck reports yet</p>
                @if(isset($property) && $property->status !== 'draft')
                    <a href="{{ route('seller.homecheck.upload', $property->id) }}" class="btn">Start HomeCheck</a>
                @endif
            @endif
        </div>

        <!-- VIEWING FEEDBACK SUMMARY -->
        @if(isset($viewingFeedbackSummary) && $viewingFeedbackSummary->count() > 0)
            <h2>Viewing Feedback Summary</h2>
            <div class="card">
                <div style="margin-bottom: 15px; padding: 12px; background: #E8F4F3; border-radius: 6px;">
                    <strong style="color: var(--abodeology-teal);">You have received feedback for {{ $viewingFeedbackSummary->count() }} viewing(s)</strong>
                </div>
                <table>
                    <tr>
                        <th>Property</th>
                        <th>Buyer</th>
                        <th>Viewing Date</th>
                        <th>Interest</th>
                        <th>Property Condition</th>
                        <th>PVA</th>
                        <th>Feedback</th>
                    </tr>
                    @foreach($viewingFeedbackSummary as $feedback)
                        <tr>
                            <td>
                                <strong>{{ $feedback['property_address'] ?? 'N/A' }}</strong>
                            </td>
                            <td>{{ $feedback['buyer_name'] ?? 'N/A' }}</td>
                            <td>
                                @if($feedback['viewing_date'])
                                    {{ \Carbon\Carbon::parse($feedback['viewing_date'])->format('M j, Y') }}
                                    <br><span style="font-size: 12px; color: #666;">{{ \Carbon\Carbon::parse($feedback['viewing_date'])->format('g:i A') }}</span>
                                @else
                                    <span style="color: #999;">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($feedback['buyer_interested']))
                                    @if($feedback['buyer_interested'])
                                        <span style="background: #28a745; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">✓ Interested</span>
                                    @else
                                        <span style="background: #dc3545; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">✗ Not Interested</span>
                                    @endif
                                @else
                                    <span style="color: #999;">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($feedback['property_condition'])
                                    <span style="text-transform: capitalize;">{{ $feedback['property_condition'] }}</span>
                                @else
                                    <span style="color: #999;">Not rated</span>
                                @endif
                            </td>
                            <td>{{ $feedback['pva_name'] ?? 'N/A' }}</td>
                            <td>
                                @if($feedback['buyer_feedback'])
                                    <span style="font-size: 12px;">{{ Str::limit($feedback['buyer_feedback'], 50) }}</span>
                                @else
                                    <span style="color: #999;">No feedback</span>
                                @endif
                            </td>
                        </tr>
                        @if($feedback['buyer_feedback'])
                            <tr>
                                <td colspan="7" style="padding: 8px 12px; background: #F9F9F9; font-size: 13px;">
                                    <strong>Full Feedback:</strong> {{ $feedback['buyer_feedback'] }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
        @endif

        <!-- SALES PROGRESSION -->
        @if(isset($salesProgression) && $salesProgression->count() > 0)
            <h2>Sales Progression</h2>
            <div class="card">
                <table>
                    <tr>
                        <th>Property</th>
                        <th>Buyer</th>
                        <th>Sale Price</th>
                        <th>MoS Issued</th>
                        <th>Exchange Date</th>
                        <th>Completion Date</th>
                        <th>Progress</th>
                    </tr>
                    @foreach($salesProgression as $progression)
                        <tr>
                            <td>
                                <strong>{{ $progression->property->address ?? 'N/A' }}</strong>
                            </td>
                            <td>{{ $progression->buyer->name ?? 'N/A' }}</td>
                            <td>
                                @if($progression->offer)
                                    <strong style="color: var(--abodeology-teal);">£{{ number_format($progression->offer->offer_amount, 0) }}</strong>
                                @else
                                    <span style="color: #999;">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($progression->memorandum_of_sale_issued)
                                    <span style="background: #28a745; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px;">✓ Yes</span>
                                @else
                                    <span style="background: #ffc107; color: #000; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Pending</span>
                                @endif
                            </td>
                            <td>
                                @if($progression->exchange_date)
                                    {{ \Carbon\Carbon::parse($progression->exchange_date)->format('M j, Y') }}
                                @else
                                    <span style="color: #999;">Not set</span>
                                @endif
                            </td>
                            <td>
                                @if($progression->completion_date)
                                    {{ \Carbon\Carbon::parse($progression->completion_date)->format('M j, Y') }}
                                @else
                                    <span style="color: #999;">Not set</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $steps = 0;
                                    $total = 6;
                                    if ($progression->memorandum_of_sale_issued) $steps++;
                                    if ($progression->enquiries_raised) $steps++;
                                    if ($progression->enquiries_answered) $steps++;
                                    if ($progression->searches_ordered) $steps++;
                                    if ($progression->searches_received) $steps++;
                                    if ($progression->exchange_date) $steps++;
                                    $percentage = ($steps / $total) * 100;
                                @endphp
                                <div style="width: 100px; background: #f0f0f0; border-radius: 4px; height: 20px; position: relative;">
                                    <div style="width: {{ $percentage }}%; background: var(--abodeology-teal); height: 100%; border-radius: 4px;"></div>
                                    <span style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-size: 11px; font-weight: 600; color: #333;">{{ round($percentage) }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        <!-- VIEWING SCHEDULE -->
        <h2>Viewing Schedule</h2>
        <div class="card">
            @if(isset($upcomingViewings) && $upcomingViewings->count() > 0)
                <div style="margin-bottom: 15px; padding: 12px; background: #E8F4F3; border-radius: 6px;">
                    <strong style="color: var(--abodeology-teal);">You have {{ $upcomingViewings->count() }} upcoming viewing(s)</strong>
                </div>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Property</th>
                        <th>Buyer</th>
                        <th>Contact</th>
                        <th>Status</th>
                    </tr>
                    @foreach($upcomingViewings as $viewing)
                        <tr>
                            <td>
                                <strong>{{ $viewing->viewing_date ? $viewing->viewing_date->format('l, M j') : 'N/A' }}</strong>
                                <br><span style="font-size: 12px; color: #666;">{{ $viewing->viewing_date ? $viewing->viewing_date->format('Y') : '' }}</span>
                            </td>
                            <td>
                                <strong>{{ $viewing->viewing_date ? $viewing->viewing_date->format('g:i A') : 'N/A' }}</strong>
                            </td>
                            <td>
                                <strong>{{ $viewing->property->address ?? 'N/A' }}</strong>
                                @if($viewing->property->postcode)
                                    <br><span style="font-size: 12px; color: #666;">{{ $viewing->property->postcode }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $viewing->buyer->name ?? 'N/A' }}</strong>
                            </td>
                            <td>
                                @if($viewing->buyer)
                                    @if($viewing->buyer->email)
                                        <span style="font-size: 12px;">{{ $viewing->buyer->email }}</span>
                                    @endif
                                    @if($viewing->buyer->phone)
                                        <br><span style="font-size: 12px;">{{ $viewing->buyer->phone }}</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <span style="background: var(--abodeology-teal); color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    {{ ucfirst($viewing->status ?? 'Scheduled') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @else
                <p style="text-align: center; color: #999; padding: 20px;">No upcoming viewings scheduled</p>
            @endif
            
            @if(isset($allViewings) && $allViewings->count() > ($upcomingViewings->count() ?? 0))
                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #dcdcdc;">
                    <h4 style="margin: 0 0 10px 0; font-size: 16px;">All Viewings ({{ $allViewings->count() }})</h4>
                    <table>
                        <tr>
                            <th>Date</th>
                            <th>Property</th>
                            <th>Buyer</th>
                            <th>Status</th>
                        </tr>
                        @foreach($allViewings->take(10) as $viewing)
                            <tr>
                                <td>{{ $viewing->viewing_date ? $viewing->viewing_date->format('M j, Y g:i A') : 'N/A' }}</td>
                                <td>{{ Str::limit($viewing->property->address ?? 'N/A', 30) }}</td>
                                <td>{{ $viewing->buyer->name ?? 'N/A' }}</td>
                                <td>{{ ucfirst($viewing->status ?? 'N/A') }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endif
        </div>

        <!-- OFFERS PANEL -->
        <h2>Offers</h2>
        <div class="card">
            @if(isset($offers) && $offers->count() > 0)
                <div style="margin-bottom: 15px; padding: 12px; background: #E8F4F3; border-radius: 6px;">
                    <strong style="color: var(--abodeology-teal);">You have {{ $offers->count() }} pending offer(s) requiring your attention</strong>
                </div>
                <table>
                    <tr>
                        <th>Property</th>
                        <th>Buyer</th>
                        <th>Offer Amount</th>
                        <th>Asking Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    @foreach($offers as $offer)
                        <tr>
                            <td>
                                <strong>{{ $offer->property->address ?? 'N/A' }}</strong>
                                @if($offer->property->postcode)
                                    <br><span style="font-size: 12px; color: #666;">{{ $offer->property->postcode }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $offer->buyer->name ?? 'N/A' }}</strong>
                                @if($offer->buyer && $offer->buyer->email)
                                    <br><span style="font-size: 12px; color: #666;">{{ $offer->buyer->email }}</span>
                                @endif
                            </td>
                            <td>
                                <strong style="color: var(--abodeology-teal); font-size: 16px;">
                                    £{{ number_format($offer->offer_amount ?? 0, 0) }}
                                </strong>
                                @if($offer->deposit_amount)
                                    <br><span style="font-size: 12px; color: #666;">Deposit: £{{ number_format($offer->deposit_amount, 0) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($offer->property->asking_price)
                                    £{{ number_format($offer->property->asking_price, 0) }}
                                    @php
                                        $difference = $offer->offer_amount - $offer->property->asking_price;
                                        $percentage = ($difference / $offer->property->asking_price) * 100;
                                    @endphp
                                    <br>
                                    @if($difference > 0)
                                        <span style="font-size: 12px; color: #28a745;">+{{ number_format($percentage, 1) }}%</span>
                                    @elseif($difference < 0)
                                        <span style="font-size: 12px; color: #dc3545;">{{ number_format($percentage, 1) }}%</span>
                                    @else
                                        <span style="font-size: 12px; color: #666;">At asking</span>
                                    @endif
                                @else
                                    <span style="color: #999;">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($offer->status === 'pending')
                                    <span style="background: #ffc107; color: #000; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Pending</span>
                                @elseif($offer->status === 'countered')
                                    <span style="background: #17a2b8; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">Countered</span>
                                @else
                                    <span style="background: #6c757d; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">{{ ucfirst($offer->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($offer->status === 'pending')
                                    <a href="{{ route('seller.offer.decision', $offer->id) }}" class="btn" style="padding: 8px 16px; font-size: 14px; background: #28a745; margin-bottom: 5px; display: block; text-align: center;">
                                        ✓ Accept Offer
                                    </a>
                                    <a href="{{ route('seller.offer.decision', $offer->id) }}" class="btn" style="padding: 6px 12px; font-size: 13px; background: var(--abodeology-teal); display: block; text-align: center;">
                                        Review Details
                                    </a>
                                @elseif($offer->status === 'countered')
                                    <a href="{{ route('seller.offer.decision', $offer->id) }}" class="btn" style="padding: 6px 12px; font-size: 13px;">View Response</a>
                                @else
                                    <span style="color: #666; font-size: 13px;">{{ ucfirst($offer->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @if($offer->conditions)
                            <tr>
                                <td colspan="6" style="padding: 8px 12px; background: #F9F9F9; font-size: 13px;">
                                    <strong>Conditions:</strong> {{ $offer->conditions }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            @else
                <p style="text-align: center; color: #999; padding: 20px;">No pending offers</p>
            @endif
            
            @if(isset($allOffers) && $allOffers->count() > $offers->count())
                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #dcdcdc;">
                    <h4 style="margin: 0 0 10px 0; font-size: 16px;">All Offers ({{ $allOffers->count() }})</h4>
                    <table>
                        <tr>
                            <th>Property</th>
                            <th>Buyer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                        @foreach($allOffers as $offer)
                            <tr>
                                <td>{{ Str::limit($offer->property->address ?? 'N/A', 30) }}</td>
                                <td>{{ $offer->buyer->name ?? 'N/A' }}</td>
                                <td>£{{ number_format($offer->offer_amount ?? 0, 0) }}</td>
                                <td>
                                    @if($offer->status === 'accepted')
                                        <span style="background: #28a745; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Accepted</span>
                                    @elseif($offer->status === 'declined')
                                        <span style="background: #dc3545; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Declined</span>
                                    @else
                                        <span style="background: #6c757d; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ ucfirst($offer->status) }}</span>
                                    @endif
                                </td>
                                <td style="font-size: 12px; color: #666;">
                                    {{ $offer->created_at->format('M j, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endif
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
