<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ECUResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);


        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'note' => $this->note,
            'fix_type' => ModuleResource::collection($this->scripts)
        ];
    }
}