@php
   $getHostel = Helper::getHostel();
    $getHostelFloor = Helper::getHostelFloor();
    $getHostelBuildingAll = Helper::getHostelBuildingAll();
    $getHostelRoom = Helper::getHostelRoom();
    $getHostelBed = Helper::getHostelBed();
    $getMonth = Helper::getMonth();
$getPaymentMode = Helper::getPaymentMode();
@endphp
@extends('layout.app')
@section('content')
<div class="content-wrapper">
    
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card card-outline card-orange mb-0">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-money"></i> &nbsp;{{ __('hostel.Electricity Bill Payment') }}</h3>
                            <div class="card-tools">
                                <a href="{{url('hostel_fees_electricity_view')}}" class="btn btn-primary  btn-sm" title="View Fees"><i class="fa fa-eye"></i>{{ __('messages.View') }} </a>
                                <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
                            </div>

                        </div>
                        <div class"card-body">
                            <form id="quickForm" action="{{ url('electricity_bill_payment_add') }}" method="post" enctype='multipart/form-data'>
                              @csrf
                            <div class="row m-2">
                                <div class="col-md-2">
                        			<label>{{ __('hostel.Select Hostel') }}</label>
                    				<select class=" form-control" id="hostel_id" name="hostel_id">
                                        <option value="">{{ __('common.Select') }}</option>
                                     @if(!empty($getHostel)) 
                                          @foreach($getHostel as $type)
                                             <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['hostel_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                          @endforeach
                                      @endif
                                    </select>
                            	</div>    
                                <div class="col-md-2">
                        			<label>{{ __('hostel.Select Building') }}</label>
                    				<select class=" form-control building_id" id="building_id" name="building_id">
                                        <option value="">{{ __('common.Select') }}</option>
                                          @if(!empty($getHostelBuildingAll)) 
                                          @foreach($getHostelBuildingAll as $type)
                                             <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['building_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                          @endforeach
                                      @endif
                                    </select>
                            	</div>  
                                <div class="col-md-2">
                        			<label>{{ __('hostel.Select Floor') }}</label>
                    				<select class=" form-control floor_id" id="floor_id" name="floor_id">
                                        <option value="">{{ __('common.Select') }}</option>
                                          @if(!empty($getHostelFloor)) 
                                          @foreach($getHostelFloor as $type)
                                             <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['floor_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                          @endforeach
                                      @endif
                                    </select>
                            	</div>   
                                <div class="col-md-2">
                        			<label>{{ __('hostel.Select Room') }}</label>
                    				<select class=" form-control room_id" id="room_id" name="room_id">
                                        <option value="">{{ __('common.Select') }}</option>
                                          @if(!empty($getHostelRoom)) 
                                          @foreach($getHostelRoom as $type)
                                             <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['room_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                          @endforeach
                                      @endif
                                    </select>
                            	</div>  
                            <!--    <div class="col-md-2">
                        			<label>{{ __('Select Bed') }}</label>
                    				<select class=" form-control bed_id" id="bed_id" name="bed_id">
                                        <option value="">{{ __('messages.Select') }}</option>
                                          @if(!empty($getHostelBed)) 
                                          @foreach($getHostelBed as $type)
                                             <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['bed_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                          @endforeach
                                      @endif
                                    </select>
                            	</div>           -->     	
                            <!--<div class="col-md-2">
                        			<label>{{ __('hostel.Select Month') }}</label>
                    				<select class=" form-control month_id" id="month_id" name="month_id">
                                        <option value="">{{ __('common.Select') }}</option>
                                         @if(!empty($getMonth)) 
                                          @foreach($getMonth as $key=> $type)
                                             <option value="{{$key > 8 ? '' : 0 }}{{ $type->id ?? ''  }}" {{ ($type->id == $search['month_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                          @endforeach
                                      @endif
                                    </select>
                            	</div> -->         	
                                <div class="col-md-1 text-center">
                                    <label class="text-white">Search</label>
                                    <button type="submit"class="btn btn-primary" >{{ __('common.Search') }}</button>
                                </div>
                            </div> 
                        </form>
                            @if(!empty($data))
                            <div class="row m-2" style="max-height: 225px;overflow-y: scroll;">
                                <table class="table table-bordered small_td" id="trColor">
                                    <thead>
                                        <tr>
                                            <th>{{ __('common.SR.NO') }}</th>
                                            <th class="text-center">{{ __('hostel.Admission No.') }} </th>
                                            <th>{{ __('common.Name') }}</th>
                                            <th>{{ __('common.Fathers Name') }}</th>
                                            <!--<th>Mother Name</th>-->
                                            <th>{{ __('common.Mobile') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $i=1;
                                        //dd($data);
                                        @endphp
                                        @foreach ($data as $item)
                                            <tr class="quickCollect" style="cursor:pointer; " onclick="showData({{ $item['id'] ?? '' }})" >
                                            <td>{{ $i++ }}</td>
                                            <td class="text-center">{{ $item['admissionNo']  ?? ''}}</td>
                                            <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                                            <td>{{ $item['father_name']  ?? ''}}</td>
                                            <!--<td>{{ $item['mother_name'] ?? '' }}</td>-->
                                            <td>{{ $item['mobile'] ?? '' }}</td>
                                        </tr>                                            
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="student_fees_detail"></div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    $('#trColor tr').click(function() {
        $(this).css('backgroundColor', '#6639b5c4');
        $(this).css('color', '#fff');
        $( this ).siblings().css( "background-color", "white" );
        $( this ).siblings().css( "color", "black" );
    });
});

    function showData(hostel_assign_id) {
        
        var hostel_id = $("#hostel_id").val();
        var building_id = $("#building_id").val();
        var floor_id = $("#floor_id").val();
        var room_id = $("#room_id").val();
        var month_id = $("#month_id").val();
 
     
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: '/hostel_fees_onclick',
            data: {
                hostel_assign_id: hostel_assign_id,
                hostel_id :hostel_id,
                building_id :building_id,
                floor_id :floor_id,
                room_id :room_id,
                month_id :month_id
            },
        
            success: function(data) {
                if (data == 0) {
                    alert('Please Assign the Fees for this Class !');
                    window.location.href = "{{ url('fees_master') }}";
                } else {

                    $('.student_fees_detail').html(data);
                }


            }
        });
    };


    function SearchValue() {
         var basurl = "{{ url('/') }}";
        var class_type_id = $('#class_type_id :selected').val();
        var section_id = $('#section_id :selected').val();
        var name = $('#name').val();
        if (section_id > 0 || class_type_id > 0 || name != '') {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: basurl+'/SearchValueStd',
                data: {
                    class_type_id: class_type_id,
                    section_id: section_id,
                    name: name
                },
                //dataType: 'json',
                success: function(data) {
                    $('.student_list_show').html(data);
                }
            });
        } else {
            alert('Please put a value in minimum one column !');
        }

    };
   
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