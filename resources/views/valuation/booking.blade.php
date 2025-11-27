<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abodeology | Book Your Valuation</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
        
        @auth
            <div style="background: #E8F4F3; padding: 15px; margin-bottom: 25px; border-radius: 6px;">
                <p style="margin: 0; font-size: 14px; color: #1E1E1E;">
                    <strong>Welcome back, {{ auth()->user()->name }}!</strong> Your information will be pre-filled below. 
                    You can update it if needed.
                </p>
            </div>
        @endauth
        
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
                   value="{{ old('name', auth()->user()->name ?? '') }}"
                   required 
                   autofocus
                   class="{{ $errors->has('name') ? 'error' : '' }}"
                   autocomplete="name"
                   {{ auth()->check() ? 'readonly' : '' }}>
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="email">Email Address *</label>
            <input type="email" 
                   id="email"
                   name="email" 
                   placeholder="your@email.com" 
                   value="{{ old('email', auth()->user()->email ?? '') }}"
                   required
                   class="{{ $errors->has('email') ? 'error' : '' }}"
                   autocomplete="email"
                   {{ auth()->check() ? 'readonly' : '' }}>
            @error('email')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="phone">Phone Number *</label>
            <input type="tel" 
                   id="phone"
                   name="phone" 
                   placeholder="01234 567890" 
                   value="{{ old('phone', auth()->user()->phone ?? '') }}"
                   required
                   class="{{ $errors->has('phone') ? 'error' : '' }}"
                   autocomplete="tel">
            @error('phone')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <div class="radio-group {{ $errors->has('role') ? 'error' : '' }}" style="{{ $errors->has('role') ? 'border: 1px solid #dc3545;' : '' }}">
                <label style="font-weight:600; margin-bottom:10px; display: block;">I am a: *</label>
                <label style="display: block; margin: 8px 0;">
                    <input type="radio" 
                           name="role" 
                           value="seller" 
                           {{ old('role', 'seller') == 'seller' ? 'checked' : '' }} 
                           required>
                    Homeowner looking to sell
                </label>
                <label style="display: block; margin: 8px 0;">
                    <input type="radio" 
                           name="role" 
                           value="both" 
                           {{ old('role') == 'both' ? 'checked' : '' }}>
                    Selling and also looking to buy
                </label>
            </div>
            @error('role')
                <div class="error-message">{{ $message }}</div>
            @enderror

            <label for="property_address">Property Address *</label>
            <input type="text" 
                   id="property_address"
                   name="property_address" 
                   placeholder="Enter full property address" 
                   value="{{ old('property_address') }}"
                   required
                   class="{{ $errors->has('property_address') ? 'error' : '' }}">
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
                   class="{{ $errors->has('postcode') ? 'error' : '' }}">
            @error('postcode')
                <div class="error-message">{{ $message }}</div>
            @enderror

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
</body>
</html>
