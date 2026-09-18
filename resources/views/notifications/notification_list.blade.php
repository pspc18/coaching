@extends('layout.app')
@section('content')

<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10 col-12">

                    <div class="card shadow-lg border-0 rounded-3" style="overflow:hidden;">
                        {{-- Header --}}
                        <div class="card-header d-flex justify-content-between align-items-center" 
                             style="background:linear-gradient(90deg, #007bff 0%, #0056b3 100%); color:white;">
                            <h3 class="card-title mb-0" style="font-weight:600;">
                                <i class="fa fa-bell"></i> &nbsp; Notifications
                            </h3>
                        </div>

                        {{-- Body --}}
                        <div class="card-body" style="background:#f8faff; min-height:300px;">
                            @if($notifications->isEmpty())
                                <p class="text-center text-muted mt-3">No notifications found</p>
                            @else
                                @foreach($notifications as $n)
                                    @php
                                       $url = url('notification_detail_stu/' . $n->id);
                                        $time = \Carbon\Carbon::parse($n->created_at)->diffForHumans();
                                    @endphp

                                    <div class="d-flex align-items-start border-bottom pb-2 mb-2 position-relative" style="transition:0.3s;">
                                        <a href="{{ $url }}" class="text-decoration-none text-dark flex-grow-1">
                                            <div class="d-flex align-items-start">
                                                <div class="position-relative me-2 pr-2 pl-2 mt-1">
                                                    {{-- 🔴 Red dot (only if unseen) --}}
                                                    @if($n->message_seen == 0)
                                                        <span class="position-absolute" style="width:8px; height:8px; background:red; border-radius:50%; top:4px; left:-8px;"></span>
                                                    @endif
                                                    <i class="fa fa-bell text-primary" style="font-size:18px;"></i>
                                                </div>
                                                <div>
                                                    <strong style="margin: 0px 40px 0px 0px;">{{ $n->title }}</strong><br>
                                                    <small class="text-muted"><i class="fa fa-clock"></i> {{ $time }}</small>
                                                </div>
                                            </div>
                                        </a>

                                        {{-- ❌ Hide button --}}
                                        <form method="GET" style="position:absolute; right:0; top:0;">
                                            <input type="hidden" name="hide_id" value="{{ $n->id }}">
                                            <button type="submit" class="btn btn-sm text-secondary" style="border:none; background:none;">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

@endsection
