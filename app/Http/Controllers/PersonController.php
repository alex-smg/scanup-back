<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Person;
use Firebase\JWT\JWT;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{DB, File, Hash, Response, Validator};
use App\Http\Resources\Person as PersonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class PersonController extends Controller
{
    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $token = $request->header('token');
        $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);

        $person = Person::find($credentials->sub);

        return PersonResource::collection(Person::where('email', '!=', $person->email)->orderBy('created_at', 'desc')->paginate(5));
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        $person = Person::find($id);
        $person->roles = $person->getRoleNames();
        $person->firstName = $person->first_name;
        $person->lastName = $person->last_name;
        $person->organization = $person->organization;
        $person->password = '';

        return $person;
    }

    /**
     * SEARCH BY FIRSTNAME AND LASTNAME OF PERSON
     * @param string $value
     * @return PersonResource
     */
    public function search(string $value): AnonymousResourceCollection
    {
        return PersonResource::collection(Person::where('first_name', 'like', '%'.$value.'%')->orwhere('last_name', 'like', '%'.$value.'%')->paginate(5));
    }

    /**
     * SEARCH BY BRAND_ID
     * @param string $value
     * @return PersonResource
     */
    public function searchbyOrganisation(string $value): AnonymousResourceCollection
    {
        return PersonResource::collection(Person::where('organization_id', 'like', $value)->get());
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
            'password' => 'required|string'
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
            'password' => 'string',
            'organization_id' => 'required',
        ]);

        if ($validation->fails())
            return $validation->errors();

        $payload = $request->only(['first_name', 'last_name', 'email', 'organization_id']);


        if (null !== $request->input('password')) {
            $payload['password'] = Hash::make($request->input('password'));
        }


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
            'exp' => time() + 60*60*24
        ];

        return JWT::encode($payload, env('JWT_SECRET'));
    }
}
