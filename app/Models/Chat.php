<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'last_message',
        'last_sender_id',
        'unread_doctor',
        'unread_user',
        'last_timestamp'
    ];

    protected $casts = [
        'last_timestamp' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function latestMessages()
    {
        return $this->hasMany(Message::class)->latest()->limit(50);
    }
}
