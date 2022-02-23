<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use HasFactory;
    protected $fillable = ['subject_id'];

    public function subjects()
    {
        return  $this->belongsTo(Subject::class, 'subject_id');
    }
}
