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
				<div class="card-header bg-primary">
					<h3 class="card-title"><i class="fa fa-edit"></i> &nbsp; {{ __('staff.Edit Teachers') }}</h3>
					<div class="card-tools">
					      <a href="{{url('teachers/index')}}" class="btn btn-primary  btn-sm" title="View Teacher"><i class="fa fa-eye"></i> <span class="Display_none_mobile">{{ __('messages.View') }} </span></a> 
					     <a href="{{url('staff_file')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}</a>
					     </div>
				</div>         
    <div class="panel panel-default">
        <div class="panel-body">
   <!-- personal details row -->
    <form id="form-submit-edit" action="{{ url('teachers/edit') }}/{{$data['id'] ?? '' }}" method="post" enctype="multipart/form-data">
        @csrf  
        
    <div class="row m-2">
        <div class=" col-md-12 title"><h5 class="text-danger">{{ __('staff.Personal Details') }}:-</h5></div>
        <div class="col-md-2">
			<div class="form-group">
				<label style="color:red;">{{ __('staff.Unique Id') }}*</label>
				<input type="text" class="form-control @error('UniqueId') is-invalid @enderror" id="UniqueId" name="UniqueId" placeholder="{{ __('staff.Unique Id') }}" readonly value="{{ $data['UniqueId'] ?? '' }}">
		       
		    </div>
		</div>

          <div class="col-md-2">
             <div class="form-group">
                <label style="color:red;">{{ __('Name') }}* </label>
                <input type="text" class="form-control @error('first_name') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)"  id="first_name" name="first_name" placeholder="{{ __('common.First Name') }}" value="{{$data['first_name'] ?? '' }}">
               
             </div>
          </div>
          <div class="col-md-2 d-none">
             <div class="form-group">
                <label style="color:red;">{{ __('common.Last Name') }}* </label>
                <input type="text" class="form-control @error('last_name') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="last_name" name="last_name" placeholder="{{ __('common.Last Name') }}" value="{{$data['last_name'] ?? '' }}">
                
             </div>
          </div> 		
		<div class="col-md-2">
	    	<div class="form-group">
				<label style="color:red;">{{ __('common.Mobile No.') }}*</label>
				<input type="text" class="form-control @error('mobile') is-invalid @enderror" id="" name="mobile" placeholder="{{ __('common.Mobile No.') }}" value="{{ $data['mobile'] ?? '' }}" maxlength="10" onkeypress="javascript:return isNumber(event)">
		       
		    </div>
		</div>

		<div class="col-md-2">
	    	<div class="form-group">
				<label>{{ __('common.Aadhaar No.') }}</label>
				<input type="text" class="form-control" id="aadhaar" name="aadhaar" placeholder="{{ __('common.Aadhaar No.') }}" value="{{ $data['aadhaar'] ?? '' }}" maxlength="12" onkeypress="javascript:return isNumber(event)">
		    </div>
		</div>
		
		 <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('common.Email') }}</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('common.Email') }}" value="{{ $data['email'] ?? '' }}">
                    </div>
                  </div>
		
		
		<div class="col-md-2">
        <div class="form-group">
				<label style="color:red;">{{ __('common.Date Of  Birth') }}*</label>
				<input type="date" class="form-control @error('dob') is-invalid @enderror" id="dob" name="dob" value="{{ $data['dob'] ?? '' }}">
		        
		    </div>
		</div>

		<div class="col-md-2">
        <div class="form-group">
                  <label style="color:red;">{{ __('common.Gender') }}*</label>
                 <select class="form-control @error('gender_id') is-invalid @enderror" id="gender_id" name="gender_id">
    				<option value="">{{ __('common.Select') }}</option>
                     @if(!empty($getgenders)) 
                          @foreach($getgenders as $value)
                             <option value="{{ $value->id}}" {{ ( $value->id == $data['gender_id'] ? 'selected' : '' ) }}>{{ $value->name ?? ''  }}</option>
                          @endforeach
                      @endif
                    </select>
		       
                </div>
		</div>

<div class="col-md-2">
                     <div class="form-group">
                        <label style="color:red;">{{ __('staff.Father/Husband Name') }}*</label>
                        <input type="text" class="form-control @error('father_name') is-invalid @enderror"onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="father_name" name="father_name" placeholder="{{ __('staff.Father/Husband Name') }}" value="{{$data['father_name'] ?? ''}}">
                                        
                     </div>
                  </div>

                  <div class="col-md-2">
                     <div class="form-group">
                        <label style="color:red;">{{ __('staff.Employee Qualification') }}*</label>
                        <input type="text" class="form-control @error('qualification') is-invalid @enderror" id="qualification" name="qualification" placeholder="{{ __('staff.Employee Qualification') }}" value="{{$data['qualification'] ?? ''}}">
                       		    
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('staff.Date of Joining') }}</label>
                        <input type="date" class="form-control" id="joining_date" name="joining_date" value="{{$data['joining_date'] ?? ''}}">
                    </div>
                  </div>



                  @if(Session::get('role_id') == 1)      

                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('Class Teacher') }}</label>
                        <select class="form-control" multiple id="class_type_id" name="class_type_id[]" >
                           <option value="">{{ __('common.Select') }}</option>
                            @if(!empty($getclassType)) 
                @php
                    $selectedClassIds = unserialize($data['class_type_id']);
                    $selectedClassIds = is_array($selectedClassIds) ? $selectedClassIds : [];
                @endphp
                @foreach($getclassType as $value)
                    <option value="{{ $value->id }}" {{ in_array($value->id, $selectedClassIds) ? 'selected' : '' }}>
                        {{ $value->name ?? '' }}
                    </option>
                @endforeach
            @endif
                        </select>
                        
                     </div>
                  </div>  
                 @endif
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="color:red;">{{ __('staff.User Name') }}*</label>
                                <input type="text" class="form-control @error('userName') is-invalid @enderror" id="userName" name="userName" value="{{$data['userName'] ?? ''}}" placeholder="{{ __('staff.User Name') }}">
                              
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="color:red;">{{ __('common.Password') }}*</label>
                              <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="{{$data['password'] ?? ''}}" placeholder="{{ __('common.Password') }}">
                                <span class="password-toggle-icon" onclick="togglePasswordVisibility()" style="position: absolute;  margin-top: -26px;  margin-left: 79%;"><i class="fa fa-eye"></i>️</span>

                               
                            </div>
                        </div>
                            <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('staff.Teacher Address') }}</label>
                        <textarea type="text" class="form-control " id="address" name="address" placeholder="{{ __('staff.Teacher Address') }}" >{{ $data['address'] ?? '' }}</textarea>
                     
                     </div>
                  </div>
                        </div>
                  <div class="row m-2">
                         
                <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('staff.Refer Name') }}</label>
                        <input type="text" class="form-control" name="refer_name" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="refer_name" placeholder="{{ __('staff.Refer Name') }}" value="{{$data['refer_name'] ?? ''}}"> 
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('staff.Refer Mobile') }}</label>
                        <input type="text" class="form-control" name="refer_mobile" id="refer_mobile" placeholder="{{ __('staff.Refer Mobile') }}" value="{{$data['refer_mobile'] ?? ''}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('staff.Refer Address') }}</label>
                        <!--<input type="text" class="form-control" name="refer_address" id="refer_address" placeholder="{{ __('Refer Address') }}" value="{{$data['refer_address'] ?? ''}}">-->
                     <textarea type="text" class="form-control " id="refer_address" name="refer_address" placeholder="{{ __('staff.Refer Address') }}" >{{ $data['refer_address'] ?? '' }}</textarea>
                     </div>
                  </div>
               
               
                  
               </div>
	</div>
	<hr>
	

	<div class="row m-2">
    <div class=" col-md-12 title"><h5 style="color:red">{{ __('staff.Document Upload') }}:-</h5></div>


        <!--camera img capture-->
       
        @php
        
        @endphp
 
		<!-- PHOTO -->
    <div class="col-md-3">
        <div class="form-group">
            <label>{{ __('common.Photo') }}</label><br>
            <div class=" form-group image-wrapper position-relative d-inline-block">
                <img width="100" height="100" class="mt-2 mb-2 doc_img" src="{{ env('IMAGE_SHOW_PATH') . (!empty($data_user_doc['photo']) ? 'profile/' . $data_user_doc['photo'] : 'default/Icon_images/noImage.png') }}" />
                @if(!empty($data_user_doc['photo']))
                    <button type="button" class="btn btn-danger btn-sm delete-image-btn" title="Delete Image" data-bs-toggle="modal" data-bs-target="#Modal_image" data-type="photo" data-id="{{ $data->id ?? '' }}">
                        <i class="fa fa-trash"></i>
                    </button>
                @endif
            </div>
            <input type="file" class="form-control mt-2" id="photo" name="photo" accept="image/*">
            <p class="text-danger" id="photo_error"></p>
        </div>
    </div>
    
    <!-- ID PROOF -->
    <div class="col-md-3">
        <div class="form-group">
            <label>{{ __('staff.Id Proof') }}</label><br>
            <div class=" form-group image-wrapper position-relative d-inline-block">
                <img width="100" height="100" class="mt-2 mb-2 doc_img"
                     src="{{ env('IMAGE_SHOW_PATH') . (!empty($data_user_doc['Id_proof_img']) ? 'teacher/teacher_' . $data->id . '/id_proof/' . $data_user_doc['Id_proof_img'] : 'default/Icon_images/noImage.png') }}" />
                @if(!empty($data_user_doc['Id_proof_img']))
                    <button type="button" class="btn btn-danger btn-sm delete-image-btn" title="Delete Image" data-bs-toggle="modal" data-bs-target="#Modal_image" data-type="Id_proof_img" data-id="{{ $data->id ?? '' }}">
                        <i class="fa fa-trash"></i>
                    </button>
                @endif
            </div>
            <input type="file" class="form-control mt-2" id="id_proof" name="id_proof" accept="image/*">
            <p class="text-danger" id="proof_error"></p>
        </div>
    </div>
    
    <!-- QUALIFICATION PROOF -->
    <div class="col-md-3">
        <div class="form-group">
            <label>{{ __('staff.Qualification Proof') }}</label><br>
            <div class=" form-group image-wrapper position-relative d-inline-block">
                <img width="100" height="100" class="mt-2 mb-2 doc_img"
                     src="{{ env('IMAGE_SHOW_PATH') . (!empty($data_user_doc['qualification_proof_img']) ? 'teacher/teacher_' . $data->id . '/qualification_proof/' . $data_user_doc['qualification_proof_img'] : 'default/Icon_images/noImage.png') }}" />
                @if(!empty($data_user_doc['qualification_proof_img']))
                    <button type="button" class="btn btn-danger btn-sm delete-image-btn" title="Delete Image"  data-bs-toggle="modal" data-bs-target="#Modal_image" data-type="qualification_proof_img" data-id="{{ $data->id ?? '' }}">
                        <i class="fa fa-trash"></i>
                    </button>
                @endif
            </div>
            <input type="file" class="form-control mt-2" id="qualification_proof" name="qualification_proof" accept="image/*">
            <p class="text-danger" id="qualification_errors"></p>
        </div>
    </div>
    
    <!-- EXPERIENCE LETTER -->
    <div class="col-md-3">
        <div class="form-group">
            <label>{{ __('staff.Experience Letter') }}</label><br>
            <div class=" form-group image-wrapper position-relative d-inline-block">
                <img width="100" height="100" class="mt-2 mb-2 doc_img"
                     src="{{ env('IMAGE_SHOW_PATH') . (!empty($data_user_doc['experience_letter_img']) ? 'teacher/teacher_' . $data->id . '/experience_letter/' . $data_user_doc['experience_letter_img'] : 'default/Icon_images/noImage.png') }}" />
                @if(!empty($data_user_doc['experience_letter_img']))
                    <button type="button" class="btn btn-danger btn-sm delete-image-btn" title="Delete Image" data-bs-toggle="modal"  data-bs-target="#Modal_image" data-type="experience_letter_img"  data-id="{{ $data->id ?? '' }}">
                        <i class="fa fa-trash"></i>
                    </button>
                @endif
            </div>
            <input type="file" class="form-control mt-2" id="experience_letter" name="experience_letter" accept="image/*">
            <p class="text-danger" id="letter_errors"></p>
        </div>
    </div>


      <div class="col-md-3">
         <div class="form-group">
            <label>{{ __('common.Pan Card No.') }}</label>
            <input type="text" class="form-control " id="pan_card" name="pan_card" placeholder="{{ __('common.Pan Card No.') }}" value="{{$data['pan_no'] ?? '' }}" maxlength="10">
         </div>
      </div>
      <div class="col-md-3">
         <div class="form-group">
            <label>{{ __('common.Bank Name') }}</label>
            <input type="text" class="form-control " id="bank" name="bank" placeholder="{{ __('common.Bank Name') }}" value="{{$data['bank_name'] ?? ''}}">
         </div>
      </div>
      <div class="col-md-2">
         <div class="form-group">
            <label>{{ __('common.Bank Account No.') }}</label>
            <input type="text" class="form-control " id="account_no" name="account_no" placeholder="{{ __('common.Bank Account No.') }}" value="{{$data['account_no'] ?? ''}}" maxlength="18">
         </div>
      </div>
      <div class="col-md-2">
         <div class="form-group">
            <label>{{ __('common.Bank IFSC Code') }}</label>
            <input type="text" class="form-control " id="ifsc_code" name="ifsc_code" placeholder="{{ __('common.Bank IFSC Code') }}" value="{{$data['ifsc_code'] ?? '' }}" maxlength="11">
         </div>
      </div>
      <div class="col-md-2">
         <div class="form-group">
            <label>{{ __('common.Salary') }}</label>
            <input type="text" class="form-control " id="salary" name="salary" placeholder="{{ __('common.Salary') }}" value="{{$data['salary'] ?? ''}}" onkeypress="javascript:return isNumber(event)">
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
                        <input type="text" class="form-control " id="medical_leave" name="medical_leave" placeholder="{{ __('staff.Medical Leave') }}" value="{{$data['medical_leave'] ?? ''}}" onkeypress="javascript:return isNumber(event)">
                     </div>
                  </div>                  
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('staff.Casual Leave') }}</label>
                        <input type="text" class="form-control " id="casual_leave" name="casual_leave" placeholder="{{ __('staff.Casual Leave') }}" value="{{$data['casual_leave'] ?? ''}}" onkeypress="javascript:return isNumber(event)">
                     </div>
                  </div>

                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('staff.Other Leave') }}</label>
                        <input type="text" class="form-control " id="other_leave" name="other_leave" placeholder="{{ __('staff.Other Leave') }}" value="{{$data['other_leave'] ?? ''}}" onkeypress="javascript:return isNumber(event)">
                     </div>
                  </div>
                   @if(Session::get('role_id') == 1)  
                  <div class="col-md-2">
                     <div class="form-group">
                        <label >{{ __('Teacher Update') }}</label>
                        <select class="form-control" id="teacher_update" name="teacher_update" >
                           <option value="">{{ __('common.Select') }}</option>
                           <option value="0" {{ (0 == $data['teacher_update']) ? 'selected' : '' }}>On</option>
                           <option value="1" {{ (1 == $data['teacher_update']) ? 'selected' : '' }}>Off</option>
                          
                        </select>
                        
                     </div>
                  </div>  
                  @endif
               </div>
               
               <div class="col-md-12 text-center">
               @if(Session::get('role_id') == 1)  
               <button type="submit" class="btn btn-primary" id="submitButton">{{ __('common.Update') }}</button>
               @else
                  @if($data['teacher_update'] == 0)
                                 <button type="submit" class="btn btn-primary btn-submit" id="submitButton">{{ __('common.Update') }}</button>

                  @endif
               @endif
               <br><br>
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



<button type="button" class="btn btn-primary" style='visibility:hidden' data-bs-toggle="modal" data-bs-target="#exampleModal">
 modal
</button>

<!-- Modal -->
<div class="modal bottom" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Class Teacher Assigned</h5>
        <button type="button" class='btn btn-danger' data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
      </div>
      <div class="modal-body">
      
      <div class='row'>
          <div class='col-md-3'><img id="ct_image" class="doc_img" src="" width="100%" height="100%"></div>
          <div class='col-md-3'><p id="ct_name"></p><p id="class_name"></p><p id="ct_mobile"></p></div>
          <div class='col-md-3'></div>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id='remove_it' data-teacher_id ='' >Unassign Class Teacher</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>



<!-- Delete Confirmation Modal -->
<div class="modal fade" id="Modal_image" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">
      <div class="modal-header">
        <h5 class="modal-title text-white">Delete Confirmation</h5>
        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">
          <i class="fa fa-times" aria-hidden="true"></i>
        </button>
      </div>

      <form action="{{ url('imageDeletestaff') }}" method="POST">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="type" id="delete_type">
          <input type="hidden" name="id" id="delete_id">
          <h5 class="text-white">Are you sure you want to delete this image?</h5>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-image-btn').forEach(button => {
        button.addEventListener('click', function () {
            document.getElementById('delete_id').value = this.dataset.id;
            document.getElementById('delete_type').value = this.dataset.type;
        });
    });
});
</script>

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
        
/*        
        $('#exampleModal').on('hidden.bs.modal', function (e) {
            alert("ok");
            $('#class_type_id').val("");
        });*/
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
        
        
          $('#class_type_id').change(function(){
              var class_type_id = $(this).val();
              var teacher_id = "{{$data['id'] ?? '' }}";
              	var basurl = "{{ url('/') }}";
              	
              	if(class_type_id != '')
              	{
              				$.ajax({
				headers: {
					'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
				},
				type: 'post',
				url: basurl + '/checkClassTeacher',
				data: {
					class_type_id: class_type_id,
				// 	teacher_id: teacher_id,
				},
				//dataType: 'json',
				success: function(data) {
				    
			if(data.teacher != null  && data.teacher.id != teacher_id)
			{
			       $('#class_type_id').val("");
			       $('#exampleModal').modal('show');
			       $('#remove_it').attr('data-teacher_id',data.teacher.id);
			       $('#remove_it').attr('data-class_type_id',class_type_id);
			       $('#ct_name').html(data.teacher.first_name + ' ' + data.teacher.last_name);
			       $('#class_name').html(data.teacher.class_name);
			       $('#ct_mobile').html(data.teacher.mobile);
			        if(data.teacher.photo != ''){
			            var ct_image = "{{ env('IMAGE_SHOW_PATH').'profile/' }}" + data.teacher.photo;
                        $('#ct_image').attr('src', ct_image);
                    }
			       
			     // toastr.error('Teacher ' +  data.teacher.first_name + data.teacher.last_name + ' is already assigned for this class')
			}
              
				}
			});
              	}
              
             
    });
          $('#remove_it').click(function(){
              var teacher_id = $(this).attr('data-teacher_id');
              var class_type_id = $(this).attr('data-class_type_id');
              	var basurl = "{{ url('/') }}";
              	
              				$.ajax({
				headers: {
					'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
				},
				type: 'post',
				url: basurl + '/removeClassTeacher',
				data: {
					teacher_id: teacher_id,
				},
				//dataType: 'json',
				success: function(data) {
			if(data.message == 'success')
			{
			    $('#class_type_id').val(class_type_id);
			        $('#exampleModal').modal('hide');
			     toastr.success('Teacher is removed from the class now you can reassign it')
			     
			}else
			{
			     $('#exampleModal').modal('hide');
			     toastr.error('Something Went Wrong')
			}
              
				}
			}); 
              
             
    });
    
    
    });
   
</script>

 <script>
       function togglePasswordVisibility() {
  const passwordInput = document.getElementById('password');
  const icon = document.querySelector('.password-toggle-icon i');
  
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    icon.classList.remove('fa-eye');
    icon.classList.add('fa-eye-slash');
  } else {
    passwordInput.type = 'password';
    icon.classList.remove('fa-eye-slash');
    icon.classList.add('fa-eye');
  }
}

    </script>
<style>
    #photo_error{
        font-weight: bold;
    font-size: 14px;
    }
    .doc_img{
        border-radius:10px;
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
   
    .password-toggle-icon {
            cursor: pointer;
        }
        .modal.bottom .modal-dialog {
  position: fixed;
  margin: auto;
  width: 100%;
  height: 100%;
  left: 0;
  right: 0;
  bottom: -100%;
  transition: bottom 0.3s ease-out;
}



.modal.bottom.show .modal-dialog {
  bottom: 0;
}


.image-wrapper {
    width: 100px;
    height: 100px;
    position: relative;
}
.image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 5px;
    border: 1px solid #ccc;
}
.delete-image-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    display: none;
}
.image-wrapper:hover .delete-image-btn {
    display: block;
}
</style>
@endsection