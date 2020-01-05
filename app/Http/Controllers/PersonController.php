<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Person;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Person as PersonResource;
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

    /**
     * @param Request $request
     * @return JsonResponse|\Illuminate\Support\MessageBag
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'first_name' => 'required|string|min:1|max:255',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
            'organization_id' => 'required'
        ]);

        if ($validation->fails())
            return $validation->errors();

        $person = $request->only(['first_name', 'last_name', 'email', 'organization_id']);
        $person['password'] = Hash::make($request->input('password'));
        Person::create($person);

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
            'last_name' => 'required',
            'email' => 'required|email|max:255',
            'password' => 'required|string',
            'organization_id' => 'required',
        ]);

        if ($validation->fails())
            return $validation->errors();

        $person = $request->only(['first_name', 'last_name', 'email', 'organization_id', 'password']);

        Person::where('id', $id)->update($person);

        return Response::json(Person::where('id', $id)->first(), 200);
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

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|JsonResponse|\Illuminate\Support\MessageBag|null
     */
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|string|email|min:2|max:255',
            'password' => 'required|string|min:2|max:255'
        ]);

        if ($validation->fails())
            return $validation->errors();

        if (Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            $user = $this->guard()->user();
            $username = $user->email;
            $password = $request->input('password');
            $basicAuth = base64_encode("{$username}:{$password}");
            $user->token = $basicAuth;

            return $user;
        }

        return Response::json(['error' => 'Unauthenticated user'], 401);
    }
}
