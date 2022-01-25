<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attempt extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['exercise_id', 'user_id', 'duration', 'score'];

    public function exercises()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    public function attempt_summaries()
    {
        return $this->hasMany(AttemptSummary::class);
    }
}
