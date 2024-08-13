<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        "actor_id", // foreign table
        "token",
        "message",
        "type"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
