@php
$getUser = Helper::getUser();
$role = Helper::roleType();
$liverole = Session::get('role_id');
@endphp

@extends('student_login.layout.app')

@section('title', 'Notice Board')
@section('page_title', 'NOTICE BOARD')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])

@section('content')

 <link rel="stylesheet" href="{{ asset('public/assets/school/css/adminlte.min.css') }}">    

       

<section class="common-page">
    <div class="common-box m-2 border-0">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12" id="accordion">

                    @if(!empty($data))
                        @php $i=1; @endphp

                        @foreach ($data as $item) 
                            
                            @php
                                $roles = explode(",", $item->role_id);
                            @endphp
                            
                            @if(in_array($liverole, $roles))

                                <div class="card card-warning card-outline">

                                    <!-- CLICK TO OPEN -->
                                    <a class="d-block w-100" data-toggle="collapse" href="#collapse_{{ $item->id }}">
                                        <div class="card-header">
                                            <h3 class="card-title">{{ $i++ }}. {{ $item->title }}</h3>
                                        </div>
                                    </a>

                                    <!-- COLLAPSE BODY -->
                                    <div id="collapse_{{ $item->id }}" class="collapse" data-parent="#accordion">
                                        <div class="card-body">

                                            <div class="row">

                                                <div class="col-md-9">
                                                    {!! html_entity_decode($item->message, ENT_QUOTES, 'UTF-8') !!} 
                                                </div>

                                                <div class="col-md-3">

                                                    <span><i class="fa fa-calendar"></i> {{ __('master.From Date') }} :
                                                        {{ date('d-m-Y', strtotime($item->from_date)) }}
                                                    </span><br>

                                                    <span><i class="fa fa-calendar"></i> {{ __('master.To Date') }} :
                                                        {{ date('d-m-Y', strtotime($item->to_date)) }}
                                                    </span><br>

                                                    <span><i class="fa fa-user"></i> Created By : Super Admin</span><br>

                                                    <h5 class="mt-2">Message To</h5>

                                                    @foreach($roles as $rid)
                                                        @foreach($role as $roleName)
                                                            @if($roleName->id == $rid)
                                                                <span><i class="fa fa-user"></i> {{ $roleName->name }}</span><br>
                                                            @endif
                                                        @endforeach
                                                    @endforeach

                                                </div>

                                            </div>

                                        </div>
                                    </div>

                                </div>

                            @endif
                        @endforeach

                    @else
                        <div class="card card-warning card-outline">
                            <h4 class="text-center mb-0 p-3">No Notice Here !!</h4>
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>
</section>
<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
            <script src="{{URL::asset('public/assets/school/js/bootstrap.bundle.min.js')}}"></script>
           
            
<script>
$(window).on("load", function() {

    @if(!empty($data_id))
        var data_id = "{{ $data_id }}";

        // OPEN THE COLLAPSE
        setTimeout(function() {
            $("#collapse_" + data_id).collapse("show");
        }, 300);

        // AUTO SCROLL
        setTimeout(function() {
            $('html, body').animate({
                scrollTop: $("#collapse_" + data_id).offset().top - 50
            }, 1000);
        }, 600);
    @endif

});
</script>

@endsection
