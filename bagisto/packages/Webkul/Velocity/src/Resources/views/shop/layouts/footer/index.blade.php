@php $footerCategories = app('Webkul\Category\Repositories\CategoryRepository')->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id); @endphp
<footer class="hws-footer">
    <div class="hws-container hws-footer__grid">
        <div class="hws-footer__brand"><a class="hws-brand hws-brand--footer" href="{{ route('shop.home.index') }}"><span class="hws-brand__mark">H</span><span><b>HIMALAYA N</b><small>WATER SCIENCE</small></span></a><p>Spec-accurate water-treatment components with practical engineering support, transparent pricing and delivery across India.</p></div>
        <div><h3>Shop systems</h3><ul>@foreach ($footerCategories->take(6) as $category) @if ($category->slug)<li><a href="{{ route('shop.productOrCategory.index', $category->slug) }}">{{ $category->name }}</a></li>@endif @endforeach</ul></div>
        <div><h3>Customer care</h3><ul><li><a href="{{ route('customer.session.index') }}">My account</a></li><li><a href="{{ route('shop.checkout.cart.index') }}">Cart</a></li><li><a href="mailto:info@himalayanwaterscience.com">Technical support</a></li><li><a href="mailto:info@himalayanwaterscience.com?subject=Bulk quotation request">Bulk quotation</a></li></ul></div>
        <div><h3>Stay updated</h3><p>Product launches, technical notes and project pricing.</p>@if(core()->getConfigData('customer.settings.newsletter.subscription'))<form class="hws-newsletter" action="{{ route('shop.subscribe') }}"><input type="email" name="subscriber_email" placeholder="Work email address" required><button type="submit">Subscribe</button></form>@endif</div>
    </div>
    <div class="hws-footer__bottom hws-container"><span>© {{ date('Y') }} Himalaya N Water Science</span><span>GST-ready invoicing · Secure checkout · Pan-India delivery</span></div>
</footer>
