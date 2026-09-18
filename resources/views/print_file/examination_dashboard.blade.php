@extends('layout.app') 
@section('content')
@php
$question_count = Helper::getCount('questions','id','count');
$exam_count = Helper::getCount('exams','id','count');
@endphp
<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                    @if(Session::get('role_id') == 3)
                        <h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp;{{ __('examination.My Examination dgf') }} </h3>
                    @else
                        <h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp;{{ __('examination.Examination Management') }}</h3>
                    @endif
                    
                    <div class="card-tools">
                        <!--<a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add User</a>-->
                    </div>
            
                </div>               
            </div>
            </div>
            <div class="col-md-3 col-6">
                <a href="{{ url('view/exam')}}" class="small-box-footer">
                <div class="small-box bg-success">
                    <div class="inner">
                    <h4 class="mobile_text_title">{{ __('examination.Exams') }}</h4>
                    <h4 class="mobile_text_title">{{\App\Models\exam\Exam::countExam() ?? '0' }}</h4>
                    </div>
                <div class="icon">
                    <i class="fa fa-leanpub"></i>
                </div>
                    <div class="text-center small-box-footer">{{ __('messages.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>
               <div class="col-md-3 col-6">
                <a href="{{ url('add/examination_schedule')}}" class="small-box-footer">
                <div class="small-box bg-danger">
                    <div class="inner">
                    <h4 class="mobile_text_title">Examination Schedule</h4>
                     <h4 class="mobile_text_title">&nbsp;</h4>
                    </div>
                <div class="icon">
                    <i class="fa fa-file-text"></i>
                </div>
                    <div class="text-center small-box-footer">{{ __('messages.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>
              <div class="col-md-3 col-6">
                <a href="{{ url('add/admit_card')}}" class="small-box-footer">
                <div class="small-box bg-info">
                    <div class="inner">
                    <h4 class="mobile_text_title">Admit Card</h4>
                     <h4>&nbsp;</h4>
                    </div>
                <div class="icon">
                    <i class="fa fa-file-text"></i>
                </div>
                    <div class="text-center small-box-footer">{{ __('messages.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>
              <div class="col-md-3 col-6">
                <a href="{{ url('add/admit_card')}}" class="small-box-footer">
                <div class="small-box bg-primary">
                    <div class="inner">
                    <h4 class="mobile_text_title">Download Admit Card</h4>
                     <h4>&nbsp;</h4>
                    </div>
                <div class="icon">
                    <i class="fa fa-file-text"></i>
                </div>
                    <div class="text-center small-box-footer">{{ __('messages.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>
            <!--  <div class="col-md-3">
                <a href="{{ url('view_exam_result')}}" class="small-box-footer">
                <div class="small-box bg-primary">
                    <div class="inner">
                    <h4>{{ __('examination.Exam Result') }}</h4>
                    <h4>{{\App\Models\StudentGrow::countExamResult() ?? '0' }}</h4>
                    </div>
                <div class="icon">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                    <div class="text-center small-box-footer">{{ __('messages.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>-->
              <div class="col-md-3 col-6">
                <a href="{{ url('fill_marks')}}" class="small-box-footer">
                <div class="small-box bg-warning">
                    <div class="inner">
                    <h4 class="mobile_text_title">Fill Marks</h4>
                    <h4 class="mobile_text_title">&nbsp;</h4>
                    </div>
                <div class="icon">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                    <div class="text-center small-box-footer">{{ __('messages.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>
              <div class="col-md-3 col-6">
                <a href="{{ url('download_marksheet')}}" class="small-box-footer">
                <div class="small-box bg-primary">
                    <div class="inner">
                    <h4 class="mobile_text_title">Download Marksheet</h4>
                    <h4>&nbsp;</h4>
                    </div>
                <div class="icon">
                    <i class="fa fa-bar-chart-o"></i>
                </div>
                    <div class="text-center small-box-footer">{{ __('messages.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>
           
                       
           
            
            

            

      </div>
    </div>
    </section>

</div>
@endsection
  
