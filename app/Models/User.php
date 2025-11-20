<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'password',
        'google_id',
        'avatar',
        'failed_login_attempts',
        'locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'locked_until' => 'datetime',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'email' => $this->email,
        ];
    }

    /**
     * Check if the account is locked.
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Lock the account.
     *
     * @param int $minutes
     * @return void
     */
    public function lockAccount(int $minutes = 30): void
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
            'failed_login_attempts' => 0,
        ]);
    }

    /**
     * Unlock the account.
     *
     * @return void
     */
    public function unlockAccount(): void
    {
        $this->update([
            'locked_until' => null,
            'failed_login_attempts' => 0,
        ]);
    }

    /**
     * Increment failed login attempts.
     *
     * @return void
     */
    public function incrementFailedLoginAttempts(): void
    {
        $attempts = ($this->failed_login_attempts ?? 0) + 1;
        
        $this->update(['failed_login_attempts' => $attempts]);

        // Lock account after 5 failed attempts
        if ($attempts >= 5) {
            $this->lockAccount(30);
        }
    }

    /**
     * Reset failed login attempts.
     *
     * @return void
     */
    public function resetFailedLoginAttempts(): void
    {
        $this->update(['failed_login_attempts' => 0]);
    }

    /**
     * Get refresh tokens for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }

    /**
     * Get properties owned by this user (as seller).
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'seller_id');
    }

    /**
     * Get valuations booked by this user (as seller).
     */
    public function valuations()
    {
        return $this->hasMany(Valuation::class, 'seller_id');
    }

    /**
     * Get offers made by this user (as buyer).
     */
    public function offers()
    {
        return $this->hasMany(Offer::class, 'buyer_id');
    }

    /**
     * Get viewings booked by this user (as buyer).
     */
    public function viewings()
    {
        return $this->hasMany(Viewing::class, 'buyer_id');
    }

    /**
     * Get viewings assigned to this user (as PVA).
     */
    public function assignedViewings()
    {
        return $this->hasMany(Viewing::class, 'pva_id');
    }

    /**
     * Get offer decisions made by this user (as seller).
     */
    public function offerDecisions()
    {
        return $this->hasMany(OfferDecision::class, 'seller_id');
    }

    /**
     * Get AML checks for this user.
     */
    public function amlChecks()
    {
        return $this->hasMany(AmlCheck::class);
    }

    /**
     * Get notes created by this user.
     */
    public function notes()
    {
        return $this->hasMany(Note::class, 'created_by');
    }

    /**
     * Get activity logs for this user.
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=206bc4&color=fff&size=128';
    }
}
