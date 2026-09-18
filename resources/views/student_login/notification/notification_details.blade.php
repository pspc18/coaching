@php
$getUser = Helper::getUser();
@endphp
@extends('student_login.layout.app')
@section('title', 'Notification Details')
@section('page_title', 'NOTIFICATION DETAILS')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])
@section('content')
<section class="common-page">
 <div class="common-box m-2 border-0">

                    <div class="card shadow-lg border-0 rounded-3" style="overflow:hidden;">
                        {{-- Header --}}
                        <div class="card-header d-flex justify-content-between align-items-center" 
                             style="background:var(--primary-dark); color:var(--common-white);">
                            <h3 class="card-title mb-0" style="font-weight:600;">
                                <i class="fa fa-bell"></i> &nbsp; Notification Details
                            </h3>
                            <!--<a href="{{ url()->previous() }}" class="btn btn-light btn-sm fw-bold shadow-sm">-->
                            <!--    <i class="fa fa-arrow-left"></i> Back-->
                            <!--</a>-->
                        </div>

                        {{-- Body --}}
                        <div class="card-body" style=" padding:2rem;">
                            {{-- Title --}}
                            <div class="mb-3">
                                <h3 class="fw-bold mb-1" style="font-size:22px;">
                                    <i class="fa fa-circle text-primary" style="font-size:10px; vertical-align:middle;"></i>
                                    &nbsp; {{ $notification->title ?? 'Untitled Notification' }}
                                </h3>
                                <p class=" mb-2" style="color:var(--text-secondary);">
                                    <i class="fa fa-clock"></i>
                                    &nbsp; {{ date('d M Y h:i A', strtotime($notification->created_at)) }}
                                </p>
                            </div>

                            {{-- Content --}}
                            <div class="p-3 mb-3 border-start border-4 border-primary rounded-3" 
                                 style="font-size:15px; line-height:1.7;box-shadow: var(--box-shadow);">
                                <i class="fa fa-align-left text-primary"></i> &nbsp;
                                {!! nl2br(e($notification->content ?? 'No content available.')) !!}
                            </div>

                            {{-- Image --}}
                            @if(!empty($notification->image))
                                <div class="text-center mt-4">
                                    <div class="p-2 border rounded-3  d-inline-block" style="box-shadow: var(--box-shadow);">
                                        <img src="{{ $notification->image }}" 
                                             alt="Notification Image" 
                                             class="img-fluid rounded-3"
                                             style="max-height:250px; transition:transform .3s;"
                                             onmouseover="this.style.transform='scale(1.05)'"
                                             onmouseout="this.style.transform='scale(1)'">
                                    </div>
                                </div>
                            @endif

                            {{-- Footer Badge --}}
                            <div class="mt-4 text-end">
                                <span class="badge rounded-pill px-3 py-2" 
                                      style="background:linear-gradient(90deg,#17a2b8,#0d6efd); color:white; font-size:13px;">
                                    <i class="fa fa-bell"></i>
                                    {{ $notification->type === 'student' ? 'Student Notification' : ucfirst($notification->type) }}
                                </span>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="card-footer text-center text-muted small" style="background:#eef3ff;">
                            <i class="fa fa-info-circle text-primary"></i> &nbsp; 
                            You’re viewing a system notification message.
                        </div>
                    </div>
                </div>
    </section>


@endsection
