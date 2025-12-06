<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['event_date' => 'date', 'event_time' => 'datetime']; // Casting waktu
    public function user() { 
        return $this->belongsTo(User::class); 
    }
}
