<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $fillable = ['email', 'password', 'first_name', 'last_name', 'organization_id'];
}
