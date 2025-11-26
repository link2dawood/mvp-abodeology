<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        
        // For admin users, fetch additional statistics
        $adminStats = null;
        $agentStats = null;
        $sellerStats = null;
        $buyerStats = null;
        
        if ($user->role === 'admin') {
            // Admin sees all system-wide stats
            $adminStats = [
                'total_properties' => \App\Models\Property::count(),
                'total_users' => \App\Models\User::count(),
                'total_valuations' => \App\Models\Valuation::count(),
                'pending_aml_checks' => \App\Models\AmlCheck::where('verification_status', 'pending')->count(),
                'active_listings' => \App\Models\Property::where('status', 'live')->count(),
                'total_offers' => \App\Models\Offer::count(),
            ];
        } elseif ($user->role === 'agent') {
            // Agent sees only their assigned/worked properties
            // Properties where agent requested instruction
            $agentPropertyIds = \App\Models\PropertyInstruction::where('requested_by', $user->id)
                ->pluck('property_id')
                ->toArray();
            
            // Get properties this agent has worked on
            $agentProperties = \App\Models\Property::whereIn('id', $agentPropertyIds)->get();
            
            // Get seller IDs from agent's properties
            $agentSellerIds = $agentProperties->pluck('seller_id')->toArray();
            
            $agentStats = [
                'assigned_properties' => $agentProperties->count(),
                'active_listings' => $agentProperties->where('status', 'live')->count(),
                'completed_valuations' => \App\Models\Valuation::where('status', 'completed')->count(),
                'pending_valuations' => \App\Models\Valuation::where('status', 'pending')->count(),
                'pending_offers' => \App\Models\Offer::whereIn('property_id', $agentPropertyIds)
                    ->where('status', 'pending')
                    ->count(),
                'total_viewings' => \App\Models\Viewing::whereIn('property_id', $agentPropertyIds)->count(),
                'pending_aml_checks' => \App\Models\AmlCheck::where('verification_status', 'pending')
                    ->whereIn('user_id', $agentSellerIds)
                    ->count(),
            ];
        } elseif (in_array($user->role, ['seller', 'both'])) {
            // Seller statistics
            $sellerProperties = \App\Models\Property::where('seller_id', $user->id)->get();
            $sellerPropertyIds = $sellerProperties->pluck('id')->toArray();
            
            $sellerStats = [
                'total_properties' => $sellerProperties->count(),
                'active_listings' => $sellerProperties->where('status', 'live')->count(),
                'pending_valuations' => \App\Models\Valuation::where('seller_id', $user->id)
                    ->where('status', 'pending')
                    ->count(),
                'completed_valuations' => \App\Models\Valuation::where('seller_id', $user->id)
                    ->where('status', 'completed')
                    ->count(),
                'pending_offers' => \App\Models\Offer::whereIn('property_id', $sellerPropertyIds)
                    ->whereIn('status', ['pending', 'countered'])
                    ->count(),
                'total_viewings' => \App\Models\Viewing::whereIn('property_id', $sellerPropertyIds)->count(),
                'upcoming_viewings' => \App\Models\Viewing::whereIn('property_id', $sellerPropertyIds)
                    ->where('viewing_date', '>=', now())
                    ->where('status', '!=', 'cancelled')
                    ->count(),
                'aml_status' => \App\Models\AmlCheck::where('user_id', $user->id)->first(),
            ];
        }
        
        if (in_array($user->role, ['buyer', 'both'])) {
            // Buyer statistics
            $buyerOffers = \App\Models\Offer::where('buyer_id', $user->id)->get();
            $buyerViewings = \App\Models\Viewing::where('buyer_id', $user->id)->get();
            
            $buyerStats = [
                'total_offers' => $buyerOffers->count(),
                'pending_offers' => $buyerOffers->where('status', 'pending')->count(),
                'accepted_offers' => $buyerOffers->where('status', 'accepted')->count(),
                'total_viewings' => $buyerViewings->count(),
                'upcoming_viewings' => $buyerViewings->where('viewing_date', '>=', now())
                    ->where('status', '!=', 'cancelled')
                    ->count(),
                'aml_status' => \App\Models\AmlCheck::where('user_id', $user->id)->first(),
            ];
        }
        
        return view('profile.show', [
            'user' => $user,
            'adminStats' => $adminStats,
            'agentStats' => $agentStats,
            'sellerStats' => $sellerStats,
            'buyerStats' => $buyerStats,
        ]);
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')->with('success', 'Password updated successfully.');
    }

    /**
     * Update the user's avatar with enhanced security.
     */
    public function updateAvatar(Request $request)
    {
        try {
            // Enhanced validation
            $request->validate([
                'avatar' => [
                    'required',
                    'image',
                    'mimes:jpeg,png,jpg,gif',
                    'max:2048',
                    'dimensions:min_width=50,min_height=50,max_width=2000,max_height=2000'
                ],
            ]);

            $user = Auth::user();
            $file = $request->file('avatar');

            // Additional security checks
            if (!$this->isSecureImageFile($file)) {
                Log::warning('Insecure file upload attempt', [
                    'user_id' => $user->id,
                    'filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType()
                ]);
                
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'File failed security validation'], 422);
                }
                return back()->withErrors(['avatar' => 'File failed security validation']);
            }

            // Delete old avatar if exists
            // Determine storage disk (S3 if configured, otherwise public)
            $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
            
            if ($user->avatar && Storage::disk($disk)->exists('avatars/' . $user->avatar)) {
                Storage::disk($disk)->delete('avatars/' . $user->avatar);
            }

            // Generate secure filename
            $avatarName = $user->id . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $file->getClientOriginalExtension();
            
            // Process and store image securely
            $this->processAndStoreImage($file, $avatarName);

            // Update user record
            $user->update(['avatar' => $avatarName]);

            // Log successful upload
            Log::info('Avatar uploaded successfully', [
                'user_id' => $user->id,
                'filename' => $avatarName
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => 'Avatar updated successfully']);
            }
            
            return redirect()->route('profile.show')->with('success', 'Avatar updated successfully.');
            
        } catch (\Exception $e) {
            Log::error('Avatar upload failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Upload failed'], 500);
            }
            
            return back()->withErrors(['avatar' => 'Upload failed. Please try again.']);
        }
    }

    /**
     * Process and store image with security measures.
     */
    private function processAndStoreImage($file, $filename)
    {
        // Determine storage disk (S3 if configured, otherwise public)
        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
        
        try {
            // Process image with Intervention Image (v3 API)
            $manager = new ImageManager(new GdDriver());
            $image = $manager->read($file->getPathname());
            
            // Remove EXIF data and resize if needed
            $image->scale(width: 400, height: 400);
            
            // Save processed image
            if ($disk === 's3') {
                // For S3, save to temporary location first, then upload
                $tempPath = storage_path('app/temp/' . $filename);
                if (!file_exists(storage_path('app/temp'))) {
                    mkdir(storage_path('app/temp'), 0755, true);
                }
                $image->save($tempPath, quality: 85);
                
                // Upload to S3
                Storage::disk($disk)->put('avatars/' . $filename, file_get_contents($tempPath));
                
                // Delete temporary file
                @unlink($tempPath);
            } else {
                // For local storage
                $path = storage_path('app/public/avatars/' . $filename);
                if (!file_exists(storage_path('app/public/avatars'))) {
                    mkdir(storage_path('app/public/avatars'), 0755, true);
                }
                $image->save($path, quality: 85);
            }
            
        } catch (\Exception $e) {
            // Fallback to simple file storage if image processing fails
            \Log::warning('Image processing failed, using fallback storage', [
                'error' => $e->getMessage(),
                'filename' => $filename
            ]);
            $file->storeAs('avatars', $filename, $disk);
        }
    }

    /**
     * Enhanced security check for uploaded files.
     */
    private function isSecureImageFile($file)
    {
        // Check file extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            return false;
        }

        // Check MIME type
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return false;
        }

        // Check file signature (magic bytes)
        $handle = fopen($file->getPathname(), 'rb');
        $header = fread($handle, 8);
        fclose($handle);

        $signatures = [
            'jpeg' => ["\xFF\xD8\xFF"],
            'png' => ["\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"],
            'gif' => ["GIF87a", "GIF89a"]
        ];

        $isValid = false;
        foreach ($signatures as $type => $sigs) {
            foreach ($sigs as $sig) {
                if (substr($header, 0, strlen($sig)) === $sig) {
                    $isValid = true;
                    break 2;
                }
            }
        }

        if (!$isValid) {
            return false;
        }

        // Check for suspicious content
        $content = file_get_contents($file->getPathname());
        $suspiciousPatterns = [
            '/<\?php/i',
            '/<script/i',
            '/eval\s*\(/i',
            '/exec\s*\(/i',
            '/system\s*\(/i',
            '/shell_exec/i'
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove the user's avatar.
     */
    public function removeAvatar()
    {
        $user = Auth::user();

        // Determine storage disk (S3 if configured, otherwise public)
        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
        
        if ($user->avatar && Storage::disk($disk)->exists('avatars/' . $user->avatar)) {
            Storage::disk($disk)->delete('avatars/' . $user->avatar);
        }

        $user->update([
            'avatar' => null,
        ]);

        return redirect()->route('profile.show')->with('success', 'Avatar removed successfully.');
    }
}
