<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_id',
        'target_type',
        'rating',
        'comment',
        'image_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function target()
    {
        if ($this->target_type === 'doctor') {
            return User::find($this->target_id);
        }
        return User::find($this->target_id);
    }
}
