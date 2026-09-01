@extends('hws::admin.layouts.menu')

@section('page_title')
    Edit Branch - {{ $branch->name }}
@stop

@section('page-content')
    <div class="content">
        <div class="page-header" style="margin-bottom: 20px;">
            <div class="page-title">
                <h1 style="display: flex; align-items: center; gap: 8px;">
                    <i class="icon angle-left-icon back-link" style="cursor: pointer;" onclick="window.location = '{{ route('hws.admin.branches.index') }}'"></i>
                    Edit Branch: {{ $branch->name }}
                </h1>
            </div>
        </div>

        <div class="page-content">
            @if ($errors->any())
                <div style="padding: 12px 20px; background: #fee2e2; color: #991b1b; border-radius: 8px; font-weight: 600; margin-bottom: 20px; font-size: 14px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Clean Box Form styled exactly like Trading Leads -->
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; margin-bottom: 24px;">
                <form method="POST" action="{{ route('hws.admin.branches.update', $branch->id) }}">
                    @csrf

                    <h3 style="font-size: 15px; font-weight: 700; color: #334155; margin: 0 0 16px; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9;">
                        🏢 General Information
                    </h3>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                Branch Code <span style="color: #ef4444;">*</span>
                            </label>
                            <input type="text" name="code" value="{{ old('code', $branch->code) }}" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; box-sizing: border-box;"/>
                        </div>

                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                Branch Name <span style="color: #ef4444;">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $branch->name) }}" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; box-sizing: border-box;"/>
                        </div>

                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}" placeholder="+91-XXXXXXXXXX" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; box-sizing: border-box;"/>
                        </div>

                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Branch Email</label>
                            <input type="email" name="email" value="{{ old('email', $branch->email) }}" placeholder="branch@domain.com" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; box-sizing: border-box;"/>
                        </div>

                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">GSTIN</label>
                            <input type="text" name="gstin" value="{{ old('gstin', $branch->gstin) }}" placeholder="05AAAAA0000A1Z5" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; box-sizing: border-box;"/>
                        </div>
                    </div>

                    <h3 style="font-size: 15px; font-weight: 700; color: #334155; margin: 20px 0 16px; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9;">
                        📍 Address & Location
                    </h3>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Street Address</label>
                        <textarea name="address" rows="2" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; resize: vertical; box-sizing: border-box;">{{ old('address', $branch->address) }}</textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">City</label>
                            <input type="text" name="city" value="{{ old('city', $branch->city) }}" placeholder="e.g. Dehradun" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; box-sizing: border-box;"/>
                        </div>

                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">
                                State <span style="color: #ef4444;">*</span> (GST State)
                            </label>
                            <select name="state" required style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; box-sizing: border-box; background: #fff;">
                                <option value="">Select State</option>
                                @foreach($states as $st)
                                    <option value="{{ $st->name }}" {{ old('state', $branch->state) == $st->name || old('state', $branch->state) == $st->code ? 'selected' : '' }}>
                                        {{ $st->name }} ({{ $st->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">PIN Code</label>
                            <input type="text" name="pincode" value="{{ old('pincode', $branch->pincode) }}" placeholder="e.g. 248001" style="width: 100%; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px 12px; font-size: 14px; box-sizing: border-box;"/>
                        </div>
                    </div>

                    <h3 style="font-size: 15px; font-weight: 700; color: #334155; margin: 20px 0 16px; padding-bottom: 8px; border-bottom: 1px solid #f1f5f9;">
                        👥 Branch Staff & Role Settings
                    </h3>

                    <div style="display: flex; gap: 24px; align-items: center; margin-bottom: 16px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: #0f172a; cursor: pointer; font-size: 13px;">
                            <input type="checkbox" name="is_head_office" value="1" {{ old('is_head_office', $branch->is_head_office) ? 'checked' : '' }}>
                            Set as Main Head Office (HQ)
                        </label>

                        <div style="display: flex; align-items: center; gap: 14px; font-size: 13px;">
                            <label style="display: flex; align-items: center; gap: 6px; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="status" value="1" {{ $branch->status ? 'checked' : '' }}> Active
                            </label>
                            <label style="display: flex; align-items: center; gap: 6px; font-weight: 600; cursor: pointer;">
                                <input type="radio" name="status" value="0" {{ !$branch->status ? 'checked' : '' }}> Inactive
                            </label>
                        </div>
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 8px;">Assign Employees / Technicians to this Branch:</label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 10px; max-height: 200px; overflow-y: auto; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; background: #f8fafc;">
                            @foreach ($admins as $admin)
                                <label style="display: flex; align-items: center; gap: 8px; background: #fff; border: 1px solid #e2e8f0; padding: 6px 10px; border-radius: 6px; cursor: pointer; font-size: 12px; margin: 0;">
                                    <input type="checkbox" name="admin_ids[]" value="{{ $admin->id }}" {{ in_array($admin->id, $assignedAdminIds) ? 'checked' : '' }}>
                                    <div>
                                        <div style="font-weight: 600; color: #0f172a;">{{ $admin->name }}</div>
                                        <div style="font-size: 10.5px; color: #64748b;">{{ $admin->role->name ?? 'User' }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-lg btn-primary">
                            Save Branch
                        </button>
                        <a href="{{ route('hws.admin.branches.index') }}" class="btn btn-lg" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; font-weight: 600;">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
