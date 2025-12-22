@extends('layouts.admin')

@section('title', 'Abodeology HomeCheck – Edit Report')

@push('styles')
<style>
 :root {
 --primary: #32b3ac;
 --primary-dark: #289a94;
 --black: #000;
 --white: #fff;
 --grey-light: #f7f7f7;
 --grey: #e5e5e5;
 --text: #222;
 --text-light: #666;
 --radius: 12px;
 --shadow: 0 4px 18px rgba(0,0,0,0.08);
 }
 body {
 margin: 0;
 font-family: "Inter", Arial, sans-serif;
 background: var(--grey-light);
 color: var(--text);
 }
 /* PAGE CONTAINER */
 .container {
 max-width: 1200px;
 margin: 30px auto;
 padding: 20px;
 }
 h1 {
 font-size: 26px;
 font-weight: 700;
 margin-bottom: 10px;
 }
 /* PROPERTY HEADER */
 .property-header {
 background: white;
 padding: 20px 25px;
 border-radius: 12px;
 box-shadow: 0 4px 18px rgba(0,0,0,0.06);
 margin-bottom: 25px;
 }
 .property-header h2 {
 margin: 0;
 font-size: 22px;
 font-weight: 700;
 }
 .property-header p {
 margin: 5px 0 0;
 color: #555;
 font-size: 15px;
 }
 /* TABS */
 .tabs {
 display: flex;
 gap: 8px;
 margin-bottom: 20px;
 }
 .tab {
 padding: 12px 20px;
 background: #ececec;
 border-radius: var(--radius);
 cursor: pointer;
 font-weight: 600;
 transition: 0.2s;
 border: none;
 }
 .tab.active {
 background: var(--primary);
 color: white;
 }
 .tab:hover { opacity: 0.9; }
 .tab-content { display: none; }
 .tab-content.active { display: block; }
 /* SECTIONS */
 .section {
 background: white;
 padding: 25px;
 border-radius: var(--radius);
 box-shadow: var(--shadow);
 margin-bottom: 25px;
 }
 label {
 display: block;
 font-weight: 600;
 margin-bottom: 5px;
 }
 input, textarea, select {
 width: 100%;
 padding: 12px;
 border-radius: var(--radius);
 border: 1px solid var(--grey);
 font-size: 14px;
 box-sizing: border-box;
 }
 .button {
 padding: 12px 20px;
 border-radius: var(--radius);
 font-size: 15px;
 font-weight: 600;
 cursor: pointer;
 border: none;
 transition: 0.2s;
 }
 .btn-primary { background: var(--primary); color: white; }
 .btn-danger { background: #d9534f; color: white; }
 .btn-outline { background: white; border: 2px solid var(--primary); color: var(--primary); }
 .btn-secondary { background: #666; color: white; }
 .button:hover { opacity: 0.9; }
 /* ROOM LIST */
 .room-list {
 padding: 10px 0;
 }
 .room-item {
 background: white;
 padding: 18px;
 margin-bottom: 12px;
 border-radius: var(--radius);
 box-shadow: var(--shadow);
 display: flex;
 justify-content: space-between;
 align-items: center;
 }
 .room-item-title {
 font-size: 17px;
 font-weight: 600;
 }
 /* MODAL */
 .modal-bg {
 position: fixed;
 inset: 0;
 background: rgba(0,0,0,0.5);
 display: none;
 justify-content: center;
 align-items: flex-start;
 padding-top: 40px;
 z-index: 20;
 overflow-y: auto;
 }
 .modal {
 background: white;
 width: 850px;
 max-width: 95%;
 border-radius: var(--radius);
 padding: 25px;
 box-shadow: var(--shadow);
 animation: fadeIn 0.2s ease;
 margin-bottom: 40px;
 }
 @keyframes fadeIn {
 from { opacity: 0; transform: translateY(-10px); }
 to { opacity: 1; transform: translateY(0); }
 }
 .modal h2 {
 margin-top: 0;
 margin-bottom: 20px;
 font-size: 22px;
 }
 .modal h3 {
 margin-top: 20px;
 margin-bottom: 15px;
 font-size: 18px;
 }
 /* IMAGE GRID */
 .image-grid {
 display: flex;
 gap: 12px;
 flex-wrap: wrap;
 margin-bottom: 15px;
 }
 .image-card {
 width: 160px;
 height: 110px;
 border-radius: 10px;
 background-size: cover;
 background-position: center;
 position: relative;
 border: 2px solid #dcdcdc;
 }
 .image-card span {
 position: absolute;
 bottom: 6px;
 right: 6px;
 background: var(--primary);
 color: white;
 font-size: 11px;
 padding: 3px 6px;
 border-radius: 6px;
 }
 .delete-img {
 position: absolute;
 top: 6px;
 right: 6px;
 background: #d9534f;
 width: 22px;
 height: 22px;
 border-radius: 50%;
 color: white;
 font-size: 13px;
 font-weight: 900;
 display: flex;
 justify-content: center;
 align-items: center;
 cursor: pointer;
 border: none;
 }
 .delete-img:hover {
 background: #c9302c;
 }
 /* AI TEXT BOX */
 .ai-box {
 background: #f3fdfa;
 border-left: 4px solid var(--primary);
 padding: 15px;
 border-radius: var(--radius);
 margin-top: 20px;
 font-size: 14px;
 }
 .ai-box strong {
 display: block;
 margin-bottom: 8px;
 color: var(--primary);
 }
 .ai-box p {
 margin: 0;
 line-height: 1.6;
 color: var(--text);
 }
 /* FILE UPLOAD */
 .file-upload-area {
 border: 2px dashed #dcdcdc;
 border-radius: var(--radius);
 padding: 40px;
 text-align: center;
 margin-bottom: 15px;
 cursor: pointer;
 transition: 0.2s;
 }
 .file-upload-area:hover {
 border-color: var(--primary);
 background: #f0f9f9;
 }
 .file-input {
 display: none;
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
 <h1>Abodeology® HomeCheck – Edit Report</h1>

 <!-- Property Header -->
 <div class="property-header">
 <h2>{{ $property->address ?? 'N/A' }}</h2>
 <p>
 @php
 $propertyDetails = [];
 if($property->bedrooms) {
 $propertyDetails[] = $property->bedrooms . ' Bedroom' . ($property->bedrooms > 1 ? 's' : '');
 }
 if($property->property_type) {
 $propertyDetails[] = ucfirst(str_replace('_', ' ', $property->property_type));
 }
 if($property->tenure) {
 $propertyDetails[] = ucfirst(str_replace('_', ' ', $property->tenure));
 }
 @endphp
 @if(!empty($propertyDetails))
 {{ implode(' • ', $propertyDetails) }}
 @endif
 @if($property->postcode)
 <br><small style="color: #999;">{{ $property->postcode }}</small>
 @endif
 </p>
 </div>

 @if(session('success'))
 <div class="success-message">{{ session('success') }}</div>
 @endif

 @if(session('error'))
 <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
 {{ session('error') }}
 </div>
 @endif

 <!-- TABS -->
 <div class="tabs">
 <div class="tab active" data-tab="details">HomeCheck Details</div>
 <div class="tab" data-tab="rooms">Rooms</div>
 <div class="tab" data-tab="ai">AI Preview</div>
 </div>

 <!-- TAB 1 – DETAILS -->
 <div class="tab-content active" id="details">
 <form action="{{ route('admin.homechecks.update', $homecheckReport->id) }}" method="POST" id="homecheckForm">
 @csrf
 @method('PUT')
 <div class="section">
 <label>Scheduled Date</label>
 <input type="date" 
 name="scheduled_date" 
 value="{{ old('scheduled_date', $homecheckReport->scheduled_date ? (\Carbon\Carbon::parse($homecheckReport->scheduled_date)->format('Y-m-d')) : '') }}"
 min="{{ date('Y-m-d') }}">
 @error('scheduled_date')
 <div class="error-message">{{ $message }}</div>
 @enderror

 <label style="margin-top:15px;">Status</label>
 <select name="status" required>
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

 <label style="margin-top:15px;">Notes</label>
 <textarea name="notes" rows="5">{{ old('notes', $homecheckReport->notes) }}</textarea>
 @error('notes')
 <div class="error-message">{{ $message }}</div>
 @enderror

 <div style="margin-top:20px;">
 <input type="checkbox" id="ai-process" name="process_ai" value="1">
 <label for="ai-process" style="display: inline; margin-left: 8px; font-weight: normal;">Process AI analysis after update</label>
 </div>
 </div>
 <button type="submit" class="button btn-primary">Save HomeCheck</button>
 </form>
 </div>

 <!-- TAB 2 – ROOMS -->
 <div class="tab-content" id="rooms">
 <div class="section">
 <button class="button btn-primary" onclick="openNewRoom()">+ Add Room</button>
 <div class="room-list" id="roomList">
 @if($roomsData && $roomsData->count() > 0)
 @foreach($roomsData as $roomName => $roomImages)
 @if($roomImages && $roomImages->count() > 0)
 @php
 $firstImage = $roomImages->first();
 $roomId = $firstImage ? $firstImage->id : null;
 @endphp
 @if($firstImage)
 <div class="room-item">
 <span class="room-item-title">{{ ucfirst($roomName) }}</span>
 <button class="button btn-outline" onclick="openRoom({{ $firstImage->id }}, '{{ addslashes($roomName) }}')">Edit</button>
 </div>
 @endif
 @endif
 @endforeach
 @endif
 </div>
 </div>
 </div>

 <!-- TAB 3 – AI PREVIEW -->
 <div class="tab-content" id="ai">
 <div class="section">
 <h2>AI Analysis Preview</h2>
 @if($roomsData && $roomsData->count() > 0)
 @php
 $hasAI = false;
 foreach($roomsData as $roomName => $roomImages) {
 if($roomImages && $roomImages->count() > 0) {
 $firstImage = $roomImages->first();
 if($firstImage && ($firstImage->ai_rating || $firstImage->ai_comments)) {
 $hasAI = true;
 break;
 }
 }
 }
 @endphp
 @if($hasAI)
 <div style="margin-top: 20px;">
 @foreach($roomsData as $roomName => $roomImages)
 @if($roomImages && $roomImages->count() > 0)
 @php
 $firstImage = $roomImages->first();
 @endphp
 @if($firstImage && ($firstImage->ai_rating || $firstImage->ai_comments))
 <div class="ai-box" style="margin-bottom: 20px;">
 <strong>{{ ucfirst($roomName) }}</strong>
 @if($firstImage->ai_rating)
 <p><strong>Rating:</strong> {{ $firstImage->ai_rating }}/10</p>
 @endif
 @if($firstImage->ai_comments)
 <p>{{ $firstImage->ai_comments }}</p>
 @endif
 </div>
 @endif
 @endif
 @endforeach
 </div>
 @else
 <p>This will show the combined report once all rooms are analysed. Click "Process AI analysis after update" in the Details tab to generate AI analysis.</p>
 @endif
 @else
 <p>No rooms available yet. Add rooms in the Rooms tab to see AI analysis.</p>
 @endif
 </div>
 </div>
</div>

<!-- MODAL (ROOM EDITOR) -->
<div class="modal-bg" id="modalBg">
 <div class="modal">
 <h2>Edit Room</h2>
 <form id="roomForm" enctype="multipart/form-data">
 @csrf
 <input type="hidden" id="roomId" name="room_id" value="">
 <input type="hidden" id="roomType" name="room_type" value="existing">
 
 <label>Room Name</label>
 <input id="roomNameInput" name="room_name" required>
 
 <h3 style="margin-top:20px;">Images</h3>
 <div class="image-grid" id="imageGrid"></div>
 
 <div style="display: flex; gap: 10px; margin-top: 15px;">
 <button type="button" class="button btn-outline" onclick="document.getElementById('uploadRegular').click()">Upload Regular Photo</button>
 <button type="button" class="button btn-outline" onclick="document.getElementById('upload360').click()">Upload 360° Photo</button>
 </div>
 
 <input type="file" id="uploadRegular" class="file-input" accept="image/jpeg,image/png,image/jpg" multiple onchange="handleFileUpload(event, false)">
 <input type="file" id="upload360" class="file-input" accept="image/jpeg,image/png,image/jpg" multiple onchange="handleFileUpload(event, true)">
 <input type="hidden" id="is360Flag" name="is_360" value="0">
 
 <div class="ai-box" id="aiBox" style="display: none;">
 <strong>AI Summary:</strong>
 <p id="aiSummary">Loading...</p>
 </div>
 
 <div style="margin-top:25px; display:flex; justify-content:space-between;">
 <button type="button" class="button btn-danger" onclick="deleteRoom()">Delete Room</button>
 <div>
 <button type="button" class="button btn-outline" onclick="closeModal()">Cancel</button>
 <button type="submit" class="button btn-primary">Save Room</button>
 </div>
 </div>
 </form>
 </div>
</div>

@push('scripts')
<script>
// Store room data
let currentRoomId = null;
let currentRoomName = null;
let roomImages = [];
let deletedImageIds = [];

// BASIC TAB LOGIC
document.querySelectorAll('.tab').forEach(tab => {
 tab.addEventListener('click', () => {
 document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
 tab.classList.add('active');
 document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
 document.getElementById(tab.dataset.tab).classList.add('active');
 });
});

// MODAL LOGIC
function openRoom(roomId, roomName) {
 currentRoomId = roomId;
 currentRoomName = roomName;
 document.getElementById('roomId').value = roomId;
 document.getElementById('roomType').value = 'existing';
 document.getElementById('roomNameInput').value = roomName;
 
 // Load images for this room
 loadRoomImages(roomId);
 
 // Load AI summary
 loadAISummary(roomId);
 
 document.getElementById('modalBg').style.display = 'flex';
}

function openNewRoom() {
 currentRoomId = null;
 currentRoomName = null;
 document.getElementById('roomId').value = '';
 document.getElementById('roomType').value = 'new';
 document.getElementById('roomNameInput').value = '';
 document.getElementById('imageGrid').innerHTML = '';
 document.getElementById('aiBox').style.display = 'none';
 roomImages = [];
 deletedImageIds = [];
 document.getElementById('modalBg').style.display = 'flex';
}

function closeModal() {
 document.getElementById('modalBg').style.display = 'none';
 currentRoomId = null;
 currentRoomName = null;
 roomImages = [];
 deletedImageIds = [];
}

function loadRoomImages(roomId) {
 // Fetch images via AJAX
 fetch(`{{ url('/admin/homecheck-room-images') }}/${roomId}`, {
 headers: {
 'X-Requested-With': 'XMLHttpRequest',
 }
 })
 .then(response => response.json())
 .then(data => {
 if (data.success && data.images) {
 const imageGrid = document.getElementById('imageGrid');
 imageGrid.innerHTML = '';
 
 data.images.forEach(image => {
 const imageCard = document.createElement('div');
 imageCard.className = 'image-card';
 imageCard.style.backgroundImage = `url('${image.url}')`;
 imageCard.innerHTML = `
 <div class="delete-img" onclick="deleteImage(${image.id}, this)">×</div>
 <span>${image.is_360 ? '360°' : 'Photo'}</span>
 `;
 imageGrid.appendChild(imageCard);
 });
 }
 })
 .catch(error => {
 console.error('Error loading images:', error);
 });
}

function loadAISummary(roomId) {
 // Fetch AI summary via AJAX
 fetch(`{{ url('/admin/homecheck-room-ai') }}/${roomId}`, {
 headers: {
 'X-Requested-With': 'XMLHttpRequest',
 }
 })
 .then(response => response.json())
 .then(data => {
 const aiBox = document.getElementById('aiBox');
 const aiSummary = document.getElementById('aiSummary');
 
 if (data.success && (data.rating || data.comments)) {
 aiBox.style.display = 'block';
 let summaryText = '';
 if (data.rating) {
 summaryText += `<strong>Rating:</strong> ${data.rating}/10<br>`;
 }
 if (data.comments) {
 summaryText += data.comments;
 }
 aiSummary.innerHTML = summaryText || 'No AI analysis available yet.';
 } else {
 aiBox.style.display = 'none';
 }
 })
 .catch(error => {
 console.error('Error loading AI summary:', error);
 document.getElementById('aiBox').style.display = 'none';
 });
}

function handleFileUpload(event, is360) {
 const files = Array.from(event.target.files);
 const imageGrid = document.getElementById('imageGrid');
 document.getElementById('is360Flag').value = is360 ? '1' : '0';
 
 files.forEach(file => {
 if (file.type.match('image.*')) {
 const reader = new FileReader();
 reader.onload = function(e) {
 const imageCard = document.createElement('div');
 imageCard.className = 'image-card';
 imageCard.style.backgroundImage = `url('${e.target.result}')`;
 imageCard.innerHTML = `
 <div class="delete-img" onclick="this.parentElement.remove()">×</div>
 <span>${is360 ? '360°' : 'Photo'}</span>
 `;
 imageGrid.appendChild(imageCard);
 
 // Store file for upload
 roomImages.push({ file: file, is360: is360 });
 };
 reader.readAsDataURL(file);
 }
 });
 
 // Clear input
 event.target.value = '';
}

function deleteImage(imageId, element) {
 if (confirm('Delete this image?')) {
 deletedImageIds.push(imageId);
 element.closest('.image-card').remove();
 }
}

function deleteRoom() {
 if (confirm('Delete this room and all its images?')) {
 // Handle room deletion
 const roomId = document.getElementById('roomId').value;
 const roomType = document.getElementById('roomType').value;
 
 if (roomType === 'existing' && roomId) {
 // Delete existing room via AJAX
 fetch(`{{ route('admin.homechecks.delete-room', $homecheckReport->id) }}`, {
 method: 'POST',
 headers: {
 'Content-Type': 'application/json',
 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value,
 },
 body: JSON.stringify({ room_id: roomId })
 })
 .then(response => response.json())
 .then(data => {
 if (data.success) {
 alert('Room deleted successfully');
 closeModal();
 window.location.reload();
 } else {
 alert('Error: ' + (data.message || 'Failed to delete room'));
 }
 })
 .catch(error => {
 console.error('Error:', error);
 alert('An error occurred while deleting the room.');
 });
 } else {
 closeModal();
 }
 }
}

// Handle room form submission
document.getElementById('roomForm').addEventListener('submit', function(e) {
 e.preventDefault();
 
 const formData = new FormData();
 formData.append('_token', document.querySelector('input[name="_token"]').value);
 formData.append('room_id', document.getElementById('roomId').value);
 formData.append('room_type', document.getElementById('roomType').value);
 formData.append('room_name', document.getElementById('roomNameInput').value);
 formData.append('is_360', document.getElementById('is360Flag').value);
 
 // Add new images
 roomImages.forEach((item, index) => {
 formData.append(`images[${index}]`, item.file);
 formData.append(`images_is_360[${index}]`, item.is360 ? '1' : '0');
 });
 
 // Add deleted image IDs
 deletedImageIds.forEach(id => {
 formData.append('delete_images[]', id);
 });
 
 // Show loading
 const submitBtn = this.querySelector('button[type="submit"]');
 const originalText = submitBtn.textContent;
 submitBtn.disabled = true;
 submitBtn.textContent = 'Saving...';
 
 fetch(`{{ route('admin.homechecks.update-room', $homecheckReport->id) }}`, {
 method: 'POST',
 body: formData,
 headers: {
 'X-Requested-With': 'XMLHttpRequest',
 }
 })
 .then(response => response.json())
 .then(data => {
 if (data.success) {
 alert('Room saved successfully!');
 closeModal();
 window.location.reload();
 } else {
 alert('Error: ' + (data.message || 'Failed to save room'));
 }
 })
 .catch(error => {
 console.error('Error:', error);
 alert('An error occurred while saving the room.');
 })
 .finally(() => {
 submitBtn.disabled = false;
 submitBtn.textContent = originalText;
 });
});
</script>
@endpush
@endsection
