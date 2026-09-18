
@extends('layout.app') 
@section('content')
@php
$c_certificate_count = Helper::getCount('c_certificates_form','id','count');
$event_count = Helper::getCount('evente_certificates','id','count');
$sports_count = Helper::getCount('sports_certificates','id','count');
$tc_count = Helper::getCount('tc_certificates','id','count');
@endphp
                                                         
<div class="content-wrapper" >

   <section class="content pt-3">
      <div class="container-fluid">
          
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-cloud-download"></i> &nbsp; Download Content</h3>
                    <div class="card-tools">
                    </div>
            
                </div>               
            </div>
            </div> 
        </div> 
    
   
        <div class="row">
         
            
                                            @if(!empty($data))
                                    @php
                                       $i=1;
                                       $assignment=0;
                                       $OtherDownloads=0;
                                       $StudyMaterial=0;
                                       $Syllbus=0;
                                    @endphp
                                    @foreach ($data  as $item)
                                     @if($item->content_type =="Assignments")
                                        <!--{{$assignment++}}-->
                                     @endif
                                       @if($item->content_type =="Other Downloads")
                                        <!--{{$OtherDownloads++}}-->
                                     @endif
                                      @if($item->content_type =="Study Material")
                                        <!--{{$StudyMaterial++}}-->
                                     @endif
                                      @if($item->content_type =="Syllabus")
                                        <!--{{$Syllbus++}}-->
                                     @endif
                                    @endforeach
                                    @endif
            <div class="col-md-3">
                <a href="{{url('studentAssignments')}}"  class="small-box-footer">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h4>Assignments</h4>
                        <h4>{{ $assignment  }}</h4>
                    </div>
                    <div class="icon">     
                        <i class="fa fa-clipboard"></i>
                    </div>
                    <div class="text-center small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
           </div>
           
            <div class="col-md-3">
                <a href="{{url('student_study_material')}}"  class="small-box-footer">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h4>Study Materials</h4>
                        <h4>{{$StudyMaterial++}}</h4>
                    </div>
                    <div class="icon">     
                        <i class="fa fa-sitemap"></i>
                    </div>
                    <div class="text-center small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
           </div>
           
            <div class="col-md-3">
                <a href="{{url('student_syllabus')}}"  class="small-box-footer">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h4>Syllabus</h4>
                        <h4>{{$Syllbus++}}</h4>
                    </div>
                    <div class="icon">     
                        <i class="fa fa-book"></i>
                    </div>
                    <div class="text-center small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>
           
            <div class="col-md-3">
                <a href="{{url('student_other_downloads')}}"  class="small-box-footer">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h4>Other Downloads</h4>
                        <h4>{{$OtherDownloads++}}</h4>
                    </div>
                    <div class="icon">     
                        <i class="fa fa-cloud-download"></i>
                    </div>
                    <div class="text-center small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
           `</div>
        </div>
    </div>


      <div class="row">
          <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-sitemap"></i> &nbsp;  Certificates</h3>
                    <div class="card-tools">
                    </div>
            
                </div>               
            </div>
            </div> 
        </div

    <div class="card-body"> 
        <div class="row">

            <div class="col-md-3">
                <a href="{{url('cc/form/index')}}" class="small-box-footer">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h4>CC List </h4>
                        <h4>{{$c_certificate_count ?? '0'}}</h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <div class="text-center small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>

            <div class="col-md-3">
                <a href="{{url('evente/certificate/index')}}" class="small-box-footer">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h4>Event List</h4>
                        <h4>{{$event_count ?? '0'}}</h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <div class="text-center small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>
            
            <div class="col-md-3">
                <a href="{{url('sport/certificate/index')}}" class="small-box-footer">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h4>Sports List  </h4>
                        <h4>{{$sports_count ?? '0'}}</h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                <div class="text-center small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>

            <div class="col-md-3">
                <a href="{{url('tc/certificate/index')}}" class="small-box-footer">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h4>TC Form List</h4>
                       <h4>{{$tc_count ?? '0'}}</h4>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <div class="text-center small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>
            
        </div>           
    </div>
      
</div>


  
       

@endsection

