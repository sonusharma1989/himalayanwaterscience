<?php

namespace Hws\Storefront\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Hws\Storefront\Http\Resources\CategoryResource;
use Hws\Storefront\Http\Resources\ProductResource;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductRepository;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
        protected ProductRepository $productRepository
    ) {
    }

    /**
     * GET /api/storefront/categories
     * Returns the full tree, root categories with nested children.
     */
    public function index(Request $request)
    {
        $locale = $request->query('locale', 'en');

        $categories = $this->categoryRepository
            ->with(['flat' => fn ($q) => $q->where('locale', $locale), 'children'])
            ->where('status', 1)
            ->whereNull('parent_id')
            ->get()
            ->each(fn ($cat) => $this->attachFlat($cat, $locale));

        return CategoryResource::collection($categories);
    }

    /**
     * GET /api/storefront/categories/{slug}
     * Category detail plus its products, paginated.
     */
    public function show(Request $request, string $slug)
    {
        $channel = $request->query('channel', 'default');
        $locale  = $request->query('locale', 'en');
        $perPage = min((int) $request->query('per_page', 20), 100);

        $category = $this->categoryRepository
            ->whereHas('category_flats', fn ($q) => $q->where('slug', $slug)->where('locale', $locale))
            ->where('status', 1)
            ->first();

        if (! $category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        $this->attachFlat($category, $locale);

        $products = $this->productRepository
            ->with(['flat' => fn ($q) => $q->where('channel', $channel)->where('locale', $locale)])
            ->where('status', 1)
            ->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id))
            ->paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            $product->flat = $product->product_flats->first();
            return $product;
        });

        return response()->json([
            'category' => new CategoryResource($category),
            'products' => ProductResource::collection($products),
        ]);
    }

    private function attachFlat($category, string $locale): void
    {
        $category->flat = $category->category_flats()->where('locale', $locale)->first();
        $category->children->each(fn ($child) => $this->attachFlat($child, $locale));
    }
}
