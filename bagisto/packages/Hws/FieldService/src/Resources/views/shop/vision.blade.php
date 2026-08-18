@extends('shop::layouts.master')

@section('page_title', 'Our Vision | Himalaya N Water Science')

@section('head')
    <meta name="description" content="Our vision for reliable, maintainable and responsible water-treatment and water-heating infrastructure across India.">
@endsection

@section('content-wrapper')
<section class="hws-vision-hero">
    <div class="hws-container hws-vision-hero__grid">
        <div><span class="hws-eyebrow">Our vision</span><h1>Reliable water infrastructure should never depend on <em>guesswork.</em></h1></div>
        <p>We want every customer—from a family and housing society to a hospital or industrial plant—to understand what is being specified, why it is required and how it will be supported after commissioning.</p>
    </div>
</section>

<section class="hws-vision-statement"><div class="hws-container">
    <span>01 · The future we are building</span>
    <blockquote>To make clean water, responsible wastewater treatment and efficient hot-water systems more dependable, transparent and serviceable across India.</blockquote>
</div></section>

<section class="hws-vision-values"><div class="hws-container">
    <div class="hws-services-intro"><div><span class="hws-eyebrow">How we work</span><h2>Engineering values that guide every project</h2></div><p>Good infrastructure is not only equipment. It is correct selection, clear documentation, disciplined execution and support that stays available.</p></div>
    <div class="hws-vision-values__grid">
        <article><b>01</b><h3>Clarity before complexity</h3><p>We explain the process, assumptions, operating cost and maintenance needs before asking for a decision.</p></article>
        <article><b>02</b><h3>Design for real conditions</h3><p>Source water, load variation, site access and operator capability guide the system—not a one-size-fits-all package.</p></article>
        <article><b>03</b><h3>Measure what matters</h3><p>Water quality, recovery, energy, consumables and uptime should be visible and reviewable.</p></article>
        <article><b>04</b><h3>Support the full lifecycle</h3><p>Commissioning is the beginning of reliable operation, followed by training, spares, service and AMC.</p></article>
    </div>
</div></section>

<section class="hws-vision-image" style="--vision-image:url('{{ asset('images/hws-services/wastewater-hero.webp') }}')"><div class="hws-container"><div><span class="hws-eyebrow">Our responsibility</span><h2>Conserve water. Protect equipment. Reduce avoidable waste.</h2><p>We pursue practical reuse, efficient treatment and durable systems wherever the application and water chemistry allow it.</p><a class="hws-btn hws-btn--primary" href="{{ route('hws.services.index') }}">Explore our services</a></div></div></section>
@endsection
