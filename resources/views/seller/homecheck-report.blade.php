@extends('layouts.seller')

@section('title', 'HomeCheck Report')

@push('styles')
<style>
    .container {
        max-width: 1100px;
        margin: 30px auto;
        padding: 20px;
    }

    /* PROPERTY INFO */
    .top-info {
        margin: 20px 0 10px;
        padding: 0;
    }

    .value-label {
        text-transform: uppercase;
        font-size: 13px;
        color: #777;
        margin-bottom: 4px;
        letter-spacing: 0.6px;
    }

    .value-number {
        font-size: 44px;
        font-weight: 800;
        color: var(--abodeology-teal);
        margin-bottom: 25px;
    }

    .address {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .details-row {
        font-size: 15px;
        color: #777;
    }

    /* NOTICE */
    .notice {
        margin: 25px 0;
        padding: 16px 20px;
        background: #fff6d6;
        border-radius: 8px;
        font-size: 14px;
        color: #7a6300;
    }

    /* ROOM CARD */
    .room-container {
        margin: 40px 0;
        position: relative;
    }

    .room {
        background: var(--white);
        display: flex;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        position: relative;
        border: 1px solid var(--line-grey);
    }

    .room img {
        width: 45%;
        object-fit: cover;
        min-height: 340px;
        display: block;
        background: #f0f0f0;
    }

    /* IMAGE GALLERY */
    .image-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--line-grey);
    }

    .gallery-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid var(--line-grey);
        cursor: pointer;
        transition: transform 0.2s, border-color 0.2s;
        aspect-ratio: 4/3;
        background: #f0f0f0;
    }

    .gallery-item:hover {
        transform: scale(1.05);
        border-color: var(--abodeology-teal);
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .gallery-badge {
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

    /* 360 VIEWER */
    #pano-viewer {
        width: 100%;
        height: 500px;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 20px;
        display: none;
    }

    #pano-viewer.active {
        display: block;
    }

    .viewer-controls {
        margin-top: 15px;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .btn-viewer {
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

    .btn-viewer:hover {
        background: #1a9a96;
    }

    .section-label-images {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--abodeology-teal);
        margin-top: 25px;
        margin-bottom: 12px;
        letter-spacing: 0.5px;
    }

    .room-content {
        width: 55%;
        padding: 35px 35px 40px;
        position: relative;
    }

    .room-title {
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .section-label {
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--abodeology-teal);
        margin-top: 20px;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
    }

    .text-block {
        font-size: 15px;
        line-height: 1.55;
        margin-bottom: 12px;
    }

    /* ICON BUTTONS */
    .icon-bar {
        position: absolute;
        top: 20px;
        right: 20px;
        display: flex;
        gap: 12px;
        z-index: 20;
    }

    .icon-btn {
        width: 38px;
        height: 38px;
        background: var(--white);
        border-radius: 50%;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.15s;
        border: none;
    }

    .icon-btn:hover {
        transform: scale(1.08);
    }

    .icon-btn img {
        width: 18px;
        opacity: 0.85;
        display: block;
    }

    .icon-btn svg {
        width: 20px;
        height: 20px;
        color: #333;
        opacity: 0.85;
    }

    .icon-btn:hover svg {
        opacity: 1;
    }

    /* MODAL */
    .modal {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.75);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 99999;
        padding: 30px;
    }

    .modal-content {
        background: var(--white);
        width: 92%;
        max-width: 1200px;
        display: flex;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 15px 50px rgba(0,0,0,0.4);
        animation: fadeIn 0.25s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
    }

    .modal-img {
        width: 50%;
        height: 100%;
        object-fit: cover;
        max-height: 90vh;
    }

    .modal-text {
        width: 50%;
        padding: 40px;
        overflow-y: auto;
        max-height: 90vh;
    }

    .close-modal {
        position: fixed;
        top: 16px;
        right: 16px;
        z-index: 100002;
        background: var(--white);
        min-width: 48px;
        min-height: 48px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        font-size: 24px;
        font-weight: 700;
        box-shadow: 0 4px 16px rgba(0,0,0,0.35);
        border: 2px solid rgba(0,0,0,0.1);
    }
    .modal .close-modal { display: flex; }
    .modal:not([style*="flex"]) .close-modal { display: none; }

    .modal-close-btn-inner {
        display: block;
        width: 100%;
        padding: 12px 20px;
        margin-top: 16px;
        background: #333;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
    }
    .modal-close-btn-inner:hover { background: #111; }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .room {
            flex-direction: column;
        }

        .room img {
            width: 100%;
            min-height: 250px;
        }

        .room-content {
            width: 100%;
            padding: 25px;
        }

        .image-gallery {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
        }

        .modal-content {
            flex-direction: column;
            width: 95%;
        }

        #modal-image-container {
            width: 100% !important;
        }

        .modal-img {
            width: 100%;
            max-height: 50vh;
        }

        #pano-viewer {
            width: 100%;
            height: 300px;
        }

        .modal-text {
            width: 100%;
            padding: 25px;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <!-- PAGE TITLE -->
    <h2 style="border-bottom: 2px solid #000000; padding-bottom: 8px; margin-bottom: 30px; font-size: 24px; font-weight: 600;">Abodeology HomeCheck Report</h2>

    <!-- PROPERTY INFO -->
    <div class="top-info">
        <div class="value-label">Estimated Market Value</div>
        <div class="value-number">£{{ number_format($property->asking_price ?? 0, 0) }}</div>
        <div class="address">{{ $property->address ?? 'N/A' }}</div>
        <div class="details-row">
            {{ $property->bedrooms ?? 'N/A' }} Bedroom {{ ucfirst(str_replace('_', ' ', $property->property_type ?? 'Property')) }}
            @if($property->tenure)
                • {{ ucfirst(str_replace('_', ' ', $property->tenure)) }}
            @endif
            • Inspected by Abodeology®
        </div>
    </div>

    <!-- NOTICE -->
    <div class="notice">
        This HomeCheck is a vendor-only preparation report. It is not a survey and must not be shared with buyers.
    </div>

    <!-- ROOMS -->
    @php
        $rooms = $homecheckData->groupBy('room_name');
        $roomIndex = 0;
    @endphp

    @foreach($rooms as $roomName => $roomImages)
        @php
            $roomId = strtolower(str_replace([' ', '-'], '', $roomName));
            $firstImage = $roomImages->first();
            
            // Get image URL using the model accessor
            $placeholderUrl = asset('media/placeholder-room.jpg');
            $imageUrl = $firstImage ? $firstImage->image_url : null;
            
            // Use placeholder if no valid image URL
            if (!$imageUrl) {
                $imageUrl = $placeholderUrl;
            }
            
            $aiComments = $firstImage->ai_comments ?? 'No analysis available for this room.';
            
            // Generate recommendations based on room type
            $recommendations = [];
            $roomType = strtolower($roomName);
            
            if (strpos($roomType, 'living') !== false || strpos($roomType, 'lounge') !== false) {
                $recommendations = ['Light declutter', 'Add soft accents', 'Fully open curtains'];
            } elseif (strpos($roomType, 'dining') !== false) {
                $recommendations = ['Clear surfaces', 'Align chairs neatly', 'Add a neutral centrepiece'];
            } elseif (strpos($roomType, 'kitchen') !== false) {
                $recommendations = ['Clear worktops', 'Remove bins', 'Add minimal décor'];
            } elseif (strpos($roomType, 'hall') !== false || strpos($roomType, 'hallway') !== false) {
                $recommendations = ['Remove shoes and coats', 'Add small neutral décor'];
            } elseif (strpos($roomType, 'bedroom') !== false) {
                $recommendations = ['Straighten bedding', 'Clear side tables', 'Add soft accents'];
            } elseif (strpos($roomType, 'bathroom') !== false) {
                $recommendations = ['Remove toiletries', 'Use neutral towels for photography'];
            } elseif (strpos($roomType, 'exterior') !== false || strpos($roomType, 'front') !== false) {
                $recommendations = ['Clean pathways', 'Remove bins', 'Tidy greenery'];
            } elseif (strpos($roomType, 'rear') !== false || strpos($roomType, 'garden') !== false) {
                $recommendations = ['Cut lawn', 'Remove clutter', 'Tidy edges'];
            } else {
                $recommendations = ['Declutter', 'Ensure good lighting', 'Clear walkways'];
            }
        @endphp

        <div class="room-container">
            <div class="room">
                <img src="{{ $imageUrl }}" 
                     alt="{{ $roomName }}" 
                     loading="lazy"
                     onerror="if(this.src !== '{{ $placeholderUrl }}') { this.src = '{{ $placeholderUrl }}'; this.onerror = null; }">
                <div class="room-content">
                    <div class="room-title">{{ ucfirst($roomName) }}</div>
                    <div class="section-label">Condition Summary</div>
                    <div id="{{ $roomId }}-text" class="text-block">
                        {{ $aiComments }}
                    </div>
                    <div class="section-label">Presentation Recommendations</div>
                    <div class="text-block">
                        @foreach($recommendations as $rec)
                            • {{ $rec }}<br>
                        @endforeach
                    </div>
                    
                    @if($roomImages->count() > 0)
                        <div class="section-label-images">Images ({{ $roomImages->count() }})</div>
                        <div class="image-gallery">
                            @foreach($roomImages as $image)
                                @if($image->image_url)
                                    <div class="gallery-item" 
                                         onclick="openImageModal('{{ $image->image_url }}', '{{ $roomName }}', {{ $image->is_360 ? 'true' : 'false' }}, {{ $image->id }}, '{{ addslashes($image->ai_comments ?? '') }}', '{{ addslashes($homecheckReport->notes ?? '') }}')">
                                        <img src="{{ $image->image_url }}" 
                                             alt="{{ $roomName }} Image" 
                                             loading="lazy"
                                             onerror="this.src='{{ asset('media/placeholder-room.jpg') }}'">
                                        @if($image->is_360)
                                            <div class="gallery-badge">360°</div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="icon-bar">
                <div class="icon-btn" onclick="speakText('{{ $roomId }}')" title="Read aloud">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C8.13 2 5 5.13 5 9C5 11.38 6.19 13.47 8 14.74V17C8 17.55 8.45 18 9 18H15C15.55 18 16 17.55 16 17V14.74C17.81 13.47 19 11.38 19 9C19 5.13 15.87 2 12 2ZM15 12.7V16H9V12.7C7.84 12.16 7 11.14 7 9.9C7 6.24 9.24 4 12 4C14.76 4 17 6.24 17 9.9C17 11.14 16.16 12.16 15 12.7ZM14 20H10V22H14V20Z" fill="currentColor"/>
                    </svg>
                </div>
                <div class="icon-btn" onclick="openModal('{{ $roomId }}', '{{ $roomName }}', '{{ $imageUrl }}', '{{ addslashes($aiComments) }}')" title="Expand view">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 4H10V6H6V10H4V4ZM20 4H14V6H18V10H20V4ZM4 20H10V18H6V14H4V20ZM20 20H14V18H18V14H20V20Z" fill="currentColor"/>
                    </svg>
                </div>
            </div>
        </div>
    @endforeach

    @if($rooms->count() === 0)
        <div class="room-container">
            <div class="room">
                <div class="room-content" style="width: 100%; text-align: center; padding: 60px 40px;">
                    <div class="room-title">No Room Data Available</div>
                    <div class="text-block" style="color: #777;">
                        HomeCheck data is still being processed. Please check back later.
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL -->
    <div id="modal" class="modal" onclick="if(event.target.id === 'modal') closeModal();">
        <button type="button" class="close-modal" onclick="closeModal()" aria-label="Close">×</button>
        <div class="modal-content">
            <div id="modal-image-container" style="width: 50%; position: relative;">
                <img id="modal-img" class="modal-img" src="" alt="Room Image" style="display: block;" onerror="if(this.src !== '{{ asset('media/placeholder-room.jpg') }}') { this.src = '{{ asset('media/placeholder-room.jpg') }}'; this.onerror = null; }">
                <div id="pano-viewer"></div>
            </div>
            <div class="modal-text">
                <h2 id="modal-title"></h2>
                <div id="modal-body"></div>
                <div class="viewer-controls" id="viewer-controls" style="display: none;">
                    <button class="btn-viewer" onclick="toggleViewer()">Toggle 360° Viewer</button>
                </div>
                <button type="button" class="modal-close-btn-inner" onclick="closeModal()">Close</button>
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

    /* TEXT-TO-SPEECH */
    function speakText(id) {
        const textElement = document.getElementById(id + "-text");
        if (!textElement) return;
        
        const text = textElement.innerText;
        if (!text) return;
        
        const utter = new SpeechSynthesisUtterance(text);
        utter.rate = 1;
        utter.pitch = 1;
        speechSynthesis.speak(utter);
    }

    /* OPEN MODAL */
    function openModal(roomId, roomName, imgSrc, bodyText) {
        const modal = document.getElementById("modal");
        const modalImg = document.getElementById("modal-img");
        
        modalImg.src = imgSrc;
        document.getElementById("modal-title").innerText = roomName;
        document.getElementById("modal-body").innerHTML = bodyText.replace(/\n/g, '<br>');
        modal.style.display = "flex";
        
        // Hide 360 viewer controls for regular images
        document.getElementById("viewer-controls").style.display = "none";
        document.getElementById("pano-viewer").classList.remove("active");
        modalImg.style.display = "block";
        
        // Destroy any existing viewer
        if (currentViewer) {
            currentViewer.destroy();
            currentViewer = null;
        }
    }

    /* OPEN IMAGE MODAL (from gallery) */
    function openImageModal(imageUrl, roomName, is360, imageId, aiComments, officialNotes) {
        const modal = document.getElementById("modal");
        const modalImg = document.getElementById("modal-img");
        const panoViewer = document.getElementById("pano-viewer");
        const viewerControls = document.getElementById("viewer-controls");
        const modalBody = document.getElementById("modal-body");
        
        // For 360 images, use proxy endpoint to avoid CORS issues
        if (is360 && imageId) {
            // Use proxy endpoint for 360° images to avoid CORS issues
            currentImageUrl = "{{ url('/seller/homecheck-image') }}/" + imageId;
        } else {
            currentImageUrl = imageUrl;
        }
        is360Image = is360;
        
        document.getElementById("modal-title").innerText = roomName;
        
        // Build modal body content with AI notes and official notes
        let bodyContent = '';
        
        if (aiComments && aiComments.trim() !== '') {
            bodyContent += '<div style="margin-bottom: 20px;"><h4 style="margin-bottom: 8px; color: var(--abodeology-teal); font-size: 16px;">AI Analysis</h4><p style="line-height: 1.6; color: #333;">' + aiComments.replace(/\n/g, '<br>') + '</p></div>';
        }
        
        if (officialNotes && officialNotes.trim() !== '') {
            bodyContent += '<div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;"><h4 style="margin-bottom: 8px; color: var(--abodeology-teal); font-size: 16px;">Official Notes</h4><p style="line-height: 1.6; color: #333;">' + officialNotes.replace(/\n/g, '<br>') + '</p></div>';
        }
        
        if (!bodyContent) {
            bodyContent = '<p style="color: #666; font-style: italic;">No additional notes available for this image.</p>';
        }
        
        modalBody.innerHTML = bodyContent;
        
        if (is360) {
            // Show 360 viewer controls
            viewerControls.style.display = "flex";
            modalImg.style.display = "none";
            panoViewer.classList.add("active");
            
            // Initialize or update 360 viewer
            if (currentViewer) {
                currentViewer.destroy();
            }
            
            try {
                currentViewer = pannellum.viewer('pano-viewer', {
                    "type": "equirectangular",
                    "panorama": currentImageUrl, // Use proxy URL for 360° images
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
                // Show error message to user
                panoViewer.innerHTML = '<div style="padding: 20px; text-align: center; color: #dc3545;"><p>Unable to load 360° image. This may be due to CORS restrictions.</p><p>Please ensure the S3 bucket has CORS configured to allow requests from this domain.</p></div>';
            }
        } else {
            // Show regular image
            viewerControls.style.display = "none";
            panoViewer.classList.remove("active");
            modalImg.style.display = "block";
            modalImg.src = imageUrl;
            
            // Destroy any existing viewer
            if (currentViewer) {
                currentViewer.destroy();
                currentViewer = null;
            }
        }
        
        // Modal body content is already set above with AI notes and official notes
        modal.style.display = "flex";
    }

    /* TOGGLE 360 VIEWER */
    function toggleViewer() {
        const modalImg = document.getElementById("modal-img");
        const panoViewer = document.getElementById("pano-viewer");
        
        if (!is360Image) return;
        
        if (panoViewer.classList.contains("active")) {
            // Switch to regular image
            panoViewer.classList.remove("active");
            modalImg.style.display = "block";
            modalImg.src = currentImageUrl;
            
            if (currentViewer) {
                currentViewer.destroy();
                currentViewer = null;
            }
        } else {
            // Switch to 360 viewer
            modalImg.style.display = "none";
            panoViewer.classList.add("active");
            
            if (!currentViewer) {
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
                    const panoViewer = document.getElementById("pano-viewer");
                    panoViewer.innerHTML = '<div style="padding: 20px; text-align: center; color: #dc3545;"><p>Unable to load 360° image. This may be due to CORS restrictions.</p><p>Please ensure the S3 bucket has CORS configured to allow requests from this domain.</p></div>';
                }
            }
        }
    }

    /* CLOSE MODAL */
    function closeModal() {
        const modal = document.getElementById("modal");
        modal.style.display = "none";
        
        // Destroy viewer when closing
        if (currentViewer) {
            currentViewer.destroy();
            currentViewer = null;
        }
        
        // Reset
        document.getElementById("pano-viewer").classList.remove("active");
        document.getElementById("viewer-controls").style.display = "none";
        is360Image = false;
        currentImageUrl = null;
    }

    /* Close modal on ESC key */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endpush
@endsection
