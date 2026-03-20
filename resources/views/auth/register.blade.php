<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account | Abodeology®</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        /* BRAND COLOURS */
        :root {
            --abodeology-teal: #2CB8B4;
            --black: #0F0F0F;
            --white: #FFFFFF;
            --soft-grey: #F4F4F4;
            --dark-text: #1E1E1E;
        }

        /* GLOBAL STYLE */
        body {
            margin: 0;
            /* background: var(--soft-grey); */
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: var(--dark-text);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* WRAPPER */
        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            /* padding: 35px 20px;
            min-height: 100vh; */
        }

        /* CARD */
        .register-box {
            background: var(--white);
            width: 100%;
            max-width: 460px;
            padding: 35px;
            border-radius: 12px;
            border: 1px solid #E5E5E5;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.07);
            text-align: center;
        }

        /* LOGO */
        .logo {
            margin-bottom: 20px;
            background: var(--black);
            padding: 20px;
            border-radius: 8px;
        }

        .logo img {
            width: 160px;
            height: auto;
            object-fit: contain;
        }

        /* HEADINGS */
        h2 {
            margin-bottom: 15px;
            font-size: 26px;
            font-weight: 600;
        }

        .subtext {
            font-size: 15px;
            color: #555;
            margin-bottom: 25px;
        }

        /* FORM FIELDS */
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"],
        select {
            width: 100%;
            padding: 14px;
            margin-bottom: 18px;
            border: 1px solid #D9D9D9;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
            box-sizing: border-box;
        }

        input:focus,
        select:focus {
            border-color: var(--abodeology-teal);
        }

        input.error {
            border-color: #dc3545;
        }

        /* ERROR MESSAGES */
        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: -15px;
            margin-bottom: 15px;
            text-align: left;
        }

        /* BUTTON */
        .btn {
            width: 100%;
            background: var(--abodeology-teal);
            color: var(--white);
            padding: 14px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #25A29F;
        }

        .btn:active {
            transform: scale(0.98);
        }

        /* FOOTER LINKS */
        .footer-links {
            margin-top: 20px;
        }

        .footer-links a {
            color: var(--abodeology-teal);
            text-decoration: none;
            font-size: 14px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .footer-links p {
            margin: 10px 0;
        }

        /* RADIO BUTTON GROUP */
        .role-box {
            background: var(--soft-grey);
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 20px;
            text-align: left;
            border: 1px solid #E5E5E5;
        }

        .role-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .role-option {
            margin-bottom: 8px;
        }

        .role-option label {
            margin-left: 8px;
            font-size: 14px;
            cursor: pointer;
        }

        .role-option input[type="radio"] {
            width: auto;
            margin: 0;
            cursor: pointer;
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 480px) {
            .wrapper {
                padding: 15px;
                align-items: flex-start;
                padding-top: 30px;
            }

            .register-box {
                padding: 25px 20px;
                max-width: 100%;
            }

            .logo img {
                width: 140px;
                height: auto;
                object-fit: contain;
            }

            h2 {
                font-size: 20px;
            }

            input[type="text"],
            input[type="email"],
            input[type="tel"],
            input[type="password"],
            select {
                padding: 12px;
                font-size: 14px;
            }

            .btn {
                padding: 12px;
                font-size: 15px;
            }

            .role-box {
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="register-box">
            <div class="logo">
                <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" onerror="this.onerror=null; this.src='{{ asset('media/abodeology-logo.png') }}'; this.onerror=function(){this.style.display='none'; this.nextElementSibling.style.display='inline-block';};">
                <span style="display: none; color: #2CB8B4; font-weight: 600; font-size: 24px;">Abodeology®</span>
            </div>
            
            <h2>Create your account</h2>
            <p class="subtext">Join Abodeology to manage your property journey.</p>
            
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

            <form action="{{ route('register') }}" method="POST">
                @csrf

                <!-- ACCOUNT ROLE SELECTION (FIRST STEP) -->
                <div class="role-box {{ $errors->has('role') ? 'error' : '' }}" style="{{ $errors->has('role') ? 'border-color: #dc3545;' : '' }}">
                    <div class="role-title">I am registering as:</div>
                    <div class="role-option">
                        <input type="radio" id="seller" name="role" value="seller" {{ old('role') == 'seller' ? 'checked' : '' }}>
                        <label for="seller">Seller</label>
                    </div>
                    <div class="role-option">
                        <input type="radio" id="buyer" name="role" value="buyer" {{ old('role') == 'buyer' ? 'checked' : '' }} required>
                        <label for="buyer">Buyer</label>
                    </div>
                    <div class="role-option">
                        <input type="radio" id="both" name="role" value="both" {{ old('role') == 'both' ? 'checked' : '' }}>
                        <label for="both">Both Buyer & Seller</label>
                    </div>
                </div>
                @error('role')
                    <div class="error-message">{{ $message }}</div>
                @enderror
                
                <input type="text" 
                       name="name" 
                       placeholder="Full name" 
                       value="{{ old('name') }}"
                       required 
                       autofocus
                       class="{{ $errors->has('name') ? 'error' : '' }}"
                       autocomplete="name">
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="email" 
                       name="email" 
                       placeholder="Email address" 
                       value="{{ old('email') }}"
                       required
                       class="{{ $errors->has('email') ? 'error' : '' }}"
                       autocomplete="email">
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="tel" 
                       id="phone"
                       name="phone" 
                       placeholder="Mobile number" 
                       value="{{ old('phone') }}"
                       required
                       class="{{ $errors->has('phone') ? 'error' : '' }}"
                       autocomplete="tel"
                       inputmode="tel"
                       pattern="[0-9+()\\-\\s]+"
                       title="Use numbers and standard phone symbols only">
                @error('phone')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <div id="vendor_address_wrapper" style="{{ in_array(old('role'), ['seller', 'both']) ? '' : 'display:none;' }}">
                    <input type="text"
                           id="vendor_address"
                           name="vendor_address"
                           placeholder="Valuation address"
                           value="{{ old('vendor_address') }}"
                           class="{{ $errors->has('vendor_address') ? 'error' : '' }}"
                           autocomplete="street-address">
                    <input type="text"
                           id="vendor_postcode"
                           name="vendor_postcode"
                           placeholder="Postcode"
                           value=""
                           autocomplete="postal-code"
                           inputmode="text"
                           style="text-transform: uppercase;">
                    @error('vendor_address')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <input type="password" 
                       name="password" 
                       placeholder="Create password" 
                       required
                       class="{{ $errors->has('password') ? 'error' : '' }}"
                       autocomplete="new-password">
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <input type="password" 
                       name="password_confirmation" 
                       placeholder="Confirm password" 
                       required
                       autocomplete="new-password">

                <button type="submit" class="btn">Create Account</button>
            </form>

            <div class="footer-links">
                <p>Already have an account? <a href="{{ route('login') }}">Log in</a></p>
            </div>
        </div>
    </div>

    <script>
        (function toggleValuationAddressByRole() {
            const roleInputs = document.querySelectorAll('input[name="role"]');
            const addressWrapper = document.getElementById('vendor_address_wrapper');
            const addressInput = document.getElementById('vendor_address');
            const postcodeInput = document.getElementById('vendor_postcode');

            function syncAddressVisibility() {
                const selectedRoleInput = document.querySelector('input[name="role"]:checked');
                const selectedRole = selectedRoleInput ? selectedRoleInput.value : null;
                const shouldShowAddress = selectedRole === 'seller' || selectedRole === 'both';

                if (!addressWrapper || !addressInput) {
                    return;
                }

                addressWrapper.style.display = shouldShowAddress ? '' : 'none';
                addressInput.required = shouldShowAddress;
                if (postcodeInput) {
                    postcodeInput.required = shouldShowAddress;
                }
            }

            roleInputs.forEach(function (input) {
                input.addEventListener('change', syncAddressVisibility);
            });

            syncAddressVisibility();
        })();

        (function syncVendorPostcodeBeforeSubmit() {
            const form = document.querySelector('form[action="{{ route('register') }}"]');
            const addressInput = document.getElementById('vendor_address');
            const postcodeInput = document.getElementById('vendor_postcode');

            if (!form || !addressInput || !postcodeInput) {
                return;
            }

            postcodeInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase().replace(/[^A-Z0-9\s]/g, '');
            });

            form.addEventListener('submit', function() {
                const postcode = postcodeInput.value.trim().toUpperCase();
                const address = addressInput.value.trim();

                if (postcode) {
                    postcodeInput.value = postcode;
                }

                if (address && postcode && !address.toUpperCase().includes(postcode)) {
                    addressInput.value = address.replace(/\s*,\s*$/, '') + ', ' + postcode;
                }
            });
        })();

        (function restrictPhoneInput() {
            const phoneInput = document.getElementById('phone');
            if (!phoneInput) {
                return;
            }

            phoneInput.addEventListener('input', function() {
                const originalValue = this.value;
                const sanitizedValue = originalValue.replace(/[^0-9+()\-\s]/g, '');

                if (sanitizedValue !== originalValue) {
                    const cursorPos = this.selectionStart || sanitizedValue.length;
                    this.value = sanitizedValue;
                    const newCursorPos = Math.max(0, cursorPos - (originalValue.length - sanitizedValue.length));
                    this.setSelectionRange(newCursorPos, newCursorPos);
                }
            });
        })();
    </script>

    @if(config('services.google.maps_api_key'))
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places" async defer></script>
    <script>
        (function initRegisterAddressAutocomplete() {
            function setupAutocomplete() {
                if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                    return false;
                }

                const vendorAddressInput = document.getElementById('vendor_address');
                const vendorPostcodeInput = document.getElementById('vendor_postcode');
                if (!vendorAddressInput) {
                    return true;
                }

                const vendorAddressAutocomplete = new google.maps.places.Autocomplete(vendorAddressInput, {
                    types: ['address'],
                    componentRestrictions: { country: 'gb' },
                    fields: ['address_components', 'formatted_address']
                });

                function normalizeVendorAddress(value) {
                    return value
                        .replace(/,\s*UK\s*/gi, ', ')
                        .replace(/,\s*United Kingdom\s*/gi, ', ')
                        .replace(/,\s*,/g, ',')
                        .replace(/,\s*$/g, '')
                        .trim();
                }

                function buildVendorAddress(place) {
                    if (!place || !place.address_components) {
                        return normalizeVendorAddress(place && place.formatted_address ? place.formatted_address : '');
                    }

                    let streetNumber = '';
                    let route = '';
                    let locality = '';
                    let administrativeArea = '';
                    let postcode = '';

                    place.address_components.forEach(function(component) {
                        const componentType = component.types[0];

                        if (component.types.includes('postal_code')) {
                            postcode = component.long_name;
                        }

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
                    });

                    let formattedAddress = '';
                    if (streetNumber && route) {
                        formattedAddress = streetNumber + ' ' + route;
                    } else if (route) {
                        formattedAddress = route;
                    } else {
                        formattedAddress = place.formatted_address || '';
                    }

                    if (locality && !formattedAddress.includes(locality)) {
                        formattedAddress += ', ' + locality;
                    }

                    if (administrativeArea && !formattedAddress.includes(administrativeArea)) {
                        formattedAddress += ', ' + administrativeArea;
                    }

                    if (postcode && !formattedAddress.includes(postcode)) {
                        formattedAddress += ', ' + postcode;
                    }

                    return normalizeVendorAddress(formattedAddress);
                }

                function cleanVendorAutocompleteDropdown() {
                    setTimeout(function() {
                        const pacContainer = document.querySelector('.pac-container');
                        if (!pacContainer) return;

                        const pacItems = pacContainer.querySelectorAll('.pac-item');
                        pacItems.forEach(function(item) {
                            const walker = document.createTreeWalker(item, NodeFilter.SHOW_TEXT, null, false);
                            let node;

                            while (node = walker.nextNode()) {
                                const originalText = node.textContent || '';
                                const cleanedText = originalText
                                    .replace(/,\s*UK\s*/gi, ', ')
                                    .replace(/,\s*United Kingdom\s*/gi, ', ')
                                    .replace(/,\s*,/g, ',')
                                    .replace(/,\s*$/g, '')
                                    .trim();

                                if (cleanedText !== originalText.trim()) {
                                    node.textContent = cleanedText;
                                }
                            }
                        });
                    }, 50);
                }

                let vendorPacObserver = null;
                function setupVendorPacObserver() {
                    if (vendorPacObserver) return;
                    vendorPacObserver = new MutationObserver(function() {
                        cleanVendorAutocompleteDropdown();
                    });
                    vendorPacObserver.observe(document.body, { childList: true, subtree: true });
                }

                let vendorDropdownCleanupInterval = null;
                vendorAddressInput.addEventListener('focus', function() {
                    setupVendorPacObserver();
                    cleanVendorAutocompleteDropdown();
                    if (!vendorDropdownCleanupInterval) {
                        vendorDropdownCleanupInterval = setInterval(cleanVendorAutocompleteDropdown, 100);
                    }
                });

                vendorAddressInput.addEventListener('input', function() {
                    setTimeout(cleanVendorAutocompleteDropdown, 50);
                });

                vendorAddressInput.addEventListener('blur', function() {
                    this.value = normalizeVendorAddress(this.value);

                    setTimeout(function() {
                        if (vendorDropdownCleanupInterval) {
                            clearInterval(vendorDropdownCleanupInterval);
                            vendorDropdownCleanupInterval = null;
                        }
                    }, 300);

                    setTimeout(function() {
                        if (vendorPacObserver) {
                            vendorPacObserver.disconnect();
                            vendorPacObserver = null;
                        }
                    }, 500);
                });

                vendorAddressAutocomplete.addListener('place_changed', function () {
                    const place = vendorAddressAutocomplete.getPlace();
                    if (!place || (!place.formatted_address && !place.address_components)) {
                        return;
                    }

                    vendorAddressInput.value = buildVendorAddress(place);

                    if (vendorPostcodeInput && place.address_components) {
                        const postcodeComponent = place.address_components.find(function(component) {
                            return component.types.includes('postal_code');
                        });

                        if (postcodeComponent) {
                            vendorPostcodeInput.value = postcodeComponent.long_name.toUpperCase();
                        }
                    }
                });

                return true;
            }

            if (!setupAutocomplete()) {
                let retries = 0;
                const maxRetries = 20;
                const timer = setInterval(function () {
                    retries++;
                    if (setupAutocomplete() || retries >= maxRetries) {
                        clearInterval(timer);
                    }
                }, 250);
            }
        })();
    </script>
    @endif
</body>
</html>
