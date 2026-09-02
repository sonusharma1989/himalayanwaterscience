@extends('hws::admin.layouts.menu')

@section('page_title', 'Lead Details')

@section('page-content')
<style>
    .hws-lead-page{padding:0 12px 32px;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#334155}.hws-head{display:flex;justify-content:space-between;gap:16px;align-items:center;margin-bottom:22px}.hws-head h1{margin:3px 0;font-size:25px;color:#0f172a}.hws-muted{color:#64748b;font-size:13px}.hws-actions{display:flex;gap:9px;flex-wrap:wrap}.hws-btn{border:0;border-radius:8px;padding:9px 14px;font-size:13px;font-weight:700;text-decoration:none;cursor:pointer;background:#3c50e0;color:#fff}.hws-btn.alt{background:#e2e8f0;color:#334155}.hws-grid{display:grid;grid-template-columns:minmax(0,2fr) minmax(290px,1fr);gap:20px}.hws-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;margin-bottom:18px;box-shadow:0 3px 10px rgba(15,23,42,.04)}.hws-card h2{font-size:15px;margin:0 0 16px;color:#0f172a}.hws-fields{display:grid;grid-template-columns:repeat(auto-fit,minmax(155px,1fr));gap:16px}.hws-field label{display:block;font-size:10px;font-weight:800;color:#94a3b8;text-transform:uppercase;margin-bottom:4px}.hws-field div{font-size:13px;font-weight:600;overflow-wrap:anywhere}.hws-wide{grid-column:1/-1}.hws-select,.hws-input{width:100%;border:1px solid #cbd5e1;border-radius:7px;padding:7px;background:#fff;color:#334155}.hws-pills{display:flex;flex-wrap:wrap;gap:7px}.hws-pill{background:#eff6ff;color:#1d4ed8;border-radius:999px;padding:5px 9px;font-size:11px;font-weight:700}.hws-photo-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px}.hws-photo-grid img{width:100%;height:110px;object-fit:cover;border-radius:9px;border:1px solid #e2e8f0}.hws-timeline{border-left:2px solid #dbeafe;margin-left:5px;padding-left:20px}.hws-event{position:relative;margin-bottom:18px}.hws-event:before{content:"";position:absolute;left:-26px;top:4px;width:10px;height:10px;border-radius:50%;background:#3c50e0}.hws-event small{color:#94a3b8;font-weight:700}.hws-event p{margin:5px 0;font-size:13px}.hws-log{display:grid;grid-template-columns:130px 1fr 190px auto;gap:9px}.hws-empty{color:#94a3b8;font-size:13px}.hws-order-items{margin:8px 0 0;padding-left:18px;font-size:13px}@media(max-width:900px){.hws-grid{grid-template-columns:1fr}.hws-head{align-items:flex-start;flex-direction:column}.hws-log{grid-template-columns:1fr}}
</style>
@php
    $reference = $lead->reference_no ?: 'SRV-'.$lead->id;
    $details = $lead->request_details ?: [];
    $format = fn($value) => filled($value) ? ucwords(str_replace('_', ' ', $value)) : '—';
@endphp
<div class="hws-lead-page">
    @if(session('success'))<div class="hws-card" style="background:#ecfdf5;color:#065f46">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="hws-card" style="background:#fef2f2;color:#991b1b">{{ $errors->first() }}</div>@endif
    <div class="hws-head">
        <div><a href="{{ $lead->sales_type === 'projects' ? route('hws.admin.leads.projects') : route('hws.admin.leads.trading') }}" class="hws-muted" style="text-decoration:none">← {{ $lead->sales_type === 'projects' ? 'Project Leads' : 'Trading Leads' }}</a><h1>{{ $reference }} · {{ $lead->customer_name }}</h1><span class="hws-muted">Created {{ optional($lead->created_at)->format('d M Y, h:i A') }}</span></div>
        <div class="hws-actions"><a class="hws-btn" href="{{ route('hws.admin.quotations.create',$lead->id) }}">Create Quotation</a>@if($quotation)<a class="hws-btn alt" href="{{ route('hws.admin.quotations.pdf',$quotation->id) }}">Download Quote</a>@if($quotation->order_id)<a class="hws-btn" href="{{ route('admin.sales.orders.view',$quotation->order_id) }}">View Order</a>@else<form method="POST" action="{{ route('hws.admin.quotations.convert-to-order',$quotation->id) }}">@csrf<button class="hws-btn" type="submit">Convert to Order</button></form>@endif @endif</div>
    </div>
    <div class="hws-grid">
        <main>
            <!-- Quotations Section (Multiple Quotations Support) -->
            <section class="hws-card" style="border-top: 4px solid #3c50e0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
                    <div>
                        <h2 style="margin: 0 0 4px; font-size: 16px; color: #0f172a;">📄 Quotations / Proposals ({{ $quotations->count() }})</h2>
                        <span class="hws-muted">All quotation revisions generated for this customer lead</span>
                    </div>
                    <a class="hws-btn" href="{{ route('hws.admin.quotations.create', $lead->id) }}" style="font-size: 12px; padding: 7px 12px;">+ New Quotation / Revision</a>
                </div>

                @if($quotations->isNotEmpty())
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 13px; text-align: left;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-weight: 700;">
                                    <th style="padding: 10px 12px;">Quote No</th>
                                    <th style="padding: 10px 12px;">Date</th>
                                    <th style="padding: 10px 12px;">Subtotal</th>
                                    <th style="padding: 10px 12px;">Discount</th>
                                    <th style="padding: 10px 12px;">Tax</th>
                                    <th style="padding: 10px 12px;">Grand Total</th>
                                    <th style="padding: 10px 12px;">Status</th>
                                    <th style="padding: 10px 12px; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotations as $q)
                                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;">
                                        <td style="padding: 10px 12px; font-weight: 700; color: #1e3a8a;">
                                            {{ $q->quote_no }}
                                        </td>
                                        <td style="padding: 10px 12px; color: #64748b;">
                                            {{ date('d M Y', strtotime($q->created_at)) }}
                                        </td>
                                        <td style="padding: 10px 12px;">
                                            ₹{{ number_format($q->subtotal, 2) }}
                                        </td>
                                        <td style="padding: 10px 12px; color: #ef4444;">
                                            {{ $q->discount > 0 ? '-₹'.number_format($q->discount, 2) : '—' }}
                                        </td>
                                        <td style="padding: 10px 12px; color: #64748b;">
                                            ₹{{ number_format($q->tax_amount, 2) }}
                                        </td>
                                        <td style="padding: 10px 12px; font-weight: 800; color: #0f172a;">
                                            ₹{{ number_format($q->grand_total, 2) }}
                                        </td>
                                        <td style="padding: 10px 12px;">
                                            @php
                                                $badgeColor = match($q->status) {
                                                    'accepted' => 'background:#d1fae5;color:#065f46;',
                                                    'sent'     => 'background:#e0e7ff;color:#3730a3;',
                                                    'rejected' => 'background:#fee2e2;color:#991b1b;',
                                                    default    => 'background:#f1f5f9;color:#475569;',
                                                };
                                            @endphp
                                            <span style="display:inline-block; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; {{ $badgeColor }}">
                                                {{ ucfirst($q->status) }}
                                            </span>
                                        </td>
                                        <td style="padding: 10px 12px; text-align: right;">
                                            <div style="display: inline-flex; gap: 6px; align-items: center;">
                                                <a href="{{ route('hws.admin.quotations.pdf', $q->id) }}" target="_blank" title="Download PDF" style="padding: 5px 9px; background: #f1f5f9; color: #334155; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">
                                                    PDF
                                                </a>
                                                @if($q->order_id)
                                                    <a href="{{ route('admin.sales.orders.view', $q->order_id) }}" style="padding: 5px 9px; background: #10b981; color: #fff; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 700;">
                                                        Order #{{ $q->order_id }}
                                                    </a>
                                                @else
                                                    <form method="POST" action="{{ route('hws.admin.quotations.convert-to-order', $q->id) }}" style="display:inline;" onsubmit="return confirm('Convert this quotation to confirmed sales order?');">
                                                        @csrf
                                                        <button type="submit" style="padding: 5px 9px; background: #3c50e0; color: #fff; border: 0; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 700;">
                                                            Convert Order
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="hws-empty" style="padding: 16px 0;">No quotation created yet. Click "+ New Quotation" to create one.</div>
                @endif
            </section>

            <section class="hws-card"><h2>Customer &amp; Lead Information</h2><div class="hws-fields">
                <div class="hws-field"><label>Name</label><div>{{ $lead->customer_name }}</div></div><div class="hws-field"><label>Phone</label><div>{{ $lead->customer_phone ?: '—' }}</div></div><div class="hws-field"><label>Email</label><div>{{ $lead->customer_email ?: '—' }}</div></div><div class="hws-field"><label>Sales Type</label><select class="hws-select" onchange="updateLeadField('{{ $lead->id }}','sales_type',this.value)">@foreach(['trading'=>'Trading','projects'=>'Projects','services'=>'Services'] as $value=>$label)<option value="{{ $value }}" {{ $lead->sales_type===$value?'selected':'' }}>{{ $label }}</option>@endforeach</select></div><div class="hws-field"><label>Source</label><div>{{ $lead->source ?: 'Field Survey' }}</div></div><div class="hws-field"><label>Request Type</label><div>{{ $format($lead->request_type) }}</div></div><div class="hws-field hws-wide"><label>Address</label><div>{{ $lead->customer_address ?: '—' }}</div></div>
            </div></section>
            <section class="hws-card"><h2>Complete Site Survey</h2><div class="hws-fields">
                <div class="hws-field"><label>Property Type</label><div>{{ $format($lead->property_type) }}</div></div><div class="hws-field"><label>Floors</label><div>{{ $lead->floors ?? '—' }}</div></div><div class="hws-field"><label>Built-up Area</label><div>{{ $lead->built_up_area_sqft ? number_format($lead->built_up_area_sqft).' sq ft' : '—' }}</div></div><div class="hws-field"><label>Rooms / Units</label><div>{{ $lead->rooms_units ?? '—' }}</div></div><div class="hws-field"><label>Water Use</label><div>{{ $lead->water_use_kld ? $lead->water_use_kld.' KLD' : '—' }}</div></div><div class="hws-field"><label>Water Source</label><div>{{ $format($lead->water_source) }}</div></div><div class="hws-field"><label>Wastewater Disposal</label><div>{{ $format($lead->wastewater_disposal) }}</div></div><div class="hws-field"><label>Space Available</label><div>{{ $format($lead->space_available) }}</div></div><div class="hws-field"><label>Survey Follow-up</label><div>{{ optional($lead->follow_up_date)->format('d M Y') ?: '—' }}</div></div><div class="hws-field"><label>Location</label><div>@if($lead->latitude && $lead->longitude)<a target="_blank" rel="noopener" href="https://maps.google.com/?q={{ $lead->latitude }},{{ $lead->longitude }}">{{ $lead->latitude }}, {{ $lead->longitude }}</a>@else — @endif</div></div><div class="hws-field hws-wide"><label>Inquiry Types</label><div class="hws-pills">@forelse($lead->inquiryTypes as $type)<span class="hws-pill">{{ $format($type->inquiry_type) }}</span>@empty<span>—</span>@endforelse</div></div><div class="hws-field hws-wide"><label>Survey Notes</label><div style="white-space:pre-wrap">{{ $lead->notes ?: '—' }}</div></div>
            </div>@if(!empty($lead->photos))<h2 style="margin-top:20px">Survey Photos</h2><div class="hws-photo-grid">@foreach($lead->photos as $photo)<a href="{{ $photo }}" target="_blank" rel="noopener"><img src="{{ $photo }}" alt="Survey photo"></a>@endforeach</div>@endif</section>
            @if(!empty($details))<section class="hws-card"><h2>Order / Request Details</h2><div class="hws-fields">@foreach($details as $key=>$value)@if($key !== 'items' && !is_array($value))<div class="hws-field"><label>{{ $format($key) }}</label><div>{{ $value }}</div></div>@endif @endforeach</div>@if(!empty($details['items']))<ul class="hws-order-items">@foreach($details['items'] as $item)<li>{{ $item['name'] ?? 'Item' }} · Qty {{ $item['quantity'] ?? 1 }} · {{ $item['price'] ?? '' }}</li>@endforeach</ul>@endif</section>@endif
            <section class="hws-card"><h2>Activity Log</h2><div id="leadTimeline" class="hws-timeline">@forelse($activities as $act)<div class="hws-event"><small>{{ date('d M Y, h:i A',strtotime($act->created_at)) }} · {{ $act->admin_name ?? 'System' }} · {{ strtoupper($act->activity_type) }}</small><p>{{ $act->notes }}</p></div>@empty<div class="hws-empty">No activities logged yet.</div>@endforelse</div></section>
        </main>
        <aside>
            <section class="hws-card"><h2>CRM Controls</h2><div class="hws-fields" id="crmControls"><div class="hws-field"><label>Pipeline Stage</label><select class="hws-select" data-field="status" {{ $lead->status==='won'?'disabled':'' }}>@foreach(['new','contacted','proposal_sent','negotiation','won','lost'] as $status)<option value="{{ $status }}" {{ $lead->status===$status?'selected':'' }}>{{ $format($status) }}</option>@endforeach</select></div><div class="hws-field"><label>Lead Type</label><select class="hws-select" data-field="temperature">@foreach(['hot','warm','cold'] as $temperature)<option value="{{ $temperature }}" {{ $lead->temperature===$temperature?'selected':'' }}>{{ ucfirst($temperature) }}</option>@endforeach</select></div><div class="hws-field hws-wide"><label>Sales Type</label><select class="hws-select" data-field="sales_type">@foreach(['trading','projects','services'] as $salesType)<option value="{{ $salesType }}" {{ ($lead->sales_type ?: 'trading')===$salesType?'selected':'' }}>{{ ucfirst($salesType) }}</option>@endforeach</select></div><div class="hws-field hws-wide"><label>Assigned Agent</label><select class="hws-select" data-field="assigned_to"><option value="">Unassigned</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" {{ $lead->assigned_to==$employee->id?'selected':'' }}>{{ $employee->name }}</option>@endforeach</select></div><div class="hws-field hws-wide"><label>Next Follow-up</label><input class="hws-input" data-field="next_follow_up_at" type="datetime-local" value="{{ optional($lead->next_follow_up_at)->format('Y-m-d\TH:i') }}"></div><div class="hws-wide"><button id="saveCrmButton" class="hws-btn" type="button" onclick="saveCrmControls('{{ $lead->id }}')" style="width:100%;justify-content:center;">Save Changes</button><div id="crmSaveStatus" style="display:none;margin-top:9px;padding:9px;border-radius:7px;font-size:12px;font-weight:700;text-align:center;"></div></div></div></section>
            @if($lead->status !== 'won')<section class="hws-card"><h2>Log Interaction</h2><form id="activityForm" method="POST" action="{{ route('hws.admin.sales-leads.activity',$lead->id) }}" onsubmit="saveActivity(event,this)">@csrf<div style="display:grid;gap:10px"><select class="hws-select" name="activity_type"><option value="call">Phone Call</option><option value="whatsapp">WhatsApp</option><option value="email">Email</option><option value="meeting">Meeting</option><option value="note">Note</option></select><textarea class="hws-input" name="notes" rows="4" placeholder="What was discussed?" required></textarea><input class="hws-input" type="datetime-local" name="next_follow_up_at"><button id="saveActivityButton" class="hws-btn" type="submit">Save Activity</button><div id="activitySaveStatus" style="display:none;padding:9px;border-radius:7px;font-size:12px;font-weight:700;text-align:center;"></div></div></form></section>@endif
        </aside>
    </div>
</div>
<script>
const leadPatchBase = `{{ url(config('app.admin_url','admin').'/field-service/sales-leads') }}`;
async function updateLeadField(id,field,value){const response=await fetch(`${leadPatchBase}/${id}/patch`,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({field,value})});const data=await response.json();if(!response.ok)throw new Error(data.message||Object.values(data.errors||{}).flat()[0]||'Unable to update lead.');return data;}
function showAjaxStatus(element,message,success){element.style.display='block';element.style.background=success?'#ecfdf5':'#fef2f2';element.style.color=success?'#047857':'#b91c1c';element.textContent=message;}
async function saveCrmControls(id){const button=document.getElementById('saveCrmButton');const status=document.getElementById('crmSaveStatus');const fields=[...document.querySelectorAll('#crmControls [data-field]:not(:disabled)')];button.disabled=true;button.textContent='Saving...';status.style.display='none';try{for(const input of fields){await updateLeadField(id,input.dataset.field,input.value);}showAjaxStatus(status,'CRM changes saved successfully.',true);button.textContent='Saved';setTimeout(()=>button.textContent='Save Changes',1200);}catch(error){showAjaxStatus(status,error.message,false);button.textContent='Save Changes';}finally{button.disabled=false;}}
async function saveActivity(event,form){event.preventDefault();const button=document.getElementById('saveActivityButton');const status=document.getElementById('activitySaveStatus');button.disabled=true;button.textContent='Saving...';status.style.display='none';try{const response=await fetch(form.action,{method:'POST',headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:new FormData(form)});const data=await response.json();if(!response.ok)throw new Error(data.message||Object.values(data.errors||{}).flat()[0]||'Unable to save activity.');document.querySelector('#leadTimeline .hws-empty')?.remove();document.getElementById('leadTimeline').insertAdjacentHTML('afterbegin',`<div class="hws-event"><small>${data.activity.created_at} · ${data.activity.admin_name} · ${data.activity.activity_type}</small><p></p></div>`);document.querySelector('#leadTimeline .hws-event:first-child p').textContent=data.activity.notes;form.reset();showAjaxStatus(status,'Activity saved successfully.',true);}catch(error){showAjaxStatus(status,error.message,false);}finally{button.disabled=false;button.textContent='Save Activity';}}
</script>
@endsection
