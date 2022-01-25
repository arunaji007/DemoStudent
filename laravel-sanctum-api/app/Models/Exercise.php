<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;
    protected $fillable = ['chapter_id'];

     public function attempts()
    {
        return $this->hasMany(Attempt::class);
    }
}
