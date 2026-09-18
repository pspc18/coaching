@php
    $getHostel = Helper::getHostel();
    $classType = Helper::classType();
    $getgenders = Helper::getgender();
    $getSetting=Helper::getSetting();
    $getPaymentMode = Helper::getPaymentMode();
   
@endphp
@extends('layout.app') 
@section('content')
<style>
.select2-container .select2-selection--single {height:38px !important;}
.select2-container--default .select2-selection--single .select2-selection__arrow {height:38px !important;}
.c_height {height: 160px;overflow-y:scroll;}
.c_height1 {height: 260px;overflow-y:scroll;}
.bed {
    display: none;
}
@media (max-width: 600px) {
  .modal div {
      font-size:10px;
  }
}
@media (min-width: 605px) {
  .level4 .btn-xs {
      font-size:1.7rem;
  }
}
</style>

<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
                    <!-- <div class="card-header bg-primary">-->
                    <!--<h3 class="card-title"><i class="fa fa-plus"></i> &nbsp;Hostel Overview</h3>-->
                    <!--<div class="card-tools">-->
                    <!--<a href="{{url('assign_student_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>{{ __('common.View') }} </a>-->
                    <!--<a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>-->
                    <!--</div>-->
                    
                    <!--</div>        -->
                    <!--<form id="quickForm" action="{{ url('hostel_assign') }}" method="post" enctype='multipart/form-data'>-->
                    <!--    @csrf-->
                    <div class="row text-center m-2">
                        <div class="col-md-12">
                        @if(!empty($getHostel)) 
                            @foreach($getHostel as $hostel)
                                <button class="btn btn-primary hostels" data-id="{{ $hostel->id ?? ''  }}">{{ $hostel->name ?? ''  }}</button>
        	                @endforeach
                        @endif
                        </div>
                        <div class="col-md-12 p-2" id="appendBuilding"></div>
                        
                        <div class="col-md-12 p-2" id="appendFloor"></div>
                       
                        
                        
                    </div>
                    <div class="row m-2 d-none">
                        <input type="hidden" id="bedId" name="bed_id" value="">
                    	 <div class="col-md-2 card c_height">
                			<label >{{ __('hostel.Select Hostel') }}<span class="text-danger">*</span></label>
            		        @if(!empty($getHostel)) 
                                @foreach($getHostel as $key=>$type)
                                    <div class="icheck-primary">
                                        <input type="radio" class="hostel" name="hostel_id" id="hostel_id{{$key}}" value="{{ $type->id ?? ''  }}" data-value="{{ $type->id ?? ''  }}">
                                        <label for="hostel_id{{$key}}">{{ $type->name ?? ''  }}</label>
                                    </div>                                  
            	                @endforeach
                            @endif
                        </div>

                        <div class="col-md-2 card level1 c_height" style="display:none">
                			<label >{{ __('hostel.Select Building') }}<span class="text-danger">*</span></label>
                                    <div class="icheck-primary" id="building_id">
                                        
                                    </div><br>                		

                    	</div>  
                        <div class="col-md-2 card level2 c_height" style="display:none">
                			<label >{{ __('hostel.Select Floor') }}<span class="text-danger">*</span></label>
            				<div class="icheck-primary" id="floor_id">
                   
                            </div><br>        			
                    	</div>  
                        <div class="col-md-2 card level3 c_height" style="display:none">
                			<label >{{ __('hostel.Select Room') }}<span class="text-danger">*</span></label>
            				<div class="icheck-primary" id="room_id">
                   
                            </div><br>     			
                    	</div>  
                        <div class="col-md-12 card level4 c_height1" style="display:none">
                			<label >{{ __('hostel.Select Bed') }}<span class="text-danger">*</span> &nbsp; <span class="text-danger"><i class="bg-danger p-1 text-danger"> &nbsp; &nbsp; </i> &nbsp;Booked</span> <span class="text-success"><i class="bg-success p-1"> &nbsp; &nbsp; </i> &nbsp;Empty</span></label>
            				<div class="icheck-primary row" id="bed_id" width="100%">
                   
                            </div>                 			
                    	</div>        	



                        <div class="col-md-12 student_detail" style="display:none;">
                            <div class="row">
                                <div class="col-md-2">
                            		<div class="form-group">
                            			<label>{{ __('hostel.Admission No.') }}</label>
                            			<input type="text" class="form-control admissionNo" id="admissionNo" name="admissionNo" placeholder="{{ __('hostel.Admission No.') }}" value="{{ $search['admissionNo'] ?? '' }}">
                            	    </div>
                            	</div>                
                                <!--<div class="col-md-2">
                            		<div class="form-group">
                            			<label>Class</label>
                            			<select class="form-control" id="class_search_id" name="class_search_id" >
                            			<option value="">Select</option>
                                         @if(!empty($classType)) 
                                              @foreach($classType as $type)
                                                 <option value="{{ $type->id ?? ''  }}">{{ $type->name ?? ''  }}</option>
                                              @endforeach
                                          @endif
                                        </select>
                            	    </div>
                            	</div>
                            	<div class="col-md-1">
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
                            	</div>-->                
                        		<div class="col-md-5">
                        			<div class="form-group">
                        				<label>{{ __('common.Search Students By Keywords') }}</label>
                        				<input type="text" class="form-control" id="search_name" placeholder="{{ __('common.Ex. Name, Mobile, Email, Aadhaar etc.') }}">
                        		    </div>
                        		</div>             	
                                <div class="col-md-1 ">
                            	    <div class="form-group">
                            	        <label class="text-white">{{ __('common.Search') }}</label>
                            			<button type="button" class="btn btn-primary" onclick="SearchValue()" >{{ __('common.Search') }}</button>
                            	    </div>                    
                            	</div>
                            </div>
                        </div>
                        <div class="student_list_show student_detail col-md-12"></div>


                    	<div class="col-md-12 text-center student_detail" style="display:none;"><h3>{{ __('hostel.Student Details') }}</h3><hr></div>
                    
                   <div class="col-md-12 name" style="display:none;">
                   <div class="row">
								<div class=" col-md-12 title mt-n3">
									<h5 class="text-danger">{{ __('hostel.Personal Details') }}:-</h5>
								</div>
                    	<input type="hidden" id="admission_id" name="admission_id" value="">
                    	<div class="col-md-3 name" style="display:none;">
									<div class="form-group">
										<label>{{ __('hostel.Admission No.') }}<span style="color:red;">*</span></label>
										<input type="text" class="form-control" id="admission_no" name="admission_no" placeholder="{{ __('hostel.Admission No.') }}" readonly value="" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
                    	  <div class="col-md-3 name" style="display:none;">
                			<label >{{ __('Name') }}<span class="text-danger">*</span></label>
            				<input type="text" class="form-control invalid" placeholder="{{ __('Name') }}" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="first_name" name="first_name" value="{{old('first_name')}}" >
                             <span class="invalid-feedback" id="first_name_invalid" role="alert">
                                <strong>{{ __('hostel.First name required') }}</strong>
                             </span>
                    	</div>  
                    	
                    	
                      <div class="col-md-3 gender_id" style="display:none;">
                              <label>{{ __('common.Gender') }} </label>
                              <select class="form-control" id="gender_id" name="gender_id">
                				<option value="">{{ __('common.Select') }}</option>
                                @if(!empty($getgenders)) 
                                      @foreach($getgenders as $value)
                                         <option value="{{ $value->id}}" {{ ($value->id == old('gender_id')) ? 'selected' : '' }}>{{ $value->name ?? ''  }}</option>
                                      @endforeach
                                  @endif
                                </select>
                            
                            </div>	 
                        
                        <div class="col-md-3 mobile" style="display:none;">
                			<label  id="alertMessage">{{ __('common.Mobile No.') }}<span class="text-danger">*</span></label>
            				<input type="text" class="form-control invalid" id="mobile" name="mobile" placeholder="{{ __('common.Mobile No.') }}" value="{{old('mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)" >
                             <span class="invalid-feedback" id="mobile_invalid" role="alert">
                                <strong>Mobile required</strong>
                             </span>
                    	</div>  
                        <div class="col-md-3 email" style="display:none;">
                			<label >{{ __('common.Email') }}</label>
            				<input type="email" class="form-control " placeholder="{{ __('common.Email') }}" id="email" name="email" value="{{old('email')}}" >
                           
                    	</div>    
                    
                          <div class="col-md-3">
									<div class="form-group">
										<label>{{ __('common.DOB') }}</label>
										<input type="date" class="form-control" id="dob" name="dob" value="">
									
									</div>
								</div>
          <div class="col-md-3">
									<div class="form-group">
										<label>{{ __('hostel.Date Of Admission') }}</label>
										<input type="date" class="form-control" id="admission_date" name="admission_date" value="{{date('Y-m-d')}}">
									
									</div>
								</div>
                		 
                            <div class="col-md-3 name" style="display:none;">
                			<label >{{ __('hostel.College Name') }}</label>
            				<input type="text" class="form-control " placeholder="{{ __('hostel.College Name') }}"  id="college" name="college" value="{{old('college')}}" >
                           
                    	</div> 
                            <div class="col-md-3 name" style="display:none;">
                			<label >{{ __('hostel.Course Name') }}</label>
            				<input type="text" class="form-control " placeholder="{{ __('hostel.Course Name') }}"  id="Course" name="Course" value="{{old('Course')}}" >
                    	
                    	</div> 
                           
                            	<div class="col-md-3">
									<lable>{{ __('hostel.Student Photo') }}</lable>
									<div class="input file form-group">
										<input type="file" class=" form-control" name="student_img" id="student_img" accept="image/png, image/jpg, image/jpeg">
                                  
									</div>
								</div>
								<div class="col-md-1">
									<img id="student_img_link"src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
									<div class="col-md-3">
									<lable>{{ __('hostel.Student Signature') }} </lable>
									<div class="input file form-group">
										<input type="file" class=" form-control" name="Student_Signature_img" id="Student_Signature_img" accept="image/png, image/jpg, image/jpeg">

                                 
									</div>
								</div>
                       	<div class="col-md-1">
									<img id="Signature_img_link"src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
                          <div class="col-md-12 address_1" style="display:none;">
                			<label>{{ __('common.Address') }} </label>
            				<textarea type="text" class="form-control" id="address" name="address" placeholder="{{ __('common.Address') }}">{{old('address')}}</textarea>

                    	</div> 
                	</div> 
                </div>
                
           
                             <div class="col-md-12 name" style="display:none;">
                    	<div class="row ">
							    <div class=" col-md-12 title">
									<h5 class="text-danger">{{ __('hostel.Parents Details') }}:-</h5>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Father/Guardian Name') }}</label>
										<input type="text" class="form-control " id="father_name" name="father_name" placeholder="{{ __('hostel.Father/Guardian Name') }}" value="{{old('father_name')}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
										
									
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Father Contact No') }}</label>
										<input type="text" class="form-control" id="father_mobile" name="father_mobile" placeholder="{{ __('hostel.Father Contact No') }}" value="{{old('father_mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
									
									
									</div>
								</div>
								<div class="col-md-3">
									<lable>{{ __('hostel.Father Photo') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="father_photo" id="father_photo" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
									<img id="father_photo_link"src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
				            	<div class="col-md-3">
									<lable>{{ __('hostel.Father Signature') }} </lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="father_Signature" id="father_Signature" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
									<img id="Signature2_img_link"src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
							</div>		
							</div>
                            
                           <div class="col-md-12 name" style="display:none;">
                               				<div class="row">
								<div class="col-md-2">
								
										<label>{{ __('common.Mothers Name') }}</label>
										<div class="input file form-group">
										<input type="text" class="form-control " id="mother_name" name="mother_name" placeholder="{{ __('common.Mothers Name') }}" value="{{old('mother_name')}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
									
									</div>
								</div>
								<div class="col-md-2">
								
										<label>{{ __('common.Mother Contact No') }}</label>
											<div class="input file form-group">
										<input type="text" class="form-control" id="mothers_mobile" name="mothers_mobile" placeholder="{{ __('common.Mother Contact No') }}" value="{{old('mothers_mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">

									</div>
								</div>
									<div class="col-md-3">
									<lable>{{ __('hostel.Mother Photo') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="mother_photo" id="mother_photo" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
									<img id="mother_photo_link"src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
									<div class="col-md-3">
									<lable>{{ __('hostel.Mother Signature') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="mother_Signature" id="mother_Signature" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
									<img id="Signature4_img_link"src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
								</div>
                           </div>
                           <div class="col-md-12 name" style="display:none;">
                           	<div class="row">
							    <div class=" col-md-12 title">
									<h5 class="text-danger">{{ __('hostel.Local Guardian Details') }}:-</h5>
								</div>
									<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Guardian Name') }}</label>
										<input type="text" class="form-control " id="guardian_name" name="guardian_name" placeholder="{{ __('hostel.Guardian Name') }}" value="{{old('guardian_name')}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
										
									
									</div>
								</div>
									<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Guardian Mobile') }}</label>
										<input type="text" class="form-control" id="guardian_mobile" name="guardian_mobile" placeholder="{{ __('hostel.Guardian Contact No') }}" value="{{old('guardian_mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
									
									
									
									</div>
								</div>
									<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Guardian Telephone') }}</label>
										<input type="text" class="form-control" id="guardian_tel" name="guardian_tel" placeholder="{{ __('hostel.Guardian Telephone') }}" value="{{old('guardian_tel')}}"  onkeypress="javascript:return isNumber(event)">
									
									
									
									</div>
								</div>
									<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Guardian Whatsapp') }}</label>
										<input type="text" class="form-control" id="guardian_whatsapp" name="guardian_whatsapp" placeholder="{{ __('hostel.Guardian Whatsapp') }}" value="{{old('guardian_whatsapp')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
									
									
									
									</div>
								</div>
									<div class="col-md-3">
									<lable>{{ __('hostel.Guardian Photo') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="guardian_photo" id="guardian_photo" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
									<img id="guardian_photo_link"src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
									<div class="col-md-3">
									<lable>{{ __('hostel.Guardian Signature') }} </lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="guardian_Signature" id="guardian_Signature" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
									<img id="Signature1_img_link"src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
									<div class="col-md-12">
									<div class="form-group">
										<label>{{ __('hostel.Guardian Address') }}</label>
										<textarea type="text" class="form-control " id="guardian_address" name="guardian_address" placeholder="{{ __('hostel.Guardian Address') }}" value="{{old('guardian_address')}}"></textarea>
										
									</div>
								</div>
								
								</div>
								</div>
							<hr>
                     
                           <div class="col-md-12 name" style="display:none">
                    <div class="row">
                         <div class=" col-md-12 title">
									<h5 class="text-danger">{{ __('hostel.Hostel Room Preference') }}:-</h5>
								</div>
                         <div class="col-md-3 amount" style="display:none;">
                		<label>{{ __('hostel.Hostel Fees') }}<span class="text-danger">*</span></label>
            				<input type="text" class="form-control invalid" id="hostel_fees" name="hostel_fees" placeholder="{{ __('hostel.Hostel Fees') }}" value="{{old('hostel_fees')}}" onkeypress="javascript:return isNumber(event)" >
                         <span class="invalid-feedback" id="hostel_fees_invalid" role="alert">
                                <strong>{{ __('hostel.Hostel Fees required') }}</strong>
                             </span> 
                    	</div> 
                    	
                    	<div class="col-md-12">
                    	    <div class="row">
                           <div class="col-md-2">
                              <div class="form-group">
                                 <label>{{ __('hostel.Receipt No')}}</label>
                                 <input type="number" name="editable_receipt_no" placeholder="" value="{{ $receipt_no ?? ''}}" readonly class="form-control">
                              </div>
                           </div>
                            @php
                                $dateObj = Carbon\Carbon::now();
                                $modifiedDate = $dateObj->addDays(30)->toDateString();
                            @endphp

                           <div class="col-md-2" >
                    			<div class="form-group">
                    			    <label>Renewal Date</label>
                        			<div class="input-group">
            							    <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                    				    <input type="date" class="form-control" id="hostel_renewal_date" name="hostel_renewal_date" value="{{ $modifiedDate ?? '' }}">
                                    </div>
                    			</div>
                	        </div>
                           
                	        <div class="col-md-3">
						    <div class="form-group">
                                <label >Total Amount:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                        <input class="form-control @error('amount') is-invalid @enderror" readonly type="number" name="hostel_amount" id="hostel_amount" value="0"> 
                                        @error('amount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
							</div>
                	        </div>
                	        
                           <div class="row">
							<div class="col-md-3">
                                <div class="form-group">
                                    <label for="discountType">Discount Type:</label>
                                    <select class="form-control" id="discountType" name="discountType" >
                                        <option value="">Select Discount Type</option>
                                        <option value="value">Flat Discount</option>
                                        <option value="percentage">Percentage (%)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="discountValue">Discount Value:</label>
                                    <input type="number" class="form-control" id="discountValue" name="discountValue" value="0" readonly="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                <label for="discountAmount">Discount Amount:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                        <input class="form-control" type="number" name="discountAmount" id="discountAmount" readonly="" value="0">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                              <div class="form-group">
                                 <label>{{ __('hostel.Discount Remark')}}</label>
                                 <input type="text" name="discount_remark" placeholder="{{ __('hostel.Discount Remark')}}" value="" id="discount_remark" class="form-control">
                              </div>
                           </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                            <label for="totalPayableAmount">Total Payable Amount:</label>
                               <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                    <input class="form-control" type="number" name="totalPayableAmount" id="totalPayableAmount" readonly="" value="0">
                                    <!-- <span class="input-group-addon">.00</span> -->
                                </div>
                            </div>
                        <div class="col-md-3">
                             <label for="totalPayableAmount"> Payable Amount:</label>
                            <div class="input-group">
                           
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                    <input class="form-control" type="text" name="amount" value="0" id="amount" autocomplete="off" data-gtm-form-interact-field-id="2">
                                </div>
                            </div>
                         <div class="col-md-3">
                            <div class="form-group">
                             <label>{{ __('hostel.Payment Mode')}}</label>
                             <select class="form-control" id="payment_mode_id" name="payment_mode_id" onchange="payment_mode_function(this.value);" required>

                                @if(!empty($getPaymentMode))
                                @foreach($getPaymentMode as $value)
                                <option value="{{ $value->id }}">{{ $value->name ?? ''}}</option>
                                @endforeach
                                @endif
                             </select>
                            </div>
                       </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="duesAmount">Dues Amount:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                    <input class="form-control" type="number" name="duesAmount" id="duesAmount" readonly="" value="0">
                                    <!-- <span class="input-group-addon">.00</span> -->
                                </div>
                            </div>
                        </div>
                        </div>
                    	</div>
                    	</div> 
                    
                        
                    </div>
                    
                </div>
                   
                    </div>
                    
                   
								<hr>
                  
                          <div class="row m-2 name" style="display:none;">
								<div class=" col-md-12 title">
									<h5 class="text-danger">{{ __('hostel.Document Upload') }}:-</h5>
								</div>
								<div class="col-md-3">
									<lable>{{ __('hostel.Student ID Proof') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="student_id_proof" id="student_id_proof" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
								<div class="col-md-1">
									<img id="student_id_proof_link"src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
								
								<div class="col-md-3">
									<lable>{{ __('hostel.College Id') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="college_id" id="college_id" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
								<div class="col-md-1">
									<img id="college_id_link" src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
								<div class="col-md-3">
									<lable>{{ __('hostel.Police Verification') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="police_verification" id="police_verification" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
								<div class="col-md-1">
									<img id="police_verification_link" src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
								<div class="col-md-3">
									<lable>{{ __('hostel.Covid Certificate') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="covid_certificate" id="covid_certificate" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
								<div class="col-md-1">
									<img id="covid_certificate_link" src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
								<div class="col-md-3">
									<lable>{{ __('hostel.Father Aadhaar') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="father_adhar" id="father_adhar" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
								<div class="col-md-1">
									<img id="father_adhar_link" src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
								<div class="col-md-3">
									<lable>{{ __('hostel.Other Document') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="other_document" id="other_document" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
								<div class="col-md-1">
									<img id="other_document_link" src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
							</div>


						
                    <div class="row m-2 student_detail d-none">
                        <div class="col-md-12 text-center">
                        <button type="submit" id="uniqueSubmit" class="btn btn-primary" >{{ __('common.submit') }} </button>
                        </div>
                    </div>
                    <!--</form>-->
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
                                         <div class="col-6 col-md-3 border"><b><i class="fa fa-barcode text-purple"></i>&nbsp; {{ __('common.Aadhaar') }}</b></div>
                                         <div class="col-6 col-md-3 border" id="aadhaar1"></div>
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
    
    var baseUrl = "{{ url('/') }}";
    
    $('.hostels').click(function(){
        var hostels = $(this).data('id');
        $.ajax({
            headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
    	    url: baseUrl + '/getBuilding/' + hostels,
    	    success: function(data){
    	        if(data != ''){
    	            $('#appendFloor').html('');
    	            $('#appendBuilding').html(data);
    	        }
    	    }
    	});
    });
    
    $('#appendBuilding').on('click', '.buildings', function() {
        var buildings = $(this).data('id');
        $.ajax({
            headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
    	    url: baseUrl + '/getFloor/' + buildings,
    	    success: function(data){
    	        if(data != ''){
    	            $('#appendFloor').html(data);
    	        }
    	    }
    	});
    });
   
   
    
});
</script>

@endsection      