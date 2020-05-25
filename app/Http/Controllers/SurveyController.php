<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Person;
use App\Utils\Upload;
use App\Survey;
use Firebase\JWT\JWT;
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
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $token = $request->header('token');
        $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);

        $person = Person::find($credentials->sub);

        return SurveyResource::collection(Survey::where('brand_id', '=', $person->organization_id)->orderBy('created_at', 'desc')->paginate(5));
    }

    /**
     * @param int $id
     * @return SurveyResource
     */
    public function show(int $id): SurveyResource
    {
        $survey = new SurveyResource(Survey::find($id));
        $survey->questions->pluck('responses');

        return $survey;
    }

    /**
     * SEARCH BY TITLE OF SURVEY
     * @param string $value
     * @return SurveyResource
     */
    public function search(string $value): AnonymousResourceCollection
    {
        return SurveyResource::collection(Survey::where('title', 'like', '%'.$value.'%')->paginate(5));
    }

    /**
     * @param Request $request
     * @return JsonResponse|\Illuminate\Support\MessageBag
     **/

    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|string|min:10|max:255',
            'description' => 'required|min:10',
            'is_mystery_brand' => 'bool',
            'started_at' => 'date',
            'ended_at' => 'date',
            'status' => ['required', Rule::in(['draft', 'disable', 'in progress'])],
            'brand_id' => 'required|integer',
        ]);

        if ($validation->fails())
            return $validation->errors();

        $dataToInsert = $request->only(['title', 'description', 'brand_id', 'status', 'started_at', 'ended_at', 'is_mystery_brand']);
        $organization = Survey::create($dataToInsert);

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
            'title' => 'required|string|min:1|max:255',
            'description' => 'required|min:1',
            'is_mystery_brand' => 'bool',
            'started_at' => 'date',
            'ended_at' => 'date',
            'status' => ['required', Rule::in(['draft', 'disable', 'in progress'])],
            'brand_id' => 'required|integer',
        ]);

        if ($validation->fails())
            return $validation->errors();

        $dataToInsert = $request->only(['title', 'description', 'brand_id', 'status', 'started_at', 'ended_at', 'is_mystery_brand']);
        Survey::where('id', $id)->update($dataToInsert);

        return Response::json(Survey::where('id', $id)->first(), 200);
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
