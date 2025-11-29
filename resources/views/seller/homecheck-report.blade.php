<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abodeology HomeCheck Report</title>
    <style>
        :root {
            --teal: #32b3ac;
            --black: #000;
            --white: #fff;
            --grey: #f6f6f6;
            --text: #222;
            --muted: #777;
            --shadow: rgba(0,0,0,0.08);
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--grey);
            font-family: "Inter", Arial, sans-serif;
            color: var(--text);
        }

        /* HEADER */
        .header {
            background: var(--black);
            padding: 28px 0;
            text-align: center;
        }

        .header img {
            width: 260px;
            height: auto;
            object-fit: contain;
        }

        /* PROPERTY INFO */
        .top-info {
            max-width: 1100px;
            margin: 40px auto 10px;
            padding: 0 20px;
        }

        .value-label {
            text-transform: uppercase;
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 4px;
            letter-spacing: 0.6px;
        }

        .value-number {
            font-size: 44px;
            font-weight: 800;
            color: var(--teal);
            margin-bottom: 25px;
        }

        .address {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .details-row {
            font-size: 15px;
            color: var(--muted);
        }

        /* NOTICE */
        .notice {
            max-width: 1100px;
            margin: 25px auto;
            padding: 16px 20px;
            background: #fff6d6;
            border-radius: 8px;
            font-size: 14px;
            color: #7a6300;
        }

        /* ROOM CARD */
        .room-container {
            max-width: 1100px;
            margin: 40px auto;
            position: relative;
        }

        .room {
            background: var(--white);
            display: flex;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            position: relative;
            border: none;
        }

        .room img {
            width: 45%;
            object-fit: cover;
            min-height: 340px;
            display: block;
            border: none;
        }

        .room-content {
            width: 55%;
            padding: 35px 35px 40px;
            position: relative;
            border: none;
        }

        .room-title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 10px;
            border: none;
        }

        .section-label {
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--teal);
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
            position: absolute;
            top: 20px;
            right: 30px;
            background: var(--white);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 20px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        }

        /* FOOTER */
        .footer {
            text-align: center;
            padding: 40px 0;
            font-size: 14px;
            color: var(--muted);
        }

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

            .modal-content {
                flex-direction: column;
                width: 95%;
            }

            .modal-img {
                width: 100%;
                max-height: 50vh;
            }

            .modal-text {
                width: 100%;
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
        <span style="display: none; color: #fff; font-weight: 600; font-size: 24px;">Abodeology®</span>
    </div>

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
            $imageUrl = $firstImage ? \Storage::url($firstImage->image_path) : asset('media/placeholder-room.jpg');
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
                <img src="{{ $imageUrl }}" alt="{{ $roomName }}" onerror="this.src='{{ asset('media/placeholder-room.jpg') }}';">
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
                </div>
            </div>
            <div class="icon-bar">
                <div class="icon-btn" onclick="speakText('{{ $roomId }}')">
                    <img src="https://img.icons8.com/ios-filled/50/speaker.png" alt="Speak">
                </div>
                <div class="icon-btn" onclick="openModal('{{ $roomId }}', '{{ $roomName }}', '{{ $imageUrl }}', '{{ addslashes($aiComments) }}')">
                    <img src="https://img.icons8.com/ios-filled/50/fit-to-width.png" alt="Expand">
                </div>
            </div>
        </div>
    @endforeach

    @if($rooms->count() === 0)
        <div class="room-container">
            <div class="room">
                <div class="room-content" style="width: 100%; text-align: center; padding: 60px 40px;">
                    <div class="room-title">No Room Data Available</div>
                    <div class="text-block" style="color: var(--muted);">
                        HomeCheck data is still being processed. Please check back later.
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL -->
    <div id="modal" class="modal" onclick="if(event.target.id === 'modal') closeModal();">
        <div class="close-modal" onclick="closeModal()">×</div>
        <div class="modal-content">
            <img id="modal-img" class="modal-img" src="" alt="Room Image">
            <div class="modal-text">
                <h2 id="modal-title"></h2>
                <div id="modal-body"></div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
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
            document.getElementById("modal-img").src = imgSrc;
            document.getElementById("modal-title").innerText = roomName;
            document.getElementById("modal-body").innerHTML = bodyText.replace(/\n/g, '<br>');
            modal.style.display = "flex";
        }

        /* CLOSE MODAL */
        function closeModal() {
            document.getElementById("modal").style.display = "none";
        }

        /* Close modal on ESC key */
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>

    <!-- FOOTER -->
    <div class="footer">
        © {{ date('Y') }} Abodeology®. All rights reserved.
    </div>
</body>
</html>

