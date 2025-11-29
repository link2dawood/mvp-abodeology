@extends('layouts.admin')

@section('title', 'AML Check - ' . ($amlCheck->user->name ?? 'N/A'))

@push('styles')
<style>
    .container {
        max-width: 1000px;
        margin: 30px auto;
        padding: 20px;
    }

    h2 {
        border-bottom: 2px solid #000000;
        padding-bottom: 8px;
        margin-bottom: 20px;
        font-size: 24px;
        font-weight: 600;
    }

    .card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        margin-bottom: 30px;
        box-shadow: 0px 3px 12px rgba(0,0,0,0.06);
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 18px;
        font-weight: 600;
        color: var(--abodeology-teal);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--line-grey);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #333;
    }

    .info-value {
        color: #666;
    }

    .status {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-verified {
        background: #d4edda;
        color: #155724;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .document-preview {
        margin-top: 15px;
        border: 1px solid var(--line-grey);
        border-radius: 6px;
        padding: 15px;
        background: #f9f9f9;
    }

    .document-preview img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    .document-preview a {
        display: inline-block;
        margin-top: 10px;
        color: var(--abodeology-teal);
        text-decoration: none;
        font-weight: 600;
    }

    .document-preview a:hover {
        text-decoration: underline;
    }

    .btn {
        background: #000000;
        color: #ffffff;
        padding: 12px 24px;
        text-decoration: none;
        border-radius: 4px;
        display: inline-block;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: opacity 0.3s ease;
        margin-right: 10px;
    }

    .btn:hover {
        opacity: 0.85;
    }

    .btn-main {
        background: var(--abodeology-teal);
    }

    .btn-main:hover {
        background: #25A29F;
    }

    .btn-success {
        background: #28a745;
    }

    .btn-success:hover {
        background: #218838;
    }

    .btn-danger {
        background: #dc3545;
    }

    .btn-danger:hover {
        background: #C73E3E;
    }

    .btn-secondary {
        background: var(--abodeology-teal);
    }

    .btn-secondary:hover {
        background: #25A29F;
    }

    select {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--line-grey);
        border-radius: 6px;
        font-size: 14px;
        margin-bottom: 15px;
    }

    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid var(--line-grey);
        border-radius: 6px;
        font-size: 14px;
        min-height: 100px;
        resize: vertical;
        margin-bottom: 15px;
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        .info-row {
            flex-direction: column;
        }

        .info-value {
            margin-top: 5px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; border: none; padding: 0;">AML Document Check</h2>
        <a href="{{ route('admin.aml-checks.index') }}" class="btn btn-secondary">← Back to AML Checks</a>
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

    <!-- User Information -->
    <div class="card">
        <h3>User Information</h3>
        <div class="info-row">
            <span class="info-label">Name:</span>
            <span class="info-value">{{ $amlCheck->user->name ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $amlCheck->user->email ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Phone:</span>
            <span class="info-value">{{ $amlCheck->user->phone ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Verification Status:</span>
            <span class="info-value">
                <span class="status status-{{ $amlCheck->verification_status }}">
                    {{ ucfirst($amlCheck->verification_status) }}
                </span>
            </span>
        </div>
        @if($amlCheck->checked_by)
            <div class="info-row">
                <span class="info-label">Checked By:</span>
                <span class="info-value">{{ $amlCheck->checker->name ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Checked At:</span>
                <span class="info-value">{{ $amlCheck->checked_at ? $amlCheck->checked_at->format('F j, Y g:i A') : 'N/A' }}</span>
            </div>
        @endif
        <div class="info-row">
            <span class="info-label">Documents Uploaded:</span>
            <span class="info-value">{{ $amlCheck->created_at->format('F j, Y g:i A') }}</span>
        </div>
    </div>

    <!-- Photo ID Documents -->
    <div class="card">
        <h3>Photo ID Documents</h3>
        @php
            $idDocuments = $amlCheck->idDocuments ?? collect();
            if ($amlCheck->id_document && $idDocuments->isEmpty()) {
                // Legacy support: show old single document
                $idDocuments = collect([(object)['file_path' => $amlCheck->id_document, 'file_name' => 'ID Document']]);
            }
        @endphp
        @if($idDocuments->isNotEmpty())
            @foreach($idDocuments as $doc)
                <div class="document-preview" style="margin-bottom: 20px;">
                    @php
                        $isImage = in_array(strtolower(pathinfo($doc->file_path ?? $doc->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
                        $disk = config('filesystems.default');
                        // Use secure route if document has an ID (AmlDocument model), otherwise use legacy method
                        if (isset($doc->id) && is_numeric($doc->id)) {
                            $url = route('admin.aml-documents.serve', $doc->id);
                        } elseif ($disk === 's3') {
                            $url = \Storage::disk('s3')->url($doc->file_path ?? $doc->file_path);
                        } else {
                            $url = asset('storage/' . ($doc->file_path ?? $doc->file_path));
                        }
                    @endphp
                    @if($isImage)
                        <img src="{{ $url }}" alt="Photo ID Document" style="max-width: 100%; height: auto; border-radius: 4px; margin-bottom: 10px;">
                    @endif
                    <div>
                        <strong>{{ $doc->file_name ?? 'ID Document' }}</strong>
                        @if(isset($doc->file_size))
                            <span style="color: #666; font-size: 12px;">({{ number_format($doc->file_size / 1024, 2) }} KB)</span>
                        @endif
                    </div>
                    <a href="{{ $url }}" target="_blank" style="display: inline-block; margin-top: 10px;">View Full Document →</a>
                </div>
            @endforeach
        @else
            <p style="color: #999;">No documents uploaded</p>
        @endif
    </div>

    <!-- Proof of Address Documents -->
    <div class="card">
        <h3>Proof of Address Documents</h3>
        @php
            $proofDocuments = $amlCheck->proofOfAddressDocuments ?? collect();
            if ($amlCheck->proof_of_address && $proofDocuments->isEmpty()) {
                // Legacy support: show old single document
                $proofDocuments = collect([(object)['file_path' => $amlCheck->proof_of_address, 'file_name' => 'Proof of Address']]);
            }
        @endphp
        @if($proofDocuments->isNotEmpty())
            @foreach($proofDocuments as $doc)
                <div class="document-preview" style="margin-bottom: 20px;">
                    @php
                        $isImage = in_array(strtolower(pathinfo($doc->file_path ?? $doc->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
                        $disk = config('filesystems.default');
                        // Use secure route if document has an ID (AmlDocument model), otherwise use legacy method
                        if (isset($doc->id) && is_numeric($doc->id)) {
                            $url = route('admin.aml-documents.serve', $doc->id);
                        } elseif ($disk === 's3') {
                            $url = \Storage::disk('s3')->url($doc->file_path ?? $doc->file_path);
                        } else {
                            $url = asset('storage/' . ($doc->file_path ?? $doc->file_path));
                        }
                    @endphp
                    @if($isImage)
                        <img src="{{ $url }}" alt="Proof of Address Document" style="max-width: 100%; height: auto; border-radius: 4px; margin-bottom: 10px;">
                    @endif
                    <div>
                        <strong>{{ $doc->file_name ?? 'Proof of Address' }}</strong>
                        @if(isset($doc->file_size))
                            <span style="color: #666; font-size: 12px;">({{ number_format($doc->file_size / 1024, 2) }} KB)</span>
                        @endif
                    </div>
                    <a href="{{ $url }}" target="_blank" style="display: inline-block; margin-top: 10px;">View Full Document →</a>
                </div>
            @endforeach
        @else
            <p style="color: #999;">No documents uploaded</p>
        @endif
    </div>

    <!-- Additional Documents -->
    @php
        $additionalDocuments = $amlCheck->additionalDocuments ?? collect();
    @endphp
    @if($additionalDocuments->isNotEmpty())
    <div class="card">
        <h3>Additional Documents</h3>
        @foreach($additionalDocuments as $doc)
            <div class="document-preview" style="margin-bottom: 20px;">
                @php
                    $isImage = in_array(strtolower(pathinfo($doc->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
                    $disk = config('filesystems.default');
                    // Use secure route if document has an ID (AmlDocument model), otherwise use legacy method
                    if (isset($doc->id) && is_numeric($doc->id)) {
                        $url = route('admin.aml-documents.serve', $doc->id);
                    } elseif ($disk === 's3') {
                        $url = \Storage::disk('s3')->url($doc->file_path);
                    } else {
                        $url = asset('storage/' . $doc->file_path);
                    }
                @endphp
                @if($isImage)
                    <img src="{{ $url }}" alt="Additional Document" style="max-width: 100%; height: auto; border-radius: 4px; margin-bottom: 10px;">
                @endif
                <div>
                    <strong>{{ $doc->file_name }}</strong>
                    @if($doc->file_size)
                        <span style="color: #666; font-size: 12px;">({{ number_format($doc->file_size / 1024, 2) }} KB)</span>
                    @endif
                </div>
                <a href="{{ $url }}" target="_blank" style="display: inline-block; margin-top: 10px;">View Full Document →</a>
            </div>
        @endforeach
    </div>
    @endif

    <!-- Verification Form -->
    @if($amlCheck->verification_status === 'pending' || $amlCheck->verification_status === 'rejected')
        <div class="card">
            <h3>Verify Documents</h3>
            <form action="{{ route('admin.aml-checks.verify', $amlCheck->id) }}" method="POST">
                @csrf
                <div>
                    <label for="verification_status" style="display: block; font-weight: 600; margin-bottom: 8px;">Verification Status *</label>
                    <select name="verification_status" id="verification_status" required>
                        <option value="">Select status...</option>
                        <option value="verified" {{ old('verification_status') === 'verified' ? 'selected' : '' }}>Verified</option>
                        <option value="rejected" {{ old('verification_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div>
                    <label for="notes" style="display: block; font-weight: 600; margin-bottom: 8px;">Notes (Optional)</label>
                    <textarea name="notes" id="notes" placeholder="Add any notes about the verification...">{{ old('notes') }}</textarea>
                </div>
                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-main">Submit Verification</button>
                    <a href="{{ route('admin.aml-checks.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    @else
        <div class="card" style="background: #d4edda;">
            <h3 style="color: #155724;">✓ Documents Verified</h3>
            <p style="color: #155724;">This AML check has been verified. Documents were checked by {{ $amlCheck->checker->name ?? 'N/A' }} on {{ $amlCheck->checked_at ? $amlCheck->checked_at->format('F j, Y') : 'N/A' }}.</p>
        </div>
    @endif
</div>
@endsection

