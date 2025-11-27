@extends('layouts.seller')

@section('title', 'Upload AML Documents')

@push('styles')
<style>
    .container {
        max-width: 900px;
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
        border: 1px solid #dcdcdc;
        padding: 25px;
        margin: 20px 0;
        border-radius: 4px;
        background: #fff;
    }

    .card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 18px;
        font-weight: 600;
    }

    .upload-box {
        background: #F4F4F4;
        border: 2px dashed #dcdcdc;
        border-radius: 6px;
        padding: 25px;
        text-align: center;
        margin-bottom: 20px;
        transition: border-color 0.3s ease;
    }

    .upload-box:hover {
        border-color: #2CB8B4;
    }

    .upload-box strong {
        display: block;
        margin-bottom: 10px;
        color: #1E1E1E;
        font-size: 16px;
    }

    .upload-box input[type="file"] {
        margin: 10px auto;
        display: block;
    }

    .upload-box p {
        margin: 10px 0 0 0;
        font-size: 13px;
        color: #666;
    }

    input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #dcdcdc;
        border-radius: 4px;
        background: #fff;
    }

    input[type="file"].error {
        border-color: #dc3545;
    }

    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: -15px;
        margin-bottom: 15px;
    }

    .btn {
        background: #000000;
        color: #ffffff;
        padding: 12px 30px;
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

    .btn-primary {
        background: #2CB8B4;
    }

    .info-box {
        background: #E8F4F3;
        border-left: 4px solid #2CB8B4;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .info-box p {
        margin: 5px 0;
        font-size: 14px;
        color: #1E1E1E;
    }

    @if(isset($amlCheck) && $amlCheck->id_document && $amlCheck->proof_of_address)
    .upload-status {
        background: #fff3cd;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .upload-status p {
        margin: 5px 0;
        font-size: 14px;
    }
    @endif
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; border: none; padding: 0;">Upload AML Documents</h2>
        <a href="{{ route('seller.properties.show', $property->id) }}" class="btn" style="background: #666;">← Back to Property</a>
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

    <div class="info-box">
        <p><strong>Property:</strong> {{ $property->address }}</p>
        <p>As part of our Anti-Money Laundering (AML) compliance, we require you to provide the following documents:</p>
        <ul style="margin: 10px 0; padding-left: 20px;">
            <li><strong>Photo ID:</strong> Passport, Driving License, or National ID Card</li>
            <li><strong>Proof of Address:</strong> Utility bill, Bank statement, or Council tax bill (dated within the last 3 months)</li>
        </ul>
        <p style="font-size: 13px; color: #666; margin-top: 10px;">All documents must be clear, legible, and in JPEG, PNG, or PDF format (max 5MB each).</p>
    </div>

    @if(isset($amlCheck) && $amlCheck->id_document && $amlCheck->proof_of_address)
        <div class="upload-status">
            <p><strong>Documents Previously Uploaded</strong></p>
            <p>Your documents are currently being reviewed. You can upload new documents to replace the existing ones if needed.</p>
            @if($amlCheck->verification_status === 'verified')
                <p style="color: #28a745; margin-top: 10px;"><strong>✓ Status: Verified</strong></p>
            @elseif($amlCheck->verification_status === 'rejected')
                <p style="color: #dc3545; margin-top: 10px;"><strong>✗ Status: Rejected</strong> - Please upload new documents.</p>
            @else
                <p style="color: #ffc107; margin-top: 10px;"><strong>⏳ Status: Under Review</strong></p>
            @endif
        </div>
    @endif

    <form action="{{ route('seller.aml.upload.store', $property->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="card">
            <h3>Photo ID Documents</h3>
            <div class="upload-box">
                <strong>Upload Photo ID Documents</strong>
                <p>Acceptable formats: Passport, Driving License, or National ID Card</p>
                <p style="font-size: 12px; color: #666; margin-top: 5px;">You can upload multiple documents (e.g., front and back of ID card)</p>
                <input type="file" 
                       name="id_documents[]" 
                       accept="image/*,.pdf"
                       multiple
                       required
                       class="{{ $errors->has('id_documents') ? 'error' : '' }}">
            </div>
            @error('id_documents')
                <div class="error-message">{{ $message }}</div>
            @enderror
            @error('id_documents.*')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="card">
            <h3>Proof of Address Documents</h3>
            <div class="upload-box">
                <strong>Upload Proof of Address Documents</strong>
                <p>Acceptable formats: Utility bill, Bank statement, or Council tax bill (dated within last 3 months)</p>
                <p style="font-size: 12px; color: #666; margin-top: 5px;">You can upload multiple documents if needed</p>
                <input type="file" 
                       name="proof_of_address_documents[]" 
                       accept="image/*,.pdf"
                       multiple
                       required
                       class="{{ $errors->has('proof_of_address_documents') ? 'error' : '' }}">
            </div>
            @error('proof_of_address_documents')
                <div class="error-message">{{ $message }}</div>
            @enderror
            @error('proof_of_address_documents.*')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="card">
            <h3>Additional Documents (Optional)</h3>
            <div class="upload-box">
                <strong>Upload Additional Documents</strong>
                <p>Any other supporting documents you'd like to provide</p>
                <input type="file" 
                       name="additional_documents[]" 
                       accept="image/*,.pdf"
                       multiple
                       class="{{ $errors->has('additional_documents') ? 'error' : '' }}">
            </div>
            @error('additional_documents.*')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Upload Documents</button>
            <a href="{{ route('seller.properties.show', $property->id) }}" class="btn" style="background: #666;">Cancel</a>
        </div>
    </form>
</div>
@endsection
