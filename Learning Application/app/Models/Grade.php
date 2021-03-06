<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use HasFactory;
    #use SoftDeletes;
    protected $fillable = ['name', 'board_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function grade()
    {
        return $this->belongsTo(Board::class);
    }
}
