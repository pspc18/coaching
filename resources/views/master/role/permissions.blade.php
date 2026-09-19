@extends('layout.app')

@section('content')
<div class="content-wrapper" style="background: #eef2f6; min-height: calc(100vh - 60px); padding: 10px;">
    {{-- Top Hero Bar --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2 p-2" style="background: linear-gradient(135deg, #002C54 0%, #0f3460 100%); border-radius: 3px; color: #fff; box-shadow: 0 1px 3px rgba(0,44,84,.15);">
        <div>
            <div style="font-size: 9.5px; text-transform: uppercase; letter-spacing: .05em; opacity: .85;">
                <i class="fa fa-shield mr-1"></i> Role & Access Control
            </div>
            <h4 class="mb-0" style="font-size: 15px; font-weight: 700; color: #fff;">
                Permissions Configuration: {{ $role->name ?? 'Role' }} (#{{ $role_id }})
            </h4>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('role_add') }}" class="btn btn-sm btn-light" style="font-size: 11px; font-weight: 600; color: #002C54;">
                <i class="fa fa-arrow-left mr-1"></i> Back to Roles
            </a>
        </div>
    </div>

    {{-- Permissions Card Container --}}
    <div class="card shadow-sm" style="border: 1px solid #cbd5e1; border-radius: 3px; overflow: hidden; height: calc(100vh - 130px); display: flex; flex-direction: column;">
        @include('master.role.permissions_partial')
    </div>
</div>
@endsection

@section('scripts')
@include('master.role.permissions_script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.querySelector('.perm-compact-wrapper, .perm-component-wrapper');
    if (container && typeof initPermissionsComponent === 'function') {
        initPermissionsComponent(container);
    }
});
</script>
@endsection
