<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Board extends Model
{
    use HasFactory;
    #use SoftDeletes;

    protected $fillable = [
        'name', 'shortName', 'image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function grade()
    {
        return $this->hasMany(Grade::class);
    }
}
