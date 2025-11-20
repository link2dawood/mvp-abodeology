<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * The user has been authenticated.
     * Redirect to role-specific dashboard after login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        return redirect()->intended($this->redirectTo($user));
    }

    /**
     * Get the redirect URL based on user role.
     *
     * @param  \App\Models\User  $user
     * @return string
     */
    protected function redirectTo($user)
    {
        switch ($user->role) {
            case 'admin':
                return route('admin.dashboard');
            
            case 'agent':
                // Agents can use admin dashboard or have their own
                return route('admin.dashboard');
            
            case 'buyer':
                return route('buyer.dashboard');
            
            case 'seller':
                return route('seller.dashboard');
            
            case 'both':
                // Users with both roles - default to buyer dashboard
                // They can navigate to seller dashboard from there
                return route('buyer.dashboard');
            
            case 'pva':
                return route('pva.dashboard');
            
            default:
                // Fallback to home for unknown roles
                return '/home';
        }
    }
}
