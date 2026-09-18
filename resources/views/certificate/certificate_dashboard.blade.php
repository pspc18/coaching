@extends('layout.app') 
@section('content')
@php
$c_certificate_count = Helper::getCount('c_certificates_form','id','count');
$event_count = Helper::getCount('evente_certificates','id','count');
$sports_count = Helper::getCount('sports_certificates','id','count');
$tc_count = Helper::getCount('tc_certificates','id','count');
@endphp 
<div class="content-wrapper">
        <div class="card-body">
        <div class="row">
            
          <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-certificate"></i> &nbsp; {{__('certificate.Certificate Management') }}</h3>
                    <div class="card-tools">
                        <!--<a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add User</a>-->
                    </div>
            
                </div>               
            </div>
            </div> 
            
            
            @if(!empty(Helper::SidebarSubPerm(5)))
                @foreach(Helper::SidebarSubPerm(5) as $sub_sidebar)
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