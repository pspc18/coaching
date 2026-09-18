@extends('layout.app')
@section('content')
@php
$getPermission = Helper::getPermission();
@endphp
<div class="content-wrapper">
    <div class="card-body">
        <div class="row">

            <div class="col-12 col-md-12">
                <div class="card card-outline card-orange">
                    <div class="card-header flex_items_toggel bg-primary">
                        <h3 class="card-title"><i class="fa fa-bus"></i> &nbsp;{{ __('bus.Bus Management') }}</h3>
                    <div class="card-tools Display_none_mobile">
                        <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
                    </div>
                        <div class="Display_none_PC">
                            <button type="button" class="btn btn-primary" data-toggle="dropdown">
                                <i class="fa fa-bars"></i>
                            </button>
                        <div class="dropdown-menu">
                        <a href="{{url('master_dashboard')}}" class="dropdown-item"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
                        </div>
                        </div>

                    </div>
                </div>
            </div>
            
            
            
            @if(!empty(Helper::SidebarSubPerm(20)))
                @foreach(Helper::SidebarSubPerm(20) as $sub_sidebar)
                <div class="col-md-3 col-6">
                    <a href="{{url($sub_sidebar['url'] ?? '')}}" class="small-box-footer">
                        <div class="small-box bg-{{$sub_sidebar['bg_color'] ?? ''}}">
                            <div class="inner">
                                <h4 class="mobile_text_title"> @if(Session::get('locale') == 'hi'){{$sub_sidebar['hindi_name'] ?? ''}} @else {{$sub_sidebar['name'] ?? ''}} @endif </h4>
                                <p>{{ __('common.Enter') }}</p>
                            </div>
                            <div class="icon">
                                <i class="fa {{$sub_sidebar['ican'] ?? ''}}"></i>
                            </div>
                            <div class="text-center small-box-footer">{{ __('common.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

@endsection