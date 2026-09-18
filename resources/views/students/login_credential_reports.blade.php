@php
    $classType = Helper::classType();
    $studentCount = isset($data) ? count($data) : 0;
@endphp
@extends('layout.app')
@section('content')

<div class="content-wrapper report-page">
    <section class="content pt-2 pb-3">
        <div class="container-fluid">
            <div class="report-hero mb-3">
                <div>
                    <span class="report-kicker">Student Access</span>
                    <h1>Login Credential Report</h1>
                    <p>Filter by class and review generated credentials in a compact table.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card report-card">
                        <div class="card-header">
                            <div>
                                <h3 class="card-title">Filter</h3>
                                <p>Select a class to load student credentials</p>
                            </div>
                            <span class="report-count">{{ $studentCount }} records</span>
                        </div>
                        <div class="card-body">
                            <form id="quickForm" action="{{ url('login_credential_reports') }}" method="post" class="row align-items-end">
                                @csrf
                                <div class="col-md-4">
                                    <div class="form-group mb-0">
                                        <label>{{ __('common.Class') }}</label>
                                        <select class="select2 form-control" id="class_type_id" name="class_type_id">
                                            <option value="">{{ __('common.Select') }}</option>
                                            @foreach($classType as $type)
                                                <option value="{{ $type->id ?? '' }}" {{ ($type->id == ($search['class_type_id'] ?? '')) ? 'selected' : '' }}>
                                                    {{ $type->name ?? '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                                        <i class="fa fa-search mr-1"></i>{{ __('common.Search') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($search['class_type_id']))
                <div class="row">
                    <div class="col-12">
                        <div class="card report-card">
                            <div class="card-header">
                                <div>
                                    <h3 class="card-title">Results</h3>
                                    <p>Student name, guardian, username, and password</p>
                                </div>
                                <span class="report-count">{{ $studentCount }} students</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive report-table-wrap">
                                    <table class="table table-hover table-striped mb-0 report-table">
                                        <thead>
                                            <tr>
                                                <th style="width:70px">{{ __('common.SR.NO') }}</th>
                                                <th>{{ __('common.Name') }}</th>
                                                <th>{{ __('common.Class') }}</th>
                                                <th>{{ __('Guardian Name') }}</th>
                                                <th>{{ __('Student UserName') }}</th>
                                                <th>{{ __('Password') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($data as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                                                    <td>{{ $item['class_name'] ?? '' }}</td>
                                                    <td>{{ $item['father_name'] ?? '' }}</td>
                                                    <td><span class="mono-badge">{{ $item['userName'] ?? '' }}</span></td>
                                                    <td><span class="mono-badge mono-muted">{{ $item['confirm_password'] ?? '' }}</span></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-4 text-muted">No student found for the selected class.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>

<style>
.report-page{background:#f4f7fb}
.report-hero{background:linear-gradient(120deg,#233b75,#365bb6);border-radius:12px;padding:18px 20px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:14px;box-shadow:0 8px 22px rgba(29,55,113,.14)}
.report-kicker{font-size:11px;letter-spacing:.08em;text-transform:uppercase;opacity:.72}
.report-hero h1{font-size:22px;line-height:1.15;margin:4px 0 2px;font-weight:700}
.report-hero p{margin:0;font-size:13px;opacity:.82}
.report-actions{display:flex;gap:8px;flex-wrap:wrap}
.report-card{border:0;border-radius:12px;box-shadow:0 4px 14px rgba(36,52,82,.06);overflow:hidden}
.report-card .card-header{background:#fff;border-bottom:1px solid #edf0f5;padding:12px 15px;display:flex;align-items:center;justify-content:space-between;gap:10px}
.report-card .card-title{margin:0;font-size:14px;font-weight:700;color:#2c3852;float:none}
.report-card .card-header p{font-size:11px;color:#8792a5;margin:2px 0 0}
.report-count{font-size:11px;padding:4px 8px;border-radius:999px;background:#eef3ff;color:#4361ee}
.report-table-wrap{max-height:68vh}
.report-table{font-size:12px}
.report-table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
.report-table{min-width:760px}
.report-table thead th{position:sticky;top:0;z-index:2;background:#f8fafc;border-bottom:1px solid #edf0f5;padding:10px 12px;font-size:12px;white-space:nowrap}
.report-table td{padding:9px 12px;vertical-align:middle;border-color:#edf0f5}
.report-table tr:hover{background:#f9fbff}
.mono-badge{display:inline-block;padding:4px 8px;border-radius:8px;background:#eef6ff;color:#164f86;font-family:ui-monospace, SFMono-Regular, Menlo, monospace;font-size:11px}
.mono-muted{background:#f4f7fb;color:#5f6d86}
@media(max-width:575px){
    .report-hero{padding:16px;align-items:flex-start;flex-direction:column}
    .report-hero h1{font-size:20px}
    .report-actions{width:100%}
    .report-actions .btn{width:100%}
    .report-card .card-header{padding:11px 12px;flex-direction:column;align-items:flex-start}
    .report-card .card-body{padding:11px 12px}
    .report-table{min-width:640px;font-size:11px}
    .report-count{align-self:flex-start}
}
</style>
@endsection
