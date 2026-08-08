<?php

namespace Hws\Storefront\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Hws\Storefront\Http\Resources\ProductResource;
use Webkul\Product\Repositories\ProductRepository;

class ProductController extends Controller
{
    public function __construct(protected ProductRepository $productRepository)
    {
    }

    /**
     * GET /api/storefront/products
     *
     * Query params:
     *   page, per_page   — pagination
     *   category_id      — filter to one category
     *   q                — search against the flat name column
     *   sort              — price_asc | price_desc | newest | name
     *   min_price, max_price
     *   channel, locale  — default to 'default' / 'en'; explicit rather
     *                      than resolved from a web session, since this
     *                      is a stateless API, not a browser request.
     *
     * Channel/locale scoping note: product_flat has been reported to
     * duplicate rows per channel in some Bagisto versions (bagisto/bagisto
     * issue #1212) — the explicit channel/locale filter below is there
     * specifically to guard against that, not just for correctness in
     * the common case.
     */
    public function index(Request $request)
    {
        $channel = $request->query('channel', 'default');
        $locale  = $request->query('locale', 'en');
        $perPage = min((int) $request->query('per_page', 20), 100);

        $query = $this->productRepository
            ->with(['flat' => function ($q) use ($channel, $locale) {
                $q->where('channel', $channel)->where('locale', $locale);
            }])
            ->where('status', 1)
            ->whereHas('product_flats', function ($q) use ($channel, $locale, $request) {
                $q->where('channel', $channel)->where('locale', $locale);

                if ($request->filled('q')) {
                    $q->where('name', 'like', '%'.$request->query('q').'%');
                }
                if ($request->filled('min_price')) {
                    $q->where('price', '>=', (float) $request->query('min_price'));
                }
                if ($request->filled('max_price')) {
                    $q->where('price', '<=', (float) $request->query('max_price'));
                }
            });

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->query('category_id'));
            });
        }

        match ($request->query('sort')) {
            'price_asc'  => $query->join('product_flat', 'products.id', '=', 'product_flat.product_id')
                                   ->orderBy('product_flat.price', 'asc')->select('products.*'),
            'price_desc' => $query->join('product_flat', 'products.id', '=', 'product_flat.product_id')
                                   ->orderBy('product_flat.price', 'desc')->select('products.*'),
            'name'       => $query->join('product_flat', 'products.id', '=', 'product_flat.product_id')
                                   ->orderBy('product_flat.name', 'asc')->select('products.*'),
            default      => $query->orderByDesc('products.created_at'),
        };

        $products = $query->paginate($perPage);

        // Attach the resolved flat as a plain property so ProductResource
        // can read it without needing to know about channel/locale itself
        $products->getCollection()->transform(function ($product) {
            $product->flat = $product->product_flats->first();
            return $product;
        });

        return ProductResource::collection($products);
    }

    /**
     * GET /api/storefront/products/{urlKey}
     */
    public function show(Request $request, string $urlKey)
    {
        $channel = $request->query('channel', 'default');
        $locale  = $request->query('locale', 'en');

        $product = $this->productRepository
            ->with(['images', 'categories'])
            ->whereHas('product_flats', function ($q) use ($urlKey, $channel, $locale) {
                $q->where('url_key', $urlKey)
                  ->where('channel', $channel)
                  ->where('locale', $locale);
            })
            ->where('status', 1)
            ->first();

        if (! $product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $product->flat = $product->product_flats()
            ->where('channel', $channel)->where('locale', $locale)
            ->first();

        return new ProductResource($product);
    }
}
