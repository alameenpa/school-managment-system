<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScoreDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['subject_id', 'score_id', 'mark'];

    public function score()
    {
        return $this->belongsTo('App\Models\Score', 'score_id', 'id')->withTrashed();
    }

    public function subject()
    {
        return $this->belongsTo('App\Models\Score', 'subject_id', 'id');
    }
}
