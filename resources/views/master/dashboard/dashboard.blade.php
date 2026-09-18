@extends('layout.app') 
@section('content')
 
<div class="content-wrapper" >

   <section class="content pt-3">
      <div class="container-fluid">
          
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-sitemap"></i> &nbsp;{{ __('master.Master Management') }}</h3>
                    <div class="card-tools">
                    </div>
            
                </div>               
            </div>
            </div> 
        </div> 
            
        <div class="row">
            
            
            @if(!empty(Helper::SidebarSubPerm(9)))
                        @foreach(Helper::SidebarSubPerm(9) as $sub_sidebar)
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
                 
             
               
                <!--<div class="col-md-3 col-6">
                    <a href="{{url('teacher_subject_add')}}" class="small-box-footer">
                        <div class="small-box bg-dark">
                            <div class="inner">
                                <h4 class="mobile_text_title">{{ __('master.Subject Teachers') }}</h4>

                                <p>{{ __('common.Enter') }}</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <div class="text-center small-box-footer">{{ __('common.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                        </div>
                    </a>
                </div>-->
                
                
           
            

        </div>
            
            
        </div>
        
    </section>

</div>
       

@endsection
  
