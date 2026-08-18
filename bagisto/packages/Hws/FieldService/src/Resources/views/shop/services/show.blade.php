@extends('shop::layouts.master')

@section('page_title', $service['title'] . ' | Himalaya N Water Science')

@section('head')
    <meta name="description" content="{{ $service['summary'] }} Get a site-specific technical quotation from Himalaya N Water Science.">
@endsection

@section('content-wrapper')
<nav class="hws-service-breadcrumb hws-container" aria-label="Breadcrumb">
    <a href="{{ route('shop.home.index') }}">Home</a><span>›</span><a href="{{ route('hws.services.index') }}">Services</a><span>›</span><b>{{ $service['title'] }}</b>
</nav>

<section class="hws-service-hero hws-service-hero--detail" style="--service-image:url('{{ asset('images/hws-services/' . $service['image']) }}')">
    <div class="hws-container hws-service-hero__inner">
        <span class="hws-eyebrow">{{ $service['group'] }}</span>
        <h1>{{ $service['title'] }}</h1>
        <p>{{ $service['summary'] }}</p>
        <div class="hws-service-hero__actions"><a class="hws-btn hws-btn--primary" href="#get-quote">Get technical quote</a><a class="hws-btn hws-btn--glass" href="#service-scope">View scope</a></div>
    </div>
</section>

<section class="hws-service-detail"><div class="hws-container hws-service-detail__layout">
    <div class="hws-service-detail__content">
        <div class="hws-service-lead"><span class="hws-eyebrow">Designed for your site</span><h2>Right process. Right capacity. Clear operating plan.</h2><p>{{ $service['intro'] }}</p></div>

        <section class="hws-service-block" id="service-scope">
            <div class="hws-service-block__title"><span>01</span><div><small>Applications</small><h2>Where this solution fits</h2></div></div>
            <div class="hws-service-check-grid">
                @foreach ($service['applications'] as $item)<div><i>✓</i><span>{{ $item }}</span></div>@endforeach
            </div>
        </section>

        <section class="hws-service-block hws-service-block--soft">
            <div class="hws-service-block__title"><span>02</span><div><small>Our scope</small><h2>What we can deliver</h2></div></div>
            <ol class="hws-service-scope-list">
                @foreach ($service['inclusions'] as $item)<li><b>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</b><span>{{ $item }}</span></li>@endforeach
            </ol>
        </section>

        <section class="hws-service-block">
            <div class="hws-service-block__title"><span>03</span><div><small>Expected outcomes</small><h2>Built for measurable performance</h2></div></div>
            <div class="hws-service-outcomes">
                @foreach ($service['outcomes'] as $item)<article><b>0{{ $loop->iteration }}</b><h3>{{ $item }}</h3></article>@endforeach
            </div>
        </section>

        <div class="hws-service-note"><strong>For an accurate proposal</strong><p>Keep your latest water test report, required daily capacity, operating hours and project location ready. If a report is unavailable, our team can recommend the next assessment step.</p></div>
    </div>

    @include('hws::shop.services.partials.quote-form', ['service' => $service])
</div></section>

@if ($related->isNotEmpty())
<section class="hws-related-services"><div class="hws-container">
    <div class="hws-section__head"><div><span class="hws-eyebrow">Related expertise</span><h2>Explore connected services</h2></div><a href="{{ route('hws.services.index') }}">View all services →</a></div>
    <div class="hws-related-services__grid">
        @foreach ($related as $item)<a href="{{ route('hws.services.show', $item['slug']) }}"><small>{{ $item['group'] }}</small><h3>{{ $item['title'] }}</h3><span>Explore ↗</span></a>@endforeach
    </div>
</div></section>
@endif
@endsection
