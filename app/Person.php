<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Contracts\Auth\Access\Authorizable as Authenticatable;


class Person extends Model implements Authenticatable
{
    use Authorizable;
    use HasRoles;

    protected $table = 'persons';
    protected $fillable = ['first_name', 'last_name', 'email', 'password', 'organization_id'];

    public function organization()
    {
        return $this->belongsTo('App\Organization');
    }
}
