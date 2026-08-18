<?php

namespace Hws\FieldService\Http\Controllers\Storefront;

use Hws\FieldService\Support\ServiceCatalog;
use Illuminate\Routing\Controller;

class ServiceController extends Controller
{
    public function index()
    {
        return view('hws::shop.services.index', [
            'serviceGroups' => ServiceCatalog::grouped(),
            'services'      => ServiceCatalog::all(),
        ]);
    }

    public function show(string $slug)
    {
        abort_unless($service = ServiceCatalog::find($slug), 404);

        return view('hws::shop.services.show', [
            'service' => $service,
            'related' => ServiceCatalog::all()
                ->where('group', $service['group'])
                ->where('slug', '!=', $service['slug'])
                ->take(3),
        ]);
    }

    public function vision()
    {
        return view('hws::shop.vision');
    }
}
