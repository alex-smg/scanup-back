<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\Survey as SurveyResource;
use App\Response as ResponseModel;
use App\Survey;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{DB, URL, Validator, Response};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\Response as ResponseResource;
use Illuminate\Validation\Rule;

class ResponseController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return ResponseResource::collection(ResponseModel::paginate(10));
    }

    /**
     * @param int $id
     * @return ResponseResource
     */
    public function show(int $id): ResponseResource
    {
        return new ResponseResource(ResponseModel::find($id));
    }

    /**
     * SEARCH BY brand_id OF SURVEY
     * @param string $value
     * @return SurveyResourceResource
     */
    public function search(string $value): AnonymousResourceCollection
    {
        return ResponseResource::collection(Survey::where('brand_id', 'like', $value)->get());
    }

    /**
     * @param Request $request
     * @return JsonResponse|\Illuminate\Support\MessageBag
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'text' => 'required|string|min:1|max:255',
            'question_id' => 'integer|required',
            'link_question' => 'nullable|integer'
        ]);

        if ($validation->fails())
            return $validation->errors();


        $dataToInsert = $request->only(['text', 'question_id', 'link_question']);
        $response = ResponseModel::create($dataToInsert);

        return Response::json($response, 201);
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
            'text' => 'required|string|min:1|max:255',
            'question_id' => 'integer|required',
            'link_question' => 'nullable|integer'
        ]);

        if ($validation->fails())
           return $validation->errors();

        $dataToInsert = $request->only(['text', 'question_id', 'link_question']);
        ResponseModel::where('id', $id)->update($dataToInsert);

        return Response::json(ResponseModel::where('id', $id)->first(), 200);
    }


    /**
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        DB::table('responses')->where('id', '=', $id)->delete();

        return Response::json([], 204);
    }
}
