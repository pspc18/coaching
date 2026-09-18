@php
    $getHostel = Helper::getHostel();
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    
<section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
            
            <div class="col-md-8 pl-0">
                <div class="card card-outline card-orange ml-1">
                 <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-inbox"></i> &nbsp;{{ __('hostel.Floor List') }}</h3>
                <div class="card-tools">
                <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>-->
                </div>
                 </div>                     
                            <div class="row m-2">
                                <div class="col-md-5">
                        			<label style="color:red;">{{ __('hostel.Select Hostel') }}*</label>
                    				<select class="  form-control select2" id="hostel_id_search" name="hostel_id_search">
                                        <option value="">{{ __('messages.Select') }}</option>
                                     @if(!empty($getHostel)) 
                                          @foreach($getHostel as $type)
                                             <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                                          @endforeach
                                      @endif
                                    </select>
                            	</div>    
                                <div class="col-md-5">
                        			<label style="color:red;">{{ __('hostel.Select Building') }}*</label>
                    				<select class="  form-control  building_id_search select2" id="building_id_search" name="building_id_search">
                                        <option value="">{{ __('messages.Select') }}</option>
                                    </select>
                            	</div>                 	
                                <div class="col-md-2 text-center">
                                    <label class="text-white">{{ __('common.Search') }}</label>
                                    <button class="btn btn-primary" onclick="SearchValue()">{{ __('common.Search') }}</button>
                                </div>
                            </div>
                            <div class="row m-2">
                                <div class="col-md-12" id="floor_list_show">
            
                                </div>
                            </div>                        
                </div>          
            </div>
            
            <div class="col-md-4 pr-0">
                <div class="card card-outline card-orange mr-1">
                 <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-inbox"></i> &nbsp;{{ __('hostel.Add Floor') }} </h3>
                <div class="card-tools">
                             <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}</a>

                </div>
                
                </div>                       
                    <form id="quickForm" action="{{ url('floor_add') }}" method="post">
                    @csrf
                    <div class="row m-2">
                        <div class="col-md-12">
                			<label style="color:red;">{{ __('hostel.Select Hostel') }}*</label>
            				<select class="  form-control  @error('hostel_id') is-invalid @enderror select2" id="hostel_id" name="hostel_id">
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
                        <div class="col-md-12">
                			<label style="color:red;">{{ __('hostel.Select Building') }}*</label>
            				<select class="  form-control  @error('building_id') is-invalid @enderror building_id select2" id="building_id" name="building_id">
                                <option value="">{{ __('messages.Select') }}</option>
                            </select>
                             @error('building_id')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div>                 	
                        <div class="col-md-12">
                            <table class="_table w-100" id="tableId">
                                <thead>
                                  <tr>
                                    <th width="80%" style="color:red;">{{ __('hostel.Floor Name/No.') }}*</th>
                                     <th width="20%"></th>
                                  </tr>
                                </thead>
                                  <tbody id="table_body">
                                      <tr id="appendRow_0">
                                        <td colspan="12">
                			                <input class="form-control @error('name') is-invalid @enderror" type="text" required name="name[]" id="name" placeholder="{{ __('hostel.Floor Name/No.') }}" value="{{ old('name') }}">
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
        $('#table_body').append('<tr id="appendRow_'+count+'" ><td colspan="12"><input class="form-control" type="text" required name="name[]" id="name" placeholder="{{ __('hostel.Floor Name/No.') }}" value="{{ old('name') }}"></td><td style="width: 92px;"><div class="action_container"><button type="button" class="btn btn-primary btn-xs addmoreprodtxtbx" id="clonebtn"><i class="fa fa-plus"></i></button><button type="button" class="btn btn-danger btn-xs removeprodtxtbx" id="removerow"><i class="fa fa-trash"></i></button></div></td> </tr>');
            

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

 $('#hostel_id_search').select2();

$('#hostel_id_search').on('change', function(e){
     var basurl = "{{ url('/') }}";
	var hostel_id_search = $(this).val();
    $.ajax({
         headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
	  url: basurl+'/hostelDataSearch/'+ hostel_id_search,
	  success: function(data){
	     if(data != ''){
	         	$(".building_id_search").html(data);
	     }else{
	         	$(".building_id_search").html(data);
	         alert('Building Not Found');
	     }
	  }
	});
});

    function SearchValue() {
        var basurl = "{{ url('/') }}";
        var hostel_id_search = $('#hostel_id_search :selected').val();
        var building_id_search = $('.building_id_search :selected').val();
        if(hostel_id_search > 0 || building_id_search > 0 ){
        $.ajax({
                 headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            type:'post',
            url: basurl+'/floor_search_data',
            data: {hostel_id:hostel_id_search,building_id:building_id_search},
             //dataType: 'json',
            success: function (data) {

                $('#floor_list_show').html(data);
               
            }
          });
        }else{
                alert('Please put a value in minimum one column !');
            }               
    };

});
</script>

@endsection      