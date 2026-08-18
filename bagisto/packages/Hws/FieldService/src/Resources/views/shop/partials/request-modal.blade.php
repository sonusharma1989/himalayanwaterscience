<div class="hws-request-modal" id="hwsRequestModal" aria-hidden="true">
    <div class="hws-request-modal__backdrop" data-hws-close></div>
    <div class="hws-request-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="hwsRequestTitle">
        <button type="button" class="hws-request-modal__close" data-hws-close aria-label="Close">×</button>
        <span class="hws-eyebrow">Himalaya N Water Science</span>
        <h2 id="hwsRequestTitle">Send a request</h2>
        <p class="hws-request-modal__intro">Share your requirement. You will receive a tracking reference instantly.</p>

        <form id="hwsRequestForm" action="{{ route('hws.customer-requests.store') }}" method="POST">
            @csrf
            <input type="hidden" name="request_type" value="engineer_callback">

            <div class="hws-request-grid">
                <label><span>Name *</span><input name="customer_name" required maxlength="255" value="{{ auth('customer')->user()?->name }}"></label>
                <label><span>Phone *</span><input name="customer_phone" required maxlength="30" value="{{ auth('customer')->user()?->phone }}"></label>
                <label><span>Email</span><input type="email" name="customer_email" maxlength="255" value="{{ auth('customer')->user()?->email }}"></label>
                <label><span>Company</span><input name="company" maxlength="255"></label>
                <label class="hws-request-wide"><span>Address / City</span><input name="customer_address" maxlength="1000"></label>
                <label><span>Product / System</span><input name="product" maxlength="255"></label>
                <label><span>Quantity / Capacity</span><input name="quantity" maxlength="100"></label>
                <label><span>Preferred date</span><input type="date" name="preferred_date"></label>
                <label class="hws-request-wide"><span>Requirement details</span><textarea name="notes" rows="4" maxlength="2000"></textarea></label>
            </div>

            <div class="hws-request-form__footer">
                <p id="hwsRequestStatus" role="status"></p>
                <button type="submit" class="hws-btn hws-btn--primary">Submit request</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('hwsRequestModal');
    const form = document.getElementById('hwsRequestForm');
    const title = document.getElementById('hwsRequestTitle');
    const status = document.getElementById('hwsRequestStatus');
    const titles = {
        bulk_quote: 'Request a bulk quote',
        engineer_callback: 'Talk to an engineer',
        site_survey: 'Book a site survey',
        installation: 'Request installation',
        service: 'Request product service',
        complaint: 'Report a problem',
        amc_service: 'Request AMC service'
    };

    function openModal(type, product) {
        form.reset();
        form.elements.request_type.value = type;
        if (product) form.elements.product.value = product;
        title.textContent = titles[type] || 'Send a request';
        status.textContent = '';
        status.className = '';
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('hws-modal-open');
        setTimeout(() => form.elements.customer_name.focus(), 50);
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('hws-modal-open');
    }

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-hws-request]');
        if (trigger) {
            event.preventDefault();
            openModal(trigger.dataset.hwsRequest, trigger.dataset.hwsProduct || '');
        }
        if (event.target.closest('[data-hws-close]')) closeModal();
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        const button = form.querySelector('button[type="submit"]');
        button.disabled = true;
        button.textContent = 'Submitting…';
        status.textContent = '';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
                body: new FormData(form)
            });
            const data = await response.json();
            if (! response.ok) throw new Error(data.message || Object.values(data.errors || {})[0]?.[0] || 'Please check the form.');
            status.className = 'is-success';
            status.textContent = data.message + ' Reference: ' + data.reference;
            button.textContent = 'Submitted';
            return;
        } catch (error) {
            status.className = 'is-error';
            status.textContent = error.message;
        }

        button.disabled = false;
        button.textContent = 'Submit request';
    });
});
</script>
