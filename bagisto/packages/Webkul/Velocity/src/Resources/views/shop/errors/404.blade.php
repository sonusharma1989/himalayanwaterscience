@extends('shop::layouts.master')

@section('page_title')
    Page Not Found — Himalaya N Water Science
@stop

@section('full-content-wrapper')
    <div class="hws-container" style="padding: 90px 20px; text-align: center; max-width: 800px; margin: 0 auto;">
        <div style="font-size: 82px; font-weight: 800; color: var(--hws-orange, #e27a34); line-height: 1;">404</div>
        <h1 style="font-size: 32px; font-weight: 600; color: var(--hws-ink, #1b1917); margin: 15px 0;">Page Not Found</h1>
        <p style="font-size: 15px; color: #666; margin-bottom: 30px; line-height: 1.6;">
            The category or product page you're looking for isn't available or might have moved. Please explore our product catalog or use the search bar above.
        </p>
        <div style="display: flex; justify-content: center; gap: 15px;">
            <a href="{{ route('shop.home.index') }}" class="hws-btn hws-btn--primary" style="padding: 12px 28px; background: var(--hws-orange, #e27a34); color: #fff; border-radius: 8px; font-weight: 600; text-decoration: none;">Go to Homepage</a>
        </div>
    </div>
@endsection
