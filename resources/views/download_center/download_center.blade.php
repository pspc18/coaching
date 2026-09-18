
@extends('layout.app') 
@section('content')

 <div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
          
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                    @if(Session::get('role_id') !== 3)
                        <h3 class="card-title"><i class="fa fa-cloud-download"></i> &nbsp;{{ __('download.Upload/ Download Content') }} </h3>
                    @else
                        <h3 class="card-title"><i class="fa fa-cloud-download"></i> &nbsp; {{ __('download.Download Center') }}</h3>
                    @endif
                    <div class="card-tools">
                                 @if(Session::get('role_id') == 3)
                                <a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs "><i class="fa fa-arrow-left"></i> <span class="" >{{ __('Back') }} </span></a>
                                 @endif
                            </div>
            
                </div>               
            </div>
            </div>
        </div> 

            
        <div class="row">
            @if(Session::get('role_id') == 3)
                    @if(!empty(Helper::SubSidebarPerm(12)))
                        @foreach(Helper::SubSidebarPerm(12) as $key => $sub_sidebar)
                        <div class="col-md-3 col-6 {{ $key == 0 ? 'd-none' : '' }}">
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
                        <!--<div class="col-md-3 col-6">
                            <a href="{{url('studentAdmitCard')}}" class="small-box-footer">
                                <div class="small-box bg-{{$sub_sidebar['bg_color'] ?? ''}}">
                                    <div class="inner">
                                        <h4 class="mobile_text_title"> Admit Card </h4>
                                        <p>{{ __('common.Enter') }}</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa {{$sub_sidebar['ican'] ?? ''}}"></i>
                                    </div>
                                    <div class="text-center small-box-footer">{{ __('common.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                                </div>
                            </a>
                        </div>-->
                    @endif 
                
            @else
                    
                    @if(!empty(Helper::SidebarSubPerm(12)))
                        @foreach(Helper::SidebarSubPerm(12) as $key => $sub_sidebar)
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
            
            @endif
        </div>
   
    
    </div>
  
</section>
</div>

  
       

@endsection

