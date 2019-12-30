<?php

namespace App\Http\Controllers;

use App\Http\Resources\Organization as OrganizationResource;
use App\Http\Resources\Person as PersonResource;
use App\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

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

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        DB::table('persons')->where('id', '=', $id)->delete();

        return Response::json([], 204);
    }
}
