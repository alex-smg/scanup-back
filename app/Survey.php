<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = ['title', 'status', 'started_at', 'ended_at', 'is_mystery_brand', 'description', 'brand_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo('App\Organization');
    }

    public function questions()
    {
        return $this->hasMany('App\Question');
    }
}
