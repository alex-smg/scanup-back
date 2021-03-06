<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Survey as SurveyEntity;

class Survey extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
            'is_mystery_brand' => $this->is_mystery_brand,
            'status' => $this->status,
            'brand' => $this->when(
                null !== $this->brand_id,
                $this->brand
            ),
            'questions' => $this->questions,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
