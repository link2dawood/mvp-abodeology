<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BuyerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the buyer dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Role check as fallback (middleware should handle this, but extra protection)
        if (!in_array($user->role, ['buyer', 'both'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access the buyer dashboard.');
        }
        
        // Buyer name from authenticated user
        $buyerName = $user->name;
        
        // AML verification status (sample data - replace with actual logic)
        $amlStatusClass = 'pending'; // 'verified', 'pending', or 'failed'
        $amlStatusText = 'Pending';
        
        // Sample data for demonstration
        // In a real application, you would fetch this from your database
        $upcomingViewings = [];
        $offers = [];
        $notifications = [
            'Your offer on 123 Main Street has been received',
            'New property matching your criteria is available',
        ];
        
        // Solicitor details (sample data - replace with actual data)
        $solicitorName = null;
        $solicitorFirm = null;
        $solicitorEmail = null;
        $solicitorPhone = null;

        return view('buyer.dashboard', compact(
            'buyerName',
            'amlStatusClass',
            'amlStatusText',
            'upcomingViewings',
            'offers',
            'notifications',
            'solicitorName',
            'solicitorFirm',
            'solicitorEmail',
            'solicitorPhone'
        ));
    }

    /**
     * Show the buyer profile page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function profile()
    {
        $user = auth()->user();
        
        // In a real application, you would fetch buyer profile data from a buyer_profiles table
        // For now, we'll use an empty object to avoid errors
        $buyerProfile = (object) [
            'buying_position' => null,
            'requires_mortgage' => null,
            'agreement_in_principle' => null,
            'deposit_amount' => null,
            'max_budget' => null,
            'financial_notes' => null,
            'solicitor_name' => null,
            'solicitor_firm' => null,
            'solicitor_email' => null,
            'solicitor_phone' => null,
        ];

        return view('buyer.profile', compact('user', 'buyerProfile'));
    }

    /**
     * Update the buyer profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'buying_position' => ['nullable', 'string', 'in:first-time-buyer,renting,sold-sstc,cash-buyer,investor-btl'],
            'requires_mortgage' => ['nullable', 'string', 'in:yes,no'],
            'agreement_in_principle' => ['nullable', 'string', 'max:255'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'max_budget' => ['nullable', 'numeric', 'min:0'],
            'financial_notes' => ['nullable', 'string', 'max:1000'],
            'solicitor_name' => ['nullable', 'string', 'max:255'],
            'solicitor_firm' => ['nullable', 'string', 'max:255'],
            'solicitor_email' => ['nullable', 'email', 'max:255'],
            'solicitor_phone' => ['nullable', 'string', 'max:20'],
            'photo_id' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
            'proof_of_address' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
            'proof_of_funds' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
        ]);

        // Update user basic information
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        // In a real application, you would save buyer profile data to a buyer_profiles table
        // For now, we'll just handle file uploads if any
        if ($request->hasFile('photo_id')) {
            // Handle photo ID upload
            // $request->file('photo_id')->store('buyer-documents', 'public');
        }

        if ($request->hasFile('proof_of_address')) {
            // Handle proof of address upload
            // $request->file('proof_of_address')->store('buyer-documents', 'public');
        }

        if ($request->hasFile('proof_of_funds')) {
            // Handle proof of funds upload
            // $request->file('proof_of_funds')->store('buyer-documents', 'public');
        }

        return redirect()->route('buyer.profile')->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the make an offer page.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function makeOffer($id)
    {
        // In a real application, you would fetch the property from database
        // For now, we'll use sample data
        $property = (object) [
            'id' => $id,
            'address' => '123 Main Street, London, SW1A 1AA',
            'asking_price' => 500000,
        ];

        return view('buyer.make-offer', compact('property'));
    }

    /**
     * Store the offer submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeOffer(Request $request, $id)
    {
        $validated = $request->validate([
            'offer_amount' => ['required', 'numeric', 'min:0'],
            'funding_type' => ['required', 'string', 'in:cash,mortgage,combination'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'buying_position' => ['required', 'string', 'in:first-time-buyer,renting,living-with-family,sold-sstc,cash-buyer,investor-btl'],
            'conditions' => ['nullable', 'string', 'max:1000'],
            'proof_of_funds' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
            'agreement_in_principle' => ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'],
            'solicitor_name' => ['nullable', 'string', 'max:255'],
            'solicitor_firm' => ['nullable', 'string', 'max:255'],
            'solicitor_email' => ['nullable', 'email', 'max:255'],
            'solicitor_phone' => ['nullable', 'string', 'max:20'],
        ], [
            'offer_amount.required' => 'Please enter an offer amount.',
            'offer_amount.numeric' => 'Offer amount must be a valid number.',
            'offer_amount.min' => 'Offer amount must be greater than zero.',
            'funding_type.required' => 'Please select how you will fund the purchase.',
            'buying_position.required' => 'Please select your buying position.',
        ]);

        $user = auth()->user();
        $property = \App\Models\Property::where('status', 'live')->findOrFail($id);

        // Prevent buyers from making offers on their own properties
        if ($property->seller_id === $user->id) {
            return back()->with('error', 'You cannot make an offer on your own property.');
        }

        try {
            \DB::beginTransaction();

            // Map funding type
            $fundingTypeMap = [
                'cash' => 'cash',
                'mortgage' => 'mortgage',
                'combination' => 'part_mortgage',
            ];

            // Create offer
            $offer = \App\Models\Offer::create([
                'property_id' => $property->id,
                'buyer_id' => $user->id,
                'offer_amount' => $validated['offer_amount'],
                'funding_type' => $fundingTypeMap[$validated['funding_type']] ?? $validated['funding_type'],
                'deposit_amount' => $validated['deposit_amount'] ?? null,
                'chain_position' => $validated['buying_position'] ?? null,
                'conditions' => $validated['conditions'] ?? null,
                'status' => 'pending',
            ]);

            // Handle file uploads if needed (can be stored in a separate offers_documents table)
            // For now, we'll skip file handling as it's not in the Offer model fillable

            \DB::commit();

            // Send notification to seller
            try {
                \Mail::to($property->seller->email)->send(
                    new \App\Mail\NewOfferNotification($offer, $property, $property->seller)
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send new offer notification to seller: ' . $e->getMessage());
            }

            // Notify all agents/admins
            $agents = \App\Models\User::whereIn('role', ['admin', 'agent'])->get();
            foreach ($agents as $agent) {
                try {
                    \Mail::to($agent->email)->send(
                        new \App\Mail\NewOfferNotification($offer, $property, $agent)
                    );
                } catch (\Exception $e) {
                    \Log::error('Failed to send new offer notification to agent: ' . $e->getMessage());
                }
            }

            return redirect()->route('buyer.dashboard')
                ->with('success', 'Your offer has been submitted successfully! The seller and agent have been notified and will respond shortly.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Offer submission error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while submitting your offer. Please try again.');
        }
    }

    /**
     * Show viewing request form for a property.
     *
     * @param  int  $id  Property ID
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showViewingRequest($id)
    {
        $user = auth()->user();
        
        // Allow buyers, both role, or sellers viewing their own properties
        $property = \App\Models\Property::where('status', 'live');
        
        if (in_array($user->role, ['buyer', 'both'])) {
            // Buyers can view any live property
            $property = $property->findOrFail($id);
        } elseif ($user->role === 'seller') {
            // Sellers can view their own live properties
            $property = $property->where('seller_id', $user->id)->findOrFail($id);
        } else {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to access this page.');
        }

        return view('buyer.viewing-request', compact('property'));
    }

    /**
     * Store viewing request and notify viewing partner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  Property ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeViewingRequest(Request $request, $id)
    {
        $user = auth()->user();
        
        if (!in_array($user->role, ['buyer', 'both'])) {
            return redirect()->route($this->getRoleDashboard($user->role))
                ->with('error', 'You do not have permission to perform this action.');
        }

        $property = \App\Models\Property::where('status', 'live')->findOrFail($id);

        $validated = $request->validate([
            'viewing_date' => ['required', 'date', 'after:today'],
            'viewing_time' => ['required', 'date_format:H:i'],
            'preferred_contact_method' => ['nullable', 'string', 'in:phone,email'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'viewing_date.required' => 'Please select a viewing date.',
            'viewing_date.date' => 'Please provide a valid date.',
            'viewing_date.after' => 'Viewing date must be in the future.',
            'viewing_time.required' => 'Please select a viewing time.',
            'viewing_time.date_format' => 'Please provide a valid time format.',
        ]);

        try {
            \DB::beginTransaction();

            // Combine date and time
            $viewingDateTime = \Carbon\Carbon::parse($validated['viewing_date'] . ' ' . $validated['viewing_time']);

            // Create viewing request
            $viewing = \App\Models\Viewing::create([
                'property_id' => $property->id,
                'buyer_id' => $user->id,
                'viewing_date' => $viewingDateTime,
                'status' => 'scheduled',
            ]);

            // Notify all viewing partners (PVAs) about the new viewing request
            try {
                $pvas = \App\Models\User::where('role', 'pva')->get();
                
                foreach ($pvas as $pva) {
                    try {
                        \Mail::to($pva->email)->send(
                            new \App\Mail\ViewingRequestNotification($viewing, $property, $user)
                        );
                    } catch (\Exception $e) {
                        // Log the error but continue with other PVAs
                        \Log::error('Failed to send viewing request notification to PVA ' . $pva->email . ': ' . $e->getMessage());
                    }
                }
                
                \Log::info('Viewing request created and notifications sent', [
                    'viewing_id' => $viewing->id,
                    'property_id' => $property->id,
                    'buyer_id' => $user->id,
                    'viewing_date' => $viewingDateTime,
                    'pvas_notified' => $pvas->count(),
                ]);
            } catch (\Exception $e) {
                // Log the error but don't fail the viewing request
                \Log::error('Failed to send viewing request notifications: ' . $e->getMessage());
            }

            \DB::commit();

            return redirect()->route('buyer.dashboard')
                ->with('success', 'Viewing request submitted successfully! A viewing partner will contact you to confirm the appointment.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Viewing request error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'An error occurred while submitting your viewing request. Please try again.');
        }
    }

    /**
     * Get the dashboard route name based on user role.
     *
     * @param string $role
     * @return string
     */
    private function getRoleDashboard(string $role): string
    {
        $dashboards = [
            'admin' => 'admin.dashboard',
            'agent' => 'admin.agent.dashboard',
            'buyer' => 'buyer.dashboard',
            'seller' => 'seller.dashboard',
            'both' => 'buyer.dashboard',
            'pva' => 'pva.dashboard',
        ];

        return $dashboards[$role] ?? 'home';
    }
}
