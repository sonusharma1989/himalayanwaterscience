@extends('shop::layouts.master')

@php
    $channel = core()->getCurrentChannel();
    $homeSEO = $channel->home_seo ? json_decode($channel->home_seo) : null;
    $categories = app('Webkul\Category\Repositories\CategoryRepository')->getVisibleCategoryTree($channel->root_category_id);
    $products = DB::table('product_flat')
        ->where('locale', app()->getLocale())
        ->where('channel', core()->getRequestedChannelCode())
        ->where('status', 1)
        ->where('visible_individually', 1)
        ->whereNotNull('url_key')
        ->orderByDesc('featured')
        ->orderByDesc('new')
        ->limit(8)
        ->get();
    $homeServices = \Hws\FieldService\Support\ServiceCatalog::all()->take(6);
@endphp

@section('page_title'){{ $homeSEO->meta_title ?? 'Himalaya N Water Science — Water Treatment Components' }}@endsection

@section('head')
    <meta name="description" content="{{ $homeSEO->meta_description ?? 'Industrial and domestic water-treatment components, transparent pricing and practical engineering support across India.' }}">
@endsection

@section('content-wrapper')
<section class="hws-hero">
    <div class="hws-container hws-hero__grid">
        <div class="hws-hero__copy">
            <span class="hws-eyebrow">Water treatment components · Pan-India</span>
            <h1>Every component.<br><em>Zero guesswork.</em></h1>
            <p>Membranes, vessels, cartridges, chemicals and instruments—factory-direct, spec-accurate, with an engineer one call away.</p>
            <div class="hws-hero__buttons"><a class="hws-btn hws-btn--primary" href="#catalog">Browse the catalog</a><a class="hws-btn hws-btn--outline" href="#" data-hws-request="engineer_callback">Talk to an engineer</a></div>
            <div class="hws-hero__trust"><span>GST invoice on every order</span><span>Transparent pricing</span><span>Free technical support</span></div>
        </div>
        <div class="hws-hero__visual" aria-label="Industrial water treatment system illustration">
            <div class="hws-water-card"><span class="hws-water-card__label">ENGINEERED WATER SYSTEMS</span><div class="hws-water-card__drop"></div><div class="hws-water-card__stats"><span><b>99.9%</b><small>contaminant control</small></span><span><b>Pan-India</b><small>project delivery</small></span></div></div>
        </div>
    </div>
</section>

<section class="hws-usp"><div class="hws-container hws-usp__grid">
    <article><b>01</b><div><h3>Transparent project pricing</h3><p>Clear component pricing and GST-ready invoices for every order.</p></div></article>
    <article><b>02</b><div><h3>Specification accuracy</h3><p>Product selection backed by practical water-treatment experience.</p></div></article>
    <article><b>03</b><div><h3>Free technical support</h3><p>Sizing, installation and commissioning guidance from our team.</p></div></article>
</div></section>

<section class="hws-home-stories" id="project-stories"><div class="hws-container">
    <div class="hws-home-stories__head">
        <div><span class="hws-eyebrow">Engineered in the field</span><h2>Systems you can see.<br>Performance you can measure.</h2></div>
        <p>Explore how we approach clean water, responsible wastewater treatment and efficient hot-water infrastructure—from design to lifecycle support.</p>
    </div>

    <div class="hws-home-stories__grid">
        <a class="hws-home-story hws-home-story--feature" href="{{ route('hws.services.show', 'water-treatment-plants') }}">
            <img src="{{ asset('images/hws-services/water-treatment-hero.webp') }}" alt="Engineer inspecting an industrial water treatment plant">
            <span class="hws-home-story__shade"></span>
            <div class="hws-home-story__copy"><small>01 · Water treatment</small><h3>Plant engineering built around your actual water report.</h3><p>WTP, RO, filtration and purification systems.</p><b>Explore solution ↗</b></div>
        </a>

        <div class="hws-home-stories__stack">
            <a class="hws-home-story" href="{{ route('hws.services.show', 'sewage-treatment-plants') }}">
                <img src="{{ asset('images/hws-services/wastewater-hero.webp') }}" alt="Engineers at a sewage and effluent treatment facility" loading="lazy">
                <span class="hws-home-story__shade"></span>
                <div class="hws-home-story__copy"><small>02 · Reuse &amp; compliance</small><h3>Wastewater treated for safer discharge and practical reuse.</h3><b>STP &amp; ETP solutions ↗</b></div>
            </a>
            <a class="hws-home-story" href="{{ route('hws.services.show', 'heat-pump-water-heating-systems') }}">
                <img src="{{ asset('images/hws-services/heating-service-hero.webp') }}" alt="Technicians commissioning a heat pump water heating system" loading="lazy">
                <span class="hws-home-story__shade"></span>
                <div class="hws-home-story__copy"><small>03 · Efficient hot water</small><h3>Lower-energy heating with dependable service support.</h3><b>Heating systems ↗</b></div>
            </a>
        </div>
    </div>

    <div class="hws-home-stories__foot"><span>Need help selecting the right process?</span><a href="#" data-hws-request="engineer_callback">Discuss your project with an engineer <b>→</b></a></div>
</div></section>

<section class="hws-home-services"><div class="hws-container">
    <div class="hws-section__head"><div><span class="hws-eyebrow">Our core services</span><h2>Engineering beyond equipment</h2></div><a href="{{ route('hws.services.index') }}">View all services →</a></div>
    <div class="hws-home-services__grid">
        @foreach ($homeServices as $service)
            <a class="hws-home-service" href="{{ route('hws.services.show', $service['slug']) }}"><small>{{ $service['group'] }}</small><div><h3>{{ $service['title'] }}</h3><p>{{ $service['summary'] }}</p></div><span>↗</span></a>
        @endforeach
    </div>
</div></section>

<section class="hws-section" id="catalog"><div class="hws-container">
    <div class="hws-section__head"><div><span class="hws-eyebrow">Browse the range</span><h2>Shop by system</h2></div><a href="{{ route('shop.search.index') }}?term=water">View all products →</a></div>
    <div class="hws-category-grid">
        @forelse ($categories->take(6) as $index => $category)
            @if ($category->slug)
                <a class="hws-category" href="{{ route('shop.productOrCategory.index', $category->slug) }}"><span class="hws-category__number">0{{ $index + 1 }}</span><div><h3>{{ $category->name }}</h3><p>Explore components</p></div><span class="hws-category__arrow">↗</span></a>
            @endif
        @empty
            <a class="hws-category" href="{{ route('shop.search.index') }}?term=membrane"><span class="hws-category__number">01</span><div><h3>RO Membranes</h3><p>Explore components</p></div><span>↗</span></a>
        @endforelse
    </div>
</div></section>

<section class="hws-section hws-section--soft"><div class="hws-container">
    <div class="hws-section__head"><div><span class="hws-eyebrow">Field-proven essentials</span><h2>Most specified</h2></div></div>
    <div class="hws-product-grid">
        @forelse ($products as $product)
            @php
                $productUrl = isset($product->url_key) && $product->url_key ? route('shop.productOrCategory.index', $product->url_key) : route('shop.search.index', ['term' => $product->name]);
                
                $imageUrl = null;
                if (isset($product->base_image) && $product->base_image) {
                    $imgs = json_decode($product->base_image, true);
                    $imageUrl = $imgs['medium_image_url'] ?? $imgs['small_image_url'] ?? null;
                }
                
                if (! $imageUrl) {
                    $imagePath = DB::table('product_images')->where('product_id', $product->product_id)->value('path');
                    if ($imagePath) {
                        $imageUrl = asset('storage/' . $imagePath);
                    }
                }
                
                if (! $imageUrl) {
                    $imageUrl = 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=800&q=80';
                }
            @endphp
            <article class="hws-product">
                <a class="hws-product__media" href="{{ $productUrl }}" style="position:relative;display:block;overflow:hidden;background:#f7f7f5;height:220px;">
                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;display:block;" />
                    <span style="position:absolute;z-index:2;top:12px;left:12px;padding:4px 8px;border-radius:6px;background:rgba(15,14,13,.65);color:#fff;font-size:9px;font-weight:700;letter-spacing:.06em;backdrop-filter:blur(4px);">HWS SELECTED</span>
                </a>
                <div class="hws-product__body"><span class="hws-product__type">Water treatment component</span><a href="{{ $productUrl }}"><h3>{{ $product->name }}</h3></a><p>Commercial grade · Technical support included</p><div class="hws-product__buy"><div><b>{{ core()->currency($product->price) }}</b><small>excl. applicable GST</small></div><a href="{{ $productUrl }}">View</a></div></div>
            </article>
        @empty
            <div class="hws-empty">Products added in Admin → Catalog will automatically appear here.</div>
        @endforelse
    </div>
</div></section>

<section class="hws-oem"><div class="hws-container hws-oem__inner"><div><span class="hws-eyebrow">For OEMs & system integrators</span><h2>Building plants at scale?</h2><p>Get project-site delivery, consolidated GST billing, datasheet packs and a dedicated engineer for sizing and commissioning.</p></div><a class="hws-btn hws-btn--primary" href="#" data-hws-request="bulk_quote">Request a bulk quote</a></div></section>
@endsection
