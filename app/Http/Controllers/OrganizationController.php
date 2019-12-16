<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Organization;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    /**
     * @param int $id
     * @return OrganizationResource
     */
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

        if (null !== $request->input('parent_id')) {
            $this->checkParentAndStatus($request);
        }

        $organization = Organization::create($request->only(['name', 'description', 'logo', 'status', 'parent_id']));

        return Response::json($organization, 201);
    }

    /**
     * @param Organization $organization
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();

        return Response::json([], 204);
    }

    /**
     * Check if the parent exist and if the child status is correct according the parent status
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    private function checkParentAndStatus(Request $request): Response
    {
        $inputStatus = $request->input('status');
        if ('organization' === $inputStatus) {
            throw new \Exception('You can\'t have an entity over an organization');
        }

        $parentOrganization = Organization::find($request->input('parent_id'));
        if (null === $parentOrganization)
            return Response::json(['error' => 'The organization parent is not found'], 404);

        $parentStatus = $parentOrganization->status;
        $checkStatus = false;
        if ('brand' === $inputStatus && 'company' === $parentStatus){
            $checkStatus = true;
        } elseif ('company' === $inputStatus && 'organization' === $parentStatus){
            $checkStatus = true;
        }

        if (!$checkStatus) {
            return Response::json(['error' => 'The organization parent is not found'], 404);
        }
    }
}
