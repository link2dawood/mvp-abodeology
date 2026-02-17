@extends('layouts.admin')

@section('title', 'Valuation Details')

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
        margin-bottom: 18px;
        padding-bottom: 12px;
        font-size: 20px;
        font-weight: 600;
        color: var(--abodeology-teal);
        border-bottom: 1px solid var(--line-grey);
    }

    .schedule-form .form-group {
        margin-bottom: 18px;
    }

    .schedule-form .form-group:last-of-type {
        margin-bottom: 14px;
    }

    .schedule-form label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        color: #333;
        margin-bottom: 6px;
    }

    .schedule-form .form-control {
        display: block;
        padding: 10px 12px;
        border-radius: 6px;
        border: 1px solid var(--line-grey);
        font-size: 14px;
        width: 100%;
        max-width: 280px;
        box-sizing: border-box;
    }

    .schedule-form .form-control:focus {
        outline: none;
        border-color: var(--abodeology-teal);
        box-shadow: 0 0 0 2px rgba(44, 184, 180, 0.15);
    }

    .schedule-form .form-note {
        font-size: 12px;
        color: #888;
        margin: 12px 0 18px 0;
        line-height: 1.5;
        padding: 0;
        background: transparent;
        border: none;
    }

    .schedule-form .form-note strong {
        color: #555;
    }

    .schedule-form .form-actions {
        margin-top: 4px;
        padding-top: 18px;
        border-top: 1px solid var(--line-grey);
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
        min-width: 140px;
        color: #666;
    }

    .top-cards {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    @media (max-width: 900px) {
        .top-cards {
            grid-template-columns: 1fr;
        }
    }
    .top-cards .card {
        margin-bottom: 0;
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

    .status-pending { 
        background: #F4C542; 
        color: #000; 
    }

    .status-scheduled { 
        background: var(--abodeology-teal); 
        color: #FFF; 
    }

    .status-completed { 
        background: #28a745; 
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
            <h2>Valuation Request Details</h2>
        </div>
        <div>
            <a href="{{ route('admin.valuations.index') }}" class="btn btn-secondary">Back to List</a>
            @if($valuation->status !== 'completed')
                <a href="{{ route('admin.valuations.onboarding', $valuation->id) }}" class="btn btn-main">Complete Onboarding</a>
            @endif
            <form action="{{ route('admin.valuations.resend-login-credentials', $valuation->id) }}" method="POST" style="display: inline-block; margin-left: 0;">
                @csrf
                <button type="submit" class="btn" style="background: #6c757d; color: #fff;">Resend login credentials</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Client details and Valuation information at top, side by side --}}
    <div class="top-cards">
        <div class="card">
            <h3>Client Information</h3>
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value">{{ $valuation->seller->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $valuation->seller->email ?? 'N/A' }}</div>
            </div>
            @if($valuation->seller->phone)
            <div class="info-row">
                <div class="info-label">Phone:</div>
                <div class="info-value">{{ $valuation->seller->phone }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Role:</div>
                <div class="info-value">{{ ucfirst($valuation->seller->role ?? 'N/A') }}</div>
            </div>
        </div>
        <div class="card">
            <h3>Valuation Information</h3>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status status-{{ $valuation->status }}">
                        {{ ucfirst($valuation->status) }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Property Address:</div>
                <div class="info-value">{{ $valuation->property_address }}</div>
            </div>
            @if($valuation->postcode)
            <div class="info-row">
                <div class="info-label">Postcode:</div>
                <div class="info-value">{{ $valuation->postcode }}</div>
            </div>
            @endif
            @if($valuation->property_type)
            <div class="info-row">
                <div class="info-label">Property Type:</div>
                <div class="info-value">{{ ucfirst(str_replace('_', ' ', $valuation->property_type)) }}</div>
            </div>
            @endif
            @if($valuation->bedrooms)
            <div class="info-row">
                <div class="info-label">Bedrooms:</div>
                <div class="info-value">{{ $valuation->bedrooms }}</div>
            </div>
            @endif
            @if($valuation->valuation_date)
            <div class="info-row">
                <div class="info-label">Preferred Date:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($valuation->valuation_date)->format('l, F j, Y') }}</div>
            </div>
            @endif
            @if($valuation->valuation_time)
            <div class="info-row">
                <div class="info-label">Preferred Time:</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($valuation->valuation_time)->format('g:i A') }}</div>
            </div>
            @endif
            @if($valuation->estimated_value)
            <div class="info-row">
                <div class="info-label">Estimated Value:</div>
                <div class="info-value">£{{ number_format($valuation->estimated_value, 2) }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Request Date:</div>
                <div class="info-value">{{ $valuation->created_at->format('l, F j, Y g:i A') }}</div>
            </div>
            @if($valuation->agent)
            <div class="info-row">
                <div class="info-label">Assigned Agent/Valuer:</div>
                <div class="info-value">{{ $valuation->agent->name }} ({{ $valuation->agent->email }})</div>
            </div>
            @else
            <div class="info-row">
                <div class="info-label">Assigned Agent/Valuer:</div>
                <div class="info-value" style="color: #999; font-style: italic;">Not assigned</div>
            </div>
            @endif
        </div>
    </div>

    @if(in_array(auth()->user()->role, ['admin', 'agent']))
    <div class="card">
        <h3>Schedule Valuation (Admin/Agent)</h3>
        <form class="schedule-form" action="{{ route('admin.valuations.schedule', $valuation->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="valuation_date">Valuation Date</label>
                <input
                    type="date"
                    id="valuation_date"
                    name="valuation_date"
                    class="form-control"
                    value="{{ old('valuation_date', $valuation->valuation_date ? \Carbon\Carbon::parse($valuation->valuation_date)->format('Y-m-d') : '') }}"
                >
            </div>
            <div class="form-group">
                <label for="valuation_time">Valuation Time (optional)</label>
                @php
                    $selectedValuationTime = old(
                        'valuation_time',
                        $valuation->valuation_time ? \Carbon\Carbon::parse($valuation->valuation_time)->format('H:i') : ''
                    );
                @endphp
                <select id="valuation_time" name="valuation_time" class="form-control">
                    <option value="">-- No time selected --</option>
                    @for ($h = 0; $h < 24; $h++)
                        @foreach (['00', '30'] as $m)
                            @php $timeValue = sprintf('%02d:%s', $h, $m); @endphp
                            <option value="{{ $timeValue }}" {{ $selectedValuationTime === $timeValue ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromFormat('H:i', $timeValue)->format('g:i A') }}
                            </option>
                        @endforeach
                    @endfor
                </select>
            </div>
            @if($agents)
            <div class="form-group">
                <label for="agent_id">Assign Agent/Valuer</label>
                <select id="agent_id" name="agent_id" class="form-control">
                    <option value="">-- No Agent/Valuer Assigned --</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" {{ $valuation->agent_id == $agent->id ? 'selected' : '' }}>
                            {{ $agent->name }} ({{ $agent->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <p class="form-note">Status updates automatically: <strong>Scheduled</strong> when a date is set, <strong>Pending</strong> when the date is cleared. <strong>Completed</strong> when the valuation form is submitted.</p>
            <div class="form-actions">
                <button type="submit" class="btn btn-main">Save Schedule</button>
            </div>
        </form>
    </div>
    @endif

    @if($valuation->seller_notes)
    <div class="card">
        <h3>Notes from Client</h3>
        <p>{{ $valuation->seller_notes }}</p>
    </div>
    @endif

    @if($valuation->id_visual_check)
    <div class="card" style="background: #d4edda;">
        <h3 style="color: #28a745; margin-top: 0;">✓ ID Visual Check Completed</h3>
        <p style="margin: 5px 0;"><strong>Status:</strong> ID document visually checked on-site</p>
        @if($valuation->id_visual_check_notes)
        <p style="margin: 5px 0;"><strong>Notes:</strong> {{ $valuation->id_visual_check_notes }}</p>
        @endif
    </div>
    @endif

    @if($valuation->notes)
    <div class="card">
        <h3>Agent Notes</h3>
        <p>{{ $valuation->notes }}</p>
    </div>
    @endif

    @if($valuation->status !== 'completed')
    <div class="card" style="background: #E8F4F3;">
        <h3 style="color: var(--abodeology-teal); margin-top: 0;">Valuation Form</h3>
        <p>Complete the Valuation Form during the on-site valuation appointment. The form will be pre-filled with seller and property details.</p>
        <p><strong>This form captures:</strong></p>
        <ul>
            <li>ID Visual Check confirmation (required by HMRC/EA Act)</li>
            <li>Detailed property information</li>
            <li>Material information</li>
            <li>Access notes and viewing preferences</li>
            <li>Pricing notes</li>
        </ul>
        <p style="font-size: 13px; color: #666; margin-top: 10px;">
            <em>Form will be saved directly to the seller's profile with status "Property Details Captured".</em>
        </p>
        <a href="{{ route('admin.valuations.valuation-form', $valuation->id) }}" class="btn btn-main" style="margin-top: 15px;">Start Valuation Form</a>
        @if($property)
            <a href="{{ route('admin.properties.show', $property->id) }}" class="btn" style="background: var(--abodeology-teal); margin-top: 15px; margin-left: 10px;">View Property</a>
        @endif
    </div>
    @else
    <div class="card" style="background: #d4edda;">
        <h3 style="color: #28a745; margin-top: 0;">✓ Valuation Completed</h3>
        <p>This valuation has been completed. The Valuation Form has been submitted and property details have been captured.</p>
        @if($property)
            <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-main" style="margin-top: 15px;">View Property</a>
        @endif
    </div>
    @endif

    {{-- HomeCheck Status Section --}}
    @if($property)
    <div class="card" style="background: #F9F9F9;">
        <h3 style="color: var(--abodeology-teal); margin-top: 0;">HomeCheck Status</h3>
        
        @if($completedHomeCheck)
            <div style="padding: 15px; background: #d4edda; border-radius: 6px; margin-bottom: 15px;">
                <p style="color: #28a745; font-weight: 600; margin: 0 0 10px 0;">
                    ✓ HomeCheck Completed
                </p>
                <p style="font-size: 13px; color: #666; margin: 5px 0;">
                    <strong>Completed:</strong> {{ $completedHomeCheck->completed_at ? \Carbon\Carbon::parse($completedHomeCheck->completed_at)->format('l, F j, Y g:i A') : 'N/A' }}
                    @if($completedHomeCheck->completer)
                        <br><strong>By:</strong> {{ $completedHomeCheck->completer->name ?? 'Agent' }}
                    @endif
                </p>
                <div style="margin-top: 10px;">
                    <a href="{{ route('admin.homechecks.show', $completedHomeCheck->id) }}" class="btn btn-main" style="margin-right: 10px;">View HomeCheck</a>
                    <a href="{{ route('admin.homechecks.edit', $completedHomeCheck->id) }}" class="btn" style="background: #6c757d; color: #fff; margin-right: 10px;">Edit</a>
                    <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-secondary">View Property</a>
                </div>
            </div>
        @elseif($activeHomeCheck)
            <div style="padding: 15px; background: #fff3cd; border-radius: 6px; margin-bottom: 15px;">
                <p style="color: #856404; font-weight: 600; margin: 0 0 10px 0;">
                    ⏳ HomeCheck {{ ucfirst(str_replace('_', ' ', $activeHomeCheck->status)) }}
                </p>
                <p style="font-size: 13px; color: #666; margin: 5px 0;">
                    @if($activeHomeCheck->scheduled_date)
                        <strong>Scheduled:</strong> {{ \Carbon\Carbon::parse($activeHomeCheck->scheduled_date)->format('l, F j, Y') }}
                    @endif
                </p>
                <div style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap;">
                    @if($activeHomeCheck->status === 'pending')
                        <a href="{{ route('admin.properties.complete-homecheck', $property->id) }}" class="btn btn-main">Start HomeCheck Upload</a>
                    @elseif($activeHomeCheck->status === 'scheduled')
                        <a href="{{ route('admin.properties.complete-homecheck', $property->id) }}" class="btn btn-main">Complete HomeCheck</a>
                    @elseif($activeHomeCheck->status === 'in_progress')
                        <a href="{{ route('admin.properties.complete-homecheck', $property->id) }}" class="btn btn-main">Continue HomeCheck Upload</a>
                    @endif
                    <a href="{{ route('admin.homechecks.edit', $activeHomeCheck->id) }}" class="btn" style="background: #6c757d; color: #fff;">Edit HomeCheck</a>
                    <a href="{{ route('admin.properties.show', $property->id) }}" class="btn" style="background: var(--abodeology-teal); color: #fff;">View Property</a>
                </div>
            </div>
        @else
            <div style="padding: 15px; background: #fff; border-radius: 6px; margin-bottom: 15px;">
                <p style="font-size: 14px; color: #666; margin-bottom: 15px;">
                    No HomeCheck has been scheduled yet. Schedule a HomeCheck appointment to capture 360° images and photos of the property.
                </p>
                @if(in_array(auth()->user()->role, ['admin', 'agent']))
                    <a href="{{ route('admin.properties.schedule-homecheck', $property->id) }}" class="btn btn-main">Schedule HomeCheck</a>
                    <a href="{{ route('admin.properties.show', $property->id) }}" class="btn" style="background: var(--abodeology-teal); color: #fff; margin-left: 10px;">View Property</a>
                @endif
            </div>
        @endif
    </div>
    @elseif($valuation->status === 'completed')
    <div class="card" style="background: #F9F9F9; border-left: 4px solid #ffc107;">
        <h3 style="color: #856404; margin-top: 0;">HomeCheck Status</h3>
        <p style="font-size: 14px; color: #666; margin-bottom: 10px;">
            Property must be created first before scheduling a HomeCheck. Complete the Valuation Form to create the property.
        </p>
        <a href="{{ route('admin.valuations.valuation-form', $valuation->id) }}" class="btn btn-main">Complete Valuation Form</a>
    </div>
    @endif
</div>
@endsection

