@extends('admin::layouts.content')

@section('page_title')
    Create {{ ucfirst($leadType ?? 'Sales') }} Lead
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Create {{ ucfirst($leadType ?? 'Sales') }} Lead</h1>
            </div>

            <div class="page-action">
                <a href="{{ $leadType === 'projects' ? route('hws.admin.leads.projects') : route('hws.admin.leads.trading') }}" class="btn btn-lg btn-primary" style="background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;">
                    Back to Leads
                </a>
            </div>
        </div>

        <div class="page-content" style="width: 100%;">
            @if($errors->any())
                <div style="padding: 12px 20px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-weight: 600; margin-bottom: 20px; font-size: 14px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('hws.admin.sales-leads.store') }}" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
                @csrf

                <!-- Basic Customer & Lead Info -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 20px;">
                    <div class="control-group">
                        <label class="required" style="font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Customer / Business Name</label>
                        <input type="text" name="customer_name" class="control" required value="{{ old('customer_name') }}" placeholder="e.g. Grand Hotel Resort" style="width: 100%;">
                    </div>

                    <div class="control-group">
                        <label class="required" style="font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Customer Phone</label>
                        <input type="text" name="customer_phone" class="control" required value="{{ old('customer_phone') }}" placeholder="+91 9876543210" style="width: 100%;">
                    </div>

                    <div class="control-group">
                        <label style="font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Customer Email</label>
                        <input type="email" name="customer_email" class="control" value="{{ old('customer_email') }}" placeholder="client@example.com" style="width: 100%;">
                    </div>

                    <div class="control-group">
                        <label class="required" style="font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Property Type</label>
                        <select name="property_type" class="control" required style="width: 100%;">
                            <option value="hotel" {{ old('property_type') == 'hotel' ? 'selected' : '' }}>Hotel / Resort</option>
                            <option value="hospital" {{ old('property_type') == 'hospital' ? 'selected' : '' }}>Hospital / Healthcare</option>
                            <option value="commercial" {{ old('property_type') == 'commercial' ? 'selected' : '' }}>Commercial Complex / Mall</option>
                            <option value="residential" {{ old('property_type') == 'residential' ? 'selected' : '' }}>Residential / Society</option>
                            <option value="industrial" {{ old('property_type') == 'industrial' ? 'selected' : '' }}>Industrial / Factory</option>
                            <option value="bungalow" {{ old('property_type') == 'bungalow' ? 'selected' : '' }}>Bungalow / Villa</option>
                            <option value="other" {{ old('property_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="control-group">
                        <label class="required" style="font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Temperature / Priority</label>
                        <select name="temperature" class="control" required style="width: 100%;">
                            <option value="hot" {{ old('temperature') == 'hot' ? 'selected' : '' }}>🔥 Hot</option>
                            <option value="warm" {{ old('temperature', 'warm') == 'warm' ? 'selected' : '' }}>⚡ Warm</option>
                            <option value="cold" {{ old('temperature') == 'cold' ? 'selected' : '' }}>❄️ Cold</option>
                        </select>
                    </div>

                    <div class="control-group">
                        <label class="required" style="font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Sales Type</label>
                        <select name="sales_type" class="control" required style="width: 100%;">
                            <option value="trading" {{ old('sales_type', $leadType) == 'trading' ? 'selected' : '' }}>Trading (Standard Products)</option>
                            <option value="projects" {{ old('sales_type', $leadType) == 'projects' ? 'selected' : '' }}>Projects (STP/ETP/WTP Turnkey)</option>
                            <option value="services" {{ old('sales_type', $leadType) == 'services' ? 'selected' : '' }}>Services & AMC</option>
                        </select>
                    </div>

                    <div class="control-group">
                        <label style="font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Assign Agent</label>
                        <select name="assigned_to" class="control" style="width: 100%;">
                            <option value="">Unassigned</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('assigned_to') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="control-group">
                        <label style="font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Lead Source</label>
                        <select name="source" class="control" style="width: 100%;">
                            <option value="Field Survey" {{ old('source') == 'Field Survey' ? 'selected' : '' }}>Field Survey</option>
                            <option value="Website Inbound" {{ old('source') == 'Website Inbound' ? 'selected' : '' }}>Website Inbound</option>
                            <option value="Phone Call" {{ old('source') == 'Phone Call' ? 'selected' : '' }}>Phone Call</option>
                            <option value="Referral" {{ old('source') == 'Referral' ? 'selected' : '' }}>Referral</option>
                            <option value="Exhibition" {{ old('source') == 'Exhibition' ? 'selected' : '' }}>Exhibition</option>
                        </select>
                    </div>

                    <div class="control-group">
                        <label style="font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Next Follow-up</label>
                        <input type="datetime-local" name="next_follow_up_at" class="control" value="{{ old('next_follow_up_at') }}" style="width: 100%;">
                    </div>

                    @if (\Hws\FieldService\Helpers\BranchScopeHelper::isHeadOfficeUser())
                        <div class="control-group">
                            <label style="font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Assign to Branch</label>
                            <select name="branch_id" class="control" style="width: 100%;">
                                @foreach ($allBranches as $b)
                                    <option value="{{ $b->id }}" {{ \Hws\FieldService\Helpers\BranchScopeHelper::getActiveBranchId() == $b->id ? 'selected' : '' }}>
                                        {{ $b->name }} ({{ $b->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <!-- Site Survey & Technical Specifications Box -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
                    <div style="font-size: 13px; font-weight: 700; color: #1e3a8a; margin-bottom: 14px; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 6px;">
                        <span>🔬</span> Site Survey & Technical Specifications
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-bottom: 16px;">
                        <div class="control-group">
                            <label style="font-weight: 600; font-size: 12px; color: #475569; margin-bottom: 4px; display: block;">Floors</label>
                            <input type="number" name="floors" class="control" value="{{ old('floors') }}" placeholder="e.g. 4" style="width: 100%;">
                        </div>

                        <div class="control-group">
                            <label style="font-weight: 600; font-size: 12px; color: #475569; margin-bottom: 4px; display: block;">Built-up Area (Sq.Ft)</label>
                            <input type="number" step="0.01" name="built_up_area_sqft" class="control" value="{{ old('built_up_area_sqft') }}" placeholder="e.g. 15000" style="width: 100%;">
                        </div>

                        <div class="control-group">
                            <label style="font-weight: 600; font-size: 12px; color: #475569; margin-bottom: 4px; display: block;">Rooms / Units</label>
                            <input type="number" name="rooms_units" class="control" value="{{ old('rooms_units') }}" placeholder="e.g. 50" style="width: 100%;">
                        </div>

                        <div class="control-group">
                            <label style="font-weight: 600; font-size: 12px; color: #475569; margin-bottom: 4px; display: block;">Water Use (KLD)</label>
                            <input type="number" step="0.01" name="water_use_kld" class="control" value="{{ old('water_use_kld') }}" placeholder="e.g. 25.5" style="width: 100%;">
                        </div>

                        <div class="control-group">
                            <label style="font-weight: 600; font-size: 12px; color: #475569; margin-bottom: 4px; display: block;">Water Source</label>
                            <select name="water_source" class="control" style="width: 100%;">
                                <option value="">Select Water Source</option>
                                <option value="municipal" {{ old('water_source') == 'municipal' ? 'selected' : '' }}>Municipal Supply</option>
                                <option value="borewell" {{ old('water_source') == 'borewell' ? 'selected' : '' }}>Borewell / Ground Water</option>
                                <option value="tanker" {{ old('water_source') == 'tanker' ? 'selected' : '' }}>Tanker Supply</option>
                                <option value="river" {{ old('water_source') == 'river' ? 'selected' : '' }}>River / Surface Water</option>
                            </select>
                        </div>

                        <div class="control-group">
                            <label style="font-weight: 600; font-size: 12px; color: #475569; margin-bottom: 4px; display: block;">Wastewater Disposal</label>
                            <select name="wastewater_disposal" class="control" style="width: 100%;">
                                <option value="">Select Disposal Method</option>
                                <option value="septic_tank" {{ old('wastewater_disposal') == 'septic_tank' ? 'selected' : '' }}>Septic Tank / Soak Pit</option>
                                <option value="open_drain" {{ old('wastewater_disposal') == 'open_drain' ? 'selected' : '' }}>Open Municipal Drain</option>
                                <option value="existing_stp" {{ old('wastewater_disposal') == 'existing_stp' ? 'selected' : '' }}>Existing STP (Needs Upgrade)</option>
                                <option value="none" {{ old('wastewater_disposal') == 'none' ? 'selected' : '' }}>None</option>
                            </select>
                        </div>

                        <div class="control-group">
                            <label style="font-weight: 600; font-size: 12px; color: #475569; margin-bottom: 4px; display: block;">Space Available</label>
                            <select name="space_available" class="control" style="width: 100%;">
                                <option value="">Select Space Availability</option>
                                <option value="open_area" {{ old('space_available') == 'open_area' ? 'selected' : '' }}>Open Ground Area</option>
                                <option value="limited" {{ old('space_available') == 'limited' ? 'selected' : '' }}>Limited / Compact Space</option>
                                <option value="basement_only" {{ old('space_available') == 'basement_only' ? 'selected' : '' }}>Basement Only</option>
                                <option value="not_sure" {{ old('space_available') == 'not_sure' ? 'selected' : '' }}>Not Sure / Need Survey</option>
                            </select>
                        </div>
                    </div>

                    <!-- Inquiry Checkboxes -->
                    <div>
                        <label style="font-weight: 600; font-size: 12px; color: #475569; margin-bottom: 8px; display: block;">Inquiry / Proposed Plant Types</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 16px;">
                            <label style="font-size: 13px; color: #334155; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                <input type="checkbox" name="inquiry_types[]" value="STP (Sewage Treatment Plant)"> STP (Sewage Treatment Plant)
                            </label>
                            <label style="font-size: 13px; color: #334155; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                <input type="checkbox" name="inquiry_types[]" value="WTP (Water Treatment Plant)"> WTP (Water Treatment Plant)
                            </label>
                            <label style="font-size: 13px; color: #334155; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                <input type="checkbox" name="inquiry_types[]" value="ETP (Effluent Treatment Plant)"> ETP (Effluent Treatment Plant)
                            </label>
                            <label style="font-size: 13px; color: #334155; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                <input type="checkbox" name="inquiry_types[]" value="RO Plant"> RO Plant / Commercial RO
                            </label>
                            <label style="font-size: 13px; color: #334155; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                <input type="checkbox" name="inquiry_types[]" value="Water Softener"> Water Softener / Filtration
                            </label>
                            <label style="font-size: 13px; color: #334155; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                <input type="checkbox" name="inquiry_types[]" value="AMC / Service"> AMC / Operation & Maintenance
                            </label>
                        </div>
                    </div>
                </div>

                <div class="control-group" style="margin-bottom: 16px;">
                    <label class="required" style="font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Customer Address / Location</label>
                    <textarea name="customer_address" class="control" required rows="2" placeholder="Full address of site..." style="width: 100%;">{{ old('customer_address') }}</textarea>
                </div>

                <div class="control-group" style="margin-bottom: 24px;">
                    <label style="font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px; display: block;">Notes / Survey Details</label>
                    <textarea name="notes" class="control" rows="3" placeholder="Technical specifications, requirements, existing setup..." style="width: 100%;">{{ old('notes') }}</textarea>
                </div>

                <div style="display: flex; gap: 12px;">
                    <button type="submit" class="btn btn-lg btn-primary">
                        Save & Create Lead
                    </button>
                    <a href="{{ $leadType === 'projects' ? route('hws.admin.leads.projects') : route('hws.admin.leads.trading') }}" class="btn btn-lg" style="background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@stop
