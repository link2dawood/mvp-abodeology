<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * The user has been registered.
     * Redirect to role-specific dashboard after registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(\Illuminate\Http\Request $request, $user)
    {
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
                // Users with both roles - default to buyer dashboard
                return route('buyer.dashboard');
            
            case 'pva':
                return route('pva.dashboard');
            
            default:
                // Fallback to home for unknown roles
                return '/home';
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:buyer,seller,both'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'role' => $data['role'],
            'password' => Hash::make($data['password']),
        ]);
        
        // Trigger Keap automation based on role
        try {
            $keapService = new \App\Services\KeapService();
            
            if (in_array($user->role, ['seller', 'both'])) {
                // New seller onboarded
                $keapService->triggerSellerOnboarded($user);
            }
            
            if (in_array($user->role, ['buyer', 'both'])) {
                // New buyer registered
                $keapService->triggerBuyerRegistered($user);
            }
        } catch (\Exception $e) {
            // Log error but don't fail registration
            \Log::error('Keap trigger error during registration: ' . $e->getMessage());
        }
        
        return $user;
    }
}
