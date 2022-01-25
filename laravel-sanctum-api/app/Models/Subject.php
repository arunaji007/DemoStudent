<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Subject extends Model
{

    #use SoftDeletes;
    use HasFactory;
    protected $fillable = ['name', 'grade_id'];

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }
    public function contents()
    {
        return $this->hasManyThrough(
            Content::class,
            Chapter::class,
            'subject_id',
            'chapter_id',
            'id',
            'id'
        );
    }
    public function exercises()
    {
        return $this->hasManyThrough(
            Exercise::class,
            Chapter::class,
            'subject_id',
            'chapter_id',
            'id',
            'id'
        );
    }
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
    public function reviews()
    {
        return $this->hasManyDeep(
            Review::class,
            [
                Chapter::class,
                Content::class,
            ],
            [
                'subject_id',
                'chapter_id',
                'content_id',
            ],
            [
                'id',
                'id',
                'id',
            ]
        );
    }
    public function attempts()
    {
        return $this->hasManyDeep(
            Attempt::class,
            [
                Chapter::class,
                Exercise::class,
            ],
            [
                'subject_id',
                'chapter_id',
                'exercise_id',
            ],
            [
                'id',
                'id',
                'id',
            ]
        );
    }
}
