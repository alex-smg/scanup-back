<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Organization;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Response, DB, URL, Validator};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\Organization as OrganizationResource;
use Psy\Util\Json;

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


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Support\MessageBag
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|min:1|max:255',
            'logo' => 'image',
            'status' => ['required', Rule::in(['organization', 'company', 'brand'])]
        ]);

        if ($validation->fails())
            return $validation->errors();

        if (null !== $request->input('parent_id')) {
            $checkStatus = $this->checkParentAndStatus($request);
            if (is_array($checkStatus))
                return Response::json($checkStatus[0], key($checkStatus));
        }

        $image = $request->file('logo');
        $imageName = 'storage/' . time().'.'.$image->getClientOriginalExtension();
        $image->storeAs('public', $imageName);
        URL::asset($imageName);

        $dataToInsert = $request->only(['name', 'description', 'status', 'parent_id']);
        $dataToInsert['logo'] = $imageName;
        $organization = Organization::create($dataToInsert);

        return Response::json($organization, 201);
    }

    /**
     * @param Organization $organization
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Organization $organization): Response
    {
        $organization->delete();

        return Response::json([], 204);
    }

    /**
     * Check if the parent exist and if the child status is correct according the parent status
     */
    private function checkParentAndStatus(Request $request)
    {
        $inputStatus = $request->input('status');
        if ('organization' === $inputStatus) {
            throw new \Exception('You can\'t have an entity over an organization');
        }

        $parentOrganization = Organization::find($request->input('parent_id'));
        if (null === $parentOrganization) {
            return [404 => 'The organization parent is not found'];
        }

        $parentStatus = $parentOrganization->status;
        $checkStatus = false;
        if ('brand' === $inputStatus && 'company' === $parentStatus){
            $checkStatus = true;
        } elseif ('company' === $inputStatus && 'organization' === $parentStatus){
            $checkStatus = true;
        }

        if (!$checkStatus) {
            return [404 => 'The organization status is not compatible with the parent status'];
        }

    }
}
