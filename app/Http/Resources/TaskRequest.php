<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskRequest extends JsonResource
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
            'url' => $this->url,
            'status' => $this->status,
            'results' => TaskResult::collection($this->results),
            'constraints' => TaskConstraint::collection($this->constraints),
            'errors' => TaskError::collection($this->errors),
        ];
    }
}
