<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;
    protected $fillable = ['chapter_id'];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
