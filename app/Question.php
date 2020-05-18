<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['title', 'multi_choice', 'image', 'survey_id'];

    public $timestamps = false;

    public function survey()
    {
        return $this->belongsTo('App\Survey');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function response()
    {
        return $this->hasMany('App\Response', 'question_id');
    }
}
