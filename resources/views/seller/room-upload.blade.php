@extends('layouts.seller')

@section('title', 'Abodeology HomeCheck – Room Upload')

@push('styles')
<style>
    body {
        font-family: 'Helvetica Neue', Arial, sans-serif;
        background: var(--soft-grey);
        color: var(--dark-text);
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 22px;
    }

    h1 {
        font-weight: 700;
        font-size: 28px;
        text-align: left;
        margin-bottom: 10px;
    }

    .subtitle {
        font-size: 15px;
        margin-bottom: 30px;
        opacity: 0.7;
        color: #666;
    }

    /* Add Room Button */
    #addRoomBtn {
        background: var(--black);
        color: var(--white);
        padding: 12px 22px;
        border: none;
        font-size: 15px;
        cursor: pointer;
        margin-bottom: 25px;
        border-radius: 6px;
        font-weight: 600;
        transition: opacity 0.3s ease;
    }

    #addRoomBtn:hover {
        opacity: 0.85;
    }

    /* Room Block */
    .room-block {
        border: 1px solid var(--line-grey);
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 12px;
        background: var(--white);
        box-shadow: 0px 2px 8px rgba(0,0,0,0.05);
    }

    .room-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .room-header input {
        font-size: 16px;
        padding: 10px 14px;
        width: 60%;
        border: 1px solid #D9D9D9;
        border-radius: 6px;
        outline: none;
        box-sizing: border-box;
    }

    .room-header input:focus {
        border-color: var(--abodeology-teal);
    }

    .room-header input.error {
        border-color: #dc3545;
    }

    .remove-room-btn {
        background: transparent;
        border: none;
        color: var(--danger);
        font-size: 18px;
        cursor: pointer;
        padding: 5px 10px;
        font-weight: bold;
        transition: color 0.3s ease;
    }

    .remove-room-btn:hover {
        color: #C73E3E;
    }

    /* Drop area */
    .drop-area {
        border: 2px dashed var(--black);
        padding: 25px;
        text-align: center;
        margin-top: 15px;
        cursor: pointer;
        border-radius: 6px;
        background: var(--soft-grey);
        transition: all 0.3s ease;
    }

    .drop-area.dragover {
        background: #e8e8e8;
        border-color: var(--abodeology-teal);
    }

    .drop-area:hover {
        background: #f0f0f0;
    }

    .preview-images {
        display: flex;
        flex-wrap: wrap;
        margin-top: 15px;
        gap: 10px;
    }

    .preview-images img {
        width: 130px;
        height: 130px;
        object-fit: cover;
        border: 1px solid var(--line-grey);
        border-radius: 6px;
        position: relative;
    }

    .preview-images .image-wrapper {
        position: relative;
        display: inline-block;
    }

    .preview-images .remove-image {
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--danger);
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    /* Submit button */
    #submitHomeCheck {
        background: var(--black);
        color: var(--white);
        padding: 14px 28px;
        border: none;
        font-size: 16px;
        margin-top: 20px;
        cursor: pointer;
        border-radius: 6px;
        font-weight: 600;
        transition: opacity 0.3s ease;
    }

    #submitHomeCheck:hover {
        opacity: 0.85;
    }

    #submitHomeCheck:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Error message */
    .error-message {
        color: var(--danger);
        font-size: 13px;
        margin-top: 5px;
        text-align: left;
    }

    /* Success message */
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
</style>
@endpush

@section('content')
<div class="container">
    <h1>Abodeology HomeCheck</h1>
    <p class="subtitle">Upload room images (360° or standard). Add as many rooms as needed.</p>

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

    @if(isset($existingRooms) && $existingRooms->count() > 0)
        <div style="background: #E8F4F3; border-left: 4px solid #2CB8B4; padding: 15px; margin-bottom: 20px; border-radius: 6px;">
            <h3 style="margin-top: 0; color: #2CB8B4; font-size: 18px;">Previously Uploaded Rooms</h3>
            @foreach($existingRooms as $roomName => $roomImages)
                <div style="margin-bottom: 15px;">
                    <strong style="color: #1E1E1E;">{{ $roomName }}</strong> ({{ $roomImages->count() }} image{{ $roomImages->count() !== 1 ? 's' : '' }})
                    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                        @foreach($roomImages as $image)
                            <img src="{{ \Storage::url($image->image_path) }}" 
                                 alt="{{ $roomName }}" 
                                 style="width: 100px; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <button id="addRoomBtn" type="button">+ Add Room</button>

    <form id="homeCheckForm" action="{{ route('seller.homecheck.store', $propertyId ?? 1) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="roomContainer"></div>
        <button type="submit" id="submitHomeCheck">Submit HomeCheck</button>
    </form>
</div>

@push('scripts')
<script>
let roomIndex = 0;
let roomImages = {}; // Store images per room

document.getElementById("addRoomBtn").addEventListener("click", () => {
    addRoomBlock();
});

function addRoomBlock() {
    roomIndex++;
    const roomId = 'room_' + roomIndex;
    
    const roomBlock = document.createElement("div");
    roomBlock.classList.add("room-block");
    roomBlock.setAttribute("data-room", roomId);
    
    roomBlock.innerHTML = `
        <div class="room-header">
            <input type="text" 
                   name="rooms[${roomIndex}][name]" 
                   placeholder="Room Name (e.g., Living Room)" 
                   class="room-name" 
                   required>
            <button type="button" class="remove-room-btn" onclick="removeRoom(this)">✕ Remove</button>
        </div>
        <div class="drop-area" 
             ondragover="handleDragOver(event)"
             ondragleave="handleDragLeave(event)"
             ondrop="handleDrop(event, '${roomId}')"
             onclick="document.getElementById('fileInput${roomId}').click()">
            Drag & drop images here or click to upload
        </div>
        <input type="file" 
               id="fileInput${roomId}" 
               name="rooms[${roomIndex}][images][]"
               multiple 
               accept="image/*"
               style="display:none"
               onchange="handleFileSelect(event, '${roomId}')">
        <div class="preview-images" id="preview${roomId}"></div>
        <div class="error-message" id="error${roomId}" style="display:none;"></div>
    `;
    
    document.getElementById("roomContainer").appendChild(roomBlock);
    roomImages[roomId] = [];
}

function removeRoom(btn) {
    const roomBlock = btn.closest('.room-block');
    const roomId = roomBlock.getAttribute('data-room');
    delete roomImages[roomId];
    roomBlock.remove();
}

function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    e.currentTarget.classList.add("dragover");
}

function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    e.currentTarget.classList.remove("dragover");
}

function handleDrop(e, roomId) {
    e.preventDefault();
    e.stopPropagation();
    e.currentTarget.classList.remove("dragover");
    const files = e.dataTransfer.files;
    handleFiles(files, roomId);
}

function handleFileSelect(e, roomId) {
    const files = e.target.files;
    handleFiles(files, roomId);
}

function handleFiles(files, roomId) {
    const preview = document.getElementById("preview" + roomId);
    const errorDiv = document.getElementById("error" + roomId);
    errorDiv.style.display = 'none';
    
    if (!roomImages[roomId]) {
        roomImages[roomId] = [];
    }
    
    Array.from(files).forEach((file) => {
        if (!file.type.startsWith('image/')) {
            errorDiv.textContent = 'Please upload only image files.';
            errorDiv.style.display = 'block';
            return;
        }
        
        if (file.size > 10 * 1024 * 1024) { // 10MB limit
            errorDiv.textContent = 'Image size must be less than 10MB.';
            errorDiv.style.display = 'block';
            return;
        }
        
        roomImages[roomId].push(file);
        displayImage(file, roomId, preview);
    });
}

function displayImage(file, roomId, preview) {
    const img = document.createElement("img");
    img.src = URL.createObjectURL(file);
    
    const wrapper = document.createElement("div");
    wrapper.classList.add("image-wrapper");
    
    const removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.classList.add("remove-image");
    removeBtn.innerHTML = "×";
    removeBtn.onclick = function() {
        removeImage(roomId, file, wrapper);
    };
    
    wrapper.appendChild(img);
    wrapper.appendChild(removeBtn);
    preview.appendChild(wrapper);
}

function removeImage(roomId, file, wrapper) {
    const index = roomImages[roomId].indexOf(file);
    if (index > -1) {
        roomImages[roomId].splice(index, 1);
    }
    wrapper.remove();
    
    // Update file input
    const input = document.getElementById('fileInput' + roomId);
    const dt = new DataTransfer();
    roomImages[roomId].forEach(f => dt.items.add(f));
    input.files = dt.files;
}

document.getElementById("homeCheckForm").addEventListener("submit", function(e) {
    const rooms = document.querySelectorAll(".room-block");
    
    if (rooms.length === 0) {
        e.preventDefault();
        alert("Please add at least one room.");
        return false;
    }
    
    let hasError = false;
    rooms.forEach((room) => {
        const roomNameInput = room.querySelector(".room-name");
        const roomId = room.getAttribute("data-room");
        const preview = document.getElementById("preview" + roomId);
        
        if (!roomNameInput.value.trim()) {
            roomNameInput.classList.add("error");
            hasError = true;
        } else {
            roomNameInput.classList.remove("error");
        }
        
        if (!preview || preview.children.length === 0) {
            const errorDiv = document.getElementById("error" + roomId);
            errorDiv.textContent = 'Please upload at least one image for this room.';
            errorDiv.style.display = 'block';
            hasError = true;
        }
    });
    
    if (hasError) {
        e.preventDefault();
        alert("Please complete all room information: name and at least one image per room.");
        return false;
    }
    
    if (!confirm('Are you sure you want to submit the HomeCheck? This action cannot be undone.')) {
        e.preventDefault();
        return false;
    }
    
    // Update file inputs before submission
    rooms.forEach((room) => {
        const roomId = room.getAttribute("data-room");
        const input = document.getElementById('fileInput' + roomId);
        if (roomImages[roomId] && roomImages[roomId].length > 0) {
            const dt = new DataTransfer();
            roomImages[roomId].forEach(file => dt.items.add(file));
            input.files = dt.files;
        }
    });
});
</script>
@endpush
@endsection
