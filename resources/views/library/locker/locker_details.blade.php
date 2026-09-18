@extends('layout.app') 
@section('content')

<div class="content-wrapper">

   <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">      
    <div class="card card-outline card-orange">
        <div class="card-header bg-primary">
        <h3 class="card-title"><i class="fa fa-money"></i> &nbsp;{{ __('Locker Details') }}</h3>
        <div class="card-tools">
                <a href="{{url('dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
        </div>
        
        </div>  

    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
                <div class="legend">
                    <span class="legend-item">
                        <img src="https://www.walsisindia.com/library/images/safe-occupied.png" alt="Locker Occupied">
                        Locker Occupied
                    </span>
                    <span class="legend-item">
                        <img src="https://www.walsisindia.com/library/images/safe-available.png" alt="Locker Available">
                        Locker Available
                    </span>
                </div>
                
                <div id="locker_details mt-2">
                    {!! $lockers ?? '' !!}    
                </div>
            </div>
        </div>
    </div>
    </div>
  </div>
</div>
</section>
</div>

<div class="modal fade" id="details_modal">
<div class="modal-dialog modal-dialog-centered">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title">Student Details</h4>
      <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
    </div>
    
    <div class="modal-body">
        <p><b>Name :- </b> <span id="stu_name"></span></p>
        <p><b>Admission No :- </b> <span id="stu_admissionNo"></span></p>
    </div>
 
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
    
  </div>
</div>
</div>

<script>
    $(document).on('click','.booked_student',function(){
        var student_name = $(this).data('name'); 
        var student_admissionNo = $(this).data('admission_no'); 
        
        $('#stu_name').html(student_name);
        $('#stu_admissionNo').html(student_admissionNo);
        $('#details_modal').modal('show');
    });
</script>
@endsection 