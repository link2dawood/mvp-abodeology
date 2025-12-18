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
        border-left: 4px solid var(--abodeology-teal);
    }

    .room-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 15px;
        color: var(--dark-text);
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
        padding: 30px;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background: var(--white);
        max-width: 90%;
        max-height: 90vh;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }

    .modal-img {
        width: 100%;
        max-height: 90vh;
        object-fit: contain;
    }

    .close-modal {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0,0,0,0.7);
        color: #fff;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        font-size: 24px;
        font-weight: 700;
        z-index: 10;
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
        @foreach($roomsData as $roomName => $roomImages)
            <div class="room-section">
                <div class="room-title">{{ ucfirst($roomName) }}</div>
                
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
                        @if($image->image_url)
                            <div class="image-item" onclick="openModal('{{ $image->image_url }}', '{{ $roomName }}')">
                                <img src="{{ $image->image_url }}" alt="{{ $roomName }} Image" loading="lazy">
                                @if($image->is_360)
                                    <div class="image-badge">360°</div>
                                @endif
                                <div class="image-info">
                                    @if($image->moisture_reading)
                                        Moisture: {{ $image->moisture_reading }}%
                                    @endif
                                    @if($image->created_at)
                                        <br>{{ $image->created_at->format('M j, Y g:i A') }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
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
    <div id="imageModal" class="modal" onclick="closeModal()">
        <div class="close-modal" onclick="closeModal()">×</div>
        <div class="modal-content">
            <img id="modalImage" class="modal-img" src="" alt="HomeCheck Image">
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModal(imageUrl, roomName) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageUrl;
        modalImage.alt = roomName + ' Image';
        modal.classList.add('active');
    }

    function closeModal() {
        document.getElementById('imageModal').classList.remove('active');
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endpush
@endsection

