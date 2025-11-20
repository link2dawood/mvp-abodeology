<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            
            $finduser = User::where('google_id', $user->id)->first();
            
            if($finduser){
                Auth::login($finduser);
                return redirect()->intended($this->redirectTo($finduser));
            } else {
                $newUser = User::updateOrCreate(['email' => $user->email],[
                    'name' => $user->name,
                    'email' => $user->email,
                    'google_id'=> $user->id,
                    'password' => Hash::make('123456dummy'),
                    'role' => 'buyer', // Default role for new OAuth users
                ]);
                
                Auth::login($newUser);
                return redirect()->intended($this->redirectTo($newUser));
            }
            
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
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
