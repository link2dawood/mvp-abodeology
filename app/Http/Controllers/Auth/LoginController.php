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
     * Show the application's login form.
     * Redirect authenticated users to their dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        // If user is already authenticated, redirect to their dashboard
        if (auth()->check()) {
            $user = auth()->user();
            return redirect($this->redirectTo($user));
        }

        return view('auth.login');
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
                // Agents have their own dashboard
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
                // Fallback to root for unknown roles (will redirect appropriately)
                return '/';
        }
    }
}
