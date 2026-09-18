@extends('layout.app') 
@section('content')
@php
$teach_count = Helper::getCount('teachers','id','count');
$drop_te_count = Helper::getCount('teachers','id','count','drop_status',1);
@endphp    


        
 <div class="content-wrapper">
    <section class="content pt-3">
    <div class="container-fluid">
        <div class="row">
            
          <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-shirtsinbulk"></i> &nbsp;{{ __('staff.Staff Management') }}</h3>
                    <div class="card-tools">
                        <!--<a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add User</a>-->
                    </div>
            
                </div>               
            </div>
            </div> 
        @if(!empty(Helper::SidebarSubPerm(2)))
                        @foreach(Helper::SidebarSubPerm(2) as $sub_sidebar)
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
       
            
  @if(Session::get('role_id') != 1 && Session::get('role_id') != 3 )
     <div class="col-md-3 col-6">
                            <a href="{{url('staff_salary_view')}}" class="small-box-footer">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h4 class="mobile_text_title"> Salary</h4>
                                        <p>{{ __('common.Enter') }}</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <div class="text-center small-box-footer">{{ __('common.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                                </div>
                            </a>
                        </div>
                        
                        @endif
            
        </div>
        
            
            
        
        </section>



</div>
@endsection
  
