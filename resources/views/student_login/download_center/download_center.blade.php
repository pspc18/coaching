
@php
$getUser = Helper::getUser();
@endphp
@extends('student_login.layout.app')
@section('title', 'Download Center')
@section('page_title', 'DOWNLOAD CENTER')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])
@section('content')
<section class="common-page">
 <div class="common-box m-2 border-0">

      
            <div class="dashboard-grid mt-3">
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
           
                <a href="{{url('studentAssignments')}}"  class="dash-card bg-primary p-2">
               
                    <div class="inner">
                        <h6>Assignments</h6>
                        <h6>{{ $assignment  }}</h6>
                    </div>
                    <div class="icon">     
                        <i class="fa fa-clipboard"></i>
                    </div>
                   
                </a>
          
           
          
                <a href="{{url('student_study_material')}}"  class="dash-card bg-success p-2">
               
                    <div class="inner">
                        <h6>Study Materials</h6>
                        <h6>{{$StudyMaterial++}}</h6>
                    </div>
                    <div class="icon">     
                        <i class="fa fa-sitemap"></i>
                    </div>
                   
                </a>
         
           
            
                <a href="{{url('student_syllabus')}}"  class="dash-card bg-danger p-2">
               
                    <div class="inner">
                        <h6>Syllabus</h6>
                        <h6>{{$Syllbus++}}</h6>
                    </div>
                    <div class="icon">     
                        <i class="fa fa-book"></i>
                    </div>
                   
               </a>
           
           
            
                <a href="{{url('student_other_downloads')}}"  class="dash-card bg-warning p-2">
              
                    <div class="inner">
                        <h6>Other Downloads</h6>
                        <h6>{{$OtherDownloads++}}</h6>
                    </div>
                    <div class="icon">     
                        <i class="fa fa-cloud-download"></i>
                    </div>
                   
                </a>
           
        </div>
        </div>
   
</section>
  

  
       

@endsection

