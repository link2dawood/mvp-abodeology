<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Valuation | Abodeology®</title>
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
            background: var(--soft-grey);
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: var(--dark-text);
            min-height: 100vh;
        }

        /* WRAPPER */
        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 35px 20px;
            min-height: 100vh;
        }

        /* CARD */
        .booking-box {
            background: var(--white);
            width: 100%;
            max-width: 600px;
            padding: 35px;
            border-radius: 12px;
            border: 1px solid #E5E5E5;
            box-shadow: 0px 4px 20px rgba(0,0,0,0.07);
        }

        /* LOGO */
        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo img {
            width: 160px;
        }

        /* HEADINGS */
        h2 {
            margin-bottom: 10px;
            font-size: 26px;
            font-weight: 600;
            text-align: center;
        }

        .subtext {
            font-size: 15px;
            color: #555;
            margin-bottom: 25px;
            text-align: center;
        }

        /* FORM FIELDS */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--dark-text);
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="time"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #D9D9D9;
            border-radius: 6px;
            font-size: 15px;
            outline: none;
            box-sizing: border-box;
            font-family: inherit;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--abodeology-teal);
            box-shadow: 0 0 0 3px rgba(44, 184, 180, 0.1);
        }

        input.error,
        select.error,
        textarea.error {
            border-color: #dc3545;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* ERROR MESSAGES */
        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
            text-align: left;
        }

        .alert-error {
            background: #fee;
            border: 1px solid #dc3545;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 20px;
            color: #dc3545;
            font-size: 14px;
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
            text-align: center;
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

        /* FORM ROW */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* RADIO BUTTON GROUP */
        .role-box {
            background: var(--soft-grey);
            border-radius: 8px;
            padding: 12px 15px;
            margin-top: 8px;
            text-align: left;
            border: 1px solid #E5E5E5;
        }

        .role-option {
            margin-bottom: 8px;
        }

        .role-option:last-child {
            margin-bottom: 0;
        }

        .role-option label {
            margin-left: 8px;
            font-size: 14px;
            cursor: pointer;
            font-weight: normal;
        }

        .role-option input[type="radio"] {
            width: auto;
            margin: 0;
            cursor: pointer;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="booking-box">
            <div class="logo">
                <img src="{{ asset('media/abodeology-logo.png') }}" alt="Abodeology Logo" onerror="this.style.display='none'">
            </div>
            
            <h2>Book Your Property Valuation</h2>
            <p class="subtext">Fill in the form below and we'll get in touch to schedule your property valuation.</p>
            
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
                
                <div class="form-group">
                    <label for="name">Full Name <span style="color: #dc3545;">*</span></label>
                    <input type="text" 
                           id="name"
                           name="name" 
                           placeholder="Enter your full name" 
                           value="{{ old('name') }}"
                           required 
                           autofocus
                           class="{{ $errors->has('name') ? 'error' : '' }}"
                           autocomplete="name">
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address <span style="color: #dc3545;">*</span></label>
                        <input type="email" 
                               id="email"
                               name="email" 
                               placeholder="your@email.com" 
                               value="{{ old('email') }}"
                               required
                               class="{{ $errors->has('email') ? 'error' : '' }}"
                               autocomplete="email">
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" 
                               id="phone"
                               name="phone" 
                               placeholder="01234 567890" 
                               value="{{ old('phone') }}"
                               class="{{ $errors->has('phone') ? 'error' : '' }}"
                               autocomplete="tel">
                        @error('phone')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- ROLE SELECTION -->
                <div class="form-group">
                    <label>I am booking this valuation as: <span style="color: #dc3545;">*</span></label>
                    <div class="role-box {{ $errors->has('role') ? 'error' : '' }}" style="{{ $errors->has('role') ? 'border-color: #dc3545;' : '' }}">
                        <div class="role-option">
                            <input type="radio" id="role_seller" name="role" value="seller" {{ old('role', 'seller') == 'seller' ? 'checked' : '' }} required>
                            <label for="role_seller">Seller (I want to sell this property)</label>
                        </div>
                        <div class="role-option">
                            <input type="radio" id="role_buyer" name="role" value="buyer" {{ old('role') == 'buyer' ? 'checked' : '' }}>
                            <label for="role_buyer">Buyer (I'm interested in buying this property)</label>
                        </div>
                        <div class="role-option">
                            <input type="radio" id="role_both" name="role" value="both" {{ old('role') == 'both' ? 'checked' : '' }}>
                            <label for="role_both">Both (I'm both buying and selling)</label>
                        </div>
                    </div>
                    @error('role')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="property_address">Property Address <span style="color: #dc3545;">*</span></label>
                    <input type="text" 
                           id="property_address"
                           name="property_address" 
                           placeholder="Enter the full property address" 
                           value="{{ old('property_address') }}"
                           required
                           class="{{ $errors->has('property_address') ? 'error' : '' }}">
                    @error('property_address')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="postcode">Postcode</label>
                        <input type="text" 
                               id="postcode"
                               name="postcode" 
                               placeholder="SW1A 1AA" 
                               value="{{ old('postcode') }}"
                               class="{{ $errors->has('postcode') ? 'error' : '' }}">
                        @error('postcode')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="property_type">Property Type</label>
                        <select id="property_type" 
                                name="property_type" 
                                class="{{ $errors->has('property_type') ? 'error' : '' }}">
                            <option value="">Select type</option>
                            <option value="detached" {{ old('property_type') == 'detached' ? 'selected' : '' }}>Detached</option>
                            <option value="semi" {{ old('property_type') == 'semi' ? 'selected' : '' }}>Semi-Detached</option>
                            <option value="terraced" {{ old('property_type') == 'terraced' ? 'selected' : '' }}>Terraced</option>
                            <option value="flat" {{ old('property_type') == 'flat' ? 'selected' : '' }}>Flat</option>
                            <option value="maisonette" {{ old('property_type') == 'maisonette' ? 'selected' : '' }}>Maisonette</option>
                            <option value="bungalow" {{ old('property_type') == 'bungalow' ? 'selected' : '' }}>Bungalow</option>
                            <option value="other" {{ old('property_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('property_type')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="bedrooms">Number of Bedrooms</label>
                        <input type="number" 
                               id="bedrooms"
                               name="bedrooms" 
                               placeholder="3" 
                               value="{{ old('bedrooms') }}"
                               min="0"
                               max="50"
                               class="{{ $errors->has('bedrooms') ? 'error' : '' }}">
                        @error('bedrooms')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="valuation_date">Preferred Valuation Date</label>
                        <input type="date" 
                               id="valuation_date"
                               name="valuation_date" 
                               value="{{ old('valuation_date') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               class="{{ $errors->has('valuation_date') ? 'error' : '' }}">
                        @error('valuation_date')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="valuation_time">Preferred Valuation Time</label>
                    <input type="time" 
                           id="valuation_time"
                           name="valuation_time" 
                           value="{{ old('valuation_time') }}"
                           class="{{ $errors->has('valuation_time') ? 'error' : '' }}">
                    @error('valuation_time')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="seller_notes">Additional Notes (Optional)</label>
                    <textarea id="seller_notes"
                              name="seller_notes" 
                              placeholder="Any additional information about your property or requirements..." 
                              class="{{ $errors->has('seller_notes') ? 'error' : '' }}">{{ old('seller_notes') }}</textarea>
                    @error('seller_notes')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn">Submit Valuation Request</button>
            </form>

            <div class="footer-links">
                <p>Already have an account? <a href="{{ route('login') }}">Log in</a></p>
                <p>Need help? <a href="mailto:support@abodeology.com">Contact Support</a></p>
            </div>
        </div>
    </div>
</body>
</html>

