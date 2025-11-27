<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Valuation;
use App\Mail\ValuationLoginCredentials;
use App\Mail\ValuationRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ValuationController extends Controller
{
    /**
     * Show the valuation booking form (public access).
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showBookingForm()
    {
        return view('valuation.booking');
    }

    /**
     * Store a new valuation booking.
     * This will:
     * 1. Check if user exists, if not create account
     * 2. Generate password and send login details via email
     * 3. Create valuation record
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'role' => ['required', 'string', 'in:seller,both'],
            'property_address' => ['required', 'string', 'max:1000'],
            'postcode' => ['required', 'string', 'max:20'],
            'property_type' => ['required', 'string', 'in:detached,semi,terraced,flat,maisonette,bungalow,other'],
            'bedrooms' => ['required', 'integer', 'min:0', 'max:50'],
            'seller_notes' => ['nullable', 'string', 'max:5000'],
        ], [
            'name.required' => 'Please provide your full name.',
            'email.required' => 'Please provide your email address.',
            'email.email' => 'Please provide a valid email address.',
            'phone.required' => 'Please provide your phone number.',
            'role.required' => 'Please select whether you are selling or selling and buying.',
            'role.in' => 'Please select a valid option.',
            'property_address.required' => 'Please provide the property address.',
            'postcode.required' => 'Please provide the postcode.',
            'property_type.required' => 'Please select the property type.',
            'bedrooms.required' => 'Please provide the number of bedrooms.',
        ]);

        try {
            // Check if user exists
            $user = User::where('email', $validated['email'])->first();

            $passwordGenerated = false;
            $password = null;

            $requestedRole = $validated['role'];

            if (!$user) {
                // Generate a secure random password
                $password = Str::random(12);
                $passwordGenerated = true;

                // Create new user account with the requested role
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'password' => Hash::make($password),
                    'role' => $requestedRole, // Use the role selected by the user
                    'email_verified_at' => now(), // Auto-verify email for valuation bookings
                ]);
            } else {
                // If user exists, update their information if needed
                $user->update([
                    'name' => $validated['name'],
                    'phone' => $validated['phone'] ?? $user->phone,
                ]);

                // Update role based on requested role and existing role
                $currentRole = $user->role;
                
                if ($requestedRole === 'both') {
                    // If they request 'both', always set to 'both'
                    $user->role = 'both';
                } elseif ($requestedRole === 'seller') {
                    // If they request 'seller'
                    if ($currentRole === 'buyer') {
                        // If currently buyer, upgrade to both
                        $user->role = 'both';
                    } elseif ($currentRole !== 'both') {
                        // If not already seller or both, set to seller
                        $user->role = 'seller';
                    }
                    // If already seller or both, keep as is
                } elseif ($requestedRole === 'buyer') {
                    // If they request 'buyer'
                    if ($currentRole === 'seller') {
                        // If currently seller, upgrade to both
                        $user->role = 'both';
                    } elseif ($currentRole !== 'both') {
                        // If not already buyer or both, set to buyer
                        $user->role = 'buyer';
                    }
                    // If already buyer or both, keep as is
                }
                
                $user->save();
            }

            // Create valuation record (always start as 'pending' - admin/agent will schedule the appointment)
            $valuation = Valuation::create([
                'seller_id' => $user->id,
                'property_address' => $validated['property_address'],
                'postcode' => $validated['postcode'] ?? null,
                'property_type' => $validated['property_type'] ?? null,
                'bedrooms' => $validated['bedrooms'] ?? null,
                // valuation_date and valuation_time will be set by admin/agent when scheduling
                'valuation_date' => null,
                'valuation_time' => null,
                // Status is 'pending' until an admin/agent explicitly schedules the appointment
                'status' => 'pending',
                'seller_notes' => $validated['seller_notes'] ?? null,
            ]);

            // Send email with login credentials (only if new account was created)
            if ($passwordGenerated && $password) {
                try {
                    Mail::to($user->email)->send(
                        new ValuationLoginCredentials($user, $password, $valuation)
                    );
                } catch (\Exception $e) {
                    // Log the error but don't fail the booking
                    \Log::error('Failed to send valuation login credentials email: ' . $e->getMessage());
                }
            }

            // Notify all agents and admins about the new valuation request
            try {
                $agents = User::whereIn('role', ['admin', 'agent'])->get();
                
                foreach ($agents as $agent) {
                    try {
                        Mail::to($agent->email)->send(
                            new ValuationRequestNotification($valuation)
                        );
                    } catch (\Exception $e) {
                        // Log the error but continue with other agents
                        \Log::error('Failed to send valuation notification to agent ' . $agent->email . ': ' . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                // Log the error but don't fail the booking
                \Log::error('Failed to send valuation notifications to agents: ' . $e->getMessage());
            }

            return redirect()->route('valuation.booking.success')
                ->with('success', 'Your valuation request has been submitted successfully! ' . 
                    ($passwordGenerated ? 'An email with your login credentials has been sent to your email address.' : 
                    'Please log in to your account to view your valuation request.'));

        } catch (\Exception $e) {
            \Log::error('Valuation booking error: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'An error occurred while processing your valuation request. Please try again or contact support.');
        }
    }

    /**
     * Show the success page after booking.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function bookingSuccess()
    {
        if (!session('success')) {
            return redirect()->route('valuation.booking');
        }

        return view('valuation.success');
    }
}
