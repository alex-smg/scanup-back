<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class Question extends Resource
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
            'title' => $this->title,
            'multiChoice' => $this->multi_choice,
            'survey' => $this->survey
        ];
    }
}
