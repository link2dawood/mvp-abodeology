@extends('layouts.admin')

@section('title', 'HomeCheck Report Details')

@push('styles')
<style>
    .container {
        max-width: 1400px;
        margin: 35px auto;
        padding: 0 22px;
    }

    h2 {
        font-size: 28px;
        margin-bottom: 8px;
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

    .info-row {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid var(--line-grey);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        min-width: 200px;
        color: #666;
    }

    .info-value {
        flex: 1;
    }

    .status {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    .status-pending { background: #6c757d; color: #fff; }
    .status-scheduled { background: var(--abodeology-teal); color: #fff; }
    .status-in_progress { background: #ffc107; color: #000; }
    .status-completed { background: #28a745; color: #fff; }
    .status-cancelled { background: #dc3545; color: #fff; }

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

    .btn-main {
        background: var(--abodeology-teal);
        color: var(--white);
    }

    .btn-secondary {
        background: #666;
        color: #fff;
    }

    .room-section {
        margin: 30px 0;
        padding: 20px;
        background: #f9f9f9;
        border-radius: 8px;
        border: 1px solid var(--line-grey);
    }

    .room-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        padding: 10px;
        margin: -10px -10px 15px -10px;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .room-header:hover {
        background: rgba(0,0,0,0.05);
    }

    .room-title {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
        color: var(--dark-text);
        flex: 1;
    }

    .room-toggle-icon {
        font-size: 18px;
        color: var(--abodeology-teal);
        transition: transform 0.3s ease;
        margin-left: 15px;
    }

    .room-section.collapsed .room-toggle-icon {
        transform: rotate(-90deg);
    }

    .room-content {
        overflow: hidden;
        transition: max-height 0.3s ease, opacity 0.3s ease;
        max-height: 5000px;
        opacity: 1;
    }

    .room-section.collapsed .room-content {
        max-height: 0;
        opacity: 0;
        margin: 0;
        padding: 0;
    }

    .room-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--line-grey);
    }

    .room-controls h3 {
        margin: 0;
    }

    .room-controls-buttons {
        display: flex;
        gap: 10px;
    }

    .btn-control {
        padding: 8px 16px;
        background: var(--abodeology-teal);
        color: var(--white);
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: background 0.2s;
    }

    .btn-control:hover {
        background: #1a9a96;
    }

    .image-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .image-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid var(--line-grey);
        cursor: pointer;
        transition: transform 0.2s;
    }

    .image-item:hover {
        transform: scale(1.05);
        border-color: var(--abodeology-teal);
    }

    .image-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }

    .image-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: rgba(0,0,0,0.7);
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .image-info {
        padding: 10px;
        background: #fff;
        font-size: 12px;
        color: #666;
    }

    .modal {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.85);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        padding: 20px;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: var(--white);
        width: 95%;
        max-width: 95%;
        height: 95vh;
        max-height: 95vh;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .modal-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        flex: 1;
    }

    /* 360 VIEWER */
    #pano-viewer {
        width: 100%;
        height: 0;
        position: relative;
        background: #000;
        overflow: hidden;
        transition: height 0.3s ease;
        flex: 1;
    }

    #pano-viewer.active {
        height: 85vh;
        min-height: 600px;
    }

    .viewer-controls {
        display: none;
        gap: 10px;
        margin-top: 15px;
        justify-content: center;
    }

    .btn-viewer {
        background: var(--abodeology-teal);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: background 0.3s ease;
    }

    .btn-viewer:hover {
        background: #25A29F;
    }

    .modal-body-content {
        padding: 20px;
        max-height: 200px;
        overflow-y: auto;
        flex-shrink: 0;
    }

    .modal-image-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        position: relative;
        min-height: 0;
        overflow: hidden;
    }

    .close-modal {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0,0,0,0.8);
        color: #fff;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        font-size: 28px;
        font-weight: 700;
        z-index: 100;
        transition: background 0.3s ease, transform 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }

    .close-modal:hover {
        background: rgba(220, 53, 69, 0.9);
        transform: scale(1.1);
    }

    .modal-header {
        position: relative;
        padding: 15px 20px;
        border-bottom: 1px solid var(--line-grey);
        background: var(--white);
        z-index: 50;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin: 20px 0;
    }

    .stat-box {
        background: var(--soft-grey);
        padding: 15px;
        border-radius: 8px;
        text-align: center;
    }

    .stat-number {
        font-size: 28px;
        font-weight: 700;
        color: var(--abodeology-teal);
    }

    .stat-label {
        font-size: 13px;
        color: #666;
        margin-top: 5px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>HomeCheck Report Details</h2>
        <div>
            <a href="{{ route('admin.homechecks.index') }}" class="btn btn-secondary">← Back to List</a>
            @if($homecheckData->count() > 0 && !$homecheckReport->report_path)
                <form action="{{ route('admin.homechecks.process-ai', $homecheckReport->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('This will process all images through AI analysis. This may take a few moments. Continue?');">
                    @csrf
                    <button type="submit" class="btn" style="background: #28a745; color: #fff;">🤖 Process AI Analysis</button>
                </form>
            @endif
            <a href="{{ route('admin.homechecks.edit', $homecheckReport->id) }}" class="btn btn-main">Edit HomeCheck</a>
            <a href="{{ route('admin.properties.show', $property->id) }}" class="btn btn-secondary">View Property</a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <!-- HomeCheck Information -->
    <div class="card">
        <h3>HomeCheck Information</h3>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="status status-{{ $homecheckReport->status }}">
                    {{ ucfirst(str_replace('_', ' ', $homecheckReport->status)) }}
                </span>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Property:</div>
            <div class="info-value">
                <strong>{{ $property->address }}</strong>
                @if($property->postcode)
                    <br><small style="color: #666;">{{ $property->postcode }}</small>
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Seller:</div>
            <div class="info-value">{{ $property->seller->name ?? 'N/A' }} ({{ $property->seller->email ?? 'N/A' }})</div>
        </div>
        @if($homecheckReport->scheduled_date)
        <div class="info-row">
            <div class="info-label">Scheduled Date:</div>
            <div class="info-value">{{ $homecheckReport->scheduled_date->format('l, F j, Y') }}</div>
        </div>
        @endif
        @if($homecheckReport->scheduler)
        <div class="info-row">
            <div class="info-label">Scheduled By:</div>
            <div class="info-value">{{ $homecheckReport->scheduler->name }} on {{ $homecheckReport->created_at->format('M d, Y g:i A') }}</div>
        </div>
        @endif
        @if($homecheckReport->completed_at)
        <div class="info-row">
            <div class="info-label">Completed:</div>
            <div class="info-value">
                {{ $homecheckReport->completed_at->format('l, F j, Y g:i A') }}
                @if($homecheckReport->completer)
                    by {{ $homecheckReport->completer->name }}
                @endif
            </div>
        </div>
        @endif
        @if($homecheckReport->notes)
        <div class="info-row">
            <div class="info-label">Notes:</div>
            <div class="info-value">{{ $homecheckReport->notes }}</div>
        </div>
        @endif
    </div>

    <!-- Statistics -->
    @if($homecheckData->count() > 0)
    <div class="card">
        <h3>HomeCheck Statistics</h3>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-number">{{ $roomsData->count() }}</div>
                <div class="stat-label">Rooms Captured</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $homecheckData->count() }}</div>
                <div class="stat-label">Total Images</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $homecheckData->where('is_360', true)->count() }}</div>
                <div class="stat-label">360° Images</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $homecheckData->whereNotNull('moisture_reading')->count() }}</div>
                <div class="stat-label">Moisture Readings</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Room Data -->
    @if($roomsData->count() > 0)
        <div class="card">
            <div class="room-controls">
                <h3>Rooms ({{ $roomsData->count() }})</h3>
                <div class="room-controls-buttons">
                    <button type="button" class="btn-control" onclick="expandAllRooms()">Expand All</button>
                    <button type="button" class="btn-control" onclick="collapseAllRooms()">Collapse All</button>
                </div>
            </div>
        </div>
        @foreach($roomsData as $roomName => $roomImages)
            <div class="room-section">
                <div class="room-header" onclick="toggleRoomCollapse(this)">
                    <div class="room-title">{{ ucfirst($roomName) }}</div>
                    <span class="room-toggle-icon">▼</span>
                </div>
                <div class="room-content">
                
                @php
                    $firstImage = $roomImages->first();
                    $moistureReading = $firstImage->moisture_reading ?? null;
                    $aiRating = $firstImage->ai_rating ?? null;
                    $aiComments = $firstImage->ai_comments ?? null;
                @endphp

                <div class="info-row" style="border: none; padding: 5px 0;">
                    <div class="info-label">Images:</div>
                    <div class="info-value">{{ $roomImages->count() }} image(s)</div>
                </div>
                @if($roomImages->where('is_360', true)->count() > 0)
                <div class="info-row" style="border: none; padding: 5px 0;">
                    <div class="info-label">360° Images:</div>
                    <div class="info-value">{{ $roomImages->where('is_360', true)->count() }}</div>
                </div>
                @endif
                @if($moistureReading)
                <div class="info-row" style="border: none; padding: 5px 0;">
                    <div class="info-label">Moisture Reading:</div>
                    <div class="info-value">{{ $moistureReading }}%</div>
                </div>
                @endif
                @if($aiRating)
                <div class="info-row" style="border: none; padding: 5px 0;">
                    <div class="info-label">AI Rating:</div>
                    <div class="info-value">{{ $aiRating }}/10</div>
                </div>
                @endif
                @if($aiComments)
                <div class="info-row" style="border: none; padding: 5px 0;">
                    <div class="info-label">AI Analysis:</div>
                    <div class="info-value">{{ $aiComments }}</div>
                </div>
                @endif

                <div class="image-gallery">
                    @foreach($roomImages as $image)
                        @if($image->id)
                            @php
                                // Use proxy endpoint - avoids slow image_url accessor (S3 file checks for every image)
                                // Faster page load and better caching with ETag headers
                                $thumbnailUrl = route('admin.homecheck.image', ['id' => $image->id]);
                                $modalUrl = route('admin.homecheck.image', ['id' => $image->id]);
                            @endphp
                            <div class="image-item" onclick="openImageModal('{{ $modalUrl }}', '{{ $roomName }}', {{ $image->is_360 ? 'true' : 'false' }}, {{ $image->id }}, '{{ addslashes($image->ai_comments ?? '') }}', '{{ addslashes($image->moisture_reading ?? '') }}', '{{ addslashes($image->ai_rating ?? '') }}')">
                                <img src="{{ $thumbnailUrl }}" alt="{{ $roomName }} Image" loading="lazy" decoding="async">
                                @if($image->is_360)
                                    <div class="image-badge">360°</div>
                                @endif
                                <div class="image-info">
                                    @if($image->moisture_reading)
                                        <strong>Moisture:</strong> {{ $image->moisture_reading }}%<br>
                                    @endif
                                    @if($image->ai_rating)
                                        <strong>AI Rating:</strong> {{ $image->ai_rating }}/10<br>
                                    @endif
                                    @if($image->ai_comments)
                                        <div style="margin-top: 8px; padding: 8px; background: #f0f0f0; border-radius: 4px; font-size: 11px; color: #333; text-align: left; max-height: 80px; overflow-y: auto;">
                                            <strong>AI Notes:</strong><br>
                                            {{ Str::limit($image->ai_comments, 150) }}
                                        </div>
                                    @endif
                                    @if($image->created_at)
                                        <br><small style="color: #999;">{{ $image->created_at->format('M j, Y g:i A') }}</small>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="card">
            <p style="text-align: center; color: #999; padding: 40px;">
                No HomeCheck data available yet. Images will appear here once the HomeCheck is completed.
            </p>
        </div>
    @endif

    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <div class="close-modal" onclick="closeModal()">×</div>
            <div class="modal-image-container">
                <img id="modalImage" class="modal-img" src="" alt="HomeCheck Image" style="display: block;">
                <div id="pano-viewer"></div>
            </div>
            <div class="modal-body-content">
                <h2 id="modalTitle" style="margin: 0 0 15px 0; color: var(--abodeology-teal);"></h2>
                <div id="modalBody"></div>
                <div class="viewer-controls" id="viewer-controls" style="display: none;">
                    <button class="btn-viewer" onclick="toggleViewer()">Toggle 360° Viewer</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Pannellum 360° Viewer -->
<script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">

<script>
    let currentViewer = null;
    let currentImageUrl = null;
    let is360Image = false;

    function openModal(imageUrl, roomName) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageUrl;
        modalImage.alt = roomName + ' Image';
        modal.classList.add('active');
    }

    function openImageModal(imageUrl, roomName, is360, imageId, aiComments, moistureReading, aiRating) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const panoViewer = document.getElementById('pano-viewer');
        const viewerControls = document.getElementById('viewer-controls');
        const modalBody = document.getElementById('modalBody');
        const modalTitle = document.getElementById('modalTitle');
        
        // All images now use the proxy endpoint (already passed from server)
        // This ensures proper caching, CORS headers, and ETag support
        currentImageUrl = imageUrl; // imageUrl is already the proxy URL from server
        is360Image = is360;
        
        modalTitle.innerText = roomName;
        
        // Build modal body content
        let bodyContent = '';
        
        if (moistureReading && moistureReading.trim() !== '') {
            bodyContent += '<div style="margin-bottom: 15px;"><strong style="color: var(--abodeology-teal);">Moisture Reading:</strong> <span style="color: #333;">' + moistureReading + '%</span></div>';
        }
        
        if (aiRating && aiRating.trim() !== '') {
            bodyContent += '<div style="margin-bottom: 15px;"><strong style="color: var(--abodeology-teal);">AI Rating:</strong> <span style="color: #333;">' + aiRating + '/10</span></div>';
        }
        
        if (aiComments && aiComments.trim() !== '') {
            bodyContent += '<div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;"><h4 style="margin-bottom: 8px; color: var(--abodeology-teal); font-size: 16px;">AI Analysis</h4><p style="line-height: 1.6; color: #333;">' + aiComments.replace(/\n/g, '<br>') + '</p></div>';
        }
        
        if (!bodyContent) {
            bodyContent = '<p style="color: #666; font-style: italic;">No additional information available for this image.</p>';
        }
        
        modalBody.innerHTML = bodyContent;
        
        if (is360) {
            // Show 360 viewer controls
            viewerControls.style.display = "flex";
            modalImage.style.display = "none";
            panoViewer.classList.add("active");
            
            // Initialize or update 360 viewer
            if (currentViewer) {
                currentViewer.destroy();
            }
            
            try {
                currentViewer = pannellum.viewer('pano-viewer', {
                    "type": "equirectangular",
                    "panorama": currentImageUrl,
                    "autoLoad": true,
                    "autoRotate": 0,
                    "compass": true,
                    "showControls": true,
                    "keyboardZoom": true,
                    "mouseZoom": true,
                    "hfov": 100,
                    "minHfov": 50,
                    "maxHfov": 120,
                    "crossOrigin": "anonymous"
                });
            } catch (error) {
                console.error('Error initializing 360 viewer:', error);
                panoViewer.innerHTML = '<div style="padding: 20px; text-align: center; color: #dc3545;"><p>Unable to load 360° image. This may be due to CORS restrictions.</p><p>Please ensure the S3 bucket has CORS configured to allow requests from this domain.</p></div>';
            }
        } else {
            // Show regular image
            viewerControls.style.display = "none";
            panoViewer.classList.remove("active");
            modalImage.style.display = "block";
            modalImage.src = imageUrl;
            
            // Destroy any existing viewer
            if (currentViewer) {
                currentViewer.destroy();
                currentViewer = null;
            }
        }
        
        modal.classList.add('active');
    }

    function toggleViewer() {
        const modalImage = document.getElementById('modalImage');
        const panoViewer = document.getElementById('pano-viewer');
        
        if (!is360Image) return;
        
        if (panoViewer.classList.contains("active")) {
            // Switch to regular image
            panoViewer.classList.remove("active");
            modalImage.style.display = "block";
            modalImage.src = currentImageUrl;
            
            if (currentViewer) {
                currentViewer.destroy();
                currentViewer = null;
            }
        } else {
            // Switch to 360 viewer
            modalImage.style.display = "none";
            panoViewer.classList.add("active");
            
            try {
                currentViewer = pannellum.viewer('pano-viewer', {
                    "type": "equirectangular",
                    "panorama": currentImageUrl,
                    "autoLoad": true,
                    "autoRotate": 0,
                    "compass": true,
                    "showControls": true,
                    "keyboardZoom": true,
                    "mouseZoom": true,
                    "hfov": 100,
                    "minHfov": 50,
                    "maxHfov": 120,
                    "crossOrigin": "anonymous"
                });
            } catch (error) {
                console.error('Error initializing 360 viewer:', error);
            }
        }
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        const panoViewer = document.getElementById('pano-viewer');
        
        modal.classList.remove('active');
        
        // Destroy viewer on close
        if (currentViewer) {
            currentViewer.destroy();
            currentViewer = null;
        }
        
        // Reset viewer state
        panoViewer.classList.remove("active");
        is360Image = false;
        currentImageUrl = null;
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    function toggleRoomCollapse(header) {
        const roomSection = header.closest('.room-section');
        roomSection.classList.toggle('collapsed');
    }

    function expandAllRooms() {
        document.querySelectorAll('.room-section').forEach(section => {
            section.classList.remove('collapsed');
        });
    }

    function collapseAllRooms() {
        document.querySelectorAll('.room-section').forEach(section => {
            section.classList.add('collapsed');
        });
    }
</script>
@endpush
@endsection

