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
    $heroServiceSlides = [
        [
            'image' => 'service-water-treatment.png',
            'tag'   => 'Industrial',
            'title' => 'Industrial Water & HVAC Systems',
            'pills' => ['WTP', 'STP', 'ETP', 'RO up to 1000 m³/day', 'HVAC systems', 'Air-source heat pumps'],
            'slug'  => 'water-treatment-plants',
        ],
        [
            'image' => 'service-softener.png',
            'tag'   => 'Commercial',
            'title' => 'Commercial Water Systems',
            'pills' => ['Resin water softeners', 'RO up to 25,000 LPH', 'School RO plants'],
            'slug'  => 'reverse-osmosis-plants',
        ],
        [
            'image' => 'service-hot-water.png',
            'tag'   => 'Domestic',
            'title' => 'Domestic Water Systems',
            'pills' => ['RO plant 7–100 LPH', 'Softener up to 200L resin', 'Domestic heat pumps'],
            'slug'  => 'commercial-residential-water-filtration',
        ],
    ];
@endphp

@section('page_title'){{ $homeSEO->meta_title ?? 'Himalaya N Water Science — Water Treatment Components' }}@endsection

@section('head')
    <meta name="description" content="{{ $homeSEO->meta_description ?? 'Industrial and domestic water-treatment components, transparent pricing and practical engineering support across India.' }}">
@endsection

@section('content-wrapper')
<section class="hws-hero">
    <div class="hws-container hws-hero__grid">
        <div class="hws-hero__copy">
            <span class="hws-eyebrow">Industrial · Commercial · Domestic — Pan-India</span>
            <h1>Every scale.<br><em>One water partner.</em></h1>
            <p>From industrial WTP, STP, ETP and HVAC heat-pump systems to commercial RO plants and domestic softeners—factory-direct components with an engineer one call away.</p>
            <div class="hws-hero__buttons"><a class="hws-btn hws-btn--primary" href="#catalog">Browse the catalog</a><a class="hws-btn hws-btn--outline" href="#" data-hws-request="engineer_callback">Talk to an engineer</a></div>
            <div class="hws-hero__trust"><span>GST invoice on every order</span><span>Transparent pricing</span><span>Free technical support</span></div>
        </div>
        <div class="hws-hero__visual" aria-label="Our water treatment services">
            <div class="hws-hero-slider" id="hwsHeroSlider">
                <button type="button" class="hws-hero-slider__arrow hws-hero-slider__arrow--prev" aria-label="Previous slide" data-hws-hero-prev>←</button>
                <button type="button" class="hws-hero-slider__arrow hws-hero-slider__arrow--next" aria-label="Next slide" data-hws-hero-next>→</button>
                <div class="hws-hero-slider__viewport">
                    <div class="hws-hero-slider__track">
                        @foreach ($heroServiceSlides as $i => $slide)
                            <div class="hws-hero-slider__slide" aria-hidden="{{ $i === 0 ? 'false' : 'true' }}">
                                <img src="{{ asset('images/hws-services/' . $slide['image']) }}" alt="{{ $slide['title'] }}" loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                                <span class="hws-hero-slider__shade"></span>
                                <div class="hws-hero-slider__copy">
                                    <small>{{ $slide['tag'] }}</small>
                                    <h3>{{ $slide['title'] }}</h3>
                                    <ul class="hws-hero-slider__pills">
                                        @foreach ($slide['pills'] as $pill)<li>{{ $pill }}</li>@endforeach
                                    </ul>
                                    <a class="hws-hero-slider__cta" href="{{ route('hws.services.show', $slide['slug']) }}">Explore {{ $slide['tag'] }} solutions ↗</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="hws-hero-slider__dots" id="hwsHeroSliderDots">
                    @foreach ($heroServiceSlides as $i => $slide)
                        <button type="button" class="{{ $i === 0 ? 'is-active' : '' }}" aria-label="Show slide {{ $i + 1 }}" aria-current="{{ $i === 0 ? 'true' : 'false' }}" data-hws-hero-slide="{{ $i }}"></button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="hws-home-stories" id="project-stories"><div class="hws-container">
    <div class="hws-home-stories__head">
        <div><span class="hws-eyebrow">Engineered in the field</span><h2>Systems you can see.<br>Performance you can measure.</h2></div>
        <div class="hws-home-stories__intro">
            <p>Explore how we approach clean water, responsible wastewater treatment and efficient hot-water infrastructure—from design to lifecycle support.</p>
            <ul class="hws-home-stories__steps"><li><b>01</b>Design</li><li><b>02</b>Install</li><li><b>03</b>Support</li></ul>
        </div>
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

    <div class="hws-home-stories__foot">
        <div><small>Not sure where to start?</small><h3>Need help selecting the right process?</h3></div>
        <a class="hws-btn hws-btn--primary" href="#" data-hws-request="engineer_callback">Discuss your project with an engineer <b>→</b></a>
    </div>
</div></section>

@php
    $serviceGroupImages = [
        'Water Treatment & Purification' => 'service-water-treatment.png',
        'Wastewater Treatment' => 'service-wastewater.png',
        'Hot Water & Energy Systems' => 'service-hot-water.png',
        'Project & Lifecycle Support' => 'service-maintenance.png',
    ];
@endphp
<section class="hws-home-services"><div class="hws-container">
    <div class="hws-section__head"><div><span class="hws-eyebrow">Our core services</span><h2>Engineering beyond equipment</h2></div><a href="{{ route('hws.services.index') }}">View all services →</a></div>
    <div class="hws-home-services__grid">
        @foreach ($homeServices as $service)
            <div class="hws-home-service">
                <div class="hws-home-service__img">
                    <a class="hws-home-service__imglink" href="{{ route('hws.services.show', $service['slug']) }}">
                        <img src="{{ asset('images/hws-services/' . ($serviceGroupImages[$service['group']] ?? 'service-water-treatment.png')) }}" alt="{{ $service['title'] }}" loading="lazy" />
                    </a>
                    <a class="hws-home-service__quote" href="#" data-hws-request="engineer_callback" data-hws-product="{{ $service['title'] }}">Get quote</a>
                </div>
                <a class="hws-home-service__body" href="{{ route('hws.services.show', $service['slug']) }}">
                    <small>{{ $service['group'] }}</small>
                    <div><h3>{{ $service['title'] }}</h3><p>{{ $service['summary'] }}</p></div>
                    <span>↗</span>
                </a>
            </div>
        @endforeach
    </div>
</div></section>

@php
    $categoryImages = [
        'ro-plants' => 'service-purification.png',
        'wastewater-treatment' => 'service-wastewater.png',
        'water-atm-dispensing' => 'service-water-treatment.png',
        'water-chillers-coolers' => 'service-hot-water.png',
        'components-spare-parts' => 'service-maintenance.png',
    ];
@endphp
<section class="hws-section" id="catalog"><div class="hws-container">
    <div class="hws-section__head"><div><span class="hws-eyebrow">Browse the range</span><h2>Shop by system</h2></div><a href="{{ route('shop.search.index') }}?term=water">View all products →</a></div>
    <div class="hws-category-grid">
        @forelse ($categories->take(6) as $index => $category)
            @if ($category->slug)
                <a class="hws-category" href="{{ route('shop.productOrCategory.index', $category->slug) }}">
                    <img src="{{ asset('images/hws-services/' . ($categoryImages[$category->slug] ?? 'service-water-treatment.png')) }}" alt="{{ $category->name }}" loading="lazy">
                    <span class="hws-category__shade"></span>
                    <span class="hws-category__number">0{{ $index + 1 }}</span>
                    <div class="hws-category__copy"><h3>{{ $category->name }}</h3><p>Explore components</p></div>
                    <span class="hws-category__arrow">↗</span>
                </a>
            @endif
        @empty
            <a class="hws-category" href="{{ route('shop.search.index') }}?term=membrane">
                <img src="{{ asset('images/hws-services/service-purification.png') }}" alt="RO Membranes" loading="lazy">
                <span class="hws-category__shade"></span>
                <span class="hws-category__number">01</span>
                <div class="hws-category__copy"><h3>RO Membranes</h3><p>Explore components</p></div>
                <span class="hws-category__arrow">↗</span>
            </a>
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

<section class="hws-testimonials" id="testimonials">
    <div class="hws-container">
        <div class="hws-section__head">
            <div>
                <span class="hws-eyebrow">What our customers say</span>
                <h2>Trusted by businesses across India</h2>
            </div>
            <div class="hws-testimonials__nav">
                <button class="hws-testimonials__arrow hws-testimonials__arrow--prev" aria-label="Previous testimonial" onclick="hwsTestimonials.prev()">←</button>
                <button class="hws-testimonials__arrow hws-testimonials__arrow--next" aria-label="Next testimonial" onclick="hwsTestimonials.next()">→</button>
            </div>
        </div>

        <div class="hws-testimonials__track" id="hwsTestimonialsTrack">
            {{-- Testimonial 1 --}}
            <article class="hws-testimonial">
                <div class="hws-testimonial__stars">★★★★★</div>
                <blockquote class="hws-testimonial__quote">
                    "HWS delivered a 2000 LPH RO plant for our factory in just 18 days. The water quality has been consistent, their engineer visited twice for post-commissioning checks, and the pricing was transparent from day one. No hidden costs."
                </blockquote>
                <div class="hws-testimonial__author">
                    <img src="{{ asset('images/hws-testimonials/customer-1.png') }}" alt="Rajesh Mehta" loading="lazy" />
                    <div>
                        <strong>Rajesh Mehta</strong>
                        <span>Director, Shree Industries Pvt. Ltd.</span>
                        <small>Pune, Maharashtra</small>
                    </div>
                </div>
            </article>

            {{-- Testimonial 2 --}}
            <article class="hws-testimonial">
                <div class="hws-testimonial__stars">★★★★★</div>
                <blockquote class="hws-testimonial__quote">
                    "We needed an STP for our 120-room hotel that met CPCB norms and could handle variable occupancy. HWS designed a compact MBBR system that runs reliably, and the treated water is used in our gardens and flushing lines."
                </blockquote>
                <div class="hws-testimonial__author">
                    <img src="{{ asset('images/hws-testimonials/customer-2.png') }}" alt="Priya Sharma" loading="lazy" />
                    <div>
                        <strong>Priya Sharma</strong>
                        <span>General Manager, The Grand Residency</span>
                        <small>Nashik, Maharashtra</small>
                    </div>
                </div>
            </article>

            {{-- Testimonial 3 --}}
            <article class="hws-testimonial">
                <div class="hws-testimonial__stars">★★★★★</div>
                <blockquote class="hws-testimonial__quote">
                    "As a plant head, I was skeptical of smaller vendors. HWS proved me wrong. Their 5000 LPH industrial RO has been running for 14 months now without a single membrane replacement. Their AMC team is responsive and knowledgeable."
                </blockquote>
                <div class="hws-testimonial__author">
                    <img src="{{ asset('images/hws-testimonials/customer-3.png') }}" alt="Suresh Patil" loading="lazy" />
                    <div>
                        <strong>Suresh Patil</strong>
                        <span>Plant Head, Sahyadri Steel Works</span>
                        <small>Kolhapur, Maharashtra</small>
                    </div>
                </div>
            </article>

            {{-- Testimonial 4 --}}
            <article class="hws-testimonial">
                <div class="hws-testimonial__stars">★★★★★</div>
                <blockquote class="hws-testimonial__quote">
                    "For our pharma unit, water purity is non-negotiable. HWS installed a multi-stage purification system with real-time TDS monitoring. The documentation they provided for our FDA audit was thorough and professional."
                </blockquote>
                <div class="hws-testimonial__author">
                    <img src="{{ asset('images/hws-testimonials/customer-4.png') }}" alt="Dr. Anjali Deshmukh" loading="lazy" />
                    <div>
                        <strong>Dr. Anjali Deshmukh</strong>
                        <span>Quality Director, MedPure Pharmaceuticals</span>
                        <small>Aurangabad, Maharashtra</small>
                    </div>
                </div>
            </article>

            {{-- Testimonial 5 --}}
            <article class="hws-testimonial">
                <div class="hws-testimonial__stars">★★★★★</div>
                <blockquote class="hws-testimonial__quote">
                    "We run a 40-room resort and needed both a water softener and heat pump hot water system. HWS handled the entire project—plumbing, electrical, commissioning—everything. Guests notice the water quality difference."
                </blockquote>
                <div class="hws-testimonial__author">
                    <img src="{{ asset('images/hws-testimonials/customer-5.png') }}" alt="Vikram Joshi" loading="lazy" />
                    <div>
                        <strong>Vikram Joshi</strong>
                        <span>Owner, Sahyadri Valley Resort</span>
                        <small>Mahabaleshwar, Maharashtra</small>
                    </div>
                </div>
            </article>
        </div>

        <div class="hws-testimonials__dots" id="hwsTestimonialsDots">
            <button class="is-active" aria-label="Slide 1" onclick="hwsTestimonials.goTo(0)"></button>
            <button aria-label="Slide 2" onclick="hwsTestimonials.goTo(1)"></button>
            <button aria-label="Slide 3" onclick="hwsTestimonials.goTo(2)"></button>
            <button aria-label="Slide 4" onclick="hwsTestimonials.goTo(3)"></button>
            <button aria-label="Slide 5" onclick="hwsTestimonials.goTo(4)"></button>
        </div>
    </div>
</section>

@push('scripts')
<script>
(function(){
    const track = document.getElementById('hwsTestimonialsTrack');
    const dots  = document.querySelectorAll('#hwsTestimonialsDots button');
    if (!track || !dots.length) return;
    let current = 0, total = dots.length, timer;

    function goTo(i) {
        current = ((i % total) + total) % total;
        const card = track.children[current];
        if (card) track.scrollTo({ left: card.offsetLeft - track.offsetLeft, behavior: 'smooth' });
        dots.forEach((d, idx) => d.classList.toggle('is-active', idx === current));
        resetTimer();
    }
    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }
    function resetTimer() { clearInterval(timer); timer = setInterval(next, 6000); }

    // sync dots on manual scroll
    let scrollTimeout;
    track.addEventListener('scroll', function() {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(function() {
            const scrollLeft = track.scrollLeft;
            let closest = 0, minDist = Infinity;
            Array.from(track.children).forEach(function(card, idx) {
                const dist = Math.abs(card.offsetLeft - track.offsetLeft - scrollLeft);
                if (dist < minDist) { minDist = dist; closest = idx; }
            });
            if (closest !== current) { current = closest; dots.forEach((d, idx) => d.classList.toggle('is-active', idx === current)); }
        }, 80);
    });

    resetTimer();
    window.hwsTestimonials = { goTo, next, prev };
})();
</script>

<script src="{{ asset('hws-home-slider.js') }}?v=2" defer></script>
@endpush

@php
    // Placeholder client list — Udaipur hotel names are unconfirmed, replace once client approves.
    $trustedClients = [
        'ITC Hotels',
        'Taj Hotels (IHCL)',
        'Lake Pichola Heritage Hotel, Udaipur',
        'Udaipur Palace Residency',
        'Fateh Sagar Grand Hotel, Udaipur',
        'Aravalli Hills Resort, Udaipur',
        'City Palace View Hotel, Udaipur',
    ];
@endphp
<section class="hws-clients"><div class="hws-container">
    <div class="hws-section__head"><div><span class="hws-eyebrow">Trusted by</span><h2>Hospitality &amp; industrial brands who rely on us</h2></div></div>
    <div class="hws-clients__grid">
        @foreach ($trustedClients as $client)
            <div class="hws-clients__item">{{ $client }}</div>
        @endforeach
    </div>
</div></section>

<section class="hws-oem"><div class="hws-container hws-oem__inner"><div><span class="hws-eyebrow">For OEMs & system integrators</span><h2>Building plants at scale?</h2><p>Get project-site delivery, consolidated GST billing, datasheet packs and a dedicated engineer for sizing and commissioning.</p></div><a class="hws-btn hws-btn--primary" href="#" data-hws-request="bulk_quote">Request a bulk quote</a></div></section>
@endsection
