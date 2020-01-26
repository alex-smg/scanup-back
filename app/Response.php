<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    protected $fillable = ['text', 'question_id'];

    public $timestamps = false;

    public function question()
    {
        return $this->belongsTo('App\Question');
    }
}
