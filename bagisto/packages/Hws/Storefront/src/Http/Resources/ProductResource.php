<?php

namespace Hws\Storefront\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Reads from the product's flat data for the current channel/locale
     * (set on $this->flat by the controller before this resource runs)
     * rather than the raw EAV product row — product_flat is Bagisto's
     * own denormalized read model, built specifically to avoid the
     * attribute-value joins the raw Product model would otherwise need.
     */
    public function toArray(Request $request): array
    {
        $flat = $this->flat;

        return [
            'id'                 => $this->id,
            'sku'                => $this->sku,
            'type'               => $this->type,
            'name'               => $flat->name ?? null,
            'url_key'            => $flat->url_key ?? null,
            'short_description'  => $flat->short_description ?? null,
            'description'        => $this->when($request->routeIs('*.products.show'), $flat->description ?? null),
            'price'              => (float) ($flat->price ?? 0),
            'special_price'      => $flat->special_price ? (float) $flat->special_price : null,
            'in_stock'           => (bool) ($this->total_quantity ?? 0 > 0 || $this->type !== 'simple'),
            'new'                => (bool) ($flat->new ?? false),
            'featured'           => (bool) ($flat->featured ?? false),
            'images'             => $this->whenLoaded('images', fn () => $this->images->map(
                fn ($img) => \Illuminate\Support\Facades\Storage::url($img->path)
            )),
            'categories'         => $this->whenLoaded('categories', fn () => $this->categories->map(fn ($cat) => [
                'id'   => $cat->id,
                'name' => $cat->name,
            ])),
        ];
    }
}
