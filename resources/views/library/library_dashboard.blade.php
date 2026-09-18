    
@extends('layout.app') 
@section('content')
@php
    $getPermission = Helper::getPermission();
@endphp
                                                                    
<div class="content-wrapper">
    
   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <!--<div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-book"></i> &nbsp;{{ __('library.Library Management') }} </h3>
                    <div class="card-tools">
                        <a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add User</a>
                    </div>
                </div>               
            </div>
            </div> --> 
            
            
            @if(!empty(Helper::SidebarSubPerm(18)))
                @foreach(Helper::SidebarSubPerm(18) as $sub_sidebar)
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
    </section>
</div>


  
       

@endsection

