@php
  $getExamType = Helper::getExamType();
  $getsubject = Helper::getSubject();;
$studentexamview = Helper::studentexamview();
@endphp
@extends('layout.app')
@section('content')

<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="nav-icon fas fa fa-leanpub"></i> &nbsp; {{ __('messages.Add Exam') }}</h3>
                    <div class="card-tools">
                    <a href="{{url('view_exam_result')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-eye"></i> {{ __('messages.View') }} </a>
                    <a href="{{url('examination_dashboard')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-arrow-left"></i> {{ __('messages.Back') }} </a>
                    </div>
                    
                    </div>        
                <form id="quickForm" action="{{ url('add_exam_result') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row m-2">

                       <div class="col-md-3">
                            <div class="form-group">
                                <label style="color:red;">{{ __('messages.Exam Name') }}*</label>
                                <select class="select2 form-control" name="exam_id" id="exam_id" onchange="showDiv(this)">
                                    <option value="">Select</option>
                                    @if(!empty($studentexamview))
                                    @foreach($studentexamview as $item)
                                    <option value="{{ $item->id ?? ''  }}" {{ ( $item['id'] == 3 ?? '' ) ? 'hidden' : '' }}>{{ $item->name ?? ''  }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>  
                    </div>  
                       
                          <div id="reason" style="display:none;">
                <div class="row m-2">
            		 <div class="col-md-3 col-12">
            		    <div class="form-group">
                            <label class="text-danger">Upload File*</label>
                            <input class="form-control" type="file" id="upload_contant" name="upload_contant" placeholder="Delivery Charge Amoun" onkeypress="javascript:return isNumber(event)">
                        </div>
                    </div>
                   
            		 <div class="col-md-3 col-12" >
            		     <label class="text-danger">&nbsp;</label>
            		     <br>
                    <button class="btn btn-danger w-100" id="downloadExcel" type="button" data-link="schoolimage/common_images/Student Grow Sheet.csv"><i class="fa fa-download"></i> Download Excel</button>
                    </div>
                    
                     <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('Exam Date') }}*</label>
                                <input type="date" class="form-control"name="date" id="date">
                            </div>
                        </div>
                    
		        </div>
            </div>
                <div class="row m-2 pb-2">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary ">{{ __('messages.Submit') }}</button>
                    </div>
                </div>
                
            
            </form>
</div>
</div>
</div>
</div>
</section>
</div>
<script type="text/javascript">
function showDiv(select){
   if(select.value==1){
    document.getElementById('reason').style.display = "block";
   } else{
    document.getElementById('reason').style.display = "none";
   }
} 
function showDiv(select){
   if(select){
    document.getElementById('reason').style.display = "block";
   } else{
    document.getElementById('reason').style.display = "none";
   }
} 
</script>
<script>
    $("#downloadExcel").click(function(){
        var excel_link = $(this).data('link');
        window.location.href = excel_link;
    })
</script>
@endsection