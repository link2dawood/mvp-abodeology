<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abodeology | Book Your Valuation</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @if(config('services.google.maps_api_key'))
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places" async defer></script>
    @endif
    
    <style>
        body {
            background: #f7f7f7;
            font-family: "Inter", Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 650px;
            background: #fff;
            margin: 40px auto;
            padding: 40px;
            border-radius: 14px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.1);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
            background: #0F0F0F;
            padding: 20px;
            border-radius: 8px;
        }
        .logo img {
            width: 230px;
            height: auto;
            object-fit: contain;
            max-width: 100%;
        }
        h1 {
            text-align: center;
            margin-bottom: 10px;
            color: #111;
            font-size: 28px;
            font-weight: 600;
        }
        p.sub {
            text-align: center;
            color: #555;
            font-size: 15px;
            margin-bottom: 25px;
        }
        label {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
            color: #111;
        }
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            box-sizing: border-box;
            font-family: inherit;
            line-height: 1.5;
            height: auto;
        }
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
            cursor: pointer;
        }
        select::-ms-expand {
            display: none;
        }
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #32b3ac;
            box-shadow: 0 0 0 3px rgba(50, 179, 172, 0.1);
        }
        input.error,
        select.error,
        textarea.error {
            border-color: #dc3545;
        }
        .radio-group {
            margin-bottom: 20px;
            background: #f2f2f2;
            padding: 18px;
            border-radius: 8px;
        }
        .radio-group label {
            font-weight: 500;
            margin: 8px 0;
            cursor: pointer;
        }
        .radio-group input[type="radio"] {
            width: auto;
            margin-right: 8px;
            cursor: pointer;
        }
        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: -15px;
            margin-bottom: 15px;
        }
        .alert-error {
            background: #fee;
            border: 1px solid #dc3545;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
            color: #dc3545;
            font-size: 14px;
        }
        button {
            width: 100%;
            background: #32b3ac;
            color: #fff;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 17px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }
        button:hover {
            background: #289a94;
        }
        .footer-links {
            margin-top: 20px;
            text-align: center;
        }
        .footer-links a {
            color: #32b3ac;
            text-decoration: none;
            font-size: 14px;
        }
        .footer-links a:hover {
            text-decoration: underline;
        }
        .footer-links p {
            margin: 10px 0;
        }
        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 25px 20px;
            }
            h1 {
                font-size: 24px;
            }
            .logo img {
                width: 180px;
            }
        }

        /* Style Google Places Autocomplete dropdown */
        .pac-container {
            font-family: inherit;
            z-index: 9999 !important;
        }
        .pac-item {
            padding: 8px 12px;
            cursor: pointer;
            font-size: 15px;
        }
        .pac-item-query {
            font-size: 15px;
        }
        /* Hide any text nodes containing only UK or United Kingdom */
        .pac-item {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <h1 style="color: #32b3ac; margin: 0; display: none;">Abodeology®</h1>
        </div>
        
        <h1>Book Your Property Valuation</h1>
        <p class="sub">Submit your details below and we'll contact you to arrange a suitable time.</p>
        
        @if ($errors->any())
            <div class="alert-error">
                <strong>Please fix the following errors:</strong>
                <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-error">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('valuation.booking.store') }}" method="POST">
            @csrf
            
            <label for="name">Full Name *</label>
            <input type="text" 
                   id="name"
                   name="name" 
                   placeholder="Enter your full name" 
                   value="{{ old('name', '') }}"
                   required 
                   autofocus
                   class="{{ $errors->has('name') ? 'error' : '' }}"
                   autocomplete="name">
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="email">Email Address *</label>
            <input type="email" 
                   id="email"
                   name="email" 
                   placeholder="your@email.com" 
                   value="{{ old('email', '') }}"
                   required
                   class="{{ $errors->has('email') ? 'error' : '' }}"
                   autocomplete="email">
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="phone">Phone Number *</label>
            <input type="tel" 
                   id="phone"
                   name="phone" 
                   placeholder="01234 567890" 
                   value="{{ old('phone', '') }}"
                   required
                   class="{{ $errors->has('phone') ? 'error' : '' }}"
                   autocomplete="tel">
            @error('phone')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <div class="radio-group {{ $errors->has('role') ? 'error' : '' }}" style="{{ $errors->has('role') ? 'border: 1px solid #dc3545;' : '' }}">
                <label style="font-weight:600; margin-bottom:10px; display: block;">I am a homeowner looking to: *</label>
                <label style="display: block; margin: 8px 0;">
                    <input type="radio" 
                           name="role" 
                           value="seller" 
                           {{ old('role', 'seller') == 'seller' ? 'checked' : '' }} 
                           required>
                    Sell only
                </label>
                <label style="display: block; margin: 8px 0;">
                    <input type="radio" 
                           name="role" 
                           value="both" 
                           {{ old('role') == 'both' ? 'checked' : '' }}>
                    Sell and looking for another property to buy.
                </label>
            </div>
            @error('role')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="property_address">Property Address *</label>
            <input type="text" 
                   id="property_address"
                   name="property_address" 
                   placeholder="Start typing your address..." 
                   value="{{ old('property_address') }}"
                   required
                   class="{{ $errors->has('property_address') ? 'error' : '' }}"
                   autocomplete="address-line1">
            @error('property_address')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="postcode">Postcode *</label>
            <input type="text" 
                   id="postcode"
                   name="postcode" 
                   placeholder="SW1A 1AA" 
                   value="{{ old('postcode') }}"
                   required
                   class="{{ $errors->has('postcode') ? 'error' : '' }}"
                   autocomplete="postal-code"
                   style="text-transform: uppercase;">
            @error('postcode')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="vendor_address">Your Address (if different from property address)</label>
            <input type="text" 
                   id="vendor_address"
                   name="vendor_address" 
                   placeholder="Start typing your address..." 
                   value="{{ old('vendor_address') }}"
                   class="{{ $errors->has('vendor_address') ? 'error' : '' }}"
                   autocomplete="address-line1">
            @error('vendor_address')
                <div class="error-message">{{ $message }}</div>
            @enderror
            <p style="font-size: 13px; color: #666; margin-top: -15px; margin-bottom: 20px;">
                This is your personal address, separate from the property you're selling. Useful if you own multiple properties.
            </p>

            <label for="property_type">Property Type *</label>
            <select id="property_type" 
                    name="property_type" 
                    required
                    class="{{ $errors->has('property_type') ? 'error' : '' }}">
                <option value="">Select property type</option>
                <option value="detached" {{ old('property_type') == 'detached' ? 'selected' : '' }}>Detached</option>
                <option value="semi" {{ old('property_type') == 'semi' ? 'selected' : '' }}>Semi-detached</option>
                <option value="terraced" {{ old('property_type') == 'terraced' ? 'selected' : '' }}>Terraced</option>
                <option value="flat" {{ old('property_type') == 'flat' ? 'selected' : '' }}>Flat / Maisonette</option>
                <option value="bungalow" {{ old('property_type') == 'bungalow' ? 'selected' : '' }}>Bungalow</option>
                <option value="other" {{ old('property_type') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('property_type')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="bedrooms">Number of Bedrooms *</label>
            <input type="number" 
                   id="bedrooms"
                   name="bedrooms" 
                   min="0" 
                   max="50"
                   placeholder="e.g. 3" 
                   value="{{ old('bedrooms') }}"
                   required
                   class="{{ $errors->has('bedrooms') ? 'error' : '' }}">
            @error('bedrooms')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="seller_notes">Additional Notes (optional)</label>
            <textarea id="seller_notes"
                      name="seller_notes" 
                      placeholder="Anything you'd like us to know?"
                      rows="3"
                      class="{{ $errors->has('seller_notes') ? 'error' : '' }}">{{ old('seller_notes') }}</textarea>
            @error('seller_notes')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <button type="submit">Submit Valuation Request</button>
        </form>

        <div class="footer-links">
            <p>Already have an account? <a href="{{ route('login') }}">Log in</a></p>
            <p>Need help? <a href="mailto:support@abodeology.co.uk">Contact Support</a></p>
        </div>
    </div>

    @if(config('services.google.maps_api_key'))
    <script>
        // Define initAutocomplete function
        function initAutocomplete() {
            // Wait for Google Maps API to be fully loaded
            if (typeof google === 'undefined' || typeof google.maps === 'undefined' || typeof google.maps.places === 'undefined') {
                setTimeout(initAutocomplete, 100);
                return;
            }
            
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAutocomplete);
                return;
            }
            let addressAutocomplete;
            let postcodeAutocomplete;

            // Initialize autocomplete for property address
            const addressInput = document.getElementById('property_address');
            if (!addressInput) return;
            
            addressAutocomplete = new google.maps.places.Autocomplete(
                addressInput,
                {
                    types: ['address'],
                    componentRestrictions: { country: 'gb' }, // Restrict to UK addresses
                    fields: ['address_components', 'formatted_address']
                }
            );
            
            // Function to clean UK from dropdown suggestions
            function cleanAutocompleteDropdown() {
                setTimeout(function() {
                    const pacContainer = document.querySelector('.pac-container');
                    if (pacContainer) {
                        const pacItems = pacContainer.querySelectorAll('.pac-item');
                        pacItems.forEach(function(item) {
                            const itemText = item.textContent || item.innerText;
                            if (itemText) {
                                // Remove UK/United Kingdom from the displayed text
                                const cleanedText = itemText.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                                if (cleanedText !== itemText) {
                                    // Update the text content
                                    const querySpan = item.querySelector('.pac-item-query');
                                    const matchedSpan = item.querySelector('.pac-matched');
                                    if (querySpan && matchedSpan) {
                                        // Try to update the visible parts
                                        const fullText = itemText.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                                        // Split by comma to get parts
                                        const parts = fullText.split(',').map(p => p.trim()).filter(p => p && !p.match(/^UK$/i) && !p.match(/^United Kingdom$/i));
                                        if (parts.length > 0) {
                                            querySpan.textContent = parts[0];
                                            if (parts.length > 1) {
                                                matchedSpan.textContent = parts.slice(1).join(', ');
                                            } else {
                                                matchedSpan.textContent = '';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }, 50);
            }

            // Watch for autocomplete dropdown appearance and modify it
            let pacObserver = null;
            
            function setupPacObserver() {
                if (pacObserver) return;
                
                pacObserver = new MutationObserver(function(mutations) {
                    const pacContainer = document.querySelector('.pac-container');
                    if (pacContainer) {
                        const pacItems = pacContainer.querySelectorAll('.pac-item');
                        pacItems.forEach(function(item) {
                            // Get all text nodes and modify them
                            const walker = document.createTreeWalker(
                                item,
                                NodeFilter.SHOW_TEXT,
                                null,
                                false
                            );
                            
                            let node;
                            while (node = walker.nextNode()) {
                                const text = node.textContent;
                                if (text && (text.includes('UK') || text.includes('United Kingdom'))) {
                                    const cleaned = text.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                                    if (cleaned !== text) {
                                        node.textContent = cleaned;
                                    }
                                }
                            }
                            
                            // Also modify the structured parts
                            const querySpan = item.querySelector('.pac-item-query');
                            const matchedSpan = item.querySelector('.pac-matched');
                            
                            if (querySpan && matchedSpan) {
                                let fullText = item.textContent || item.innerText;
                                fullText = fullText.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                                
                                const parts = fullText.split(',').map(p => p.trim()).filter(p => p && !p.match(/^UK$/i) && !p.match(/^United Kingdom$/i));
                                
                                if (parts.length > 0) {
                                    querySpan.textContent = parts[0];
                                    if (parts.length > 1) {
                                        matchedSpan.textContent = parts.slice(1).join(', ');
                                    } else {
                                        matchedSpan.textContent = '';
                                    }
                                }
                            }
                        });
                    }
                });
                
                pacObserver.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }

            addressAutocomplete.addListener('place_changed', function() {
                const place = addressAutocomplete.getPlace();
                
                if (!place.address_components) {
                    return;
                }

                // Extract address components
                let streetNumber = '';
                let route = '';
                let postcode = '';
                let locality = '';
                let administrativeArea = '';

                for (const component of place.address_components) {
                    const componentType = component.types[0];

                    switch (componentType) {
                        case 'street_number':
                            streetNumber = component.long_name;
                            break;
                        case 'route':
                            route = component.long_name;
                            break;
                        case 'postal_code':
                            postcode = component.long_name;
                            break;
                        case 'postal_town':
                        case 'locality':
                            if (!locality) {
                                locality = component.long_name;
                            }
                            break;
                        case 'administrative_area_level_1':
                            administrativeArea = component.short_name;
                            break;
                    }
                }

                // Build address from components, excluding country
                let formattedAddress = '';
                if (streetNumber && route) {
                    formattedAddress = streetNumber + ' ' + route;
                } else if (route) {
                    formattedAddress = route;
                } else {
                    formattedAddress = place.formatted_address || '';
                }
                
                // Add locality if available
                if (locality && !formattedAddress.includes(locality)) {
                    formattedAddress += ', ' + locality;
                }
                
                // Add administrative area if available
                if (administrativeArea && !formattedAddress.includes(administrativeArea)) {
                    formattedAddress += ', ' + administrativeArea;
                }
                
                // Remove any UK references
                formattedAddress = formattedAddress.replace(/,\s*UK\s*$/i, '').replace(/,\s*United Kingdom\s*$/i, '').trim();
                
                document.getElementById('property_address').value = formattedAddress;

                // Auto-fill postcode if found
                if (postcode) {
                    document.getElementById('postcode').value = postcode;
                }
            });

            // Remove ", UK" in real-time as user types or when autocomplete suggestions appear
            addressInput.addEventListener('focus', function() {
                setupPacObserver();
                setTimeout(cleanAutocompleteDropdown, 100);
            });
            
            // Real-time removal as user types
            addressInput.addEventListener('input', function() {
                setTimeout(cleanAutocompleteDropdown, 50);
                let value = this.value;
                const originalValue = value;
                // Remove UK from anywhere in the string (not just end)
                value = value.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                // Clean up any double commas or trailing commas
                value = value.replace(/,\s*,/g, ',').replace(/,\s*$/g, '').trim();
                if (value !== originalValue) {
                    const cursorPos = this.selectionStart;
                    this.value = value;
                    // Try to maintain cursor position
                    const newCursorPos = Math.max(0, cursorPos - (originalValue.length - value.length));
                    this.setSelectionRange(newCursorPos, newCursorPos);
                }
            });
            
            // Also remove ", UK" on blur
            addressInput.addEventListener('blur', function() {
                let value = this.value.trim();
                value = value.replace(/,\s*UK\s*$/i, '').replace(/,\s*United Kingdom\s*$/i, '').trim();
                if (value !== this.value) {
                    this.value = value;
                }
                // Clean up observer
                setTimeout(function() {
                    if (pacObserver) {
                        pacObserver.disconnect();
                        pacObserver = null;
                    }
                }, 500);
            });
            
            // Continuous cleanup of dropdown while visible
            let dropdownCleanupInterval = null;
            
            addressInput.addEventListener('focus', function() {
                // Start continuous cleanup when input is focused
                if (!dropdownCleanupInterval) {
                    dropdownCleanupInterval = setInterval(function() {
                        const pacContainer = document.querySelector('.pac-container');
                        if (pacContainer && pacContainer.style.display !== 'none') {
                            // Clean all items in dropdown
                            const pacItems = pacContainer.querySelectorAll('.pac-item');
                            pacItems.forEach(function(item) {
                                // Modify all text content
                                const allText = item.textContent || item.innerText || '';
                                if (allText.includes('UK') || allText.includes('United Kingdom')) {
                                    // Replace text in all child nodes
                                    const walker = document.createTreeWalker(
                                        item,
                                        NodeFilter.SHOW_TEXT,
                                        null,
                                        false
                                    );
                                    
                                    let node;
                                    while (node = walker.nextNode()) {
                                        if (node.textContent) {
                                            const cleaned = node.textContent.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                                            if (cleaned !== node.textContent) {
                                                node.textContent = cleaned;
                                            }
                                        }
                                    }
                                    
                                    // Also update structured parts
                                    const querySpan = item.querySelector('.pac-item-query');
                                    const matchedSpan = item.querySelector('.pac-matched');
                                    if (querySpan) {
                                        let queryText = querySpan.textContent || '';
                                        queryText = queryText.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                                        querySpan.textContent = queryText;
                                    }
                                    if (matchedSpan) {
                                        let matchedText = matchedSpan.textContent || '';
                                        matchedText = matchedText.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                                        matchedSpan.textContent = matchedText;
                                    }
                                }
                            });
                        }
                    }, 100); // Check every 100ms
                }
            });
            
            // Clean up interval when input loses focus
            addressInput.addEventListener('blur', function() {
                setTimeout(function() {
                    if (dropdownCleanupInterval) {
                        clearInterval(dropdownCleanupInterval);
                        dropdownCleanupInterval = null;
                    }
                }, 300);
            });

            // Initialize autocomplete for vendor address (if different from property)
            const vendorAddressInput = document.getElementById('vendor_address');
            if (vendorAddressInput) {
                const vendorAddressAutocomplete = new google.maps.places.Autocomplete(
                    vendorAddressInput,
                    {
                        types: ['address'],
                        componentRestrictions: { country: 'gb' },
                        fields: ['address_components', 'formatted_address']
                    }
                );

                // Function to clean UK from vendor address dropdown
                function cleanVendorAutocompleteDropdown() {
                    setTimeout(function() {
                        const pacContainer = document.querySelector('.pac-container');
                        if (pacContainer) {
                            const pacItems = pacContainer.querySelectorAll('.pac-item');
                            pacItems.forEach(function(item) {
                                const itemText = item.textContent || item.innerText;
                                if (itemText) {
                                    const cleanedText = itemText.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                                    if (cleanedText !== itemText) {
                                        const querySpan = item.querySelector('.pac-item-query');
                                        const matchedSpan = item.querySelector('.pac-matched');
                                        if (querySpan && matchedSpan) {
                                            const fullText = itemText.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                                            const parts = fullText.split(',').map(p => p.trim()).filter(p => p && !p.match(/^UK$/i) && !p.match(/^United Kingdom$/i));
                                            if (parts.length > 0) {
                                                querySpan.textContent = parts[0];
                                                if (parts.length > 1) {
                                                    matchedSpan.textContent = parts.slice(1).join(', ');
                                                } else {
                                                    matchedSpan.textContent = '';
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    }, 50);
                }

                // Watch for vendor address autocomplete dropdown
                let vendorPacObserver = null;
                
                function setupVendorPacObserver() {
                    if (vendorPacObserver) return;
                    
                    vendorPacObserver = new MutationObserver(function(mutations) {
                        const pacContainer = document.querySelector('.pac-container');
                        if (pacContainer) {
                            const pacItems = pacContainer.querySelectorAll('.pac-item');
                            pacItems.forEach(function(item) {
                                const walker = document.createTreeWalker(
                                    item,
                                    NodeFilter.SHOW_TEXT,
                                    null,
                                    false
                                );
                                
                                let node;
                                while (node = walker.nextNode()) {
                                    const text = node.textContent;
                                    if (text && (text.includes('UK') || text.includes('United Kingdom'))) {
                                        const cleaned = text.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                                        if (cleaned !== text) {
                                            node.textContent = cleaned;
                                        }
                                    }
                                }
                                
                                const querySpan = item.querySelector('.pac-item-query');
                                const matchedSpan = item.querySelector('.pac-matched');
                                
                                if (querySpan && matchedSpan) {
                                    let fullText = item.textContent || item.innerText;
                                    fullText = fullText.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                                    
                                    const parts = fullText.split(',').map(p => p.trim()).filter(p => p && !p.match(/^UK$/i) && !p.match(/^United Kingdom$/i));
                                    
                                    if (parts.length > 0) {
                                        querySpan.textContent = parts[0];
                                        if (parts.length > 1) {
                                            matchedSpan.textContent = parts.slice(1).join(', ');
                                        } else {
                                            matchedSpan.textContent = '';
                                        }
                                    }
                                }
                            });
                        }
                    });
                    
                    vendorPacObserver.observe(document.body, {
                        childList: true,
                        subtree: true
                    });
                }

                vendorAddressInput.addEventListener('focus', function() {
                    setupVendorPacObserver();
                    setTimeout(cleanVendorAutocompleteDropdown, 100);
                });

                vendorAddressInput.addEventListener('input', function() {
                    setTimeout(cleanVendorAutocompleteDropdown, 50);
                });
                
                vendorAddressInput.addEventListener('blur', function() {
                    setTimeout(function() {
                        if (vendorPacObserver) {
                            vendorPacObserver.disconnect();
                            vendorPacObserver = null;
                        }
                    }, 500);
                });

                vendorAddressAutocomplete.addListener('place_changed', function() {
                    const place = vendorAddressAutocomplete.getPlace();
                    
                    if (!place.address_components) {
                        return;
                    }

                    // Extract address components
                    let streetNumber = '';
                    let route = '';
                    let locality = '';
                    let administrativeArea = '';

                    for (const component of place.address_components) {
                        const componentType = component.types[0];

                        switch (componentType) {
                            case 'street_number':
                                streetNumber = component.long_name;
                                break;
                            case 'route':
                                route = component.long_name;
                                break;
                            case 'postal_town':
                            case 'locality':
                                if (!locality) {
                                    locality = component.long_name;
                                }
                                break;
                            case 'administrative_area_level_1':
                                administrativeArea = component.short_name;
                                break;
                        }
                    }

                    // Build address from components, excluding country
                    let formattedAddress = '';
                    if (streetNumber && route) {
                        formattedAddress = streetNumber + ' ' + route;
                    } else if (route) {
                        formattedAddress = route;
                    } else {
                        formattedAddress = place.formatted_address || '';
                    }
                    
                    // Add locality if available
                    if (locality && !formattedAddress.includes(locality)) {
                        formattedAddress += ', ' + locality;
                    }
                    
                    // Add administrative area if available
                    if (administrativeArea && !formattedAddress.includes(administrativeArea)) {
                        formattedAddress += ', ' + administrativeArea;
                    }
                    
                    // Remove any UK references
                    formattedAddress = formattedAddress.replace(/,\s*UK\s*$/i, '').replace(/,\s*United Kingdom\s*$/i, '').trim();
                    
                    vendorAddressInput.value = formattedAddress;
                });

                // Remove ", UK" in real-time for vendor address
                // Real-time removal as user types
                vendorAddressInput.addEventListener('input', function() {
                    let value = this.value;
                    const originalValue = value;
                    // Remove UK from anywhere in the string
                    value = value.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                    // Clean up any double commas or trailing commas
                    value = value.replace(/,\s*,/g, ',').replace(/,\s*$/g, '').trim();
                    if (value !== originalValue) {
                        const cursorPos = this.selectionStart;
                        this.value = value;
                        const newCursorPos = Math.max(0, cursorPos - (originalValue.length - value.length));
                        this.setSelectionRange(newCursorPos, newCursorPos);
                    }
                });
                
                // Also remove ", UK" on blur
                vendorAddressInput.addEventListener('blur', function() {
                    let value = this.value.trim();
                    value = value.replace(/,\s*UK\s*$/i, '').replace(/,\s*United Kingdom\s*$/i, '').trim();
                    if (value !== this.value) {
                        this.value = value;
                    }
                });
                
                // Continuous cleanup of vendor address dropdown while visible
                let vendorDropdownCleanupInterval = null;
                
                vendorAddressInput.addEventListener('focus', function() {
                    if (!vendorDropdownCleanupInterval) {
                        vendorDropdownCleanupInterval = setInterval(function() {
                            const pacContainer = document.querySelector('.pac-container');
                            if (pacContainer && pacContainer.style.display !== 'none') {
                                const pacItems = pacContainer.querySelectorAll('.pac-item');
                                pacItems.forEach(function(item) {
                                    const allText = item.textContent || item.innerText || '';
                                    if (allText.includes('UK') || allText.includes('United Kingdom')) {
                                        const walker = document.createTreeWalker(
                                            item,
                                            NodeFilter.SHOW_TEXT,
                                            null,
                                            false
                                        );
                                        
                                        let node;
                                        while (node = walker.nextNode()) {
                                            if (node.textContent) {
                                                const cleaned = node.textContent.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                                                if (cleaned !== node.textContent) {
                                                    node.textContent = cleaned;
                                                }
                                            }
                                        }
                                        
                                        const querySpan = item.querySelector('.pac-item-query');
                                        const matchedSpan = item.querySelector('.pac-matched');
                                        if (querySpan) {
                                            let queryText = querySpan.textContent || '';
                                            queryText = queryText.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                                            querySpan.textContent = queryText;
                                        }
                                        if (matchedSpan) {
                                            let matchedText = matchedSpan.textContent || '';
                                            matchedText = matchedText.replace(/,\s*UK\s*/gi, ', ').replace(/,\s*United Kingdom\s*/gi, ', ').trim();
                                            matchedSpan.textContent = matchedText;
                                        }
                                    }
                                });
                            }
                        }, 100);
                    }
                });
                
                vendorAddressInput.addEventListener('blur', function() {
                    setTimeout(function() {
                        if (vendorDropdownCleanupInterval) {
                            clearInterval(vendorDropdownCleanupInterval);
                            vendorDropdownCleanupInterval = null;
                        }
                    }, 300);
                });
            }

            // Format postcode to uppercase
            const postcodeInput = document.getElementById('postcode');
            if (postcodeInput) {
                postcodeInput.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
                postcodeInput.addEventListener('blur', function() {
                    this.value = this.value.trim().toUpperCase();
                });
            }

            // Initialize autocomplete for postcode (using geocoder for UK postcodes)
            postcodeAutocomplete = new google.maps.places.Autocomplete(
                document.getElementById('postcode'),
                {
                    types: ['(regions)'],
                    componentRestrictions: { country: 'gb' }
                }
            );

            postcodeAutocomplete.addListener('place_changed', function() {
                const place = postcodeAutocomplete.getPlace();
                
                if (!place.address_components) {
                    return;
                }

                // Extract postcode
                for (const component of place.address_components) {
                    if (component.types.includes('postal_code')) {
                        document.getElementById('postcode').value = component.long_name;
                        break;
                    }
                }
            });
        } // End of initAutocomplete function

        // Initialize when both Google Maps API and DOM are ready
        window.addEventListener('load', function() {
            if (typeof google !== 'undefined' && typeof google.maps !== 'undefined' && typeof google.maps.places !== 'undefined') {
                initAutocomplete();
            } else {
                // Retry if API hasn't loaded yet
                let retries = 0;
                const checkApi = setInterval(function() {
                    retries++;
                    if (typeof google !== 'undefined' && typeof google.maps !== 'undefined' && typeof google.maps.places !== 'undefined') {
                        clearInterval(checkApi);
                        initAutocomplete();
                    } else if (retries > 50) {
                        clearInterval(checkApi);
                        console.warn('Google Maps API failed to load after 5 seconds. Address autocomplete will not be available.');
                    }
                }, 100);
            }
        });
    </script>
    @endif
</body>
</html>
