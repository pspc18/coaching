@php
$getgenders = Helper::getgender();
$getclassType = Helper::classType();
@endphp
@extends('layout.app') 
@section('content')
<div class="content-wrapper">

    <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">   
   <div class="card card-outline card-orange">
				<div class="card-header bg-primary flex_items_toggel">
					<h3 class="card-title"><i class="fa fa-shirtsinbulk"></i> &nbsp;{{ __('staff.Add Teachers') }}</h3>
					<div class="card-tools"> 
					    <a href="{{url('teachers/index')}}" class="btn btn-primary  btn-sm" title="View Teacher"><i class="fa fa-eye"></i> <span class="Display_none_mobile">{{ __('messages.View') }} </span></a> 
					    <a href="{{url('staff_file')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i> <span class="Display_none_mobile">{{ __('messages.Back') }} </span></a>

					</div>
				</div>        
      <div class="panel panel-default">
         <div class="panel-body">
            <!-- personal details row -->
            <form id="form-submit-print" action="{{ url('teachers/add') }}" method="post" enctype="multipart/form-data">
               @csrf  
               <div class="row m-2">
                  <div class=" col-md-12 title">
                     <h5 class="text-danger">{{ __('staff.Personal Details') }}:-</h5>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label style="color:red;">{{ __('staff.Unique Id') }}*</label>
                        <input type="text" class="form-control @error('UniqueId') is-invalid @enderror"  id="UniqueId" name="UniqueId" placeholder="{{ __('staff.Unique Id') }}" readonly value="{{ ($BillCounter->counter  ?? '') + 1 }}">
                       
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label style="color:red;">{{ __(' Name') }}* </label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="first_name" name="first_name" placeholder="{{ __('common.First Name') }}" value="{{old('first_name')}}">
                       
                     </div>
                  </div>
                  <div class="col-md-2 d-none">
                     <div class="form-group">
                        <label style="color:red;">{{ __('common.Last Name') }}* </label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="last_name" name="last_name" placeholder="{{ __('common.Last Name') }}" value="{{old('last_name')}}">
                       
                     </div>
                  </div>                  
                  <div class="col-md-2">
                     <div class="form-group">
                        <label style="color:red;">{{ __('common.Mobile No.') }}*</label>
                        <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="" name="mobile" placeholder="{{ __('common.Mobile No.') }}" value="{{old('mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
                       
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('common.Aadhaar No.') }}</label>
                        <input type="text" class="form-control" id="aadhaar" name="aadhaar" placeholder="{{ __('common.Aadhaar No.') }}" value="{{old('aadhaar')}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
                       		    
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('common.Email') }}</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('common.Email') }}" value="{{old('email')}}">
                    </div>
                  </div>
                  <div class="col-md-2 col-6">
                     <div class="form-group">
                        <label style="color:red;">{{ __('common.Date Of  Birth') }}*</label>
                        <input type="date" class="form-control @error('dob') is-invalid @enderror" id="dob" name="dob" value="">
                        
                     </div>
                  </div>
                  
                  <div class="col-md-2 col-6">
                     <div class="form-group">
                        <label style="color:red;">{{ __('common.Gender') }}</label>
                        <select class="form-control @error('gender_id') is-invalid @enderror select2" id="gender_id" name="gender_id">
                           <option value="">{{ __('common.Select') }}</option>
                           @if(!empty($getgenders)) 
                           @foreach($getgenders as $value)
                           <option value="{{ $value->id}}" {{ ($value->id == old('gender_id')) ? 'selected' : '' }}>{{ $value->name ?? ''  }}</option>
                           @endforeach
                           @endif
                        </select>
                       
                     </div>
                  </div>
                  
                  <div class="col-md-2">
                     <div class="form-group">
                        <label style="color:red;">{{ __('staff.Father/Husband Name') }}*</label>
                        <input type="text" class="form-control @error('father_name') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="father_name" name="father_name" placeholder="{{ __('staff.Father/Husband Name') }}" value="{{old('father_name')}}">
                                           
                     </div>
                  </div>

                  <div class="col-md-2">
                     <div class="form-group">
                        <label style="color:red;">{{ __('staff.Employee Qualification') }}*</label>
                        <input type="text" class="form-control @error('qualification') is-invalid @enderror" id="qualification" name="qualification" placeholder="{{ __('staff.Employee Qualification') }}" value="{{old('qualification')}}">
                       		    
                     </div>
                  </div>
                  <div class="col-md-2 col-6">
                     <div class="form-group">
                        <label>{{ __('staff.Date of Joining') }}</label>
                        <input type="date" class="form-control" id="joining_date" name="joining_date" value="{{date('Y-m-d')}}">
                    </div>
                  </div>
                  

                  <div class="col-md-2 col-6">
                     <div class="form-group">
                        <label>{{ __('common.Class') }}</label>
                        <select class="form-control select2" multiple="" id="" name="class_type_id[]" >
                           <option value="">{{ __('common.Select') }}</option>
                           @if(!empty($getclassType)) 
                           @foreach($getclassType as $value)
                           <option value="{{ $value->id}}" {{ ($value->id == old('class_type_id')) ? 'selected' : '' }}>{{ $value->name ?? ''  }}</option>
                           @endforeach
                           @endif
                        </select>
                     </div>
                  </div>    
                  
                
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="color:red;">{{ __('staff.User Name') }}*</label>
                                <input type="text" class="form-control @error('userName') is-invalid @enderror" id="userName" name="userName" value="{{old('userName')}}" placeholder="{{ __('staff.User Name') }}">
                              
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="color:red;">{{ __('common.Password') }}*</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="{{old('password')}}" placeholder="{{ __('common.Password') }}">
                               
        
                            </div>
                        </div>

                 <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('common.Address') }}</label>
                        <textarea type="text" class="form-control " id="address" name="address" placeholder="{{ __('staff.Teacher Address') }}" >{{old('address')}}</textarea>
                     
                     </div>
                  </div>
                   <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('staff.Refer Name') }}</label>
                        <input type="text" class="form-control" name="refer_name" id="refer_name" onkeydown="return /[a-zA-Z ]/i.test(event.key)" placeholder="{{ __('staff.Refer Name') }}" value="{{old('refer_name')}}">
                     </div> 
                  </div>
                  
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('staff.Refer Mobile') }}</label>
                        <input type="text" class="form-control" name="refer_mobile" id="refer_mobile" placeholder="{{ __('staff.Refer Mobile') }}" value="{{old('refer_mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
                     </div>
                  </div>
                
                     <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('staff.Refer Address') }}</label>
                        <!--<input type="text" class="form-control" name="refer_address" id="refer_address" placeholder="{{ __('Refer Address') }}" value="{{old('refer_address')}}">-->
                    <textarea type="text" class="form-control " id="refer_address" name="refer_address" placeholder="{{ __('staff.Refer Address') }}" >{{old('refer_address')}}</textarea>
                     </div>
                  </div>
                  </div>
                  <div class=" col-md-12 title">
                     <h5 style="color:red">{{ __('staff.Document Upload') }}:-</h5>
                  </div>
               <hr>
               <!-- document upload details  -->
               <div class="row m-2">
                  
                  <!--camera img capture-->
                 
                  <div class="row col md-12">
                     <div class="col-md-3">
                        <div class="form-group">
                           <label>{{ __('common.Photo') }}</label>
                           <input type="file" class="form-control " id="photo" name="photo" value="{{old('photo')}}" accept="image/png, image/jpg, image/jpeg">
								            <p class="text-danger" id="photo_error"></p>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label>{{ __('staff.Id Proof') }}</label>
                           <input type="file" class="form-control " id="id_proof" name="id_proof" value="{{old('id_proof')}}" accept="image/png, image/jpg, image/jpeg">
								            <p class="text-danger" id="proof_error"></p>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label>{{ __('staff.Qualification Proof') }}</label>
                           <input type="file" class="form-control " id="qualification_proof" name="qualification_proof" value="{{old('qualification_proof')}}" accept="image/png, image/jpg, image/jpeg">
								            <p class="text-danger" id="qualification_errors"></p>
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label>{{ __('staff.Experience Letter') }}</label>
                           <input type="file" class="form-control " id="experience_letter" name="experience_letter" value="{{old('experience_letter')}}" accept="image/png, image/jpg, image/jpeg">
								            <p class="text-danger" id="letter_errors"></p>
                        </div>
                     </div>
                     <div class="col-md-3">
                     <div class="form-group">
                        <label>{{ __('common.Pan Card No.') }}</label>
                        <input type="text" class="form-control " id="pan_card" name="pan_card" placeholder="{{ __('common.Pan Card No.') }}" value="{{old('pan_card')}}" maxlength="10">
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>{{ __('common.Bank Name') }}</label>
                        <input type="text" class="form-control " id="bank" name="bank" placeholder="{{ __('common.Bank Name') }}" value="{{old('bank')}}">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('common.Bank Account No.') }}</label>
                        <input type="text" class="form-control " id="account_no" name="account_no" placeholder="{{ __('common.Bank Account No.') }}" value="{{old('account_no')}}" maxlength="18">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('common.Bank IFSC Code') }}</label>
                        <input type="text" class="form-control " id="ifsc_code" name="ifsc_code" placeholder="{{ __('common.Bank IFSC Code') }}" value="{{old('ifsc_code')}}" maxlength="11">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('common.Salary') }}</label>
                        <input type="text" class="form-control " id="salary" name="salary" placeholder="{{ __('common.Salary') }}" value="{{old('salary')}}" onkeypress="javascript:return isNumber(event)">
                     </div>
                  </div>
                  </div>
               </div>
               <hr>
               <!-- leave details  -->
               <div class="row m-2 d-none">
                  <div class=" col-md-12 title">
                     <h5 style="color:red">{{ __('staff.Leave Details') }}:-</h5>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('staff.Medical Leave') }}</label>
                        <input type="text" class="form-control " id="medical_leave" name="medical_leave" placeholder="{{ __('staff.Medical Leave') }}" value="{{old('medical_leave')}}" onkeypress="javascript:return isNumber(event)">
                     </div>
                  </div>                  
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('staff.Casual Leave') }}</label>
                        <input type="text" class="form-control " id="casual_leave" name="casual_leave" placeholder="{{ __('staff.Casual Leave') }}" value="{{old('casual_leave')}}" onkeypress="javascript:return isNumber(event)">
                     </div>
                  </div>

                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('staff.Other Leave') }}</label>
                        <input type="text" class="form-control " id="other_leave" name="other_leave" placeholder="{{ __('staff.Other Leave') }}" value="{{old('other_leave')}}" onkeypress="javascript:return isNumber(event)">
                     </div>
                  </div>
               </div>


               <div class="col-md-12 text-center">
                  <button type="submit" id="submitButton" class="btn btn-primary btn-submit">{{ __('common.submit') }}</button><br><br>
               </div>
            </form>
         </div>
      </div>
   </div>
   </div>
   </div>
   </div>
   </section>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $('#photo').change(function(e){
            $('#photo_error').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#photo_error').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#photo_error').html("");
            }
        }else{
            $('#photo_error').html("Image Size File");
            $(this).val('');
        }
        });
    });
   

    $(document).ready(function(){
        $('#id_proof').change(function(e){
            $('#proof_error').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#proof_error').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#proof_error').html("");
            }
        }else{
            $('#proof_error').html("Image Size File");
            $(this).val('');
        }
        });
    });
   

    $(document).ready(function(){
        $('#qualification_proof').change(function(e){
            $('#qualification_errors').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#qualification_errors').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#qualification_errors').html("");
            }
        }else{
            $('#qualification_errors').html("Image Size File");
            $(this).val('');
        }
        });
    });
    $(document).ready(function(){
        $('#experience_letter').change(function(e){
            $('#letter_errors').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#letter_errors').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#letter_errors').html("");
            }
        }else{
            $('#letter_errors').html("Image Size File");
            $(this).val('');
        }
        });
    });
   
</script>
<style>
    #photo_error{
        font-weight: bold;
    font-size: 14px;
    }
    #letter_errors{
        font-weight: bold;
    font-size: 14px;
    }
    #qualification_errors{
        font-weight: bold;
    font-size: 14px;
    }
    #proof_error{
        font-weight: bold;
    font-size: 14px;
    }
   
   
</style>
@endsection