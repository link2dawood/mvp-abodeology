@extends('layouts.seller')

@section('title', 'Offer Decision')

@push('styles')
<style>
    /* BRAND */
    :root {
        --abodeology-teal: #2CB8B4;
        --black: #0F0F0F;
        --white: #FFFFFF;
        --soft-grey: #F4F4F4;
        --dark-text: #1E1E1E;
        --line-grey: #EAEAEA;
        --danger: #E14F4F;
        --warning: #F4C542;
    }

    /* PAGE WRAPPER */
    .container {
        max-width: 1180px;
        margin: 35px auto;
        padding: 0 22px;
    }

    h2 {
        margin-bottom: 8px;
        font-size: 28px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 25px;
    }

    /* CARD */
    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        margin-bottom: 30px;
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 20px;
    }

    .card p {
        margin: 10px 0;
        font-size: 14px;
        line-height: 1.6;
    }

    .card p strong {
        font-weight: 600;
    }

    /* BADGES */
    .badge {
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        display: inline-block;
    }

    .badge-good {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .badge-warn {
        background: var(--warning);
        color: var(--black);
    }

    .badge-bad {
        background: var(--danger);
        color: var(--white);
    }

    /* TABLE */
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .table th {
        background: var(--abodeology-teal);
        color: var(--white);
        padding: 10px;
        text-align: left;
        font-size: 14px;
        width: 30%;
    }

    .table td {
        padding: 10px;
        border-bottom: 1px solid var(--line-grey);
        font-size: 14px;
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    /* TEXTAREAS */
    textarea {
        width: 100%;
        padding: 14px;
        border-radius: 6px;
        border: 1px solid #D9D9D9;
        font-size: 15px;
        margin-top: 10px;
        height: 120px;
        resize: vertical;
        outline: none;
        box-sizing: border-box;
        font-family: inherit;
    }

    textarea:focus {
        border-color: var(--abodeology-teal);
    }

    textarea.error {
        border-color: #dc3545;
    }

    /* ERROR MESSAGES */
    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
        text-align: left;
    }

    /* SUCCESS MESSAGE */
    .success-message {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-radius: 6px;
        padding: 12px;
        margin-bottom: 20px;
        color: #155724;
        font-size: 14px;
        text-align: left;
    }

    /* BUTTONS */
    .btn {
        padding: 14px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        margin-top: 15px;
        margin-right: 10px;
        font-size: 15px;
        border: none;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .btn-accept {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-accept:hover {
        background: #25A29F;
    }

    .btn-decline {
        background: var(--danger);
        color: var(--white);
    }

    .btn-decline:hover {
        background: #C73E3E;
    }

    .btn-counter {
        background: var(--black);
        color: var(--white);
    }

    .btn-counter:hover {
        background: #1A1A1A;
    }

    .btn:active {
        transform: scale(0.98);
    }

    /* LIST */
    .card ul {
        padding-left: 20px;
        margin: 15px 0;
    }

    .card ul li {
        margin-bottom: 8px;
        font-size: 14px;
        line-height: 1.6;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2>Offer Received</h2>
    <p class="page-subtitle">Review the buyer's offer details and choose how you wish to proceed.</p>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="background: #fee; border: 1px solid #dc3545; border-radius: 6px; padding: 12px; margin-bottom: 20px; color: #dc3545; font-size: 14px; text-align: left;">
            <strong>Error:</strong>
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="offerDecisionForm" method="POST" action="{{ route('seller.offer.decision.update', $offer->id ?? 1) }}">
        @csrf
        @method('PUT')

        <!-- OFFER SUMMARY -->
        <div class="card">
            <h3>Offer Summary</h3>
            <table class="table">
                <tr>
                    <th>Buyer</th>
                    <td>
                        <strong>{{ $offer->buyer->name ?? 'N/A' }}</strong>
                        @if($offer->buyer)
                            <br><span style="font-size: 12px; color: #666;">{{ $offer->buyer->email }}</span>
                            @if($offer->buyer->phone)
                                <br><span style="font-size: 12px; color: #666;">{{ $offer->buyer->phone }}</span>
                            @endif
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Property Address</th>
                    <td>{{ $offer->property->address ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Asking Price</th>
                    <td>£{{ number_format($offer->property->asking_price ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <th>Offer Amount</th>
                    <td>
                        @if($offer->released_to_seller)
                            <strong style="color: var(--abodeology-teal); font-size: 18px;">£{{ number_format($offer->offer_amount, 2) }}</strong>
                        @else
                            <span style="color: #666; font-style: italic;">Amount withheld pending agent review. Please contact your agent to discuss this offer.</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Offer Date</th>
                    <td>{{ $offer->created_at->format('l, F j, Y g:i A') }}</td>
                </tr>
                <tr>
                    <th>Buying Position</th>
                    <td>{{ $offer->chain_position ? ucfirst(str_replace('-', ' ', $offer->chain_position)) : 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Funding Type</th>
                    <td>{{ ucfirst(str_replace('_', ' ', $offer->funding_type ?? 'N/A')) }}</td>
                </tr>
                @if($offer->deposit_amount)
                    <tr>
                        <th>Deposit Amount</th>
                        <td>£{{ number_format($offer->deposit_amount, 2) }}</td>
                    </tr>
                @endif
                @if($offer->conditions)
                    <tr>
                        <th>Conditions</th>
                        <td style="white-space: pre-wrap;">{{ $offer->conditions }}</td>
                    </tr>
                @endif
            </table>
        </div>

        @php
            $buyerAmlCheck = $offer->buyer ? \App\Models\AmlCheck::where('user_id', $offer->buyer->id)->first() : null;
        @endphp

        @if($buyerAmlCheck)
            <!-- VERIFICATION -->
            <div class="card">
                <h3>Buyer Verification</h3>
                <p><strong>AML Status:</strong>
                    @if($buyerAmlCheck->verification_status === 'verified')
                        <span class="badge badge-good">Verified</span>
                    @elseif($buyerAmlCheck->verification_status === 'rejected')
                        <span class="badge badge-bad">Rejected</span>
                    @else
                        <span class="badge badge-warn">Pending</span>
                    @endif
                </p>
                @if($buyerAmlCheck->id_document && $buyerAmlCheck->proof_of_address)
                    <p style="color: #28a745; font-size: 13px; margin-top: 10px;">✓ AML documents provided</p>
                @else
                    <p style="color: #ffc107; font-size: 13px; margin-top: 10px;">⚠ AML documents pending</p>
                @endif
            </div>
        @endif

        <!-- DECISION -->
        <div class="card">
            <h3>Your Decision</h3>
            <p>Select how you wish to proceed. Your choice will notify the buyer immediately.</p>
            
            <div id="counterOfferSection" style="display: none; margin-bottom: 15px; padding: 15px; background: #F9F9F9; border-radius: 6px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">Counter-Offer Amount (£)</label>
                <input type="number" 
                       name="counter_amount" 
                       id="counterAmount"
                       placeholder="Enter counter-offer amount"
                       min="0"
                       step="1000"
                       style="width: 100%; padding: 12px; border: 1px solid #D9D9D9; border-radius: 6px; font-size: 15px; box-sizing: border-box;">
                <p style="font-size: 12px; color: #666; margin-top: 5px;">Enter the amount you would like to counter-offer.</p>
            </div>
            
            <textarea 
                name="notes" 
                id="decisionNotes"
                placeholder="Add notes or comments for the buyer (optional)">{{ old('notes') }}</textarea>
            @error('notes')
                <div class="error-message">{{ $message }}</div>
            @enderror
            @error('counter_amount')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="hidden" name="decision" id="decisionInput" value="">

            <br>

            <button type="button" class="btn btn-accept" onclick="submitDecision('accepted')">✓ Accept Offer</button>
            <button type="button" class="btn btn-decline" onclick="submitDecision('declined')">✗ Decline Offer</button>
            <button type="button" class="btn btn-counter" onclick="showCounterOffer()">💬 Counter-Offer</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function showCounterOffer() {
        const counterSection = document.getElementById('counterOfferSection');
        const counterAmount = document.getElementById('counterAmount');
        
        if (counterSection.style.display === 'none') {
            counterSection.style.display = 'block';
            counterAmount.focus();
        } else {
            counterSection.style.display = 'none';
            counterAmount.value = '';
        }
    }

    function submitDecision(decision) {
        // Validate that a decision has been made
        if (!decision) {
            alert('Please select a decision.');
            return false;
        }

        // If counter-offer, validate counter amount
        if (decision === 'counter') {
            const counterAmount = document.getElementById('counterAmount').value;
            if (!counterAmount || parseFloat(counterAmount) <= 0) {
                alert('Please enter a valid counter-offer amount.');
                document.getElementById('counterAmount').focus();
                return false;
            }
        }

        // Set the decision value
        document.getElementById('decisionInput').value = decision;

        // Confirm action based on decision
        let confirmMessage = '';
        switch(decision) {
            case 'accepted':
                confirmMessage = 'Are you sure you want to accept this offer? The property status will be updated to "Sold Subject to Contract" and a Memorandum of Sale will be generated. The buyer will be notified immediately.';
                break;
            case 'declined':
                confirmMessage = 'Are you sure you want to decline this offer? The buyer will be notified immediately.';
                break;
            case 'counter':
                const counterAmount = document.getElementById('counterAmount').value;
                confirmMessage = 'Are you sure you want to send a counter-offer of £' + parseFloat(counterAmount).toLocaleString('en-GB', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + '? The buyer will be notified.';
                break;
        }

        if (confirm(confirmMessage)) {
            document.getElementById('offerDecisionForm').submit();
        }
    }
</script>
@endpush
@endsection
