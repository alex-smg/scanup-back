<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Response as ResponseQuestion;
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
        return ResponseResource::collection(ResponseQuestion::paginate(10));
    }

    /**
     * @param int $id
     * @return ResponseResource
     */
    public function show(int $id): ResponseResource
    {
        return new ResponseResource(ResponseQuestion::find($id));
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
            'question_id' => 'integer',
        ]);

        if ($validation->fails())
            return $validation->errors();


        $dataToInsert = $request->only(['text', 'question_id']);
        $response = ResponseQuestion::create($dataToInsert);

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
            'question_id' => 'integer',
        ]);
        if ($validation->fails())
           return $validation->errors();

        $dataToInsert = $request->only(['text', 'question_id']);
        ResponseQuestion::where('id', $id)->update($dataToInsert);

        return Response::json(ResponseQuestion::where('id', $id)->first(), 200);
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
