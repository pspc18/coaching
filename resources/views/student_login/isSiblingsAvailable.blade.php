@php
   $classType = Helper::classType();
    $getAttendanceStatus= Helper::getAttendanceStatus();
   
@endphp
@extends('layout.app') 
@section('content')

<input type="hidden" id="session_id" value="{{ Session::get('role_id') ?? '' }}">
 <div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
    <div class="card card-outline card-orange">
         <div class="card-header bg-primary">
        <h3 class="card-title"><i class="fa fa-calendar-check-o"></i> &nbsp;{{ __('Siblings') }}</h3>
        <div class="card-tools">
      
        </div>
        
        </div>         
         
           @if(!empty($siblings))
             <div class='row p-2'>
           @foreach($siblings as $sibling)
           
          
           @php
            $class_name = '';
           if(!empty($sibling->class_type_id ?? ''))
           {
           $class_name = DB::table('class_types')->where('id',$sibling->class_type_id ?? '')->first();
           $class_name = $class_name->name ?? '';
           }
           @endphp
         
               <div class="col-12 col-sm-6 col-md-3">
                        <a href="{{ url('sibling/login') }}/{{$sibling->id}}">
                        <div class="info-box mb-3 text-dark padd_box">
                            
                            <span class="info-box-icon bg-info elevation-1 box_padding"><img style='border-radius:10px'width='90px' height='80px'src="{{env('IMAGE_SHOW_PATH')}}profile/{{$sibling->image}}"
                            alt="Image" class="logo">
</span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{$sibling->first_name ?? ''}} {{$sibling->last_name ?? ''}}</span>
                                <span class="info-box-number" style="font-size: 15px;">
                                    {{$class_name}}</span>
                            </div>
                        </div>
                    </a>
                </div>
    
         
           
       
  @endforeach
    </div>
@endif
         
         
    </div>

  
</div>
</div>
</div>
</section>
</div>

<style>
    .main-header{
        display:none !important;
    }
    .main-sidebar{
        display:none !important;
    }
    .main_mobile_footer{
        display:none !important;
    }
</style>
         
@endsection 