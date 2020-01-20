<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Question;
use App\Http\Resources\Question as QuestionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Support\MessageBag
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|string',
            'multi_choice' => 'boolean',
            'survey_id' => 'required|integer'
        ]);

        if ($validation->fails())
            return $validation->errors();

        $question = Question::create($request->only(['title', 'multi_choice', 'survey_id']));

        return Response::json($question, 201);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Support\MessageBag
     */
    public function update(Request $request, int $id)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required|string',
            'multi_choice' => 'boolean',
            'survey_id' => 'required|integer'
        ]);

        if ($validation->fails())
            return $validation->errors();

        Question::where('id', $id)->update($request->only(['title', 'multi_choice', 'survey_id']));

        return Response::json(Question::where('id', $id)->first(), 200);
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        //
    }
}
