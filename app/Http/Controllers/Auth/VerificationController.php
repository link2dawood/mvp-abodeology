<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * The user has been verified.
     * Redirect to role-specific dashboard after email verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function verified(\Illuminate\Http\Request $request)
    {
        $user = $request->user();
        return redirect($this->redirectTo($user));
    }

    /**
     * Get the redirect URL based on user role.
     *
     * @param  \App\Models\User  $user
     * @return string
     */
    protected function redirectTo($user)
    {
        if (!$user) {
            return '/home';
        }

        switch ($user->role) {
            case 'admin':
                return route('admin.dashboard');
            
            case 'agent':
                return route('admin.agent.dashboard');
            
            case 'buyer':
                return route('buyer.dashboard');
            
            case 'seller':
                return route('seller.dashboard');
            
            case 'both':
                // Users with both roles - redirect to combined dashboard
                return route('combined.dashboard');
            
            case 'pva':
                return route('pva.dashboard');
            
            default:
                return '/home';
        }
    }
}
