@php
$getCountry = Helper::getCountry();
$getState = Helper::getState();
$roleType = Helper::roleType();
$getPermisnByBranch = Helper::getPermisnByBranch();
$allPermisn = explode(',',$getPermisnByBranch['branch_sidebar_id']);
$subsidebar  = DB::table('sidebar_sub')->whereNull('deleted_at')->groupBy('sidebar_id')->orderBy('sidebar_id','ASC')->get();
$allowSubSidebar  = explode(',',$getPermisnByBranch['sidebar_sub_id']);
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
                    <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('user.Edit User') }} </h3>
                    <div class="card-tools">
                    <!--<a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add </a>-->
                    <a href="{{url('viewUser')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(6)->view ? '' : 'd-none' }}" title="View Users"><i class="fa fa-eye"></i> {{ __('common.View') }}  </a>
                    <a href="{{url('user_dashboard')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}  </a>
                    </div>
                    
                    </div>                 
                <form id="form-submit-edit" action="{{ url('editUser') }}/{{($data->id)}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row m-2">
          @if(Session::get('role_id') == 1)
                        <div class="col-md-3">
                            <div class="form-group"> 
                                <label style="color:red;">{{ __('Branch Access') }} *</label>
                              
                                     @php
                            $selectedBranches = array();
                                if($data->access_branch_id > 0){ 
                                $val = $data->access_branch_id;
                                $selectedBranches = explode(',', $val);
                         }
                        @endphp 

                                <select class="form-control @error('access_branch_id') is-invalid @enderror select2" 
                                    id="access_branch_id" 
                                    multiple 
                                    name="access_branch_id[]">
                                    
                                    <option value="">{{ __('common.Select') }}</option>
                                    
                                    @foreach($branch as $Branch)
                                        <option value="{{ $Branch->id }}" @if(in_array($Branch->id,$selectedBranches)) selected="" @endif>
                                            {{ $Branch->branch_name }}
                                        </option>
                                    @endforeach
                                
                                </select>

                              
                            </div>
                        </div>
        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="color:red;">{{ __('Name') }}*</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="first_name" name="first_name" value="{{ $data->first_name ??  old('first_name') }}" placeholder="{{ __('common.First Name') }}">
                               
                            </div>
                        </div>
                        
                 
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="color:red;">{{ __('common.Mobile No.') }} *</label>
                                <input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" value="{{ $data->mobile ??  old('mobile') }}" placeholder="{{ __('common.Mobile No.') }} " maxlength="10" onkeypress="javascript:return isNumber(event)">
        
                              
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('common.Email') }}</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="exampleInputEmail1" name="email" value="{{ $data->email ??  old('email') }}" placeholder="{{ __('common.Email') }}">
                              
        
                            </div>
                        </div>                
        		<!--<div class="col-md-3" >
                    <div class="form-group">
                     <label>Country</label>
                      <select class="form-control select2" name="country" id="country_id">
                          @if(!empty($getCountry)) 
                              @foreach($getCountry as $country)
                                 <option value="{{ $country->id ?? ''  }}" {{ ($country->id == Session::get('countries_id')) ? 'selected' : '' }}>{{ $country->name ?? ''  }}</option>
                              @endforeach
                          @endif
                      
                      
                        	@error('country')
        						<span class="invalid-feedback" role="alert">
        							<strong>{{ $message }}</strong>
        						</span>
        					@enderror
                      </select>
                      </div>
                    </div>-->
        			<div class="col-md-3">
        				<div class="form-group"> 
        					<label for="State" class="required"  style="color:red;">{{ __('common.State') }}*</label>
        					<select class="select2 form-control @error('state') is-invalid @enderror" id="state_id" name="state">
        					    <option value="" >{{ __('common.Select') }}</option>
                                @if(!empty($getState)) 
                                      @foreach($getState as $state)
                                         <option value="{{ $state->id ?? ''}}" {{ ( $state['id'] == $data['state_id'] ??  old('state_id')) ? 'selected' : '' }}>{{ $state->name ?? ''}}</option>
                                      @endforeach
                                @endif
                            </select>
                               				
        				</div>
        			</div>
        			<div class="col-md-3">
        			    <div class="form-group">
        			        <label for="City"  style="color:red;">{{ __('common.City') }}*</label>
        			        <select class="select2 form-control @error('city') is-invalid @enderror" name="city" id="city_id">
        			            <option value="" >{{ __('common.Select') }}</option>
        			            @if(!empty($getcitie)) 
                                @foreach($getcitie as $cities)
                                    <option value="{{ $cities->id ?? ''  }}" {{ $cities->id == old('city_id', $data->city_id) ? 'selected' : ''}}>{{ $cities->name ?? ''  }}</option>                                @endforeach
                                @endif
        					</select>
        				     					
        			    </div>
        			</div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('common.Address') }}</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ $data->address ??  old('address') }}" placeholder="{{ __('common.Address') }}">
                              
                            </div>
                        </div>
        
        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="color:red;">{{ __('user.User Name') }}*</label>
                                <input type="text" class="form-control @error('userName') is-invalid @enderror" id="userName" name="userName" value="{{ $data->userName ??  old('userName') }}" placeholder="{{ __('user.User Name') }}">
                               
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="color:red;">{{ __('common.Password') }}*</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="{{ $data->confirm_password ??  old('confirm_password') }}" placeholder="{{ __('common.Password') }}">
                               
        
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="color:red;">{{ __('common.Confirm Password') }}*</label>
                                <input type="text" class="form-control @error('confirm_password') is-invalid @enderror" id="confirm_password" name="confirm_password" value="{{ $data->confirm_password ??  old('confirm_password') }}" placeholder="{{ __('common.Confirm Password') }}">
                               
        
                            </div>
                        </div>
                         <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Class Name</label>
                                        <div class="custom-multi" id="classMulti">
                                            <button type="button" class="custom-btn" onclick="toggleDropdown()">
                                                <span id="selectedText">None selected Class</span>
                                                <span>▼</span>
                                            </button>
                                            <div class="custom-dropdown" id="ClassDropdown">
                                
                                                <!-- ✅ Select All Fixed -->
                                                <label class="dropdown-item">
                                                    <input type="checkbox" id="selectAll" onchange="selectAllFees(this)">
                                                    Select All
                                                </label>
                                
                                                <div id="feesOptions">
                                
                                                    @php
                                                        $selectedClasses = explode(',', $data->class_type_id ?? '');
                                                    @endphp
                                
                                                    @if(!empty(Helper::classType()))
                                                        @foreach(Helper::classType() as $type)
                                                            <label class="dropdown-item">
                                                                <input type="checkbox"
                                                                       class="class-checkbox"
                                                                       value="{{ $type->id }}"
                                                                       data-name="{{ $type->name }}"
                                                                       {{ in_array($type->id, $selectedClasses) ? 'checked' : '' }}
                                                                       onchange="updateSelectedText()">
                                                                {{ $type->name }}
                                                            </label>
                                                        @endforeach
                                                    @endif
                                
                                                </div>
                                            </div>
                                        </div>
                                
                                        <!-- Hidden Field -->
                                        <input type="hidden" name="class_type_id" id="class_type_id"
                                               value="{{ $data->class_type_id ?? '' }}">
                                    </div>
                                </div>
                        
                        
                        <!-- <input type="hidden" value="{{$data['role_id'] ?? ''}}" name="role_id" />
                        
                        -->
                       
                        @if($data['role_id'] !== 1)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="color:red;">{{ __('user.Role') }}*</label>
                        
                                <select class="select2 form-control" name="role_id">
                                    <option value="">{{ __('common.Select') }}</option>
                                 @if(!empty($roleType)) 
                                      @foreach($roleType as $value)
                                     
                                         <option value="{{ $value->id ?? ''  }}" {{ ( $value['id'] == $data['role_id'] ??  old('role_id')) ? 'selected' : '' }} {{ $value->id == 1 ? 'disabled' : ''}}>{{ $value->name ?? ''  }}</option>
                                      @endforeach
                                @endif
                                </select>
                            </div>
                        </div>
                        @endif
                
                		<div class=" col-md-12 title">
                     <h5 style="color:red">Document Upload:-</h5>
                  </div>
                    <hr>
                    <div class="row m-2">
                  
                  <!--camera img capture-->
                 
                  <div class="row col md-12">

                        <!-- ================= PHOTO ================= -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Photo</label>
                                <input type="file" class="form-control" name="photo"
                                       accept="image/png, image/jpg, image/jpeg">
                            </div>
                    
                            @if($data->image)
                            <div class="mt-2 text-center">
                                <img src="{{ asset($data->image) }}"
                                     onclick="openImageModal('{{ asset($data->image) }}')"
                                     style="width:100px;height:100px;object-fit:cover;border:1px solid #ddd;border-radius:6px;cursor:pointer;">
                            </div>
                            @endif
                        </div>
                    
                    
                        <!-- ================= ID PROOF ================= -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>ID Proof</label>
                                <input type="file" class="form-control" name="id_proof"
                                       accept="image/png, image/jpg, image/jpeg">
                            </div>
                    
                            @if($data->Id_proof_img)
                            <div class="mt-2 text-center">
                                <img src="{{ asset($data->Id_proof_img) }}"
                                     onclick="openImageModal('{{ asset($data->Id_proof_img) }}')"
                                     style="width:100px;height:100px;object-fit:cover;border:1px solid #ddd;border-radius:6px;cursor:pointer;">
                            </div>
                            @endif
                        </div>
                    
                    
                        <!-- ================= QUALIFICATION ================= -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Qualification Proof</label>
                                <input type="file" class="form-control" name="qualification_proof"
                                       accept="image/png, image/jpg, image/jpeg">
                            </div>
                    
                            @if($data->qualification_proof_img)
                            <div class="mt-2 text-center">
                                <img src="{{ asset($data->qualification_proof_img) }}"
                                     onclick="openImageModal('{{ asset($data->qualification_proof_img) }}')"
                                     style="width:100px;height:100px;object-fit:cover;border:1px solid #ddd;border-radius:6px;cursor:pointer;">
                            </div>
                            @endif
                        </div>
                    
                    
                        <!-- ================= EXPERIENCE ================= -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Experience Letter</label>
                                <input type="file" class="form-control" name="experience_letter"
                                       accept="image/png, image/jpg, image/jpeg">
                            </div>
                    
                            @if($data->experience_letter_img)
                            <div class="mt-2 text-center">
                                <img src="{{ asset($data->experience_letter_img) }}"
                                     onclick="openImageModal('{{ asset($data->experience_letter_img) }}')"
                                     style="width:100px;height:100px;object-fit:cover;border:1px solid #ddd;border-radius:6px;cursor:pointer;">
                            </div>
                            @endif
                        </div>
                    





                     <div class="col-md-3">
                     <div class="form-group">
                        <label>Pan Card No.</label>
                        <input type="text" class="form-control " id="pan_card" name="pan_card" placeholder="Pan Card No." value="{{ $data->pan_card ??  old('pan_card') }}" maxlength="10">
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Bank Name</label>
                        <input type="text" class="form-control " id="bank" name="bank" placeholder="Bank Name" value="{{ $data->bank ??  old('bank') }}">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>Bank Account No.</label>
                        <input type="text" class="form-control " id="account_no" name="account_no" placeholder="Bank Account No." value="{{ $data->account_no ??  old('account_no') }}" maxlength="18">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>Bank IFSC Code</label>
                        <input type="text" class="form-control " id="ifsc_code" name="ifsc_code" placeholder="Bank IFSC Code" value="{{ $data->ifsc_code ??  old('ifsc_code') }}" maxlength="11">
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label style="color:red;">Date of Birth *</label>
                        <input type="date" class="form-control @error('dob') is-invalid @enderror" id="dob" name="dob" value="{{ old('dob', $data->dob) }}" max="{{ date('Y-m-d', strtotime('-1 day')) }}">
                        @error('dob')<span class="invalid-feedback">{{ $message }}</span>@enderror
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label style="color:red;">Joining Date *</label>
                        <input type="date" class="form-control @error('joining_date') is-invalid @enderror" id="joining_date" name="joining_date" value="{{ old('joining_date', $data->joining_date) }}">
                        @error('joining_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label>Joining Amount / Per Month</label>
                        <input type="text" class="form-control @error('joining_amount') is-invalid @enderror" id="joining_amount" name="joining_amount" placeholder="Joining Amount / Per Month" value="{{ old('joining_amount', $data->joining_amount) }}" onkeypress="javascript:return isNumber(event)">
                        @error('joining_amount')<span class="invalid-feedback">{{ $message }}</span>@enderror
                     </div>
                  </div>
                  </div>
               </div>
                    </div>

                    <div class="row m-2 pb-2">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary btn-submit">{{ __('common.Update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
</section>
</div>  
<!-- Image View Modal -->
<div class="modal fade" id="imageViewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Photo Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <img id="modalImage"
                     src=""
                     style="max-width:100%; max-height:500px;">
            </div>

            <div class="modal-footer">
                <a id="downloadBtn"
                   href=""
                   download
                   class="btn btn-success">
                   Download
                </a>

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

<script>

function toggleDropdown(){
    const d = document.getElementById('ClassDropdown');
    d.style.display = d.style.display === 'block' ? 'none' : 'block';
}

function selectAllFees(src){
    document.querySelectorAll('.class-checkbox')
        .forEach(cb => cb.checked = src.checked);

    updateSelectedText();
}

function updateSelectedText(){
    let names = [];
    let ids = [];

    document.querySelectorAll('.class-checkbox:checked').forEach(cb=>{
        names.push(cb.dataset.name);
        ids.push(cb.value);
    });

    document.getElementById('selectedText').innerText =
        names.length ? names.join(', ') : 'None Selected Class';

    document.getElementById('class_type_id').value = ids.join(',');

    // Auto update Select All state
    let total = document.querySelectorAll('.class-checkbox').length;
    let checked = document.querySelectorAll('.class-checkbox:checked').length;

    document.getElementById('selectAll').checked = (total === checked);
}

// Page load pe selected text show kare
document.addEventListener("DOMContentLoaded", function(){
    updateSelectedText();
});

// Outside click close (Fixed)
document.addEventListener('click', function(e){

    const multi = document.getElementById('classMulti');
    const dropdown = document.getElementById('ClassDropdown');

    if(!multi || !dropdown) return;   // 🔥 Safe check

    if(!multi.contains(e.target)){
        dropdown.style.display = 'none';
    }

});

</script>
<script>
function openImageModal(imageSrc) {

    document.getElementById('modalImage').src = imageSrc;

    // Download button link set
    document.getElementById('downloadBtn').href = imageSrc;

    var myModal = new bootstrap.Modal(document.getElementById('imageViewModal'));
    myModal.show();
}
</script>
<script>
    $(document).ready(function(){
        $('#photo').change(function(e){
            $('#image_error').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_error').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_error').html("");
            }
        }else{
            $('#image_error').html("Image Size File");
            $(this).val('');
        }
        });
    });
    
</script>
    
    <style>


.custom-btn {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ccc;
  background: #fff;
  text-align: left;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
}

.custom-dropdown {
  display: none;
  position: absolute;
  width: 100%;
  max-height: 250px;
  overflow: auto;
  background: #fff;
  border: 1px solid #ccc;
  z-index: 999;
}

.custom-btn {
  text-align: left;
  cursor: pointer;
}
.custom-multi {
  position: relative;
  width: 100%;
}

.dropdown-item {
  padding: 6px;
  display: block;
}

label {
  display: inline-block;
  max-width: 100%;
  margin-bottom: 2px;
  font-weight: 600;
  font-size: 14px;
}
    #image_error{
        font-weight: bold;
    font-size: 14px;
    }
    </style>


 @endsection    
