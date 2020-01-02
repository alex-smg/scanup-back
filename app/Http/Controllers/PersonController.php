<?php

namespace App\Http\Controllers;

use App\Http\Resources\Person as PersonResource;
use App\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

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
     * @param Request $request
     * @return JsonResponse|\Illuminate\Support\MessageBag
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'first_name' => 'required|string|min:1|max:255',
            'last_name' => 'required|image',
            'email' => 'integer',
            'organization_id' => 'required'
        ]);

        if ($validation->fails())
            return $validation->errors();

        $person = $request->only(['name', 'description', 'status', 'parent_id']);

        return Response::json($person, 201);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse|\Illuminate\Support\MessageBag
     */
    public function update(Request $request, int $id)
    {
        $validation = Validator::make($request->all(), [
            'first_name' => 'required|string|min:1|max:255',
            'last_name' => 'required|image',
            'email' => 'integer',
            'organization_id' => 'required'
        ]);

        if ($validation->fails())
            return $validation->errors();

        $dataToInsert = $request->only(['name', 'description', 'status', 'parent_id']);

        Person::where('id', $id)->update($dataToInsert);

        return Response::json(Person::where('id', $id)->get(), 200);
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
