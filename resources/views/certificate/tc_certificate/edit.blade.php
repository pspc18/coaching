@php
   $getstudents = Helper::getstudents();
   $classType = Helper::classType();
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
							<h3 class="card-title"><i class="fa fa-certificate"></i> &nbsp;{{ __('certificate.Edit Tc Certificate') }} </h3>
							<div class="card-tools"><a href="{{url('tc/certificate/index')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(5)->view ? '' : 'd-none' }}" ><i class="fa fa-eye"></i>{{ __('common.View') }}  </a>
							     <a href="{{url('tc/certificate/index')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a> </div>
						</div>
   </div>
</div> 
</div>
</div>
<div class="tc_list_show"></div>
        <div class="card">
            <div class="card-body">
                <form id="quickForm" action="{{ url('tc/certificate/edit') }}/{{($data->id)}}" method="post" >
                     @csrf
            <div class="row">
                <div class="col-md-3">
        			<div class="form-group">
        				<label style="color:red;">{{ __('certificate.Admission No.') }}*</label>
        				<input type="text" name="admission_id" id="admission_id" class="form-control" onkeydown="return /[a-zA-Z ]/i.test(event.key)" readonly="readonly" placeholder="{{ __('certificate.Admission No.') }}" value="{{$data->admission_id}}">
                        	
        		    </div>
		        </div>
                <div class="col-md-3">
        			<div class="form-group">
        				<label style="color:red;">{{ __('certificate.Student Name') }}*</label>
        				<input type="text" name="name" id="first_name" class="form-control @error('name') is-invalid @enderror" readonly="readonly" onkeydown="return /[a-zA-Z ]/i.test(event.key)" placeholder="{{ __('certificate.Student Name') }}" value="{{$data->stu_name}}">
                        	@error('name')
        						<span class="invalid-feedback" role="alert">
        							<strong>{{ $message }}</strong>
        						</span>
        					@enderror
        		    </div>
		        </div>
            		<div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('common.Class') }} *</label>
            				
            				<select class="form-control @error('class_type_id') is-invalid @enderror" id="class_type_id1" name="class_type_id" value="{{$data->student_class}}">
            				   
                             @if(!empty($classType)) 
                                  @foreach($classType as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ $type->id == $data->student_class ? 'selected' : '' }} >{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                             @error('class_type_id')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
            		    </div>
            		</div>
            		
        
                    <div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('certificate.examination last taken with result.') }}*</label>
            			    <div class="flex_Inputes">
                				<div class="d-flex">
                    				<p>{{ __('certificate.Yes') }}</p>
                    				<input type="radio" name="taken_result" id="taken_result"  {{("yes" == $data['taken_result'] ) ? 'checked' : '' }} class="form-control @error('fail_pass') is-invalid @enderror radio_input_size" value="yes" >
                    				</div>
                    			<div class="d-flex ml-3">
                    				<p>{{ __('certificate.No') }}</p>
                    				<input type="radio" name="taken_result" id="taken_result"  {{("no" == $data['taken_result'] ) ? 'checked' : '' }} class="form-control @error('fail_pass') is-invalid @enderror radio_input_size" value="no" >
                                </div>	
                            </div>
                            	@error('taken_result')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
            			<div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('common.Fathers Name') }} *</label>
            				<input type="text" name="father_name" id="father_name" onkeydown="return /[a-zA-Z ]/i.test(event.key)" readonly="readonly" class="form-control @error('father_name') is-invalid @enderror" placeholder="{{ __('common.Fathers Name') }}" value="{{$data->stu_father_name}}">
                            	@error('father_name')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
            		<div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('common.Mothers Name') }}*</label>
            				<input type="text" name="mother_name" id="mother_name1" onkeydown="return /[a-zA-Z ]/i.test(event.key)" readonly="readonly" class="form-control @error('mother_name') is-invalid @enderror" placeholder="{{ __('common.Mothers Name') }}" value="{{$data->mother_names}}">
                            	@error('mother_name')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
           
                    <div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('common.Date Of  Birth') }}*</label>
            				<input type="date" name="dob" id="dob1" class="form-control @error('dob') is-invalid @enderror" value="{{$data->dob}}">
                            	@error('dob')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
            	<!--	<div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('master.Student s Admission No.') }} *</label>
            				<input type="text" name="students_admission_no" id="admission_no1" class="form-control @error('students_admission_no') is-invalid @enderror" placeholder=" {{ __('master.Student s Admission No.') }}" value="{{$data->students_admission_no}}" onkeypress="javascript:return isNumber(event)">
                            	@error('students_admission_no')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>-->
            		<div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('certificate.Admission Date') }}*</label>
            				<input type="date" name="admission_date" id="admission_date1" class="form-control @error('admission_date') is-invalid @enderror" value="{{$data->admission_date}}">
                            	@error('admission_date')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
            
            		<div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('certificate.Issue Date') }}*</label>
            				<input type="date" name="issue_date" id="issue_date" class="form-control @error('issue_date') is-invalid @enderror" value="{{$data->issue_date}}">
                            	@error('issue_date')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
                   <div class="col-md-3">
            			<div class="form-group">
            				<label>{{ __('certificate.Fail/Pass.') }}</label>
            				<label>{{ $data['fail_pass'] }} </label>
            				<div class="flex_Inputes">
                				<div class="d-flex">
                    				<p>{{ __('certificate.Yes') }}</p>
                    				<input type="radio" name="fail_pass" id="fail_pass"  {{("yes" == $data['fail_pass'] ) ? 'checked' : '' }} class="form-control @error('fail_pass') is-invalid @enderror radio_input_size" value="yes" >
                    				</div>
                    			<div class="d-flex ml-3">
                    				<p>{{ __('certificate.No') }}</p>
                    				<input type="radio" name="fail_pass" id="fail_pass"  {{("no" == $data['fail_pass'] ) ? 'checked' : '' }} class="form-control @error('fail_pass') is-invalid @enderror radio_input_size" value="no" >
                                </div>	
                            </div>
                            	@error('fail_pass')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
                   <div class="col-md-3">
            			<div class="form-group">
            				<label>{{ __('certificate.Studied.') }}</label>
            				<div class="flex_Inputes">
                				<div class="d-flex">
                    				<p>{{ __('certificate.Yes') }}</p>
                    				<input type="radio" name="subjects_studied" id="subjects_studied"  {{("yes" == $data['subject'] ) ? 'checked' : '' }} class="form-control @error('fail_pass') is-invalid @enderror radio_input_size" value="yes" >
                    				</div>
                    			<div class="d-flex ml-3">
                    				<p>{{ __('certificate.No') }}</p>
                    				<input type="radio" name="subjects_studied" id="subjects_studied"  {{("no" == $data['subject'] ) ? 'checked' : '' }} class="form-control @error('fail_pass') is-invalid @enderror radio_input_size" value="no" >
                                </div>	
                            </div>
                            	@error('subjects_studied')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
                   <div class="col-md-3">
            			<div class="form-group">
            				<label>{{ __('certificate.Promotion to Higher Class.') }}</label>
            				<div class="flex_Inputes">
                				<div class="d-flex">
                    				<p>{{ __('certificate.Yes') }}</p>
                    				<input type="radio" name="higher_class" id="higher_class"  {{("yes" == $data['higher_class'] ) ? 'checked' : '' }} class="form-control @error('fail_pass') is-invalid @enderror radio_input_size" value="yes" >
                    				</div>
                    			<div class="d-flex ml-3">
                    				<p>{{ __('certificate.No') }}</p>
                    				<input type="radio" name="higher_class" id="higher_class"  {{("no" == $data['higher_class'] ) ? 'checked' : '' }} class="form-control @error('fail_pass') is-invalid @enderror radio_input_size" value="no" >
                                </div>	
                            </div>
                            	@error('higher_class')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
                   <div class="col-md-3">
            			<div class="form-group">
            				<label>{{ __('certificate.Paid School Dues.') }}</label>
            				<div class="flex_Inputes">
                				<div class="d-flex">
                    				<p>{{ __('certificate.Yes') }}</p>
                    				<input type="radio" name="paid_school_dues" id="paid_school_dues"  {{("yes" == $data['paid_school_dues'] ) ? 'checked' : '' }} class="form-control @error('fail_pass') is-invalid @enderror radio_input_size" value="yes" >
                    				</div>
                    			<div class="d-flex ml-3">
                    				<p>{{ __('certificate.No') }}</p>
                    				<input type="radio" name="paid_school_dues" id="paid_school_dues"  {{("no" == $data['paid_school_dues'] ) ? 'checked' : '' }} class="form-control @error('fail_pass') is-invalid @enderror radio_input_size" value="no" >
                                </div>	
                            </div>
                            	@error('paid_school_dues')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
                   <div class="col-md-3">
            			<div class="form-group">
            				<label>{{ __('certificate.Have you got any scholarship.') }}</label>
            				<div class="flex_Inputes">
                				<div class="d-flex">
                    				<p>{{ __('certificate.Yes') }}</p>
                    				<input type="radio" name="any_scholarship" id="any_scholarship"  {{("yes" == $data['any_scholarship'] ) ? 'checked' : '' }} class="form-control @error('fail_pass') is-invalid @enderror radio_input_size" value="yes" >
                    				</div>
                    			<div class="d-flex ml-3">
                    				<p>{{ __('certificate.No') }}</p>
                    				<input type="radio" name="any_scholarship" id="any_scholarship"  {{("no" == $data['any_scholarship'] ) ? 'checked' : '' }} class="form-control @error('fail_pass') is-invalid @enderror radio_input_size" value="no" >
                                </div>	
                            </div>
                            	@error('any_scholarship')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
                   <div class="col-md-3">
            			<div class="form-group">
            				<label>{{ __('certificate.Have You any Type Sports Certificate.') }}</label>
            				<input type="text" name="sports_certificate" id="sports_certificate" class="form-control @error('sports_certificate') is-invalid @enderror" placeholder="{{ __('certificate.Cricket/Kabaddi/Yes /No.') }}" value="{{$data->sports_certificate}}">
                            	@error('sports_certificate')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
            		    <div class="col-md-3">
            			<div class="form-group">
            				<label>{{ __('certificate.SSSM ID No.') }}</label>
            				<input type="text" name="sssm_id_no" id="sssm_id_no" class="form-control @error('sssm_id_no') is-invalid @enderror" placeholder="{{ __('certificate.SSSM ID No.') }}" value="{{$data->sssm_id_no}}" onkeypress="javascript:return isNumber(event)">
                            	@error('sssm_id_no')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
            		    <div class="col-md-3">
            			<div class="form-group">
            				<label>{{ __('certificate.What is Behavior.') }}</label>
            				<input type="text" name="behavior" id="behavior" class="form-control @error('behavior') is-invalid @enderror" placeholder="{{ __('certificate.What is Behavior.') }}" value="{{$data->behavior}}" >
                            	@error('behavior')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
            		
            		<div class="col-md-3">
            			<div class="form-group">
            				<label>{{ __('certificate.Student UID No.') }}</label>
            				<input type="text" name="student_uid_no" id="student_uid_no" class="form-control @error('student_uid_no') is-invalid @enderror" placeholder="{{ __('certificate.Student UID No.') }}" value="{{$data->student_uid_no}}" onkeypress="javascript:return isNumber(event)">
                            	@error('student_uid_no')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
            		
            	
            	
            	<!--	<div class="col-md-3">
            			<div class="form-group">
            				<label>{{ __('master.Class In Which Leaving') }} </label>
            				<input type="text" name="which_leaving" id="which_leaving" class="form-control @error('which_leaving') is-invalid @enderror" placeholder="{{ __('master.Class In Which Leaving') }}" value="{{ old('which_leaving') }}">
                            	@error('which_leaving')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>-->
            
                    <div class="col-md-3">
            			<div class="form-group">
            				<label>{{ __('certificate.Due If Any') }}</label>
            				<input type="text" name="due_any" id="due_any" class="form-control @error('due_any') is-invalid @enderror" placeholder="{{ __('certificate.Due If Any') }}" value="{{$data->due_any}}">
                            	@error('due_any')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
            
            	       <div class="col-md-3">
            			<div class="form-group">
            				<label>{{ __('certificate.Medium') }}</label>
            				<input type="text" name="mudium" id="mudium" class="form-control @error('mudium') is-invalid @enderror" placeholder="{{ __('certificate.Medium') }}" value="{{$data->mudium}}">
                            	@error('mudium')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
            	       <div class="col-md-3">
            			<div class="form-group">
            				<label>{{ __('certificate.Reasons for leaving the school') }}</label>
            				<input type="text" name="reasons_leaving" id="reasons_leaving" class="form-control @error('reasons_leaving') is-invalid @enderror" placeholder="{{ __('certificate.Reasons for leaving the school') }}" value="{{$data->reasons_leaving}}">
                            	@error('reasons_leaving')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror
            		    </div>
            		</div>
            	       <div class="col-md-3">
            			<div class="form-group">
            				<label>{{ __('certificate.Any other remarks') }}</label>
            				<input type="text" name="any_remark" id="any_remark" class="form-control @error('any_remark') is-invalid @enderror" placeholder="{{ __('certificate.Any other remarks') }}" value="{{$data->any_remark}}">
                            	@error('any_remark')
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
                         <button type="submit" class="btn btn-primary">{{ __('common.Submit') }}</button>
                        </div>
                    </div>
            </form>
        </div>
</div>


<script>
    
        function SearchValue() {
            
            var class_type_id = $('#class_type_id :selected').val();
            var country_id = $('#country_id :selected').val();
            var state_id = $('#state_id :selected').val();
            var city_id = $('#city_id :selected').val();
            $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                type:'post',
                url: '/search_tc',
                data: {class_type_id:class_type_id,country_id:country_id,state_id:state_id,city_id:city_id},
                 //dataType: 'json',
                success: function (data) {
                   
                  
                    $('.tc_list_show').html(data);
                   
                }
              });
        };
    
         function showData(student_id) {
           
            $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                type:'post',
                url: '/tc_add_click',
                data: {student_id : student_id},
                 dataType: 'json',
                success: function (data) {
                     
                     console.log(data);
                 if(data){
                     
                $('#name').val(data.name);
                $('#class_id').val(data.class_type_id);
                $('#father_name').val(data.father_name);
                $('#iessu_date').val(data.iessu_date);
                $('#mother_name').val(data.mother_name);
                $('#dob').val(data.dob);
                $('#students_adnission_no').val(data.students_adni_no);
                $('#admission_date').val(data.dob);
                $('#subject').val(data.subject);
                 $('#student_uid_no').val(data.regUniqueId);
                 }else{
                     alert('No more records found');
                 }
                    
                }
              });
        };    
        </script>
        
        <style>
            .flex_Inputes{
                display: flex;
            }
            
            .radio_input_size{
                width: 20px;
                margin-left: 10px;
            }
        </style>
@endsection