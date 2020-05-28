<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Person;
use App\Utils\Upload;
use App\Organization;
use Firebase\JWT\JWT;
use Illuminate\Validation\Rule;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Response, DB, URL, Validator};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\Organization as OrganizationResource;

class OrganizationController extends Controller
{
    /**
     * @var Upload
     */
    protected $upload;

    public function __construct(Upload $upload)
    {
        $this->upload = $upload;
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return OrganizationResource::collection(Organization::orderBy('created_at', 'desc')->paginate(5));
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function indexAll(): AnonymousResourceCollection
    {
        return OrganizationResource::collection(Organization::orderBy('name')->get());
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
     * SEARCH BY NAME OF ORGANIZATION
     * @param string $value
     * @return OrganizationResource
     */
    public function search(string $value): AnonymousResourceCollection
    {
        return OrganizationResource::collection(Organization::where('name', 'ilike', '%'.$value.'%')->paginate(5));
    }

    /**
     * @param Request $request
     * @return JsonResponse|\Illuminate\Support\MessageBag
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|min:1|max:255',
            'logo' => 'required|image',
            'parent_id' => 'integer',
            'status' => ['required', Rule::in(['organization', 'company', 'brand'])]
        ]);

        if ($validation->fails())
            return $validation->errors();

        if (null !== $request->input('parent_id')) {
            $checkStatus = $this->checkParentAndStatus($request);
            if (is_array($checkStatus))
                return Response::json(reset($checkStatus), key($checkStatus));
        }

        $imageName = $this->upload->storeAsset($request, 'logo');

        $dataToInsert = $request->only(['name', 'description', 'status', 'parent_id']);
        $dataToInsert['logo'] = $imageName;
        $organization = Organization::create($dataToInsert);

        return Response::json($organization, 201);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return |\Illuminate\Support\MessageBag
     * @throws \Exception
     */
    public function update(Request $request, int $id)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|min:1|max:255',
            'parent_id' => 'integer',
            'status' => ['required', Rule::in(['organization', 'company', 'brand'])]
        ]);

        if ($validation->fails())
            return $validation->errors();

        if (null !== $request->input('parent_id')) {
            $checkStatus = $this->checkParentAndStatus($request);
            if (is_array($checkStatus)) {
                return Response::json(reset($checkStatus), key($checkStatus));
            }
        }

        $dataToInsert = $request->only(['name', 'description', 'status', 'parent_id']);

        if ($request->file('logo')) {
            $imageName = $this->upload->storeAsset($request, 'logo');
            $dataToInsert['logo'] = $imageName;
        }

        Organization::where('id', $id)->update($dataToInsert);

        return Response::json(Organization::where('id', $id)->get(), 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        DB::table('organizations')->where('id', '=', $id)->delete();

        return Response::json([], 204);
    }

    /**
     * Check if the parent exist and if the child status is correct according the parent status
     *
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    private function checkParentAndStatus(Request $request)
    {
        $inputStatus = $request->input('status');
        /*if ('organization' === $inputStatus) {
            throw new \Exception('You can\'t have an entity over an organization');
        }*/
        $parentOrganization = Organization::find($request->input('parent_id'));
        if (null === $parentOrganization) {
            return [400 => 'The organization parent is not found'];
        }

        $parentStatus = $parentOrganization->status;
        $checkStatus = false;
        if ('brand' === $inputStatus && 'company' === $parentStatus){
            $checkStatus = true;
        } elseif ('company' === $inputStatus && 'organization' === $parentStatus){
            $checkStatus = true;
        }

        if (!$checkStatus) {
            return [400 => 'The organization status is not compatible with the parent status'];
        }

    }
}
