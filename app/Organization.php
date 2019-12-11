<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = ['name', 'description', 'logo', 'status'];

    public function parent()
    {
        return $this->hasOne('App\Organization', 'parent_id');
    }
}
