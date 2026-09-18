@extends('layout.app') 
@section('content')
    
    <div class="content-wrapper">
           
        <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Student Id Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active"><a href="students_dashboard">Student</a></li>
                </ol>
            </div>
            </div>
        </div>
        </div>
      <hr class="bg-danger m-2">
     <div class="card m-2">
             <div class="card-body">
                  
                 <h3 class=" text-danger ml-3">Student Id:-</h3>
                 <form id="quickForm" action="{{ url('student/id/edit') }}/{{$data->id}}" method="post" >
                      @csrf
                 <div class="row m-2">
                     
                  <div class="col-md-3">
                     <div class="form-group">
                           <label style="color:red;">Aadhar Card (Student):*</label>
                           <input type="text" class="form-control @error('aadhar_card') is-invalid @enderror" id="aadhar_card" name="aadhar_card" placeholder="Aadhar Card Student" value="{{ $data['aadhar_card']}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
                          @error('aadhar_card')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                    </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                           <label style="color:red;">Aadhar Card (Father):*</label>
                           <input type="text" class="form-control @error('aadhar_father') is-invalid @enderror" id="aadhar_father" name="aadhar_father" placeholder="Aadhar Card Father" value="{{ $data['aadhar_father']}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
                          @error('aadhar_father')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                    </div>
                <div class="col-md-3">
                <div class="form-group">
                           <label>SSMD Id:</label>
                           <input type="text" class="form-control @error('sssm_id') is-invalid @enderror" id="sssm_id" name="sssm_id" placeholder="SSMD Id" value="{{ $data['sssm_id']}}" onkeypress="javascript:return isNumber(event)">
                          @error('sssm_id')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                </div>
                <div class="col-md-3">
                   <div class="form-group">
                           <label>Family Id:</label>
                           <input type="text" class="form-control @error('family') is-invalid @enderror" id="family" name="family" placeholder="Family Id" value="{{ $data['family']}}" onkeypress="javascript:return isNumber(event)">
                           @error('family')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                </div>  
    </div>
    <div class="row m-2">
                  <div class="col-md-3">
                     <div class="form-group">
                           <label>Child Id:</label>
                           <input type="text" class="form-control @error('child') is-invalid @enderror" id="child" name="child" placeholder="Child Id" value="{{ $data['child']}}">
                           @error('child')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                    </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                           <label style="color:red;"> Bank Name (Father):*</label>
                           <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" placeholder="Bank Name" value="{{ $data['rigistrabank_nametion']}}">
                          @error('bank_name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                    </div>
                <div class="col-md-3">
                <div class="form-group">
                           <label style="color:red;"> Account Number (Father):*</label>
                           <input type="number" class="form-control @error('father_account') is-invalid @enderror" id="father_account" name="father_account" placeholder="Account Number" value="{{ $data['father_account']}}">
                         @error('father_account')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                </div>
                <div class="col-md-3">
                   <div class="form-group">
                           <label style="color:red;">Bank IFSC Code (Father):*</label>
                           <input type="number" class="form-control @error('father_ifsc_code') is-invalid @enderror" id="father_ifsc_code" name="father_ifsc_code" placeholder="Bank IFSC Code" value="{{ $data['father_ifsc_code']}}">
                          @error('father_ifsc_code')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                </div>  
    </div>
    <div class="row m-2">
                <div class="col-md-3">
                  <div class="form-group">
                           <label style="color:red;"> Bank Name (Student):*</label>
                           <input type="text" class="form-control @error('bank') is-invalid @enderror" id="bank" name="bank" placeholder="Bank Name" value="{{ $data['bank']}}">
                          @error('bank')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                </div>    
                <div class="col-md-3">
                <div class="form-group">
                           <label style="color:red;"> Account Number (Student):*</label>
                           <input type="number" class="form-control @error('student_account') is-invalid @enderror" id="student_account" name="student_account" placeholder="Account Number" value="{{ $data['student_account']}}">
                           @error('student_account')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                </div>
                <div class="col-md-3">
                   <div class="form-group">
                           <label style="color:red;">Bank IFSC Code (Student):*</label>
                           <input type="number" class="form-control @error('student_ifsc_code') is-invalid @enderror" id="student_ifsc_code" name="student_ifsc_code" placeholder="Bank IFSC Code" value="{{ $data['student_ifsc_code']}}">
                          @error('student_ifsc_code')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                </div>  
    </div>
                
                 <h3 class=" text-danger ml-3">Family Contacts:-</h3>
                 <div class="row m-2">
                     
                  <div class="col-md-3">
                     <div class="form-group">
                           <label style="color:red;">Father Contacts Number:*</label>
                           <input type="number" class="form-control @error('father_contacts_number') is-invalid @enderror" id="father_contacts_number" name="father_contacts_number" placeholder="Father Contacts Number" value="{{ $data['father_contacts_number']}}">
                          @error('father_contacts_number')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                    </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                           <label style="color:red;">Father Email Id:*</label>
                           <input type="email" class="form-control @error('father_email') is-invalid @enderror" id="father_email" name="father_email" placeholder="Father Email Id" value="{{ $data['father_email']}}">
                           @error('father_email')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                    </div>
                <div class="col-md-3">
                <div class="form-group">
                           <label>Mother Contacts Number:</label>
                           <input type="number" class="form-control @error('mother_contacts_number') is-invalid @enderror" id="mother_contacts_number" name="mother_contacts_number" placeholder="Mother Contacts Number" value="{{ $data['mother_contacts_number']}}">
                          @error('mother_contacts_number')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                </div>
                <div class="col-md-3">
                   <div class="form-group">
                           <label>Mother Email Id:</label>
                           <input type="email" class="form-control @error('mother_email_id') is-invalid @enderror" id="mother_email_id" name="mother_email_id" placeholder="Mother Email Id" value="{{ $data['mother_email_id']}}">
                          @error('mother_email_id')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                </div>  
    </div>
    <div class="row m-2">
                  <div class="col-md-3">
                     <div class="form-group">
                           <label style="color:red;">Father Occupation:*</label>
                           <input type="text" class="form-control @error('father_occupation') is-invalid @enderror" id="father_occupation" name="father_occupation" placeholder="Father Occupation" value="{{ $data['father_occupation']}}">
                         @error('father_occupation')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                    </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                           <label>Mother Occupation:</label>
                           <input type="text" class="form-control @error('mother_occupation') is-invalid @enderror" id="mother_occupation" name="mother_occupation" placeholder="Mother Occupation" value="{{ $data['mother_occupation']}}">
                           @error('mother_occupation')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                    </div>
                <div class="col-md-3">
                <div class="form-group">
                           <label>Student Contacts Number:</label>
                           <input type="number" class="form-control @error('student_contacts_number') is-invalid @enderror" id="student_contacts_number" name="student_contacts_number" placeholder="Student Contacts Number" value="{{ $data['student_contacts_number']}}">
                           @error('student_contacts_number')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                </div>
                <div class="col-md-3">
                   <div class="form-group">
                           <label style="color:red;">Student Email Id:*</label>
                           <input type="email" class="form-control @error('student_email') is-invalid @enderror" id="student_email" name="student_email" placeholder="Student Email Id" value="{{ $data['student_email']}}">
                           @error('student_email')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                          </div>
                </div>  
    </div>
    <div class="row m-2">
        <div class="col-md-3">
            <div class="form-group">
                <label style="color:red;">Student's Full Adress:*</label>
                <input type="text" class="form-control @error('student_adress') is-invalid @enderror" id="student_adress" name="student_adress" placeholder="Student's Full Adress" value="{{ $data['student_adress']}}">
                @error('student_adress')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
            </div>
        </div>  
    </div>
                <div class="row m-2">
                        <div class="col-md-10 text-center">
                            <button type="submit" class="btn btn-success btn-sm">Update</button>
                    </div>
                   </div>
                   </form>
    </div>
                 
    
    
    
    
    
    
 </div>    
    
    </div>
@endsection    