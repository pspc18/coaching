@extends('layout.app')
@section('content')

@include('attendance.theme')

<div class="content-wrapper attendance-page">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="attendance-shell">
                <div class="attendance-hero">
                    <div class="attendance-hero-inner">
                        <div>
                            <div class="attendance-hero-kicker">Attendance Management</div>
                            <h1 class="attendance-hero-title">Self Attendance</h1>
                            <p class="attendance-hero-subtitle">Biometric mode with same process, compact and mobile-first.</p>
                        </div>
                        <div class="attendance-hero-actions">
                            <div class="attendance-chip">{{ $displayName }} ({{ $uniqueId }})</div>
                            <a href="{{ url('attendance/self') }}" class="btn btn-outline-light btn-sm"><i class="fa fa-refresh mr-1"></i>Reset</a>
                        </div>
                    </div>
                </div>
                <div class="attendance-stat-grid">
                    <div class="attendance-stat"><span class="label">Date</span><strong>{{ date('d M Y', strtotime($selectedDate)) }}</strong></div>
                    <div class="attendance-stat"><span class="label">Check In</span><strong>{{ $mark->in_time ?? '-' }}</strong></div>
                    <div class="attendance-stat"><span class="label">Check Out</span><strong>{{ $mark->out_time ?? '-' }}</strong></div>
                    <div class="attendance-stat"><span class="label">Status</span><strong>{{ ucfirst((string) ($mark->status ?? 'Pending')) }}</strong></div>
                </div>

                <div class="card card-outline card-orange self-card">
                    <div class="card-header bg-primary d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h3 class="card-title mb-0"><i class="fa fa-user"></i> &nbsp;Self Attendance (Biometric)</h3>
                            <div class="text-white-50">Mark your own attendance</div>
                        </div>
                        <div class="self-chip">{{ $displayName }} ({{ $uniqueId }})</div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ url('attendance/self') }}" class="attendance-toolbar">
                            @csrf
                            <div class="row w-100">
                                <div class="col-md-3">
                                    <div class="att-field">
                                        <label>Date</label>
                                        <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" max="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="att-field">
                                        <label>Check In</label>
                                        <input type="time" name="in_time" class="form-control" value="{{ $mark->in_time ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="att-field">
                                        <label>Check Out</label>
                                        <input type="time" name="out_time" class="form-control" value="{{ $mark->out_time ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="att-field">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="">Select</option>
                                            <option value="in" {{ in_array(($mark->status ?? ''), ['in', 'present'], true) ? 'selected' : '' }}>In</option>
                                            <option value="out" {{ ($mark->status ?? '') === 'out' ? 'selected' : '' }}>Out</option>
                                            <option value="absent" {{ ($mark->status ?? '') === 'absent' ? 'selected' : '' }}>Absent</option>
                                            <option value="halfday" {{ ($mark->status ?? '') === 'halfday' ? 'selected' : '' }}>Half Day</option>
                                            <option value="holiday" {{ ($mark->status ?? '') === 'holiday' ? 'selected' : '' }}>Holiday</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 d-flex flex-wrap" style="gap:8px;">
                                <button class="btn btn-primary">Save</button>
                                <a href="{{ url('attendance/self') }}" class="btn btn-outline-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
