@extends('layout.app')
@section('content')

@include('attendance.theme')

<div class="content-wrapper attendance-page">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="card card-outline card-orange self-card">
                <div class="card-header bg-primary d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h3 class="card-title mb-0"><i class="fa fa-qrcode"></i> &nbsp;Self Attendance (QR)</h3>
                        <div class="text-white-50">Mark your own attendance</div>
                    </div>
                    <div class="self-chip">{{ $displayName }} ({{ $uniqueId }})</div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">QR attendance for self marking will be enabled soon.</div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
