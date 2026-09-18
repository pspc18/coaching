@php
    $getHostel = Helper::getHostel();
    $getHostelFloor = Helper::getHostelFloor();
    $getHostelBuildingAll = Helper::getHostelBuildingAll();
    $getHostelRoom = Helper::getHostelRoom();
    $getHostelBed = Helper::getHostelBed();
  
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
							<h3 class="card-title"><i class="fa fa-users"></i> &nbsp; {{ __('hostel.View Hostel Students') }}</h3>
							<div class="card-tools"> 
							    <a href="{{url('hostel_assign')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i>{{ __('common.Add') }}  </a> 
							    <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
							</div>
						</div>
                
                            <div class="row m-2">
                                <div class="col-md-12">
                                    <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                        <thead>
                                           
                                            <tr role="row">
                                                <th>{{ __('common.SR.NO') }}</th>
                                                <th>{{ __('hostel.Student Name') }}</th>
                                                <th>{{ __('Fees') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="student_list_show">
            
                                            @if(!empty($data))
                                            
                                            @php
                                            
                                                $i=1
                                            @endphp
            
                                            @foreach ($data  as $item)
                                            
                                           
                                           <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $item['first_name']  }} {{ $item['last_name']  ?? ''}}</td>
                                               <td>{{ $item['hostel_fees'] ?? ''}}</td>
                                                                                 
                                            </tr>
                                            @endforeach
                                            
                                            @else
                                            <tr><td colspan="12" class="text-center">{{ __('hostel.No Student Found') }} !</td></tr>
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


<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  var admission_id = $(this).data('admission_id'); 
  
  $('#admission_id').val(admission_id); 
  $('#delete_id').val(delete_id); 
  } );
</script>
<script>
$('#hostel_id').on('change', function(e){
     var basurl = "{{ url('/') }}";
	var hostel_id = $(this).val();
    $.ajax({
         headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
	  url: basurl+'/hostelData/'+hostel_id,
	  success: function(data){
	     if(data != ''){
	         	$(".building_id").html(data);
	     }else{
	         	$(".building_id").html(data);
	         alert('Building Not Found');
	     }
	  }
	});
});

$('#building_id').on('change', function(e){
     var basurl = "{{ url('/') }}";
	var building_id = $(this).val();
    $.ajax({
         headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
	  url: basurl+'/BuildingData/'+building_id,
	  success: function(data){
	     if(data != ''){
	         	$(".floor_id").html(data);
	     }else{
	         	$(".floor_id").html(data);
	         alert('Floor Not Found');
	     }
	  }
	});
});

$('#floor_id').on('change', function(e){
     var basurl = "{{ url('/') }}";
	var floor_id = $(this).val();
    $.ajax({
         headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
	  url: basurl+'/FloorData/'+floor_id,
	  success: function(data){
	     if(data != ''){
	         	$(".room_id").html(data);
	     }else{
	         	$(".room_id").html(data);
	         alert('Room Not Found');
	     }
	  }
	});
});



    function SearchValue() {
        var basurl = "{{ url('/') }}";
        var hostel_id = $('#hostel_id :selected').val();
        var building_id = $('.building_id :selected').val();
        var floor_id = $('.floor_id :selected').val();
        var room_id = $('.room_id :selected').val();
        var bed_id = $('.bed_id :selected').val();
        if(hostel_id > 0 || building_id > 0 || floor_id > 0 || room_id > 0 || bed_id > 0){
        $.ajax({
                 headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            type:'post',
            alert("data");
            url: basurl+'/hostel_student_search',
            data: {hostel_id:hostel_id,building_id:building_id,floor_id:floor_id,room_id:room_id,bed_id:bed_id},
             //dataType: 'json',
            success: function (data) {

                $('#student_list_show').html(data);
               
            }
          });
        }else{
                alert('Please put a value in minimum one column !');
            }               
    };
</script>

                        <!-- The Modal -->
                        <div class="modal" id="Modal_id">
                          <div class="modal-dialog">
                            <div class="modal-content" style="background: #555b5beb;">
                        
                              <div class="modal-header">
                                <h4 class="modal-title text-white">{{__('common.Delete Confirmation') }}</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                              </div>
                        
                              <form action="{{ url('hostel_student_delete') }}" method="post">
                                      	 @csrf
                              <div class="modal-body">
                                      <input type=hidden id="delete_id" name="delete_id">
                                      <input type=hidden id="admission_id" name="admission_id">
                                      <h5 class="text-white">{{__('common.Are you sure you want to delete') }}  ?</h5>
                              </div>
                              <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{__('common.Close') }}</button>
                                            <button type="submit" class="btn btn-danger waves-effect waves-light">{{__('common.Delete') }}</button>
                                 </div>
                               </form>
                            </div>
                          </div>
                        </div>
                        
                 
    <script>
        $('#hostel_id').on('change', function(e){
     var basurl = "{{ url('/') }}";
	var hostel_id = $(this).val();
    $.ajax({
         headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
		  url: basurl+'/hostelData/'+hostel_id,
	  success: function(data){
			$("#building_id").html(data);
	  }
	});
	
});    

$('#building_id').on('change', function(e){
     var basurl = "{{ url('/') }}";
	var building_id = $(this).val();
    $.ajax({
         headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
	 	  url: basurl+'/BuildingData/'+building_id,
	  success: function(data){
			$("#floor_id").html(data);
	  }
	});
	
});
$('#floor_id').on('change', function(e){
     var basurl = "{{ url('/') }}";
	var floor_id = $(this).val();
    $.ajax({
         headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
	 		  url: basurl+'/FloorData/'+floor_id,
	  success: function(data){
			$("#room_id").html(data);
	  }
	});
	
});
$('#room_id').on('change', function(e){
     var basurl = "{{ url('/') }}";
	var room_id = $(this).val();
    $.ajax({
         headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
	 	  url: basurl+'/RoomData/'+room_id,
	  success: function(data){
			$("#bed_id").html(data);
	  }
	});
	
});
    </script>
@endsection      