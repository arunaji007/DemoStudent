<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttemptSummary extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        "attempt_id",
        "mark",
        "answer",
        "answer_type",
        "answer_id",
        "question_id"
    ];
    public function attempts()
    {
        return $this->belongsTo(Attempt::class, 'attempt_id');
    }
}
