<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Response extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'question' => $this->when(
                $this->question_id,
                $this->question
            ),
            'linkQuestion' => $this->when(
                $this->link_question,
                $this->linkQuestion
            ),
        ];
    }
}
