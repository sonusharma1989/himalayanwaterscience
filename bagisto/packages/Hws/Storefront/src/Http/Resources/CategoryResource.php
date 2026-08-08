<?php

namespace Hws\Storefront\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $flat = $this->flat;

        return [
            'id'          => $this->id,
            'parent_id'   => $this->parent_id,
            'name'        => $flat->name ?? null,
            'slug'        => $flat->slug ?? null,
            'description' => $this->when($request->routeIs('*.categories.show'), $flat->description ?? null),
            'position'    => $this->position,
            'status'      => (bool) $this->status,
            'children'    => CategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
