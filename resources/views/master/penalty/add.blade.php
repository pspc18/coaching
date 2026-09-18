@php
   
   $classType = Helper::classType();
     $getSection = Helper::getSection();
   $getState = Helper::getState();
   $getCity = Helper::getCity();
   $getCountry = Helper::getCountry();
   
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
                            <h3 class="card-title"><i class="fa fa-user"></i> &nbsp;Add Penalty Form</h3>
                			<div class="card-tools"> <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> Back</a> </div>
                   
                        </div>
                               
                    <form id="quickForm" action="#" method="post" >
                        @csrf 
            <div class="row m-2">
                <div class="col-md-2">
            		<div class="form-group">
            			<label>Class</label>
            			<select class="form-control" id="class_type_id" name="class_type_id" >
            			<option value="">Select</option>
                         @if(!empty($classType)) 
                              @foreach($classType as $type)
                                 <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                              @endforeach
                          @endif
                        </select>
            	    </div>
            	</div>
            <div class="col-md-2">
            		<div class="form-group">
            			<label>Section</label>
            				<select class="form-control section_search_id" id="section_search_id" name="section_search_id" >
            			   <option value="">Select</option>
                         @if(!empty($getSection)) 
                              @foreach($getSection as $type)
                                 <option value="{{ $type->id ?? ''  }}">{{ $type->name ?? ''  }}</option>
                              @endforeach
                          @endif
                        </select>
            	    </div>
            	</div>
            	<div class="col-md-3" >
                    <div class="form-group">
                     <label>Country</label>
                      <select class="form-control select2" name="country_id" id="country_id" >
                          <option value="">Select</option>
                          @if(!empty($getCountry)) 
                              @foreach($getCountry as $country)
                                 <option value="{{ $country->id ?? ''  }}" {{ ($country->id == Session::get('countries_id')) ? 'selected' : '' }}>{{ $country->name ?? ''  }}</option>
                              @endforeach
                          @endif
                      </select>
                      </div>
                </div>
            	<div class="col-md-2">
            		<div class="form-group"> 
            			<label for="State" class="required">State</label>
            			<select class="form-control" id="state_id" name="state_id" >
                        <option value="">Select</option>
                    @if(!empty($getState)) 
                          @foreach($getState as $state)
                             <option value="{{ $state->id ?? ''}}" {{ ($state->id == Session::get('state_id')) ? 'selected' : '' }}>{{ $state->name ?? ''}}</option>
                          @endforeach
                    @endif
                      
                        </select>
            		
            		</div>
            	</div>
            	<div class="col-md-2">
            	    <div class="form-group">
            	        <label for="City">City</label>
            	        <select class="form-control" name="city_id" id="city_id" >
            	        <option value="">Select</option>      
            	            @if(!empty($getCity)) 
                          @foreach($getCity as $cities)
                             <option value="{{ $cities->id ?? ''}}" {{ ($state->id == Session::get('state_id')) ? 'selected' : '' }}>{{ $cities->name ?? ''  }}</option>
                          @endforeach
                      @endif
            			</select>
            	    </div>
            	</div>
                <div class="col-md-1 ">
                     <label for="">&nbsp;</label>
            	    <button type="button" class="btn btn-primary" onclick="SearchValue()"style="margin-top:28px;">Search</button>
            	</div>
            			
            </div>
        </form>
    </div>
            </div>
        </div>
    </div>
<div class="pelanty_list_show"></div>
    <div class="card m-2">
    <div class="card-body">
        <form id="quickForm" action="{{ url('penalty/index') }}" method="post" enctype="multipart/form-data">   
         @csrf
       <div class="row">
       <div class="col-md-3">
            		<div class="form-group">
            			<label>Class</label>
            				<select class="form-control @error('student_section') is-invalid @enderror section_id" id="student_section" name="student_section" >
            				   <option value="" >Select</option>
                         @if(!empty($classType)) 
                              @foreach($classType as $type)
                                 <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                              @endforeach
                          @endif
                        </select>
            	    </div>
            	</div>
	    
	    	<div class="col-md-3">
            			<div class="form-group">
            				<label  style="color:red;">Section*</label>
            					<select class="form-control @error('student_section') is-invalid @enderror section_id" id="student_section" name="student_section" >
            				   <option value="" >Select</option>
                             @if(!empty($getSection)) 
                                  @foreach($getSection as $type)
                                     <option value="{{ $type->id }}">{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                             @error('student_section')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
            		    </div>
            		 </div>
		<div class="col-md-3">
	    	<div class="form-group">
				<label>Student Name:</label>
				<input type="text" class="form-control" id="name" name="name" placeholder=" Name" value="{{old('name')}}">
				 @error('name')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
		    </div>
		</div>
		 <div class="col-md-3">
	    	<div class="form-group">
				<label>Student Roll No.</label>
				<input type="text" class="form-control @error('student_no') is-invalid @enderror" id="student_no" name="student_no" placeholder="Student Roll No." value="{{old('student_no')}}">
				@error('student_no')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
		    </div>
		</div>
	    
	     <div class="col-md-3">
	    	<div class="form-group">
				<label style="color:red;">Pelanty Amount*</label>
				<input type="text" class="form-control @error('pelanty_amount') is-invalid @enderror" id="pelanty_amount" name="pelanty_amount" placeholder="Pelanty Amount" value="{{old('pelanty_amount')}}">
				 @error('pelanty_amount')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
		    </div>
		</div>
		 <div class="col-md-3">
	    	<div class="form-group">
				<label>Pelanty Reason</label>
				<input type="text" class="form-control @error('pelanty_reason') is-invalid @enderror" id="pelanty_reason" name="pelanty_reason" placeholder="Pelanty Reason" value="{{old('pelanty_reason')}}">
				 @error('pelanty_reason')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
		    </div>
		  </div>
		  <div class="col-md-3">
	    	<div class="form-group">
				<label>Pelanty Remark</label>
				<input type="text" class="form-control @error('pelanty_remark') is-invalid @enderror" id="pelanty_remark" name="pelanty_remark" placeholder="Pelanty Remark" value="{{old('pelanty_remark')}}">
				 @error('pelanty_remark')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
		    </div>
		</div>
		 
       </div> 
    </div>  
    <div class="row m-2">
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary ">Submit</button>
        </div>
    </div>
    </form>
</div> 
</div>         

<script>
    
        function SearchValue() {
            
            var class_type_id = $('#class_type_id :selected').val();
            var section_id = $('#section_id :selected').val();
            var country_id = $('#country_id :selected').val();
            var state_id = $('#state_id :selected').val();
            var city_id = $('#city_id :selected').val();
            $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                type:'post',
                url: '/student_search_pelanty',
                data: {class_type_id:class_type_id,section_id:section_id,country_id:country_id,state_id:state_id,city_id:city_id},
                 //dataType: 'json',
                success: function (data) {
                   
                  
                    $('.pelanty_list_show').html(data);
                   
                }
              });
        };
    
         function showData(student_id) {
           
            $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                type:'post',
                url: '/pelanty_add_click',
                data: {student_id : student_id},
                 dataType: 'json',
                success: function (data) {
                    
                     console.log(data);
                 if(data){
                $('#name').val(data.name);
                $('#class').val(data.class_type_id);
                $('#student_section ').val(data.section_id);
                $('#pelanty_remark').val(data.remark_1);
                $('#student_no').val(data.roll_no);
                
                 }else{
                     alert('No more records found');
                 }
                    
                }
              });
        };    
        </script>
@endsection