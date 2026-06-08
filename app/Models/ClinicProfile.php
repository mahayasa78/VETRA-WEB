<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClinicProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address',
        'phone',
        'latitude',
        'longitude',
        'is_open',
        'operational_hours'
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'operational_hours' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctors()
    {
        // Return doctors yang bekerja di klinik ini (clinic_id = user_id dari clinic)
        return $this->hasMany(DoctorProfile::class, 'clinic_id', 'user_id');
    }
}
