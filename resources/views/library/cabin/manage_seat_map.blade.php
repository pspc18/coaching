@extends('layout.app') 
@section('content')

<style>
    .heading_text p{
        font-size:20px;
        text-align: center;
        text-transform: capitalize;
    }    
    
    #time_slot_name{
        color:#6639b5;
        font-weight:600;
    }
    
    #time_slot_name2{
        color:#6639b5;
        font-weight:600;
    }
    
    #seat_name_main{
        font-weight: 600;
        color: blue;
    }
    
    #student_details{
        font-weight: 600;
        color: green;
    }
    
    .seat_name_main{
        font-weight: 600;
        color: blue;
    }
    
    .label{
        width: 100%;
        text-align: center;
        font-size: 14px;
        font-weight: 400;
    }
    
    .flex_btn{
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top:10px;
        margin-bottom:10px;
    }
    
    .buttons{
        border: none;
        padding: 5px 20px;
        margin: 5px 2px;
        border-radius: 6px;
        color: white;
        font-weight: 600;   
    }
    
    .close_btn{
        background-color:red;
    }
    
    .success_btn{
        background-color:green;
    }
    
</style>

<div class="content-wrapper">

   <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">      
    <div class="card card-outline card-orange">
        <div class="card-header bg-primary">
        <h3 class="card-title"><i class="fa fa-money"></i> &nbsp;{{ __('Seat Details') }}</h3>
        <div class="card-tools">
                <a href="{{url('library_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
        </div>
        
        </div>  
     
         <form id="quickForm" action="{{ url('manage_seat_map') }}" method="post" >
                        @csrf 
                    <div class="row m-2">
                        <div class="col-md-12 p-0">
                        <div class="col-md-4">
    						<div class="form-group">
    							<label>Select Slot:</label>
    							<select class="form-control select2" name="time_slot_id" id="time_slot_id" onchange="this.form.submit()">
    								<option value="">{{ __('common.Select') }}</option>
    								@if(!empty($time_slot))
        								@foreach($time_slot as $item)
        								<option value="{{ $item->id ?? ''  }}" {{ ($item->id == $search['time_slot_id']) ? 'selected' : '' }}>{{ $item->study_time ?? ''  }}</option>
        								@endforeach
    								@endif
    							</select>
    						</div>
    					</div>
					</div>
                    </div>
                </form>        

    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
                <div class="legend">
                <span class="legend-item">
                    <i class="fa fa-seat"></i>
                    <img src="https://www.walsisindia.com/library/images/car-seat-occupied.png" alt="Seat Alloted">
                    Seat Alloted
                </span>
                <span class="legend-item">
                    <img src="https://www.walsisindia.com/library/images/car-seat-available.png" alt="Seat Available">
                    Seat Available
                </span>
                </div>
                <div id="seatMapContainer">
                    {!! $cabins ?? '' !!}    
                </div>
            </div>
        </div>
    </div>
    </div>
  </div>
</div>
</section>
</div>


<div class="modal fade" id="seat_assign">
<div class="modal-dialog modal-dialog-centered modal-sm">
  <div class="modal-content">
    <div class="modal-body">
        <form action="{{ url('blank_seat_assign') }}" method="post">
            @csrf
        <div class="heading_text">
            <p>Select User to Assign Seat in slot <span id="time_slot_name"></span></p>
            
            <div class="col-md-12 text-center">
                <div class="seat seat_not_assigned" id="seat_name_name">
                    
                </div>
            </div>
            
            <div class="col-md-12 mt-2 mb-2">
                <div class="form-group">
                    <label class="label">Select User to Assign Seat</label>
                    <input type="hidden" name="library_cabin_id" id="library_cabin_id">
                    <select class="form-control select2" name="library_plan_id" id="library_assign_id">
                        <option value="">Select</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex_btn">
            <button type="button" class="buttons close_btn" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="buttons success_btn">Submit</button>
        </div>
        </form>
    </div>
  </div>
</div>
</div>
<div class="modal fade" id="seat_unassign">
<div class="modal-dialog modal-dialog-centered modal-sm">
  <div class="modal-content">
    <div class="modal-body">
        <form action="{{ url('seat_unassign') }}" method="post">
            @csrf
        <div class="heading_text">
            <p>Are you sure you want to delete Seat <span id="seat_name_main"></span> for <span id="student_details"></span> in slot <span id="time_slot_name2"></span>?
            <div class="col-md-12 text-center">
                <div class="seat assigned" id="seat_name_text">
                    
                </div>
                
                <input type="hidden" name="library_plan_id" id="planId">
            </div>
        </div>
        <div class="flex_btn">
            <button type="button" class="buttons close_btn" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="buttons success_btn">Yes, Delete it !</button>
        </div>
        </form>
    </div>
  </div>
</div>
</div>

<script>

    $(document).on('click','.booked_seat_data',function(){
       var seat_id = $(this).data('seat_id'); 
       var seat_name = $(this).data('seat_name'); 
       var student_name = $(this).data('name');
       var plan_id = $(this).data('plan_id');
       var time_slot_name = $(this).data('time_slot_name');
       
       unAssignSeat(seat_id,seat_name,student_name,plan_id,time_slot_name);
    });

    function unAssignSeat(seat_id,seat_name,student_name,plan_id,time_slot_name){
        var time_slot_id = $('#time_slot_id').val();
        // var time_slot_name = $('#time_slot_id').find('option:selected').html();
        
        $('#time_slot_name2').html(time_slot_name);
        $('#planId').val(plan_id);
        $('#seat_name_main').html("S-" + seat_name);
        $('#seat_name_text').html("S-" + seat_name);
        $('#student_details').html(student_name);
        $('#seat_unassign').modal('show');
    }

    function assignSeat(seat_id,seat_name){
        var time_slot_id = $('#time_slot_id').val();
        var time_slot_name = $('#time_slot_id').find('option:selected').html();
        $('#time_slot_name').html(time_slot_name);
        $('#library_cabin_id').val(seat_id);
        $('#seat_name_name').html("S-" + seat_name);
        var BASEURL = "{{ url('/') }}";
        
        $.ajax({
            headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            type:'post',
            url: BASEURL + '/get_unassigned_users',
            data: {time_slot_id : time_slot_id, seat_id : seat_id},
            dataType: 'json',
            success: function (data) {
                if(data.length != 0){
                    var users = [];
                    
                    users.push("<option value=''>Select</option>");
                    for(var i = 0; i < data.length; i++){
                        var code = "<option value='"+ data[i].id +"'>"+ data[i].first_name +"</option>";
                        users.push(code);
                    }
                    
                    $('#library_assign_id').html(users);
                }
                $('#seat_assign').modal('show');
            }
        });
    }
</script>
@endsection 