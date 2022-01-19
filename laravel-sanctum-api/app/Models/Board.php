<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'shortName', 'image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
