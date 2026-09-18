
@php
  $classType = Helper::classType();
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
            <h3 class="card-title"><i class="fa fa-flask"></i> &nbsp;{{ __('My Homework Assignments') }} </h3>
            <div class="card-tools">
          <a href="{{url('student_homework')}}" class="btn btn-primary  btn-xs "><i class="fa fa-arrow-left"></i> <span class="" >{{ __('Back') }} </span></a>   
          </div>
            
            </div>        
            
    
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
				<table id="" class="table table-bordered table-striped  dataTable">
					<thead>
						<tr role="row">
						    <th>{{ __('messages.Sr.No.') }}</th>
							<th>{{ __('messages.Date') }}</th>
							<!--<th>{{ __('messages.Name') }}</th>-->
							<!--<th>{{ __('messages.Fathers Name') }}</th>-->
							<th class="text-center">{{ __('homework.Homework Status') }}</th>
							<th>{{ __('messages.Action') }}</th>
					</thead>
					<tbody>
    					@if(!empty($students)) 
    						@php 
    						    $i=1 ;
    						  //  dd($students);
    						@endphp 
    						
    					@foreach ($students as $type)
    					 
    					@php
                          $value="";
                          $count = Helper::count($type->id);
                                @endphp
    						<tr>
    						    
    						    <td>{{ $i++ }}</td>
    							
    							
    							<td>{{date('d-m-Y', strtotime($type['submission_date'])) ?? '' }}</td>
    							<!--<td>{{ $type['Admission']['first_name'] ?? '' }} {{ $type['Admission']['last_name'] ?? '' }}</td>-->
    							<!--<td>{{ $type['Admission']['father_name'] ?? '' }}</td>-->
    							   @php
                                $value = Helper::homework_check($type['id'],$type['admission_id'],$type['homework_id']);
                                @endphp
    						      	<td class="text-center"><small class="badge badge-{{  ($value[0]-$value[1]) == 0 ? 'success'   : 'danger'  }}"><i class="fa fa-{{  ($value[0]-$value[1]) == 0 ? 'check'   : 'clock'  }}"></i>  {{ ($value[0]-$value[1]) == 0 ? 'Checked' : $value[1].' Checked out of '.$value[0]  }}</small></td>
                                <td>
                                    <button title="View Assignments" data-homework_id="{{ $type['homework_id'] }}" data-admission_id="{{ $type['admission_id'] }}" class="btn btn-primary btn-xs viewModal"><i class="fa fa-reorder"></i></button>
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
<!-- The Modal -->
<div class="modal" id="viewModal2">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-header">
      <h3>Uploaded Assignment View dsfas</h3>
      </div>
      <div class="modal-body" id="homework_list_view">
     <main class="main">
  <div class="carousel">
    <button type="button" class="carousel_btn jsPrev" aria-label="Previous slide">
      <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
      </svg>
    </button>

    <div class="carousel_track-container">
      <ul class="carousel_track">
       
      </ul>
    </div>

    <button type="button" class="carousel_btn jsNext" aria-label="Next slide">
      <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
      </svg>
    </button>
  </div>
</main>
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
 var basurl = "{{ url('/') }}";
    var admission_id = $(this).data("admission_id");
    var homework_id = $(this).data("homework_id");
		$.ajax({
			url: basurl+'/particular/hw/details',
			type: 'post',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {admission_id: admission_id,homework_id:homework_id},
			success: function(data) {
				$('#homework_list').html(data);

                var fillStuName = $('#stuName').data('first_name');
            
                $('#fillStuName').html(fillStuName);			    
			//	toastr.error(data);
			},
		});

}); 

$(document).on('click', ".viewModal2", function() {
  $('#viewModal2').modal('toggle');

     $(".carousel_track").html("");
    var upload_id = $(this).data("upload_id");
   // alert(upload_id);
  var id_count = $(".viewModal_"+upload_id).length;
  var select_item="is-selected";
  var item = 0;
  var count = 0;
   for(var i=0; i<parseInt(id_count); i++)
   {
       
       count++;
       if(item==0)
       
   {
       item++;
   }else
   {
       select_item=" ";
   }
     
        
       var src=$( ".viewModal_"+upload_id ).eq( i ).data("href");
     //  $("#homework_list_view").append('<img src="'+src+'"  style="width:100%">');
       $(".carousel_track").append('<li class="carousel_slide '+select_item+'"><div class="carousel_image"><img src="'+src+'" role="presentation"></div></li>');
   }
   if(count==1)
   {
       $(".carousel_track").append('<li class="carousel_slide"><div class="carousel_image"><img src="'+src+'" role="presentation"></div></li>');
   
   }

}); 

</script>

<script>
    $(document).on('click', ".submitReview", function () {
    var submit_id = $(this).data('submit');
  var basurl = "{{ url('/') }}";
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
                url: basurl+"/evaluate/homework",
                data: data,
              //  dataType: "html",
                success: function (response) {
                toastr.success('Review Submitted Successfully.');
                },

            });
   
});
</script>


<script>


$( document ).ready(function() {  
    $('#viewModal').modal({
        backdrop: 'static',
        keyboard: false
    })
});



$(document).on('click', "#resendEmail", function () {
 var basurl = "{{ url('/') }}";
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
            });
           $.ajax({
                type: "POST",
                url: basurl"/upload/homework/resend",
                success: function (response) {
                toastr.success('E-mail Resent Successfully.');
                },

            });
   
});
</script>  


<style>
    .main {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 95vh;
}

.carousel {
  --color-default-state: #ddd;
  --color-active-state: #1bb9ed;
  position: relative;
  width: 80vw;
  height: 80vmin;
}

.carousel_track-container {
  overflow: hidden;
  width: 100%;
  height: 100%;
}

.carousel_track {
  position: relative;
  width: inherit;
  height: inherit;
  margin: 0;
  padding: 0;
  list-style: none;
}

.carousel_slide,
.carousel_image {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  margin: 0;
  padding: 0;
}

.carousel_slide {
  padding: 5% 12% 8%;
  text-align: center;
  transform: translateX(-100%);
  transition: transform .3s ease-out;
}

.carousel_slide.is-selected {
  transform: translateX(0);
}

.carousel_slide.is-selected ~ .carousel_slide {
  transform: translateX(100%);
}

.carousel_image {
  z-index: -1;
}

.carousel_image > img {
 max-width: 100%;
    max-height: 100%;
  border: none;
}

.carousel_title {
  font-size: 40px;
  color: #fff;
}

.carousel_btn,
.carousel_dot {
  z-index: 1;
  padding: 0;
  cursor: pointer;
  border: none;
}

.carousel_btn {
  position: absolute;
  top: 50%;
  background-color: transparent;
  transform: translateY(-50%);
}

.carousel_btn:focus,
.carousel_dot:focus,
.carousel_btn:active,
.carousel_dot:active {
  outline: none;
}

.carousel_btn > * {
  pointer-events: none;
}

.carousel_btn svg {
  fill: var(--color-default-state);
  transform-origin: center;
  transition: fill .2s;
}

.carousel_btn:hover svg {
  fill: var(--color-active-state);
}

.jsPrev {
  left: 10px;
}

.jsNext {
  right: 10px;
}

.jsPrev svg {
  transform: rotate(-90deg);
}

.jsNext svg {
  transform: rotate(90deg);
}

.carousel_nav {
  position: absolute;
  bottom: 10px;
  left: 50%;
  transform: translateX(-50%);
}

.carousel_dot {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background-color: var(--color-default-state);
  transition: background-color .2s;
}

.carousel_dot + .carousel_dot {
  margin-left: 20px;
}

.carousel_dot.is-selected {
  background-color: var(--color-active-state);
}

</style>
@endsection 