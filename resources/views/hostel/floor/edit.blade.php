@php
  $getHostel = Helper::getHostel(); 
  $getHostelBuildingAll = Helper::getHostelBuildingAll();
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
                <h3 class="card-title"><i class="fa fa-inbox"></i> &nbsp; {{ __('hostel.Floor List') }}</h3>
                <div class="card-tools">
                <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>-->
                <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
                </div>
                 </div>                    

                            <div class="row m-2">
                                <div class="col-md-5">
                        			<label style="color:red;">{{ __('hostel.Select Hostel') }}*</label>
                    				<select class="form-control" id="hostel_id_search" name="hostel_id_search">
                                        <option value="">{{ __('common.Select') }}</option>
                                     @if(!empty($getHostel)) 
                                          @foreach($getHostel as $type)
                                             <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                                          @endforeach
                                      @endif
                                    </select>
                            	</div>    
                                <div class="col-md-5">
                        			<label style="color:red;">{{ __('hostel.Select Building') }}*</label>
                    				<select class="form-control building_id_search" id="building_id_search" name="building_id_search">
                                        <option value="">{{ __('common.Select') }}</option>
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
                <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp; {{ __('hostel.Edit Floor') }}</h3>
                <div class="card-tools">
                <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>
                <a href="{{url('students_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> Back</a>-->
                </div>
                
                </div>                     
                    <form id="quickForm" action="{{ url('floor_edit') }}/{{ $data['id'] ?? '' }}" method="post">
                    @csrf
                    <div class="row m-2">
                        <div class="col-md-12">
                			<label style="color:red;">{{ __('hostel.Select Hostel') }}*</label>
            				<select class="form-control @error('hostel_id') is-invalid @enderror" id="hostel_id" name="hostel_id">
                             @if(!empty($getHostel)) 
                                  @foreach($getHostel as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ( $type['id'] == $data['hostel_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
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
            				<select class="form-control @error('building_id') is-invalid @enderror building_id" id="building_id" name="building_id">
                            @if(!empty($getHostelBuildingAll)) 
                                  @foreach($getHostelBuildingAll as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ( $type['id'] == $data['building_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                  @endforeach
                            @endif                            
                            </select>
                             @error('building_id')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div>                     	
                        <div class="col-md-12">
                			<label style="color:red;">{{ __('hostel.Floor Name') }}*</label>
                			<input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" placeholder="{{ __('hostel.Floor Name') }}" value="{{ $data['name'] ?? '' }}">
                             @error('name')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
                    	</div>
                    </div>
     
                    <div class="row m-2">
                        <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">{{ __('common.Update') }} </button>
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

$('#hostel_id_search').on('change', function(e){
     var basurl = "{{ url('/') }}";
	var hostel_id_search = $(this).val();
    $.ajax({
         headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
	  url: basurl+'/hostelDataSearch/'+hostel_id_search,
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
</script>

@endsection      