<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DoctorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'clinic_id',
        'spesialis',
        'bio',
        'experience_years',
        'is_online',
        'license_number'
    ];

    protected $casts = [
        'is_online' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clinic()
    {
        return $this->belongsTo(User::class, 'clinic_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'doctor_id', 'user_id');
    }

    public function reviews()
    {
        return Review::where('target_id', $this->user_id)->where('target_type', 'doctor');
    }

    public function getAverageRatingAttribute()
    {
        return Review::where('target_id', $this->user_id)
            ->where('target_type', 'doctor')
            ->avg('rating') ?? 0;
    }
}
