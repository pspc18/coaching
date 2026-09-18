
@extends('layout.app') 
@section('content')
<div class="content-wrapper">
    
   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-hospital-o"></i> &nbsp; {{ __('hostel.Hostel Management') }}</h3>
                    <div class="card-tools">
                        <a href="{{url('hostel_assign_dashboard')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(15)->view ? '' : 'd-none' }}" title="Add User"><i class="fa fa-eye"></i> Diagramatic VIew</a>
                    </div>
            
                </div>               
            </div>
            </div>  
            
            @if(!empty(Helper::SidebarSubPerm(15)))
                @foreach(Helper::SidebarSubPerm(15) as $sub_sidebar)
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
            
            
        
            
            <!--<div class="col-md-3">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h4>{{ __('hostel.Room') }}</h4>
                        <h4>{{\App\Models\hostel\HostelRoom::countTotelRoom() }}
                        <span>
                            <a href="{{url('room_add')}}" class="btn btn-primary btn-xs ml-4" title="Add Room"><i class="fa fa-plus"></i>{{ __('common.Add') }} </a>
                            <a href="{{url('room_view')}}" class="btn btn-primary btn-xs ml-4" title="View Room"><i class="fa fa-eye"></i>{{ __('common.View') }} </a>
                        </span>
                        </h4>                        
                    </div>
                    <div class="icon">     
                        <i class="fa fa-trello"></i>
                    </div>
                    <div class="text-center small-box-footer">{{ __('common.More info') }} <i class="fa fa-arrow-circle-right"></i></div>
                </div>
            </div> --> 

            <!--<div class="col-md-3">
                <a href="{{url('room_view')}}"  class="small-box-footer">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h4>Room View</h4>
                        <h4>{{\App\Models\hostel\HostelRoom::countTotelRoom() }}</h4>
                    </div>
                    <div class="icon">     
                        <i class="fa fa-trello"></i>
                    </div>
                    <div class="text-center small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>-->
            

            <!--<div class="col-md-3">
                <a href="{{url('bed_view')}}"  class="small-box-footer">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h4>Bed View</h4>
                        <h4>{{\App\Models\hostel\HostelBed::countTotelBed() }}</h4>
                    </div>
                    <div class="icon">
                        <i class="fa fa-bed"></i>
                    </div>
                    <div class="text-center small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>-->

         
                <!-- <div class="col-md-3">-->
                <!--    <a href="{{url('studentsAttendanceAdd')}}" class="small-box-footer">-->
                <!--        <div class="small-box bg-primary">-->
                <!--            <div class="inner">-->
                <!--                <h4>{{ __('student.Students Attendance') }}</h4>-->
                <!--                <p>{{ __('messages.Enter') }}</p>-->
                <!--            </div>-->
                <!--            <div class="icon">-->
                <!--                <i class="fa fa-bar-chart"></i>-->
                <!--            </div>-->
                <!--            <div class="text-center small-box-footer">{{ __('messages.More info') }} <i class="fa fa-arrow-circle-right"></i></div>-->
                <!--        </div>-->
                <!--    </a>-->
                <!--</div>-->
                <!--<div class="col-md-3">-->
                <!--    <a href="{{url('studentsAttendanceView')}}" class="small-box-footer">-->
                <!--        <div class="small-box bg-success">-->
                <!--            <div class="inner">-->
                <!--                <h4>{{ __('student.Attendance View') }}</h4>-->
                <!--                <p>{{ __('messages.Enter') }}</p>-->
                <!--            </div>-->
                <!--            <div class="icon">-->
                <!--                <i class="fa fa-bar-chart"></i>-->
                <!--            </div>-->
                <!--            <div class="text-center small-box-footer">{{ __('messages.More info') }} <i class="fa fa-arrow-circle-right"></i></div>-->
                <!--        </div>-->
                <!--    </a>-->
                <!--</div>-->
        </div>
    
        <div class="row" style="margin-top:5%;">
              <!--<div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-hospital-o"></i> &nbsp; {{ __('hostel.Hostel Expenses Management') }}</h3>
                    <div class="card-tools">
                        <a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add User</a>
                    </div>
            
                </div>               
            </div>
            </div> -->
            
            
        </div>
        <div class="row" style="margin-top:5%;">
              <!--<div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-hospital-o"></i> &nbsp; {{ __('hostel.Hostel Mess Management') }}</h3>
                    <div class="card-tools">
                        <a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add User</a>
                    </div>
            
                </div>               
            </div>
            </div> -->
            
             
        </div>
        
    </div>
    </section>
</div>


  
       

@endsection

