@extends('layouts.admin')

@section('title', 'Edit HomeCheck Report')

@push('styles')
<style>
    .container {
        max-width: 1200px;
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
        color: var(--abodeology-teal);
    }

    input[type="date"],
    select,
    textarea,
    input[type="text"],
    input[type="number"] {
        width: 100%;
        padding: 12px;
        margin-bottom: 18px;
        border: 1px solid #dcdcdc;
        border-radius: 4px;
        font-size: 15px;
        box-sizing: border-box;
        font-family: inherit;
    }

    select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23333' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 35px;
        line-height: 1.5;
        height: auto;
    }

    textarea {
        height: 120px;
        resize: vertical;
    }

    input:focus,
    select:focus,
    textarea:focus {
        border-color: #2CB8B4;
        outline: none;
    }

    input.error,
    select.error,
    textarea.error {
        border-color: #dc3545;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
        color: #1E1E1E;
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

    .btn-main {
        background: #2CB8B4;
    }

    .btn-secondary {
        background: #666;
    }

    .info-box {
        background: #E8F4F3;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }

    .info-box p {
        margin: 5px 0;
        font-size: 14px;
        color: #1E1E1E;
    }

    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-row > div {
        flex: 1;
    }

    /* Room Upload Styles */
    .room-block {
        background: #F9F9F9;
        border: 2px solid #dcdcdc;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .room-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .room-name-input {
        flex: 1;
        padding: 10px;
        border: 1px solid #dcdcdc;
        border-radius: 6px;
        font-size: 16px;
        margin-right: 10px;
    }

    .remove-room-btn {
        background: #dc3545;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    .image-type-toggle {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .image-type-btn {
        padding: 8px 15px;
        border: 2px solid #2CB8B4;
        background: white;
        color: #2CB8B4;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .image-type-btn.active {
        background: #2CB8B4;
        color: white;
    }

    .file-upload-area {
        border: 2px dashed #dcdcdc;
        border-radius: 8px;
        padding: 40px;
        text-align: center;
        margin-bottom: 15px;
        transition: border-color 0.3s ease;
        cursor: pointer;
    }

    .file-upload-area:hover {
        border-color: #2CB8B4;
        background: #F0F9F9;
    }

    .file-upload-area.dragover {
        border-color: #2CB8B4;
        background: #E8F4F3;
    }

    .file-input {
        display: none;
    }

    .preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .preview-item {
        position: relative;
        border: 2px solid #dcdcdc;
        border-radius: 8px;
        overflow: hidden;
    }

    .preview-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        display: block;
    }

    .preview-item .remove-image {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        cursor: pointer;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .preview-item .image-type-badge {
        position: absolute;
        bottom: 5px;
        left: 5px;
        background: rgba(44, 184, 180, 0.9);
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .add-room-btn {
        background: #28a745;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 20px;
    }

    .add-room-btn:hover {
        background: #218838;
    }

    .existing-rooms {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
    }

    .existing-rooms h4 {
        margin-top: 0;
        color: #666;
        font-size: 14px;
    }

    .existing-room-item {
        padding: 8px;
        margin: 5px 0;
        background: white;
        border-radius: 4px;
        font-size: 13px;
    }

    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
            gap: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; border: none; padding: 0;">Edit HomeCheck Report</h2>
        <a href="{{ route('admin.homechecks.show', $homecheckReport->id) }}" class="btn btn-secondary">← Back to HomeCheck</a>
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
        @if($property->postcode)
            <p><strong>Postcode:</strong> {{ $property->postcode }}</p>
        @endif
    </div>

    <form action="{{ route('admin.homechecks.update', $homecheckReport->id) }}" method="POST" enctype="multipart/form-data" id="homecheckForm">
        @csrf
        @method('PUT')

        <!-- Basic HomeCheck Details -->
        <div class="card">
            <h3>HomeCheck Details</h3>

            <div class="form-row">
                <div>
                    <label for="scheduled_date">Scheduled Date <span style="color: #dc3545;">*</span></label>
                    <input type="date" 
                           id="scheduled_date"
                           name="scheduled_date" 
                           value="{{ old('scheduled_date', $homecheckReport->scheduled_date ? $homecheckReport->scheduled_date->format('Y-m-d') : '') }}"
                           min="{{ date('Y-m-d') }}"
                           class="{{ $errors->has('scheduled_date') ? 'error' : '' }}">
                    @error('scheduled_date')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="status">Status <span style="color: #dc3545;">*</span></label>
                    <select id="status"
                            name="status" 
                            required
                            class="{{ $errors->has('status') ? 'error' : '' }}">
                        <option value="">Select Status</option>
                        <option value="pending" {{ old('status', $homecheckReport->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="scheduled" {{ old('status', $homecheckReport->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="in_progress" {{ old('status', $homecheckReport->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ old('status', $homecheckReport->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $homecheckReport->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div>
                <label for="notes">Notes</label>
                <textarea id="notes"
                          name="notes" 
                          rows="4"
                          maxlength="1000"
                          placeholder="Add any notes about the HomeCheck (optional)"
                          class="{{ $errors->has('notes') ? 'error' : '' }}">{{ old('notes', $homecheckReport->notes) }}</textarea>
                <small style="color: #666; font-size: 13px;">Maximum 1000 characters</small>
                @error('notes')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Existing Rooms -->
        @if($homecheckData && $homecheckData->count() > 0)
        <div class="card">
            <h3>Existing Rooms</h3>
            <div class="existing-rooms">
                <h4>Previously uploaded rooms ({{ $homecheckData->groupBy('room_name')->count() }} room(s)):</h4>
                @foreach($homecheckData->groupBy('room_name') as $roomName => $roomImages)
                    <div class="existing-room-item">
                        <strong>{{ $roomName }}</strong> - {{ $roomImages->count() }} image(s)
                        @if($roomImages->where('is_360', true)->count() > 0)
                            <span style="color: #2CB8B4;">({{ $roomImages->where('is_360', true)->count() }} 360° images)</span>
                        @endif
                    </div>
                @endforeach
            </div>
            <p style="color: #666; font-size: 13px; margin-top: 10px;">You can add new rooms below. Existing rooms cannot be modified from this page.</p>
        </div>
        @endif

        <!-- Add New Rooms Section -->
        <div class="card">
            <h3>Add New Rooms (Optional)</h3>
            <p style="color: #666; margin-bottom: 15px;">Add additional rooms and images to this HomeCheck report.</p>

            <!-- Rooms Container -->
            <div id="roomsContainer">
                <!-- Rooms will be added dynamically via JavaScript -->
            </div>

            <!-- Room Controls -->
            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <button type="button" class="add-room-btn" id="addRoomBtn">+ Add Room</button>
            </div>
        </div>

        @if($homecheckReport->scheduler)
            <div class="info-box" style="background: #f9f9f9;">
                <p style="margin: 0; font-size: 13px; color: #666;">
                    <strong>Scheduled by:</strong> {{ $homecheckReport->scheduler->name }} 
                    on {{ $homecheckReport->created_at->format('M d, Y g:i A') }}
                </p>
            </div>
        @endif

        @if($homecheckReport->completed_at)
            <div class="info-box" style="background: #d4edda;">
                <p style="margin: 0; font-size: 13px; color: #155724;">
                    <strong>Completed:</strong> 
                    @if($homecheckReport->completer)
                        by {{ $homecheckReport->completer->name }}
                    @endif
                    on {{ $homecheckReport->completed_at->format('M d, Y g:i A') }}
                </p>
            </div>
        @endif

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-main">
                Update HomeCheck
            </button>
            <a href="{{ route('admin.homechecks.show', $homecheckReport->id) }}" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let roomIndex = 0;
let roomImages = {};

// Add Room Button Click
document.getElementById('addRoomBtn').addEventListener('click', function() {
    addRoomBlock();
});

// Add Room Block Function
function addRoomBlock() {
    roomIndex++;
    const roomId = 'room_' + roomIndex;
    
    roomImages[roomId] = {
        images: [],
        is360: false
    };
    
    const roomBlock = document.createElement('div');
    roomBlock.className = 'room-block';
    roomBlock.setAttribute('data-room-id', roomId);
    
    roomBlock.innerHTML = `
        <div class="room-header">
            <input type="text" 
                   name="rooms[${roomIndex}][name]" 
                   class="room-name-input" 
                   placeholder="Room Name (e.g., Living Room, Kitchen, Bedroom 1)" 
                   required>
            <button type="button" class="remove-room-btn" onclick="removeRoom(this)">✕ Remove Room</button>
        </div>
        
        <div class="image-type-toggle">
            <button type="button" class="image-type-btn active" data-room="${roomId}" data-type="photo" onclick="setImageType('${roomId}', 'photo')">
                📷 Regular Photo
            </button>
            <button type="button" class="image-type-btn" data-room="${roomId}" data-type="360" onclick="setImageType('${roomId}', '360')">
                🌐 360° Image
            </button>
        </div>
        
        <div class="file-upload-area" 
             id="uploadArea_${roomId}"
             ondragover="handleDragOver(event)"
             ondragleave="handleDragLeave(event)"
             ondrop="handleDrop(event, '${roomId}')"
             onclick="document.getElementById('fileInput_${roomId}').click()">
            <p style="margin: 0; font-size: 16px; color: #666;">
                📤 Drag & drop images here or click to upload
            </p>
            <p style="margin: 10px 0 0 0; font-size: 13px; color: #999;">
                Supported formats: JPG, PNG (Max 10MB per image)
            </p>
        </div>
        
        <input type="file" 
               id="fileInput_${roomId}" 
               name="rooms[${roomIndex}][images][]"
               multiple 
               accept="image/jpeg,image/png,image/jpg"
               class="file-input"
               onchange="handleFileSelect(event, '${roomId}')">
        
        <input type="hidden" name="rooms[${roomIndex}][is_360]" id="is360_${roomId}" value="0">
        
        <div class="preview-grid" id="preview_${roomId}"></div>
        
        <div style="margin-top: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #1E1E1E;">Moisture Reading (%)</label>
            <input type="number" 
                   name="rooms[${roomIndex}][moisture_reading]" 
                   step="0.1" 
                   min="0" 
                   max="100"
                   placeholder="e.g., 45.5"
                   style="width: 100%; padding: 10px; border: 1px solid #dcdcdc; border-radius: 6px; font-size: 15px; box-sizing: border-box;">
            <p style="font-size: 12px; color: #666; margin-top: 5px;">Optional: Enter moisture reading for this room (0-100%)</p>
        </div>
        
        <div class="error-message" id="error_${roomId}" style="display: none;"></div>
    `;
    
    document.getElementById('roomsContainer').appendChild(roomBlock);
}

// Set Image Type
function setImageType(roomId, type) {
    roomImages[roomId].is360 = (type === '360');
    document.getElementById('is360_' + roomId).value = type === '360' ? '1' : '0';
    
    const buttons = document.querySelectorAll(`[data-room="${roomId}"]`);
    buttons.forEach(btn => {
        if (btn.dataset.type === type) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    
    updatePreviewBadges(roomId);
}

// Update Preview Badges
function updatePreviewBadges(roomId) {
    const preview = document.getElementById('preview_' + roomId);
    if (!preview) return;
    
    const is360 = roomImages[roomId].is360;
    preview.querySelectorAll('.image-type-badge').forEach(badge => {
        badge.textContent = is360 ? '360°' : 'Photo';
    });
}

// Remove Room
function removeRoom(button) {
    const roomBlock = button.closest('.room-block');
    const roomId = roomBlock.getAttribute('data-room-id');
    delete roomImages[roomId];
    roomBlock.remove();
}

// Handle Drag Over
function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    e.currentTarget.classList.add('dragover');
}

// Handle Drag Leave
function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    e.currentTarget.classList.remove('dragover');
}

// Handle Drop
function handleDrop(e, roomId) {
    e.preventDefault();
    e.stopPropagation();
    e.currentTarget.classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        processFiles(Array.from(files), roomId);
    }
}

// Handle File Select
function handleFileSelect(e, roomId) {
    const files = e.target.files;
    if (files.length > 0) {
        processFiles(Array.from(files), roomId);
    }
}

// Process Files
function processFiles(files, roomId) {
    const errorDiv = document.getElementById('error_' + roomId);
    errorDiv.style.display = 'none';
    errorDiv.textContent = '';
    
    const validFiles = [];
    
    files.forEach(file => {
        if (!file.type.match('image.*')) {
            errorDiv.textContent = 'Please upload only image files (JPG, PNG)';
            errorDiv.style.display = 'block';
            return;
        }
        
        if (file.size > 10 * 1024 * 1024) {
            errorDiv.textContent = 'File size must not exceed 10MB';
            errorDiv.style.display = 'block';
            return;
        }
        
        validFiles.push(file);
    });
    
    if (validFiles.length === 0) return;
    
    if (!roomImages[roomId]) {
        roomImages[roomId] = { images: [], is360: false };
    }
    roomImages[roomId].images.push(...validFiles);
    
    updateFileInput(roomId);
    displayPreviews(roomId);
}

// Update File Input
function updateFileInput(roomId) {
    const input = document.getElementById('fileInput_' + roomId);
    const dt = new DataTransfer();
    
    roomImages[roomId].images.forEach(file => {
        dt.items.add(file);
    });
    
    input.files = dt.files;
}

// Display Previews
function displayPreviews(roomId) {
    const preview = document.getElementById('preview_' + roomId);
    if (!preview) return;
    
    preview.innerHTML = '';
    
    roomImages[roomId].images.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            
            const is360 = roomImages[roomId].is360;
            
            previewItem.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <span class="image-type-badge">${is360 ? '360°' : 'Photo'}</span>
                <button type="button" class="remove-image" onclick="removeImage('${roomId}', ${index})">×</button>
            `;
            
            preview.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });
}

// Remove Image
function removeImage(roomId, index) {
    roomImages[roomId].images.splice(index, 1);
    updateFileInput(roomId);
    displayPreviews(roomId);
}

// Form Validation
document.getElementById('homecheckForm').addEventListener('submit', function(e) {
    const rooms = document.querySelectorAll('.room-block');
    
    if (rooms.length > 0) {
        let hasError = false;
        
        rooms.forEach(room => {
            const roomId = room.getAttribute('data-room-id');
            const roomNameInput = room.querySelector('.room-name-input');
            const errorDiv = document.getElementById('error_' + roomId);
            
            if (!roomNameInput.value.trim()) {
                roomNameInput.style.borderColor = '#dc3545';
                errorDiv.textContent = 'Room name is required';
                errorDiv.style.display = 'block';
                hasError = true;
            } else {
                roomNameInput.style.borderColor = '';
            }
            
            if (!roomImages[roomId] || roomImages[roomId].images.length === 0) {
                errorDiv.textContent = 'Please upload at least one image for this room';
                errorDiv.style.display = 'block';
                hasError = true;
            }
        });
        
        if (hasError) {
            e.preventDefault();
            alert('Please complete all required fields: room name and at least one image per room.');
            return false;
        }
    }
});
</script>
@endpush
@endsection
