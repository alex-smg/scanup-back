<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Organization;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Response, DB, Validator};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\Organization as OrganizationResource;

class OrganizationController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return OrganizationResource::collection(DB::table('organizations')->paginate(10));
    }

    public function show(int $id): OrganizationResource
    {
        return new OrganizationResource(Organization::find($id));
    }

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|min:1|max:255',
//            'logo' => 'image'
            'status' => ['required', Rule::in(['organization', 'company', 'brand'])]
        ]);

        if ($validation->fails())
            return $validation->errors();

        $organization = Organization::create($request->only(['name', 'description', 'logo', 'status', 'parent_id']));

        return Response::json($organization, 201);
    }
}
