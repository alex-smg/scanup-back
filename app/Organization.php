<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = ['name', 'description', 'logo', 'status', 'parent_id'];

    public function parent()
    {
        return $this->hasOne('App\Organization', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Organization', 'parent_id');
    }
}
