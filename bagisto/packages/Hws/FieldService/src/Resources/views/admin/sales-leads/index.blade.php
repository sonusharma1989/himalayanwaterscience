@extends('admin::layouts.content')

@php
    $leadType = $leadType ?? null;
    $leadPageTitle = $leadPageTitle ?? 'Sales CRM & Leads';
    $leadIndexRoute = $leadIndexRoute ?? 'hws.admin.sales-leads.index';
@endphp

@section('page_title')
    {{ $leadPageTitle }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ $leadPageTitle }}</h1>
            </div>

            <div class="page-action">
                <a href="{{ route('hws.admin.sales-leads.create', ['type' => $leadType ?? 'trading']) }}" class="btn btn-lg btn-primary">
                    + New Lead
                </a>
            </div>
        </div>

        <div class="page-content">
            @if (session('success'))
                <div style="padding: 12px 20px; background: #d1fae5; color: #065f46; border-radius: 8px; font-weight: 600; margin-bottom: 20px; font-size: 14px;">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="padding: 12px 20px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-weight: 600; margin-bottom: 20px; font-size: 14px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <datagrid-plus src="{{ route($leadIndexRoute) }}"></datagrid-plus>
        </div>
    </div>

    <script>
        function hwsShowToast(message, type = 'success') {
            const toast = document.createElement('div');
            Object.assign(toast.style, {
                position: 'fixed', top: '24px', right: '24px', zIndex: '100000',
                padding: '14px 28px', borderRadius: '10px', fontWeight: '700',
                fontSize: '14px', color: '#fff', boxShadow: '0 10px 25px -5px rgba(0,0,0,0.15)',
                transition: 'all .35s cubic-bezier(.4,0,.2,1)', opacity: '0', transform: 'translateY(-20px)',
                background: type === 'success' ? '#10b981' : '#ef4444',
            });
            toast.innerText = (type === 'success' ? '✓ ' : '⚠ ') + message;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '1'; toast.style.transform = 'translateY(0)'; }, 50);
            setTimeout(() => {
                toast.style.opacity = '0'; toast.style.transform = 'translateY(-20px)';
                setTimeout(() => toast.remove(), 350);
            }, 3000);
        }

        function hwsSubmitEditLead(event, form, leadId) {
            event.preventDefault();

            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerText;

            submitBtn.disabled = true;
            submitBtn.innerText = 'Saving Changes...';

            fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;

                    if (data.success) {
                        hwsShowToast(data.message || 'Lead updated successfully!', 'success');
                        document.getElementById(`hwsEditLead${leadId}`).style.display = 'none';
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        hwsShowToast('Something went wrong. Please try again.', 'error');
                    }
                })
                .catch(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerText = originalText;
                    hwsShowToast('An error occurred while updating the lead.', 'error');
                });
        }

        function hwsQuickAssignLead(leadId, agentId, selectElem) {
            const originalBg = selectElem.style.background;
            selectElem.disabled = true;
            selectElem.style.background = '#e2e8f0';

            fetch(`{{ url(config('app.admin_url', 'admin')) }}/field-service/sales-leads/${leadId}/patch`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    field: 'assigned_to',
                    value: agentId || null
                })
            })
            .then(res => res.json())
            .then(data => {
                selectElem.disabled = false;
                selectElem.style.background = originalBg;
                if (data.success) {
                    hwsShowToast(data.message || 'Agent assigned successfully!', 'success');
                    selectElem.style.borderColor = '#10b981';
                    setTimeout(() => selectElem.style.borderColor = '#cbd5e1', 2000);
                } else {
                    hwsShowToast('Failed to assign agent.', 'error');
                }
            })
            .catch(() => {
                selectElem.disabled = false;
                selectElem.style.background = originalBg;
                hwsShowToast('Error assigning agent.', 'error');
            });
        }
    </script>
@stop
