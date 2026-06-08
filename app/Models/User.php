<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'profile_pic',
        'google_id',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // JWT Interface
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['role' => $this->role];
    }

    // Relasi
    public function doctorProfile()
    {
        return $this->hasOne(DoctorProfile::class);
    }

    public function clinicProfile()
    {
        return $this->hasOne(ClinicProfile::class);
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function articles()
    {
        return $this->hasMany(Article::class, 'author_id');
    }

    public function chatsAsUser()
    {
        return $this->hasMany(Chat::class, 'user_id');
    }

    public function chatsAsDoctor()
    {
        return $this->hasMany(Chat::class, 'doctor_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class);
    }

    // Scopes
    public function scopeDoctors($query)
    {
        return $query->where('role', 'doctor');
    }

    public function scopeClinics($query)
    {
        return $query->where('role', 'clinic');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
