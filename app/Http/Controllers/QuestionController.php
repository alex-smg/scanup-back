<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Question;
use App\Http\Resources\Question as QuestionResource;
use App\Utils\Upload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
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
        return QuestionResource::collection(Question::paginate(10));
    }

    /**
     * @param int $id
     * @return QuestionResource
     */
    public function show(int $id): QuestionResource
    {
        return new QuestionResource(Question::find($id));
    }

    /**
     * SEARCH BY survey_id OF question
     * @param string $value
     * @return SurveyResourceResource
     */
    public function search(string $value): AnonymousResourceCollection
    {
        return QuestionResource::collection(Question::where('survey_id', 'like', $value)->get());
    }

    /**
     * @param Request $request
     * @return JsonResponse|\Illuminate\Support\MessageBag
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|string',
            'multi_choice' => 'boolean',
            'image' => 'image',
            'survey_id' => 'required|integer'
        ]);

        $data = $request->only(['title', 'multi_choice', 'survey_id']);

        if (null !== $request->file('image')) {
            $imageName = $this->upload->storeAsset($request, 'image');
            $data['image'] = $imageName;
        }

        if ($validation->fails())
            return $validation->errors();

        $question = Question::create($data);

        return Response::json($question, 201);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse|\Illuminate\Support\MessageBag
     */
    public function update(Request $request, int $id)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|string',
            'multi_choice' => 'boolean',
            'image' => 'image',
            'survey_id' => 'required|integer'
        ]);

        $data = $request->only(['title', 'multi_choice', 'survey_id']);

        if (null !== $request->file('image')) {
            $imageName = $this->upload->storeAsset($request, 'image');
            $data['image'] = $imageName;
        }

        if ($validation->fails())
            return $validation->errors();

        Question::where('id', $id)->update($data);

        return Response::json(Question::where('id', $id)->first(), 200);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        DB::table('questions')->where('id', '=', $id)->delete();

        return Response::json([], 204);
    }
}
