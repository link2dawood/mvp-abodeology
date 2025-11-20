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

    <form id="offerDecisionForm" method="POST" action="{{ route('seller.offer.decision', $offer->id ?? 1) }}">
        @csrf
        @method('PUT')

        <!-- OFFER SUMMARY -->
        <div class="card">
            <h3>Offer Summary</h3>
            <table class="table">
                <tr>
                    <th>Buyer</th>
                    <td>{{ $offer->buyer_name ?? 'John Doe' }}</td>
                </tr>
                <tr>
                    <th>Offer Amount</th>
                    <td><strong>£{{ number_format($offer->offer_amount ?? $offer->amount ?? 450000, 0) }}</strong></td>
                </tr>
                <tr>
                    <th>Offer Date</th>
                    <td>{{ $offer->offer_date ?? date('d M Y') }}</td>
                </tr>
                <tr>
                    <th>Position</th>
                    <td>{{ $offer->buyer_position_text ?? 'First-time buyer' }}</td>
                </tr>
                <tr>
                    <th>Funding</th>
                    <td>{{ $offer->funding_type ?? 'Mortgage' }}</td>
                </tr>
                <tr>
                    <th>Deposit</th>
                    <td>£{{ number_format($offer->deposit_amount ?? 45000, 2) }}</td>
                </tr>
                <tr>
                    <th>Conditions</th>
                    <td>{{ $offer->conditions ?? 'Subject to survey and mortgage approval' }}</td>
                </tr>
            </table>
        </div>

        <!-- VERIFICATION -->
        <div class="card">
            <h3>Buyer Verification</h3>
            <p><strong>AML Status:</strong>
                <span class="badge badge-{{ $offer->aml_badge_class ?? 'good' }}">
                    {{ $offer->aml_status_text ?? 'Verified' }}
                </span>
            </p>
            <p><strong>Proof of Funds:</strong>
                <span class="badge badge-{{ $offer->pof_badge_class ?? 'good' }}">
                    {{ $offer->pof_status_text ?? 'Verified' }}
                </span>
            </p>
            <p><strong>Documents Provided:</strong></p>
            <ul>
                <li>Photo ID — {{ $offer->photo_id_status ?? 'Verified' }}</li>
                <li>Proof of Address — {{ $offer->address_proof_status ?? 'Verified' }}</li>
                <li>Proof of Funds — {{ $offer->pof_documents_status ?? 'Verified' }}</li>
                <li>Mortgage AIP — {{ $offer->aip_status ?? 'Provided' }}</li>
            </ul>
        </div>

        <!-- SOLICITOR -->
        <div class="card">
            <h3>Buyer's Solicitor</h3>
            <p><strong>Name:</strong> {{ $offer->solicitor_name ?? 'Jane Smith' }}</p>
            <p><strong>Firm:</strong> {{ $offer->solicitor_firm ?? 'Smith & Partners Solicitors' }}</p>
            <p><strong>Email:</strong> {{ $offer->solicitor_email ?? 'jane.smith@smithpartners.co.uk' }}</p>
            <p><strong>Phone:</strong> {{ $offer->solicitor_phone ?? '020 1234 5678' }}</p>
        </div>

        <!-- DECISION -->
        <div class="card">
            <h3>Your Decision</h3>
            <p>Select how you wish to proceed. Your choice will notify the buyer immediately.</p>
            
            <textarea 
                name="notes" 
                id="decisionNotes"
                placeholder="Add notes or conditions (optional)">{{ old('notes') }}</textarea>
            @error('notes')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <input type="hidden" name="decision" id="decisionInput" value="">

            <br>

            <button type="button" class="btn btn-accept" onclick="submitDecision('accepted')">Accept Offer</button>
            <button type="button" class="btn btn-decline" onclick="submitDecision('declined')">Decline Offer</button>
            <button type="button" class="btn btn-counter" onclick="submitDecision('counter')">Discuss / Counter-Offer</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function submitDecision(decision) {
        // Validate that a decision has been made
        if (!decision) {
            alert('Please select a decision.');
            return false;
        }

        // Set the decision value
        document.getElementById('decisionInput').value = decision;

        // Confirm action based on decision
        let confirmMessage = '';
        switch(decision) {
            case 'accepted':
                confirmMessage = 'Are you sure you want to accept this offer? The buyer will be notified immediately.';
                break;
            case 'declined':
                confirmMessage = 'Are you sure you want to decline this offer? The buyer will be notified immediately.';
                break;
            case 'counter':
                confirmMessage = 'Are you sure you want to request a counter-offer discussion? The buyer will be notified.';
                break;
        }

        if (confirm(confirmMessage)) {
            document.getElementById('offerDecisionForm').submit();
        }
    }
</script>
@endpush
@endsection
