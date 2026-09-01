<aside class="hws-service-quote" id="get-quote">
    <span class="hws-eyebrow">Project enquiry</span>
    <h2>Get a technical quote</h2>
    <p>Share the capacity and site details. The enquiry will be registered in our sales system with a tracking reference.</p>

    <form class="hws-service-quote__form" action="{{ route('hws.customer-requests.store') }}" method="POST" data-hws-inline-quote>
        @csrf
        <input type="hidden" name="request_type" value="service">
        <input type="hidden" name="product" value="{{ $service['title'] }}">

        <div class="hws-service-form-grid">
            <label><span>Name *</span><input name="customer_name" required maxlength="255" value="{{ auth('customer')->user()?->name }}"></label>
            <label><span>Phone *</span><input name="customer_phone" required maxlength="30" value="{{ auth('customer')->user()?->phone }}"></label>
            <label><span>Work email</span><input type="email" name="customer_email" maxlength="255" value="{{ auth('customer')->user()?->email }}"></label>
            <label><span>Company</span><input name="company" maxlength="255"></label>
            <label class="hws-service-form-wide"><span>City / project location</span><input name="customer_address" maxlength="1000"></label>
            <label><span>Required capacity</span><input name="quantity" maxlength="100" placeholder="e.g. 50 KLD / 2,000 LPH"></label>
            <label><span>Preferred discussion date</span><input type="date" name="preferred_date"></label>
            <label class="hws-service-form-wide"><span>Water source and requirement</span><textarea name="notes" rows="4" maxlength="2000" placeholder="Source water, application, operating hours, current issue or target quality"></textarea></label>
        </div>

        <div class="hws-service-quote__footer">
            <p data-hws-inline-status role="status"></p>
            <button class="hws-btn hws-btn--primary" type="submit">Request quotation</button>
        </div>
    </form>
</aside>

@once
    @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-hws-inline-quote]').forEach(function (form) {
                form.addEventListener('submit', async function (event) {
                    event.preventDefault();
                    const status = form.querySelector('[data-hws-inline-status]');
                    const button = form.querySelector('button[type="submit"]');
                    const original = button.textContent;
                    status.textContent = '';
                    status.className = '';
                    button.disabled = true;
                    button.textContent = 'Submitting…';

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
                        button.textContent = 'Request submitted';
                        return;
                    } catch (error) {
                        status.className = 'is-error';
                        status.textContent = error.message;
                    }

                    button.disabled = false;
                    button.textContent = original;
                });
            });
        });
        </script>
    @endpush
@endonce
