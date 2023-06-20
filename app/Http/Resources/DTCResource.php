<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DTCResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'brand' => $this->brand_name,
            'ecu' => $this->ecu_name,
            'files' => DTCFileResource::collection($this->files)
        ];
    }
}