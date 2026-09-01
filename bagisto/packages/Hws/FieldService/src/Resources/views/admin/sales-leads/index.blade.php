@extends('hws::admin.layouts.menu')

@php
    $leadType = $leadType ?? null;
    $leadPageTitle = $leadPageTitle ?? 'Sales CRM & Leads';
    $leadIndexRoute = $leadIndexRoute ?? 'hws.admin.sales-leads.index';
@endphp

@section('page_title')
    {{ $leadPageTitle }}
@stop

@section('page-content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ $leadPageTitle }}</h1>
            </div>

            <div class="page-action">
                <button type="button" onclick="document.getElementById('newLeadForm').style.display='block'; this.style.display='none';" id="addLeadBtn" class="btn btn-lg btn-primary">
                    + New Lead
                </button>
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

            <!-- Create Lead Form -->
            <div id="newLeadForm" style="display: none; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="font-size: 16px; font-weight: 700; color: #334155; margin: 0;">Create Sales Lead</h3>
                    <button type="button" onclick="document.getElementById('newLeadForm').style.display='none'; document.getElementById('addLeadBtn').style.display='inline-flex';" style="background: transparent; border: 0; color: #64748b; font-size: 18px; cursor: pointer; font-weight: 700;">×</button>
                </div>

                <form method="POST" action="{{ route('hws.admin.sales-leads.store') }}">
                    @csrf
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Customer / Business Name</label>
                            <input type="text" name="customer_name" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px;"/>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Customer Phone</label>
                            <input type="text" name="customer_phone" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px;"/>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Property Type</label>
                            <select name="property_type" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                                <option value="hotel">Hotel</option>
                                <option value="hospital">Hospital</option>
                                <option value="bungalow">Bungalow</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Customer Email</label>
                            <input type="email" name="customer_email" placeholder="client@example.com" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px;"/>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Lead Type</label>
                            <select name="temperature" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                                <option value="warm">Warm</option>
                                <option value="hot">Hot</option>
                                <option value="cold">Cold</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Sales Type</label>
                            @if ($leadType)
                                <input type="hidden" name="sales_type" value="{{ $leadType }}">
                                <input type="text" value="{{ ucfirst($leadType) }}" disabled style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:8px 12px;background:#f8fafc;"/>
                            @else
                                <select name="sales_type" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                                    <option value="trading">Trading</option>
                                    <option value="projects">Projects</option>
                                    <option value="services">Services</option>
                                </select>
                            @endif
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Assign Agent</label>
                            <select name="assigned_to" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                                <option value="">Unassigned</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Lead Source</label>
                            <select name="source" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;">
                                <option value="Field Survey">Field Survey</option>
                                <option value="Website">Website</option>
                                <option value="Reference">Reference</option>
                                <option value="Cold Call">Cold Call</option>
                                <option value="Social Media">Social Media</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Next Follow-up</label>
                            <input type="datetime-local" name="next_follow_up_at" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; background: #fff;"/>
                        </div>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Address</label>
                        <textarea name="customer_address" required rows="2" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; resize: vertical;"></textarea>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-lg btn-primary">Save Lead</button>
                    </div>
                </form>
            </div>

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
    </script>
@stop
