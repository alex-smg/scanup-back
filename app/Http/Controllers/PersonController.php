<?php

namespace App\Http\Controllers;

use App\Http\Resources\Organization as OrganizationResource;
use App\Http\Resources\Person as PersonResource;
use App\Organization;
use App\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PersonController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return PersonResource::collection(Person::paginate(10));
    }

    /**
     * @param int $id
     * @return PersonResource
     */
    public function show(int $id): PersonResource
    {
        return new PersonResource(Person::find($id));
    }
}
