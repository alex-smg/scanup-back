<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Person;
use Firebase\JWT\JWT;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{DB, Hash, Response, Validator};
use App\Http\Resources\Person as PersonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

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
            'role' => ['required', Rule::in(['super-admin', 'admin', 'moderator', 'viewer'])],
            'password' => 'required|string',
            'organization_id' => 'required'
        ]);

        if ($validation->fails())
            return $validation->errors();

        $person = $request->only(['first_name', 'last_name', 'email', 'organization_id']);
        $person['password'] = Hash::make($request->input('password'));
        $response = Person::create($person);

        $response->assignRole($request->input('role'));

        return Response::json(new PersonResource($response), 201);
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
            'role' => ['required', Rule::in(['super-admin', 'admin', 'moderator', 'viewer'])],
            'password' => 'required|string',
            'organization_id' => 'required',
        ]);

        if ($validation->fails())
            return $validation->errors();

        $payload= $request->only(['first_name', 'last_name', 'email', 'organization_id', 'password']);
        $payload['password'] = Hash::make($request->input('password'));

        Person::where('id', $id)->update($payload);
        $person = Person::where('id', $id)->first();
        $person->assignRole($request->input('role'));

        return Response::json(new PersonResource($person), 200);
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

        $person = Person::where('email', $request->input('email'))->first();
        if (!$person) {
            return response()->json(['error' => 'Email does not exist.'], 400);
        }

        if (Hash::check($request->input('password'), $person->password)) {
            return response()->json(['token' => $this->jwt($person)], 200);
        }

        return response()->json(['error' => 'Email or password is wrong.'], 400);

    }

    /**
     * Create a new token.
     *
     * @param  \App\Person $person
     * @return string
     */
    private function jwt(Person $person) {
        $payload = [
            'iss' => "lumen-jwt",
            'sub' => $person->id,
            'role' => $person->roles,
            'iat' => time(),
            'exp' => time() + (60*60)*3
        ];

        return JWT::encode($payload, env('JWT_SECRET'));
    }
}
