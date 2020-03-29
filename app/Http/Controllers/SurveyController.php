<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Utils\Upload;
use App\Survey;
use Illuminate\Validation\Rule;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Response, DB, URL, Validator};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\Survey as SurveyResource;

class SurveyController extends Controller
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
        return SurveyResource::collection(Survey::paginate(5));
    }

    /**
     * @param int $id
     * @return SurveyResource
     */
    public function show(int $id): SurveyResource
    {
        return new SurveyResource(Survey::find($id));
    }

    /**
     * SEARCH BY TITLE OF SURVEY
     * @param string $value
     * @return SurveyResource
     */
    public function search(string $value): AnonymousResourceCollection
    {
        return SurveyResource::collection(Survey::where('title', 'ilike', '%'.$value.'%')->paginate(5));
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
            'title' => 'required|string|min:1|max:255',
            'image' => 'required|image',
            'description' => 'required|min:1',
            'status' => ['required', Rule::in(['draft', 'disable', 'in progress'])],
            'brand_id' => 'required|integer',
        ]);

        if ($validation->fails())
            return $validation->errors();

        $dataToInsert = $request->only(['title', 'description', 'brand_id', 'status']);
        $imageName = $this->upload->storeAsset($request, 'image');

        $dataToInsert['image'] = $imageName;
        Survey::where('id', $id)->update($dataToInsert);

        return Response::json(Survey::where('id', $id)->first(), 200);
    }



    /**
     * @param Request $request
     * @return JsonResponse|\Illuminate\Support\MessageBag
     **/

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|string|min:1|max:255',
            'image' => 'required|image',
            'description' => 'required|min:1',
            'status' => ['required', Rule::in(['draft', 'disable', 'in progress'])],
            'brand_id' => 'required|integer',
        ]);

        if ($validation->fails())
            return $validation->errors();

        $imageName = $this->upload->storeAsset($request, 'image');

        $dataToInsert = $request->only(['title', 'description', 'brand_id', 'status']);
        $dataToInsert['image'] = $imageName;
        $organization = Survey::create($dataToInsert);

        return Response::json($organization, 201);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        DB::table('surveys')->where('id', '=', $id)->delete();

        return Response::json([], 204);
    }

}
