<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Score extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['student_id', 'term_id'];

    public function scoreDetails()
    {
        return $this->hasMany('App\Models\ScoreDetail', 'score_id');
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Student', 'student_id', 'id')->withTrashed();
    }

    public function term()
    {
        return $this->belongsTo('App\Models\Term', 'term_id', 'id');
    }
}
