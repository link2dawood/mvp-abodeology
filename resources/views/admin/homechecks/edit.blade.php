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

    /* Room Block Styles */
    .room-block {
        background: #F9F9F9;
        border: 2px solid #dcdcdc;
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
        border: 1px solid #dcdcdc;
        border-radius: 6px;
        font-size: 16px;
        margin-right: 10px;
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

    .existing-image-item {
        position: relative;
        border: 2px solid #28a745;
        border-radius: 8px;
        overflow: hidden;
        background: #f0f9f0;
    }

    .existing-image-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        display: block;
    }

    .existing-image-item .delete-existing-image {
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

    .existing-image-item .existing-badge {
        position: absolute;
        bottom: 5px;
        left: 5px;
        background: rgba(40, 167, 69, 0.9);
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

            <div style="margin-top: 15px; padding: 15px; background: #f0f8ff; border-radius: 6px; border: 1px solid #b3d9ff;">
                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: #0066cc;">
                    <input type="checkbox" 
                           name="process_ai" 
                           value="1" 
                           id="process_ai"
                           style="margin-right: 10px; width: 18px; height: 18px; cursor: pointer;">
                    <span>🤖 Process AI Analysis After Update</span>
                </label>
                <p style="margin: 8px 0 0 30px; font-size: 13px; color: #666;">
                    If checked, AI analysis will be automatically processed for all images (including newly uploaded ones) after saving changes. This may take a few moments.
                </p>
            </div>
        </div>

        <!-- Add New Rooms Section - Moved to Top -->
        <div class="card" style="margin-bottom: 20px;">
            <h3 style="margin-bottom: 15px; color: var(--abodeology-teal);">Add New Rooms</h3>
            <p style="color: #666; margin-bottom: 15px; font-size: 14px;">Click the button below to add a new room to this HomeCheck report.</p>
            
            <!-- Rooms Container for New Rooms -->
            <div id="roomsContainer" style="margin-bottom: 15px;">
                <!-- Rooms will be added dynamically via JavaScript -->
            </div>
            
            <!-- Add Room Button -->
            <button type="button" class="add-room-btn" id="addRoomBtn">+ Add New Room</button>
        </div>

        <!-- Existing Rooms (Editable) -->
        @if($roomsData && $roomsData->count() > 0)
        <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h3 style="margin: 0; color: var(--abodeology-teal);">Add/Remove Rooms</h3>
                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="expandAllRooms()" style="background: #28a745; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px;">Expand All</button>
                    <button type="button" onclick="collapseAllRooms()" style="background: #6c757d; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px;">Collapse All</button>
                </div>
            </div>
            <p style="color: #666; margin-bottom: 15px;">View, edit room names, delete individual images, delete entire rooms, or add new images to existing rooms.</p>
        </div>
        
        <div class="card" style="padding: 0; background: transparent; border: none; box-shadow: none;">

            @foreach($roomsData as $roomName => $roomImages)
                @if($roomImages && $roomImages->count() > 0)
                @php
                    $firstImage = $roomImages->first();
                    $roomId = $firstImage ? 'existing_room_' . $firstImage->id : 'room_' . $loop->index;
                    $moistureReading = $firstImage ? ($firstImage->moisture_reading ?? null) : null;
                @endphp

                @if($firstImage)
                <div class="room-block" data-room-name="{{ $roomName }}" id="existing_room_block_{{ $firstImage->id }}">
                    <div class="room-header" onclick="toggleRoomCollapse('{{ $firstImage->id }}')">
                        <div class="room-header-content">
                            <span class="room-toggle-icon">▼</span>
                            <input type="text" 
                                   name="existing_rooms[{{ $firstImage->id }}][name]" 
                                   value="{{ old('existing_rooms.' . $firstImage->id . '.name', $roomName) }}"
                                   class="room-name-input" 
                                   placeholder="Room Name" 
                                   required
                                   onclick="event.stopPropagation();">
                            <input type="hidden" name="existing_rooms[{{ $firstImage->id }}][id]" value="{{ $firstImage->id }}">
                            <input type="hidden" name="existing_rooms[{{ $firstImage->id }}][delete_room]" id="delete_room_{{ $firstImage->id }}" value="0">
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <button type="button" 
                                    class="update-room-btn" 
                                    onclick="event.stopPropagation(); updateRoom({{ $firstImage->id }}, 'existing')"
                                    style="background: #28a745; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                ✓ Update Room
                            </button>
                            <button type="button" 
                                    class="remove-room-btn" 
                                    onclick="event.stopPropagation(); deleteRoom('{{ $firstImage->id }}', '{{ $roomName }}')"
                                    style="background: #dc3545; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                🗑️ Delete Entire Room
                            </button>
                        </div>
                    </div>

                    <div class="room-content">
                    <!-- Existing Images -->
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 600; margin-bottom: 10px; display: block;">Existing Images (click × to delete)</label>
                        <div class="preview-grid" id="existing_images_{{ $firstImage->id }}">
                            @foreach($roomImages as $image)
                                @if($image->id)
                                    @php
                                        // Use proxy endpoint - avoids slow image_url accessor (S3 file checks)
                                        try {
                                            $imageUrl = route('admin.homecheck.image', ['id' => $image->id]);
                                        } catch (\Exception $e) {
                                            $imageUrl = url('/admin/homecheck-image/' . $image->id);
                                        }
                                    @endphp
                                    <div class="existing-image-item" data-image-id="{{ $image->id }}">
                                        <img src="{{ $imageUrl }}" alt="{{ $roomName }} Image" loading="lazy" decoding="async">
                                        <span class="existing-badge">{{ $image->is_360 ? '360°' : 'Photo' }}</span>
                                        <button type="button" 
                                                class="delete-existing-image" 
                                                onclick="deleteExistingImage({{ $image->id }}, '{{ $firstImage->id }}')"
                                                title="Delete this image">×</button>
                                        <input type="hidden" 
                                               name="existing_rooms[{{ $firstImage->id }}][delete_images][]" 
                                               id="delete_image_{{ $image->id }}" 
                                               value="">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Add New Images to Existing Room -->
                    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dcdcdc;">
                        <label style="font-weight: 600; margin-bottom: 10px; display: block;">Add More Images to This Room</label>
                        
                        <div class="image-type-toggle">
                            <button type="button" class="image-type-btn active" data-room="{{ $roomId }}" data-type="photo" onclick="setImageType('{{ $roomId }}', 'photo')">
                                📷 Regular Photo
                            </button>
                            <button type="button" class="image-type-btn" data-room="{{ $roomId }}" data-type="360" onclick="setImageType('{{ $roomId }}', '360')">
                                🌐 360° Image
                            </button>
                        </div>
                        
                        <div class="file-upload-area" 
                             id="uploadArea_{{ $roomId }}"
                             ondragover="handleDragOver(event)"
                             ondragleave="handleDragLeave(event)"
                             ondrop="handleDrop(event, '{{ $roomId }}')"
                             onclick="document.getElementById('fileInput_{{ $roomId }}').click()">
                            <p style="margin: 0; font-size: 16px; color: #666;">
                                📤 Drag & drop images here or click to upload
                            </p>
                            <p style="margin: 10px 0 0 0; font-size: 13px; color: #999;">
                                Supported formats: JPG, PNG
                            </p>
                        </div>
                        
                        <input type="file" 
                               id="fileInput_{{ $roomId }}" 
                               name="existing_rooms[{{ $firstImage->id }}][new_images][]"
                               multiple 
                               accept="image/jpeg,image/png,image/jpg"
                               class="file-input"
                               onchange="handleFileSelect(event, '{{ $roomId }}')">
                        
                        <input type="hidden" name="existing_rooms[{{ $firstImage->id }}][is_360]" id="is360_{{ $roomId }}" value="0">
                        
                        <div class="preview-grid" id="preview_{{ $roomId }}"></div>
                    </div>

                    <!-- Moisture Reading -->
                    <div style="margin-top: 15px;">
                        <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #1E1E1E;">Moisture Reading (%)</label>
                        <input type="number" 
                               name="existing_rooms[{{ $firstImage->id }}][moisture_reading]" 
                               step="0.1" 
                               min="0" 
                               max="100"
                               value="{{ old('existing_rooms.' . $firstImage->id . '.moisture_reading', $moistureReading) }}"
                               placeholder="e.g., 45.5"
                               style="width: 100%; padding: 10px; border: 1px solid #dcdcdc; border-radius: 6px; font-size: 15px; box-sizing: border-box;">
                        <p style="font-size: 12px; color: #666; margin-top: 5px;">Optional: Enter moisture reading for this room (0-100%)</p>
                    </div>
                    </div>
                </div>
                @endif
                @endif
            @endforeach
        </div>
        @endif

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
let deletedImages = {};

// Delete Existing Image
function deleteExistingImage(imageId, roomKey) {
    if (confirm('Are you sure you want to delete this image?')) {
        document.getElementById('delete_image_' + imageId).value = imageId;
        const imageElement = document.querySelector('[data-image-id="' + imageId + '"]');
        if (imageElement) {
            imageElement.style.display = 'none';
        }
        deletedImages[imageId] = true;
    }
}

// Delete Entire Room
function deleteRoom(roomId, roomName) {
    if (confirm('Are you sure you want to delete the entire room "' + roomName + '" and all its images? This action cannot be undone.')) {
        document.getElementById('delete_room_' + roomId).value = '1';
        const roomBlock = document.getElementById('existing_room_block_' + roomId);
        if (roomBlock) {
            roomBlock.style.display = 'none';
            roomBlock.style.opacity = '0.5';
        }
    }
}

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
        <div class="room-header" style="display: flex; justify-content: space-between; align-items: center;">
            <input type="text" 
                   name="rooms[${roomIndex}][name]" 
                   class="room-name-input" 
                   placeholder="Room Name (e.g., Living Room, Kitchen, Bedroom 1)" 
                   required
                   style="flex: 1; margin-right: 10px;">
            <button type="button" 
                    class="update-room-btn" 
                    onclick="updateRoom(${roomIndex}, 'new')"
                    style="background: #28a745; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: 600;">
                ✓ Update Room
            </button>
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
                Supported formats: JPG, PNG (Images will be compressed automatically)
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
    if (!roomImages[roomId]) {
        roomImages[roomId] = { images: [], is360: false };
    }
    roomImages[roomId].is360 = (type === '360');
    const is360Input = document.getElementById('is360_' + roomId);
    if (is360Input) {
        is360Input.value = type === '360' ? '1' : '0';
    }
    
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
    
    const is360 = roomImages[roomId] ? roomImages[roomId].is360 : false;
    preview.querySelectorAll('.image-type-badge').forEach(badge => {
        badge.textContent = is360 ? '360°' : 'Photo';
    });
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
    if (!roomImages[roomId]) {
        roomImages[roomId] = { images: [], is360: false };
    }
    
    const errorDiv = document.getElementById('error_' + roomId);
    if (errorDiv) {
        errorDiv.style.display = 'none';
        errorDiv.textContent = '';
    }
    
    const validFiles = [];
    
    files.forEach(file => {
        if (!file.type.match('image.*')) {
            if (errorDiv) {
                errorDiv.textContent = 'Please upload only image files (JPG, PNG)';
                errorDiv.style.display = 'block';
            }
            return;
        }
        
        // No file size restriction - images will be compressed in background
        validFiles.push(file);
    });
    
    if (validFiles.length === 0) return;
    
    roomImages[roomId].images.push(...validFiles);
    
    updateFileInput(roomId);
    displayPreviews(roomId);
}

// Update File Input
function updateFileInput(roomId) {
    const input = document.getElementById('fileInput_' + roomId);
    if (!input) return;
    
    const dt = new DataTransfer();
    
    if (roomImages[roomId] && roomImages[roomId].images) {
        roomImages[roomId].images.forEach(file => {
            dt.items.add(file);
        });
    }
    
    input.files = dt.files;
}

// Display Previews
function displayPreviews(roomId) {
    const preview = document.getElementById('preview_' + roomId);
    if (!preview || !roomImages[roomId]) return;
    
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
    if (roomImages[roomId] && roomImages[roomId].images) {
        roomImages[roomId].images.splice(index, 1);
        updateFileInput(roomId);
        displayPreviews(roomId);
    }
}

// Toggle Room Collapse
function toggleRoomCollapse(roomId) {
    const roomBlock = document.getElementById('existing_room_block_' + roomId);
    if (roomBlock) {
        roomBlock.classList.toggle('collapsed');
    }
}

// Expand All Rooms
function expandAllRooms() {
    const roomBlocks = document.querySelectorAll('.room-block');
    roomBlocks.forEach(block => {
        block.classList.remove('collapsed');
    });
}

// Collapse All Rooms
function collapseAllRooms() {
    const roomBlocks = document.querySelectorAll('.room-block');
    roomBlocks.forEach(block => {
        block.classList.add('collapsed');
    });
}

// Update Individual Room
function updateRoom(roomId, type) {
    const homecheckId = {{ $homecheckReport->id }};
    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value);
    formData.append('_method', 'PUT');
    
    let roomData = null;
    let roomElement = null;
    
    if (type === 'existing') {
        // Existing room
        roomElement = document.getElementById('existing_room_block_' + roomId);
        if (!roomElement) {
            alert('Room element not found');
            return;
        }
        
        // Get room name
        const roomNameInput = roomElement.querySelector('input[name*="[name]"]');
        const roomName = roomNameInput ? roomNameInput.value : '';
        
        if (!roomName) {
            alert('Please enter a room name');
            return;
        }
        
        // Get moisture reading
        const moistureInput = roomElement.querySelector('input[name*="[moisture_reading]"]');
        const moistureReading = moistureInput ? moistureInput.value : '';
        
        // Get is_360 flag
        const is360Input = roomElement.querySelector('input[name*="[is_360]"]');
        const is360 = is360Input ? is360Input.value : '0';
        
        // Get delete images list
        const deleteImageInputs = roomElement.querySelectorAll('input[name*="[delete_images]"]');
        const deleteImages = [];
        deleteImageInputs.forEach(input => {
            if (input.value) {
                deleteImages.push(input.value);
            }
        });
        
        // Get new images
        const newImagesInput = roomElement.querySelector('input[name*="[new_images]"]');
        const newImages = newImagesInput ? newImagesInput.files : [];
        
        formData.append('existing_rooms[' + roomId + '][id]', roomId);
        formData.append('existing_rooms[' + roomId + '][name]', roomName);
        formData.append('existing_rooms[' + roomId + '][moisture_reading]', moistureReading);
        formData.append('existing_rooms[' + roomId + '][is_360]', is360);
        formData.append('existing_rooms[' + roomId + '][delete_room]', '0');
        
        // Add delete images
        deleteImages.forEach(imgId => {
            formData.append('existing_rooms[' + roomId + '][delete_images][]', imgId);
        });
        
        // Add new images
        for (let i = 0; i < newImages.length; i++) {
            formData.append('existing_rooms[' + roomId + '][new_images][]', newImages[i]);
        }
        
    } else {
        // New room
        roomElement = document.querySelector('[data-room-id="room_' + roomId + '"]');
        if (!roomElement) {
            alert('Room element not found');
            return;
        }
        
        // Get room name
        const roomNameInput = roomElement.querySelector('input[name*="[name]"]');
        const roomName = roomNameInput ? roomNameInput.value : '';
        
        if (!roomName) {
            alert('Please enter a room name');
            return;
        }
        
        // Get moisture reading
        const moistureInput = roomElement.querySelector('input[name*="[moisture_reading]"]');
        const moistureReading = moistureInput ? moistureInput.value : '';
        
        // Get is_360 flag
        const is360Input = roomElement.querySelector('input[name*="[is_360]"]');
        const is360 = is360Input ? is360Input.value : '0';
        
        // Get images
        const imagesInput = roomElement.querySelector('input[name*="[images]"]');
        const images = imagesInput ? imagesInput.files : [];
        
        if (images.length === 0) {
            alert('Please upload at least one image for this room');
            return;
        }
        
        formData.append('rooms[' + roomId + '][name]', roomName);
        formData.append('rooms[' + roomId + '][moisture_reading]', moistureReading);
        formData.append('rooms[' + roomId + '][is_360]', is360);
        
        // Add images
        for (let i = 0; i < images.length; i++) {
            formData.append('rooms[' + roomId + '][images][]', images[i]);
        }
    }
    
    // Show loading state
    const updateBtn = roomElement.querySelector('.update-room-btn');
    const originalText = updateBtn.textContent;
    updateBtn.disabled = true;
    updateBtn.textContent = 'Updating...';
    updateBtn.style.opacity = '0.6';
    
    // Send AJAX request
    const updateUrl = '{{ route("admin.homechecks.update-room", $homecheckReport->id) }}';
    fetch(updateUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Room updated successfully!');
            
            // If it's a new room, reload the page to show it as existing
            if (type === 'new') {
                window.location.reload();
            } else {
                // Clear file inputs for existing rooms
                const fileInput = roomElement.querySelector('input[type="file"]');
                if (fileInput) {
                    fileInput.value = '';
                }
                const previewGrid = roomElement.querySelector('.preview-grid[id^="preview_"]');
                if (previewGrid && previewGrid.id.includes('preview_')) {
                    previewGrid.innerHTML = '';
                }
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to update room'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the room. Please try again.');
    })
    .finally(() => {
        updateBtn.disabled = false;
        updateBtn.textContent = originalText;
        updateBtn.style.opacity = '1';
    });
}
</script>
@endpush
@endsection
