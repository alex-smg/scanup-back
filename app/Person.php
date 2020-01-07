<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'persons';
    protected $fillable = ['first_name', 'last_name', 'email', 'password', 'organization_id'];

    public function organization()
    {
        return $this->belongsTo('App\Organization');
    }
}
