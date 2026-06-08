<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'clinic_id',
        'pet_id',
        'complaint',
        'booking_date',
        'booking_time',
        'scheduled_at',
        'status',
        'doctor_notes'
    ];

    protected $casts = [
        'booking_date' => 'date',
        'scheduled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function clinic()
    {
        return $this->belongsTo(User::class, 'clinic_id');
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }
}
