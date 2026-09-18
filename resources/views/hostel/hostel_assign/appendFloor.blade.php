
@if(!empty($floor)) 
    @foreach($floor as $flr)
        @php
            $getRoom = DB::table('hostel_room')->where('floor_id', $flr->id)->whereNull('deleted_at')->get();
        @endphp
        <div class="row m-2 card-outline card-orange  " style="border: 1px solid #002C54 !important">
            <div class="col-md-12 text-left border-bottom"><i class="fa fa-server"></i> &nbsp; {{ $flr->name ?? '' }}</div>
            
            <div class="col-md-12" style="text-align: left; display:flex">
            @if(!empty($getRoom) && count($getRoom) > 0)
            @foreach($getRoom as $room)
                @php
                    $getBed = DB::table('hostel_bed')->where('room_id', $room->id)->whereNull('deleted_at')->get();
                    $assignBedCheck = DB::table('hostel_assign')->where('room_id', $room->id)->where('bed_status', 1)->whereNull('deleted_at')->count();
                    $tatalBed = count($getBed);
                    $filledBed = $assignBedCheck;
                    $emptyBed = $tatalBed - $assignBedCheck;
                @endphp
                
                <div class="col-2 col-md-1  btn-xs m-1 rooming "  style="width: 96px; height: 96px; border-radius: 60px;" data-id="{{ $room->id ?? '' }}"  data-floorid="{{ $flr->id ?? '' }}">
                    <div style="background: gainsboro;border-radius: 50px;height: 78px;width: 77px; border: 2px solid gainsboro; text-align: center; cursor: pointer;"><i style="font-size:35px;margin-top: 12px;" class="fa fa-trello"></i><p>{{ $room->name ?? '' }}</p></div>
                    
                    
                </div>
            @endforeach
            @else
            <div class="d-flex text-center">
                <a class="btn btn-primary btn-xs" href="{{ url('room_add') }}" target="_blank"><i class="fa fa-external-link	"></i> No Room Found! Add New Room</a>
            </div>
            @endif
            </div>
        

            
            <div class="col-md-12 p-2  " id="appendbed_{{ $flr->id ?? '' }}"></div>
        </div>
    @endforeach
@endif

<script>
    $(document).ready(function(){
    
    var baseUrl = "{{ url('/') }}";
    $('#appendFloor').on('click', '.rooming', function() {
        var rooming = $(this).data('id');
        var floorid = $(this).data('floorid');
   
        $.ajax({
            headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
    	    url: baseUrl + '/getbed/' + rooming,
    	    success: function(data){
    	        if(data != ''){
    	            $('#appendbed_' + floorid).html(data);
    	        }
    	    }
    	});
    });
    
});
</script>


