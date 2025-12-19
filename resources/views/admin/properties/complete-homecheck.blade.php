@extends('layouts.admin')

@section('title', 'Complete HomeCheck - ' . $property->address)

@push('styles')
<style>
    h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .page-subtitle {
        color: #666;
        margin-bottom: 25px;
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

    .property-info {
        background: #F4F4F4;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .room-block {
        background: #F9F9F9;
        border: 2px solid var(--line-grey);
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .room-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        cursor: pointer;
        padding: 10px;
        margin: -10px -10px 15px -10px;
        border-radius: 6px;
        transition: background 0.2s ease;
    }

    .room-header:hover {
        background: rgba(44, 184, 180, 0.1);
    }

    .room-header-content {
        display: flex;
        align-items: center;
        flex: 1;
        gap: 10px;
    }

    .room-toggle-icon {
        font-size: 18px;
        color: var(--abodeology-teal);
        transition: transform 0.3s ease;
        min-width: 20px;
    }

    .room-block.collapsed .room-toggle-icon {
        transform: rotate(-90deg);
    }

    .room-content {
        transition: max-height 0.3s ease, opacity 0.3s ease;
        overflow: hidden;
    }

    .room-block.collapsed .room-content {
        max-height: 0;
        opacity: 0;
        margin: 0;
        padding: 0;
    }

    .room-block:not(.collapsed) .room-content {
        max-height: 5000px;
        opacity: 1;
    }

    .room-name-input {
        flex: 1;
        padding: 10px;
        border: 1px solid var(--line-grey);
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

    .remove-room-btn:hover {
        background: #c82333;
    }

    .image-type-toggle {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    .image-type-btn {
        padding: 8px 15px;
        border: 2px solid var(--abodeology-teal);
        background: white;
        color: var(--abodeology-teal);
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .image-type-btn.active {
        background: var(--abodeology-teal);
        color: white;
    }

    .file-upload-area {
        border: 2px dashed var(--line-grey);
        border-radius: 8px;
        padding: 40px;
        text-align: center;
        margin-bottom: 15px;
        transition: border-color 0.3s ease;
        cursor: pointer;
    }

    .file-upload-area:hover {
        border-color: var(--abodeology-teal);
        background: #F0F9F9;
    }

    .file-upload-area.dragover {
        border-color: var(--abodeology-teal);
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
        border: 2px solid var(--line-grey);
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

    .btn {
        padding: 12px 25px;
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

    .notes-textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--line-grey);
        border-radius: 6px;
        font-family: inherit;
        font-size: 14px;
        min-height: 100px;
        resize: vertical;
    }

    .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 5px;
    }

    .success-message {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
        padding: 12px 20px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2>Complete HomeCheck</h2>
            <p class="page-subtitle">Upload 360° images and photos for each room</p>
        </div>
        <div>
            <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-secondary">Back to Property</a>
        </div>
    </div>

    @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Property Information -->
    <div class="card">
        <div class="property-info">
            <h3 style="margin-top: 0; color: var(--abodeology-teal);">Property Information</h3>
            <p><strong>Address:</strong> {{ $property->address }}</p>
            @if($property->postcode)
                <p><strong>Postcode:</strong> {{ $property->postcode }}</p>
            @endif
            @if($homecheckReport)
                <p><strong>Scheduled Date:</strong> {{ $homecheckReport->scheduled_date ? \Carbon\Carbon::parse($homecheckReport->scheduled_date)->format('l, F j, Y') : 'Not set' }}</p>
            @endif
        </div>
    </div>

    <!-- HomeCheck Upload Form -->
    <form action="{{ route('admin.properties.complete-homecheck.store', $property->id) }}" method="POST" enctype="multipart/form-data" id="homecheckForm">
        @csrf

        <!-- Existing HomeCheck Data (if any) -->
        @if($homecheckData && $homecheckData->count() > 0)
        <div class="card">
            <h3>Previously Uploaded Images</h3>
            <p style="color: #666; margin-bottom: 15px;">You can add more images to existing rooms or create new rooms below.</p>
            @foreach($homecheckData->groupBy('room_name') as $roomName => $roomImages)
                <div style="margin-bottom: 20px; padding: 15px; background: #F9F9F9; border-radius: 6px;">
                    <strong>{{ $roomName }}</strong> - {{ $roomImages->count() }} image(s)
                </div>
            @endforeach
        </div>
        @endif

        <!-- Rooms Container -->
        <div id="roomsContainer">
            <!-- Rooms will be added dynamically via JavaScript -->
        </div>

        <!-- Room Controls -->
        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <button type="button" class="add-room-btn" id="addRoomBtn">+ Add Room</button>
            <button type="button" class="btn btn-secondary" id="expandAllBtn" style="background: #6c757d; color: white;">Expand All</button>
            <button type="button" class="btn btn-secondary" id="collapseAllBtn" style="background: #6c757d; color: white;">Collapse All</button>
        </div>

        <!-- Notes Section -->
        <div class="card">
            <h3>Additional Notes</h3>
            <textarea name="notes" class="notes-textarea" placeholder="Add any additional notes about the HomeCheck (optional)">{{ old('notes', $homecheckReport->notes ?? '') }}</textarea>
        </div>

        <!-- Submit Button -->
        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-main" style="font-size: 16px; padding: 15px 30px;">Complete HomeCheck</button>
            <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let roomIndex = 0;
let roomImages = {}; // Store images per room: { roomId: { images: [], is360: false } }

// Add Room Button Click
document.getElementById('addRoomBtn').addEventListener('click', function() {
    addRoomBlock();
});

// Add Room Block Function
function addRoomBlock() {
    roomIndex++;
    const roomId = 'room_' + roomIndex;
    
    // Initialize room data
    roomImages[roomId] = {
        images: [],
        is360: false
    };
    
    const roomBlock = document.createElement('div');
    roomBlock.className = 'room-block';
    roomBlock.setAttribute('data-room-id', roomId);
    
    roomBlock.innerHTML = `
        <div class="room-header" onclick="toggleRoomCollapse('${roomId}')">
            <div class="room-header-content">
                <span class="room-toggle-icon">▼</span>
                <input type="text" 
                       name="rooms[${roomIndex}][name]" 
                       class="room-name-input" 
                       placeholder="Room Name (e.g., Living Room, Kitchen, Bedroom 1)" 
                       required
                       onclick="event.stopPropagation();"
                       style="flex: 1;">
            </div>
            <button type="button" class="remove-room-btn" onclick="event.stopPropagation(); removeRoom(this)">✕ Remove Room</button>
        </div>
        
        <div class="room-content">
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
                Supported formats: JPG, PNG
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
                   style="width: 100%; padding: 10px; border: 1px solid #D9D9D9; border-radius: 6px; font-size: 15px; box-sizing: border-box;">
            <p style="font-size: 12px; color: #666; margin-top: 5px;">Optional: Enter moisture reading for this room (0-100%)</p>
        </div>
        
        <div class="error-message" id="error_${roomId}" style="display: none;"></div>
        </div>
    `;
    
    document.getElementById('roomsContainer').appendChild(roomBlock);
}

// Set Image Type (Photo or 360)
function setImageType(roomId, type) {
    roomImages[roomId].is360 = (type === '360');
    document.getElementById('is360_' + roomId).value = type === '360' ? '1' : '0';
    
    // Update button states
    const buttons = document.querySelectorAll(`[data-room="${roomId}"]`);
    buttons.forEach(btn => {
        if (btn.dataset.type === type) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    
    // Update preview badges
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
        // Validate file type
        if (!file.type.match('image.*')) {
            errorDiv.textContent = 'Please upload only image files (JPG, PNG)';
            errorDiv.style.display = 'block';
            return;
        }
        
        // Validate file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            errorDiv.textContent = 'File size must not exceed 10MB';
            errorDiv.style.display = 'block';
            return;
        }
        
        validFiles.push(file);
    });
    
    if (validFiles.length === 0) return;
    
    // Add to room images
    if (!roomImages[roomId]) {
        roomImages[roomId] = { images: [], is360: false };
    }
    roomImages[roomId].images.push(...validFiles);
    
    // Update file input
    updateFileInput(roomId);
    
    // Display previews
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
    
    if (rooms.length === 0) {
        e.preventDefault();
        alert('Please add at least one room.');
        return false;
    }
    
    let hasError = false;
    
    rooms.forEach(room => {
        const roomId = room.getAttribute('data-room-id');
        const roomNameInput = room.querySelector('.room-name-input');
        const preview = document.getElementById('preview_' + roomId);
        const errorDiv = document.getElementById('error_' + roomId);
        
        // Validate room name
        if (!roomNameInput.value.trim()) {
            roomNameInput.style.borderColor = '#dc3545';
            errorDiv.textContent = 'Room name is required';
            errorDiv.style.display = 'block';
            hasError = true;
        } else {
            roomNameInput.style.borderColor = '';
        }
        
        // Validate images
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
    
    if (!confirm('Are you sure you want to submit the HomeCheck? This will process all images and generate an AI report.')) {
        e.preventDefault();
        return false;
    }
});

// Toggle Room Collapse
function toggleRoomCollapse(roomId) {
    const roomBlock = document.querySelector(`[data-room-id="${roomId}"]`);
    if (roomBlock) {
        roomBlock.classList.toggle('collapsed');
    }
}

// Expand All Rooms
document.getElementById('expandAllBtn').addEventListener('click', function() {
    const roomBlocks = document.querySelectorAll('.room-block');
    roomBlocks.forEach(block => {
        block.classList.remove('collapsed');
    });
});

// Collapse All Rooms
document.getElementById('collapseAllBtn').addEventListener('click', function() {
    const roomBlocks = document.querySelectorAll('.room-block');
    roomBlocks.forEach(block => {
        block.classList.add('collapsed');
    });
});

// Add first room on page load
window.addEventListener('DOMContentLoaded', function() {
    addRoomBlock();
});
</script>
@endpush
@endsection
