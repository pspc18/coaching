@php
    $getHostel = Helper::getHostel();
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
                    <h3 class="card-title"><i class="fa fa-bed"></i> &nbsp;{{ __('hostel.Add Bed') }}</h3>
                    <div class="card-tools">
                    <a href="{{url('bed_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>{{ __('common.View') }} </a>
                    <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a>
                    </div>
                    
                    </div>        
        <form id="quickForm" action="{{ url('bed_add') }}" method="post">
        @csrf
        <div class="row m-2">
            <div class="col-md-3">
    			<label style="color:red;">{{ __('hostel.Select Hostel') }}*</label>
				<select class="  form-control @error('hostel_id') is-invalid @enderror select2" id="hostel_id" name="hostel_id">
                    <option value="">{{ __('messages.Select') }}</option>
                 @if(!empty($getHostel)) 
                      @foreach($getHostel as $type)
                         <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                      @endforeach
                  @endif
                </select>
                 @error('hostel_id')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror                  			
        	</div>    
            <div class="col-md-3">
    			<label style="color:red;">{{ __('hostel.Select Building') }}*</label>
				<select class="  form-control @error('building_id') is-invalid @enderror building_id select2" id="building_id" name="building_id">
                    <option value="">{{ __('messages.Select') }}</option>
                </select>
                 @error('building_id')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror                  			
        	</div>  
            <div class="col-md-3">
    			<label style="color:red;">{{ __('hostel.Select Floor') }}*</label>
				<select class="  form-control @error('floor_id') is-invalid @enderror floor_id select2" id="floor_id" name="floor_id">
                    <option value="">{{ __('messages.Select') }}</option>
                </select>
                 @error('floor_id')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror                  			
        	</div>  
            <div class="col-md-3">
    			<label style="color:red;">{{ __('hostel.Select Room') }}*</label>
				<select class="  form-control @error('room_id') is-invalid @enderror room_id select2" id="room_id" name="room_id">
                    <option value="">{{ __('messages.Select') }}</option>
                </select>
                 @error('room_id')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror                  			
        	</div>                 	
            <div class="col-md-3">
                <table class="_table w-100" id="tableId">
                    <thead>
                      <tr>
                        <th width="80%" style="color:red;">{{ __('hostel.Bed Name/No.') }}*</th>
                         <th width="20%"></th>
                      </tr>
                    </thead>
                  <tbody id="table_body">
                      <tr id="appendRow_0">
                        <td colspan="12">
			                <input class="form-control @error('name') is-invalid @enderror" type="text" required name="name[]" id="name" placeholder="{{ __('hostel.Bed Name/No.') }}" value="{{ old('name') }}">
                        </td>
                        <td style="width: 92px;">
                          <div class="action_container">
                                <button type="button" class="btn btn-primary btn-xs addmoreprodtxtbx" id="clonebtn"><i class="fa fa-plus"></i></button>
                                <button type="button" class="btn btn-danger btn-xs removeprodtxtbx" id="removerow"><i class="fa fa-trash"></i></button>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                </table> 
        	</div>
        </div>

        <div class="row m-2">
            <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary">{{ __('common.submit') }} </button>
            </div>
        </div>
        </form>
            </div>
</div>
</div>
</div>
</section>
</div>

<script>
$(document).ready(function() {
    
    count=0;
        $( ".removeprodtxtbx" ).eq( 0 ).css( "display", "none" );
        $(document).on("click", "#clonebtn", function() {
    count++;
        $('#table_body').append('<tr id="appendRow_'+count+'" ><td colspan="12"><input class="form-control" type="text" required name="name[]" id="name" placeholder="{{ __('hostel.Bed Name/No.') }}" value="{{ old('name') }}"></td><td style="width: 92px;"><div class="action_container"><button type="button" class="btn btn-primary btn-xs addmoreprodtxtbx" id="clonebtn"><i class="fa fa-plus"></i></button><button type="button" class="btn btn-danger btn-xs removeprodtxtbx" id="removerow"><i class="fa fa-trash"></i></button></div></td> </tr>');
            

        $( ".removeprodtxtbx" ).eq( count ).css( "display", "block" );
        $( ".addmoreprodtxtbx" ).eq( count ).css( "display", "none" );

        });
    
        $(document).on("click", "#removerow", function() {
            $(this).parents('tr').remove();
            count--;
            window.calculateSum()
        });

 $('#hostel_id').select2();
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

 $('#building_id').select2();
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

 $('#floor_id').select2();
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
});



// $( document ).ready(function() {
    
    
//     var hostel_name = localStorage.getItem("hostel_name");
//     if( hostel_name == null)
//     {
//         localStorage.setItem("hostel_name", $('#hostel_id').val());
        
//     }
//     else
//     {
        
//     }
    

    
    
// });


</script>

@endsection      