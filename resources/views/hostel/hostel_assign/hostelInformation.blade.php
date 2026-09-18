    @php
    $getHostel = Helper::getHostel();
    $getHostelFloor = Helper::getHostelFloor();
    $getHostelBuildingAll = Helper::getHostelBuildingAll();
    $getHostelRoom = Helper::getHostelRoom();
    $getHostelBed = Helper::getHostelBed();
   // dd($data);
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
							<h3 class="card-title"><i class="fa fa-info-circle"></i> &nbsp;  Room Availability</h3>
							<div class="card-tools"> 
							    <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
							</div>
						</div>
                            
                            <div class="row m-2">
                                @if(!empty($hostel))
                                @foreach($hostel as $hstl)
                                    @php
                                        $buildingData = $building->where('hostel_id', $hstl->id);
                                    @endphp
                                    <!--<div class="col-md-12 border border-dark">
                                        <p class="mb-0 border-bottom"><i class="fa fa-hospital-o"></i> Hostel : {{ $hstl->name ?? '' }}</p>-->
                                        <div class="row m-2">
                                            @if(!empty($buildingData))
                                            @foreach($buildingData as $build)
                                                @php
                                                    $floorData = $floor->where('building_id', $build->id);
                                                @endphp
                                                <div class="col-md-12 card border border-warning">
                                                    <p class="mb-0 border-bottom"><i class="fa fa-building"></i> Building : {{ $build->name ?? '' }}</p>
                                                    <div class="row m-2">
                                                        @if(!empty($floorData))
                                                        @foreach($floorData as $flr)
                                                            @php
                                                                $roomData = $room->where('floor_id', $flr->id);
                                                            @endphp
                                                        <div class="col-md-12 card border border-primary">
                                                            <p class="mb-0 border-bottom"><i class="fa fa-inbox"></i> Floor : {{ $flr->name ?? '' }}</p>
                                                            <div class="row m-2">
                                                                
                                                            @if(count($roomData) > 0 )
                                                            @foreach($roomData as $rm)
                                                                @php
                                                                    $bedData = $bed->where('room_id', $rm->id);
                                                                @endphp
                                                                <div class="col-md-2">
                                                                    <p class="mb-0 border-bottom"><i class="fa fa-trello"></i> Room : {{ $rm->name ?? '' }}</p>
                                                                    <div class="card m-1 d-block">
                                                                    @if(count($bedData) > 0 )
                                                                    @foreach($bedData as $key => $bd)
                                                                        @php
                                                                            $assignCheck = DB::table('hostel_assign')->where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'))->where('bed_id', $bd->id)->whereNull('deleted_at')->first();
                                                                        @endphp
                                                                        
                                                                        
                                                                            @if(!empty($assignCheck))
                                                                                <div class="btn btn-danger modal_bed m-1 bed_type" data-id="{{ $bd->id ?? '' }} " data-status="{{ $assignCheck->bed_status ?? '' }}">
                                                                                        <i class="fa fa-bed"></i> <br> {{ $bd->name ?? '' }}
                                                                                </div>
                                                                              
                                                                                
                                                                            @else
                                                                            <a href="{{url('hostel_assign')}}" >
                                                                                <div  class="btn btn-success m-1" >
                                                                                    
                                                                                    <i class="fa fa-bed"></i> <br> {{ $bd->name ?? '' }}
                                                                                </div>
                                                                                    </a>
                                                                            @endif
                                                                      
                                                                    @endforeach
                                                                    @else
                                                                     <a href="{{url('bed_add')}}">
                                                                         No Bed Found!
                                                                     </a>
                                                                    @endif
                                                                    </div>
                                                                </div>
                                                            
                                                            @endforeach
                                                             @else
                                                                No Bed Found!
                                                            @endif                                                                
                                                            </div>
                                                        </div>
                                                        @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            @endif
                                        </div>

                                    <!--</div>-->
                                @endforeach
                                @endif
                            </div>  
                       
					</div>
				</div>
			</div>
		</div>
	</section>
</div>


          <!-- The Modal -->
                        <div class="modal" id="myModal">
                          <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                        
                              <div class="modal-header">
                                <h4 class="modal-title">{{ __('hostel.Assigned Student Details') }}</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                              </div>
                        
                              <form action="#" method="post">
                              <div class="modal-body">
                                     <div class="row">
                                         <div class="col-6 col-md-3 border"><b><i class="fa fa-user text-purple"></i>&nbsp; {{ __('common.Name') }}</b></div>
                                         <div class="col-6 col-md-3 border" id="name1"></div>
                                         <div class="col-6 col-md-3 border"><b><i class="fa fa-phone text-purple"></i>&nbsp; {{ __('common.Mobile') }}</b></div>
                                         <div class="col-6 col-md-3 border" id="mobile1"></div>
                                         <div class="col-6 col-md-3 border"><b><i class="fa fa-envelope text-purple"></i>&nbsp; {{ __('common.Email') }}</b></div>
                                         <div class="col-6 col-md-3 border" id="email1"></div>
                                         <div class="col-6 col-md-3 border"><b><i class="fa fa-address-book-o text-purple"></i>&nbsp; {{ __('common.Fathers Name') }}</b></div>
                                         <div class="col-6 col-md-3 border" id="f_name1"></div>
                                         <div class="col-6 col-md-3 border"><b><i class="fa fa-map-marker text-purple"></i>&nbsp; {{ __('common.Address') }}</b></div>
                                         <div class="col-6 col-md-3 border" id="address_11"></div>
                                         <div class="col-6 col-md-3 border"><b><i class="fa fa-money text-purple"></i>&nbsp; {{ __('hostel.Hostel Fees') }}</b></div>
                                         <div class="col-6 col-md-3 border" id="first_amount1"></div>
                                         <div class="col-6 col-md-3 border"><b><i class="fa fa-hospital-o text-purple"></i>&nbsp; {{ __('hostel.Hostel') }}</b></div>
                                         <div class="col-6 col-md-3 border" id="1hostel_id"></div>
                                         <div class="col-6 col-md-3 border"><b><i class="fa fa-building text-purple"></i>&nbsp; {{ __('hostel.Building') }}</b></div>
                                         <div class="col-6 col-md-3 border" id="building_id1"></div>
                                         <div class="col-6 col-md-3 border"><b><i class="fa fa-inbox text-purple"></i>&nbsp; {{ __('hostel.Floor') }}</b></div>
                                         <div class="col-6 col-md-3 border" id="floor_id1"></div>
                                         <div class="col-6 col-md-3 border"><b><i class="fa fa-trello text-purple"></i>&nbsp; {{ __('hostel.Room') }}</b></div>
                                         <div class="col-6 col-md-3 border" id="room_id1"></div>
                                         <div class="col-6 col-md-3 border"><b><i class="fa fa-bed text-purple"></i>&nbsp; {{ __('hostel.Bed') }}</b></div>
                                         <div class="col-6 col-md-3 border" id="bed_id1"></div>
                                         <div class="col-6 col-md-3 border"><b><i class="fa fa-bed text-purple"></i>&nbsp; {{ __('hostel.join date') }}</b></div>
                                         <div class="col-6 col-md-3 border" id="join_date"></div>
                                         <h2 class="modal-title">{{ __('Fees Detail') }}</h2>
                                         <div style="width:100%">
                                             <table  class="table table-bordered table-striped dataTable dtr-inline ">
                                                 <thead>
                                                     <tr>
                                                     <th>Invoice No.</th>
                                                     <th>Total Amount</th>
                                                     <th>Paid Amount</th>
                                                     <th>Due Amount</th>
                                                     <th>Discount</th>
                                                     <th>Renewal Date</th>
                                                 </tr>
                                                 </thead>
                                                 <tbody id="invoice">
                                                    
                                                     
                                                 </tbody>
                                             </table>
                                         </div>
                                        <h2 class="modal-title">{{ __('Expense Detail') }}</h2>
                                         <div style="width:100%">
                                             <table  class="table table-bordered table-striped dataTable dtr-inline ">
                                                 <thead>
                                                     <tr>
                                                     <th>Expense Date</th>
                                                     <th>Payment Mode</th>
                                                     <th>Expense Name</th>
                                                     <th>Expense Amount</th>
                                                     <th>Left Amount</th>
                                                     <th>Status</th>
                                                 </tr>
                                                 </thead>
                                                 <tbody id="expense">
                                                    
                                                     
                                                 </tbody>
                                             </table>
                                         </div>

                                     </div> 
                                     <div class="row">
                                         <input type="hidden" name="hostel_assign_id" id="hostel_assign_id" class="form-control" value="">
                                     <!--<div class="col-6 col-md-3">{{ __('hostel.Meter Reading Unit') }}</div>
                                         <div class="col-6 col-md-3 ">
                                          <input type="text" name="meter_unit" id="meter_unit" class="form-control" placeholder="meter reading unit" onkeypress="javascript:return isNumber(event)">
                                          <input type="hidden" name="hostel_assign_id" id="hostel_assign_id" class="form-control" value="">

                                         </div>-->
                                     </div>                                        
                              </div>
                              <div class="modal-footer">
                                        <button type="button" class="btn btn-danger waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
                                            
                                 </div>
                               </form>
                            </div>
                          </div>
                        </div>



 <script>
          $(document).ready(function(){
        $(".modal_bed").click(function(){
            $('#myModal').modal('toggle');
    id = $(this).data("id");

    $("#meter_unit").val('');
    $("#hostel_assign_id").val('');
var basurl = "{{ url('/') }}";
       $.ajax({
             headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
        type:'post',
        url: basurl +'/stu_bed_detail',
        data: {bed_id:id},
         dataType: 'json',
        success: function(response) {  
            
            
            
            var data = response.data;
            
           

	     if(data != ''){
	         	$("#name1").html(data.first_name);
	         	$("#mobile1").html(data.mobile);
	         	$("#email1").html(data.email);
	         	$("#aadhaar1").html(data.aadhaar);
	         	$("#f_name1").html(data.father_name);
	         	$("#address_11").html(data.address);
	         	$("#first_amount1").html(data.hostel_fees);
	         	$("#1hostel_id").html(data.hostel_name);
	         	$("#building_id1").html(data.building_name);
	         	$("#floor_id1").html(data.floor_name);
	         	$("#room_id1").html(data.room_name);
	         	$("#bed_id1").html(data.bed_name);
	         	var originalDate = data.date;
                var momentDate = moment(originalDate, 'YYYY-MM-DD');
                var formattedDate = momentDate.format('DD-MM-YYYY');
             $("#join_date").html(formattedDate);
             $("#hostel_assign_id").val(data.id);
             $("#meter_unit").val(data.meter_unit);
              $("#invoice").html(response.invoice);
              $("#expense").html(response.expense);

	     }else{
	         	toastr.danger('Student Not Found !');
	     }            
           
        }
      }); 
        });
        
        
        
        $('.modal_bed').mouseenter(function(){
            var hoverBedId = $(this).data('id');
           $('#hoverdiv_' + hoverBedId).removeClass('d-none');
        });
        $('.modal_bed').mouseleave(function(){
            var hoverBedId = $(this).data('id');
           $('#hoverdiv_' + hoverBedId).addClass('d-none');
        });
        
        
    });
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





</script>

                        
                 
      
<style>

    a{
        color:#007bff;
    }
    @media (min-width: 768px) {
  .col-md-2 {
    -webkit-flex: 0 0 16.666667%;
    -ms-flex: 0 0 16.666667%;
    flex: 0 0 16.666667%;
    max-width: 12%;
  }
}
</style>

@endsection      