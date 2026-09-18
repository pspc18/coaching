
@php
  $classType = Helper::classType();
  $getSection = Helper::getSection();
@endphp
@extends('layout.app') 
@section('content')

 <div class="content-wrapper">
    
     <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">   
    <div class="card card-outline card-orange">
         <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-flask"></i> &nbsp; View Homework Assignments</h3>
            <div class="card-tools">
          <a href="{{url('homework/index')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
          </div>
            
            </div>        
            
        
        @if(Session::get('role_id') !== 3)
         <form id="quickForm" action="{{ url('homework/details') }}/{{ $id ?? '' }}" method="post" >
                        @csrf 
                    <div class="row m-2">
            		<div class="col-md-5">
            			<div class="form-group">
            				<label>Search By Keywords</label>
            				<input type="text" class="form-control" id="name" name="name" placeholder="Ex. Name, Mobile, Email, Aadhaar, Father/ Mother Name etc." value="{{ $search['name'] ?? '' }}">
            		    </div>
            		</div>                     	
                        <div class="col-md-1 ">
                             <label class="text-white">Search</label>
                    	    <button type="submit" class="btn btn-primary" >Search</button>
                    	</div>
                   			
                    </div>
                </form>
        
             @endif
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
				<table id="example1" class="table table-bordered table-striped  dataTable">
					<thead>
						<tr role="row">
						    <th>Sr. No.</th>
							<th>Class</th>
							<th>Date</th>
							<th>Name</th>
							<th>Father Name</th>
							<th class="text-center">Homework Status</th>
							<th>Action</th>
					</thead>
					<tbody>
    					@if(!empty($students)) 
    						@php 
    						    $i=1 
    						@endphp 
    						
    					@foreach ($students as $type)
    					@php
                                $count = Helper::count($type->id);
                                @endphp
    						<tr>
    						    
    						    <td>{{ $i++ }}</td>
    							<td>{{ $type['ClassType']['name'] ?? '' }} ({{ $type['Section']['name'] ?? '' }})</td>
    							<td>{{ $type['submission_date'] ?? '' }}</td>
    							<td>{{ $type['Admission']['first_name'] ?? '' }} {{ $type['Admission']['last_name'] ?? '' }}</td>
    							<td>{{ $type['Admission']['father_name'] ?? '' }}</td>
    							<td class="text-center"><small class="badge badge-{{  $count == 0 ? 'success'   : 'danger'  }}"><i class="fa fa-{{  $count == 0 ? 'check'   : 'clock'  }}"></i>  {{  $count == 0 ? 'Checked'   : $count.' Unchecked'  }}</small></td>
                                <td>
                                    <button title="View Assignments" data-admission_id="{{ $type['admission_id'] }}" class="btn btn-primary btn-xs viewModal"><i class="fa fa-reorder"></i></button>
                                </td>                                                
                           </tr>
    					@endforeach 
    					@endif 
				    </tbody>	
				</table> 
        </div>
        </div>
    </div>
</div>
</div>
</div>
</section>

</div>


<!-- The Modal -->
<div class="modal" id="viewModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-header">
        <h4 class="modal-title">Student Name : <span id="fillStuName"></span></h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>
      <div class="modal-body" id="homework_list">
      
      </div>
      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect waves-light" data-bs-dismiss="modal">Close</button>
         </div>
    </div>
  </div>
</div>








<script>

$(document).on('click', ".viewModal", function() {
  $('#viewModal').modal('toggle');

    var admission_id = $(this).data("admission_id");
		$.ajax({
			url: '/particular/hw/details',
			type: 'post',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {admission_id: admission_id},
			success: function(data) {
				$('#homework_list').html(data);

                var fillStuName = $('#stuName').data('first_name');
            
                $('#fillStuName').html(fillStuName);			    
			//	toastr.error(data);
			},
		});

}); 

$( document ).ready(function() {  
    $('#viewModal').modal({
        backdrop: 'static',
        keyboard: false
    })
});

$(document).on('click', ".submitReview", function () {
    var submit_id = $(this).data('submit');
  
    var numItems = $('.submit_'+submit_id).length
   
    var key_id = parseInt(numItems);
    var newData = "";
    var newData_id = "";
    var review = [];
    var id = [];
    
    for(var i = 0; i < key_id; i++){
         newData = $('.submit_'+submit_id).eq( i ).val();
         newData_id = $('.submit_'+submit_id).eq( i ).data("id");
         review[i]= newData;
         id[i]= newData_id;
       //  toastr.error(newData_value);
    }
     
    var data = {
                'review': review,
                'id': id,
                  }
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
            });
           $.ajax({
                type: "POST",
                url: "/evaluate/homework",
                data: data,
              //  dataType: "html",
                success: function (response) {
                toastr.success('Review Submitted Successfully.');
                },

            });
   
});

$(document).on('click', "#resendEmail", function () {

        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
            });
           $.ajax({
                type: "POST",
                url: "/upload/homework/resend",
                success: function (response) {
                toastr.success('E-mail Resent Successfully.');
                },

            });
   
});
</script>  

@endsection 