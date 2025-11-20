<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * The user's password has been reset.
     * Redirect to role-specific dashboard after password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function resetPassword($user, $password)
    {
        $this->setUserPassword($user, $password);
        $user->save();
        
        event(new \Illuminate\Auth\Events\PasswordReset($user));
        
        $this->guard()->login($user);
    }

    /**
     * Get the redirect URL based on user role after password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return '/home';
        }

        switch ($user->role) {
            case 'admin':
                return route('admin.dashboard');
            
            case 'agent':
                return route('admin.dashboard');
            
            case 'buyer':
                return route('buyer.dashboard');
            
            case 'seller':
                return route('seller.dashboard');
            
            case 'both':
                return route('buyer.dashboard');
            
            case 'pva':
                return route('pva.dashboard');
            
            default:
                return '/home';
        }
    }
}
