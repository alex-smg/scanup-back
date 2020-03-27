<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    protected $fillable = ['text', 'question_id', 'link_question'];

    public $timestamps = false;

    public function question()
    {
        return $this->belongsTo('App\Question');
    }

    public function linkQuestion()
    {
        return $this->belongsTo('App\Question', 'link_question');
    }
}
