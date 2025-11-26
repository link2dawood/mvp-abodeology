@extends('layouts.admin')

@section('title', 'Upload Listing - Photos, Floorplan, EPC')

@push('styles')
<style>
    .upload-section {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        border: 1px solid var(--line-grey);
        box-shadow: 0px 3px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }

    .upload-section h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 20px;
        color: var(--abodeology-teal);
    }

    .file-upload-area {
        border: 2px dashed var(--line-grey);
        border-radius: 8px;
        padding: 30px;
        text-align: center;
        margin-bottom: 15px;
        transition: border-color 0.3s ease;
    }

    .file-upload-area:hover {
        border-color: var(--abodeology-teal);
    }

    .file-upload-area input[type="file"] {
        display: none;
    }

    .file-upload-label {
        cursor: pointer;
        display: inline-block;
        padding: 10px 20px;
        background: var(--abodeology-teal);
        color: var(--white);
        border-radius: 6px;
        font-weight: 600;
        transition: background 0.3s ease;
    }

    .file-upload-label:hover {
        background: #25A29F;
    }

    .file-list {
        margin-top: 15px;
    }

    .file-item {
        display: flex;
        align-items: center;
        padding: 10px;
        background: #F9F9F9;
        border-radius: 6px;
        margin-bottom: 8px;
    }

    .primary-photo-checkbox {
        margin-right: 10px;
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

    .btn-primary {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-primary:hover {
        background: #25A29F;
    }

    .btn-secondary {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-secondary:hover {
        background: #25A29F;
    }

    .help-text {
        font-size: 13px;
        color: #666;
        margin-top: 8px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2>Upload Listing Materials</h2>
            <p style="color: #666; margin-top: 5px;">Property: {{ $property->address }}</p>
        </div>
        <div>
            <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-secondary">Back to Property</a>
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

    @if($errors->any())
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.properties.listing-upload.store', $property->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="upload-section">
            <h3>Property Photos *</h3>
            <p class="help-text">Upload at least one photo. The first photo will be used as the primary image by default, or select a primary photo below.</p>
            @error('photos')
                <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
            @enderror
            @error('photos.*')
                <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
            @enderror
            
            <div class="file-upload-area">
                <label for="photos" class="file-upload-label">Choose Photos</label>
                <input type="file" id="photos" name="photos[]" multiple accept="image/*" onchange="previewPhotos(this)">
                <div id="photo-preview" class="file-list"></div>
            </div>

            <div id="primary-photo-selection" style="display: none;">
                <label><strong>Select Primary Photo:</strong></label>
                <div id="primary-photo-options"></div>
            </div>
        </div>

        <div class="upload-section">
            <h3>Floorplan (Optional)</h3>
            <p class="help-text">Upload a floorplan document (PDF, DOC, DOCX, or image).</p>
            @error('floorplan')
                <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
            @enderror
            
            <div class="file-upload-area">
                <label for="floorplan" class="file-upload-label">Choose Floorplan</label>
                <input type="file" id="floorplan" name="floorplan" accept=".pdf,.doc,.docx,image/*" onchange="previewFile(this, 'floorplan-preview')">
                <div id="floorplan-preview" class="file-list"></div>
            </div>
        </div>

        <div class="upload-section">
            <h3>EPC Certificate (Optional)</h3>
            <p class="help-text">Upload an Energy Performance Certificate (PDF, DOC, DOCX, or image).</p>
            @error('epc')
                <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
            @enderror
            
            <div class="file-upload-area">
                <label for="epc" class="file-upload-label">Choose EPC</label>
                <input type="file" id="epc" name="epc" accept=".pdf,.doc,.docx,image/*" onchange="previewFile(this, 'epc-preview')">
                <div id="epc-preview" class="file-list"></div>
            </div>
        </div>

        <div class="upload-section">
            <h3>Additional Documents (Optional)</h3>
            <p class="help-text">Upload additional documents such as planning permissions, building control certificates, FENSA certificates, or any other relevant documents (PDF, DOC, DOCX, or images).</p>
            @error('additional_documents')
                <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
            @enderror
            @error('additional_documents.*')
                <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
            @enderror
            
            <div class="file-upload-area">
                <label for="additional_documents" class="file-upload-label">Choose Additional Documents</label>
                <input type="file" id="additional_documents" name="additional_documents[]" multiple accept=".pdf,.doc,.docx,image/*" onchange="previewMultipleFiles(this, 'additional-documents-preview')">
                <div id="additional-documents-preview" class="file-list"></div>
            </div>
        </div>

        <input type="hidden" id="primary_photo_index" name="primary_photo_index" value="0">

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Create Listing Draft</button>
            <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
let selectedPhotos = [];

function previewPhotos(input) {
    const preview = document.getElementById('photo-preview');
    const primarySelection = document.getElementById('primary-photo-selection');
    const primaryOptions = document.getElementById('primary-photo-options');
    preview.innerHTML = '';
    primaryOptions.innerHTML = '';
    selectedPhotos = [];

    if (input.files && input.files.length > 0) {
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            selectedPhotos.push(file);

            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <span style="flex: 1;">${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
            `;
            preview.appendChild(fileItem);

            // Create primary photo option
            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'primary_photo';
            radio.value = i;
            radio.id = `primary_${i}`;
            radio.className = 'primary-photo-checkbox';
            if (i === 0) radio.checked = true;
            radio.onchange = function() {
                document.getElementById('primary_photo_index').value = i;
            };

            const label = document.createElement('label');
            label.htmlFor = `primary_${i}`;
            label.appendChild(radio);
            label.appendChild(document.createTextNode(` Photo ${i + 1}`));
            
            primaryOptions.appendChild(label);
            primaryOptions.appendChild(document.createElement('br'));
        }
        
        primarySelection.style.display = 'block';
        document.getElementById('primary_photo_index').value = 0;
    }
}

function previewFile(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.innerHTML = `
            <span>${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
        `;
        preview.appendChild(fileItem);
    }
}

function previewMultipleFiles(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';

    if (input.files && input.files.length > 0) {
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.innerHTML = `
                <span>${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
            `;
            preview.appendChild(fileItem);
        }
    }
}
</script>
@endsection
