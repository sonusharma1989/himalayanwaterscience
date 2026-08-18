@extends('shop::layouts.master')

@section('page_title', 'Water Treatment, Heating & Maintenance Services | Himalaya N Water Science')

@section('head')
    <meta name="description" content="End-to-end WTP, RO, STP, ETP, filtration, water heating, installation, AMC and after-sales services across India.">
@endsection

@section('content-wrapper')
<section class="hws-service-hero hws-service-hero--index" style="--service-image:url('{{ asset('images/hws-services/water-treatment-hero.webp') }}')">
    <div class="hws-container hws-service-hero__inner">
        <span class="hws-eyebrow">Engineering · Execution · Lifecycle support</span>
        <h1>Water systems built around <em>real operating conditions.</em></h1>
        <p>From water analysis and process design to installation, commissioning and AMC—we take responsibility for the complete system.</p>
        <div class="hws-service-hero__actions">
            <a class="hws-btn hws-btn--primary" href="#core-services">Explore services</a>
            <a class="hws-btn hws-btn--glass" href="#service-enquiry" data-hws-request="bulk_quote">Discuss a project</a>
        </div>
    </div>
</section>

<section class="hws-service-proof"><div class="hws-container hws-service-proof__grid">
    <div><b>01</b><span>Site-specific engineering</span></div>
    <div><b>02</b><span>End-to-end project execution</span></div>
    <div><b>03</b><span>Pan-India technical support</span></div>
    <div><b>04</b><span>AMC and lifecycle care</span></div>
</div></section>

<section class="hws-services-catalog" id="core-services"><div class="hws-container">
    <div class="hws-services-intro">
        <div><span class="hws-eyebrow">Our core services</span><h2>One engineering partner.<br>Every stage covered.</h2></div>
        <p>Choose a solution below to review its applications, scope and outcomes. For accurate selection, send your water report and required capacity with the quote form.</p>
    </div>

    @foreach ($serviceGroups as $group => $groupServices)
        <div class="hws-service-group">
            <div class="hws-service-group__head"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $group }}</h3><i>{{ $groupServices->count() }} services</i></div>
            <div class="hws-service-grid">
                @foreach ($groupServices as $service)
                    <article class="hws-service-card">
                        <a class="hws-service-card__media" href="{{ route('hws.services.show', $service['slug']) }}">
                            <img src="{{ asset('images/hws-services/' . $service['image']) }}" alt="{{ $service['title'] }} engineering service" loading="lazy">
                            <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </a>
                        <div class="hws-service-card__body">
                            <small>{{ $service['group'] }}</small>
                            <h4><a href="{{ route('hws.services.show', $service['slug']) }}">{{ $service['title'] }}</a></h4>
                            <p>{{ $service['summary'] }}</p>
                            <a class="hws-service-card__link" href="{{ route('hws.services.show', $service['slug']) }}">View service <span>↗</span></a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endforeach
</div></section>

<section class="hws-delivery-process"><div class="hws-container">
    <div class="hws-section__head"><div><span class="hws-eyebrow">How we deliver</span><h2>From requirement to reliable operation</h2></div></div>
    <div class="hws-delivery-process__grid">
        <article><b>01</b><h3>Assess</h3><p>Water quality, capacity, site constraints and end-use targets.</p></article>
        <article><b>02</b><h3>Engineer</h3><p>Process selection, equipment sizing, layout and commercial proposal.</p></article>
        <article><b>03</b><h3>Execute</h3><p>Supply, installation, controls integration and performance trials.</p></article>
        <article><b>04</b><h3>Support</h3><p>Operator handover, consumables, service response and AMC.</p></article>
    </div>
</div></section>

<section class="hws-service-vision-cta"><div class="hws-container hws-service-vision-cta__inner">
    <div><span class="hws-eyebrow">Our vision</span><h2>Make dependable water infrastructure easier to specify, operate and maintain.</h2></div>
    <a class="hws-btn hws-btn--outline" href="{{ route('hws.vision') }}">Read our vision</a>
</div></section>
@endsection
