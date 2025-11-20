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

        // In a real application, you would save the offer to database
        // Example:
        // $offer = Offer::create([
        //     'property_id' => $id,
        //     'buyer_id' => auth()->id(),
        //     'offer_amount' => $validated['offer_amount'],
        //     'funding_type' => $validated['funding_type'],
        //     'deposit_amount' => $validated['deposit_amount'] ?? null,
        //     'buying_position' => $validated['buying_position'],
        //     'conditions' => $validated['conditions'] ?? null,
        //     'solicitor_name' => $validated['solicitor_name'] ?? null,
        //     'solicitor_firm' => $validated['solicitor_firm'] ?? null,
        //     'solicitor_email' => $validated['solicitor_email'] ?? null,
        //     'solicitor_phone' => $validated['solicitor_phone'] ?? null,
        //     'status' => 'pending',
        //     'submitted_at' => now(),
        // ]);
        //
        // Handle file uploads
        // if ($request->hasFile('proof_of_funds')) {
        //     $proofOfFundsPath = $request->file('proof_of_funds')->store('offers/documents', 'public');
        //     $offer->update(['proof_of_funds_path' => $proofOfFundsPath]);
        // }
        //
        // if ($request->hasFile('agreement_in_principle')) {
        //     $aipPath = $request->file('agreement_in_principle')->store('offers/documents', 'public');
        //     $offer->update(['agreement_in_principle_path' => $aipPath]);
        // }
        //
        // Send notification to seller
        // Notification::send($property->seller, new NewOfferNotification($offer));

        return redirect()->route('buyer.make-offer', $id)->with('success', 'Your offer has been submitted successfully. The seller has been notified and will respond shortly.');
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
            'agent' => 'admin.dashboard',
            'buyer' => 'buyer.dashboard',
            'seller' => 'seller.dashboard',
            'both' => 'buyer.dashboard',
            'pva' => 'pva.dashboard',
        ];

        return $dashboards[$role] ?? 'home';
    }
}
