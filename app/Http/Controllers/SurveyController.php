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
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return SurveyResource::collection(Survey::paginate(10));
    }

}
