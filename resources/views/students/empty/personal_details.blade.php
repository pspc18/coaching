@extends('layout.app') 
@section('content')
    
    <div class="content-wrapper">
           
        <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Student Management</h1>
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
      <hr class="bg-success">
     <div class="card m-2">
             <h3 class=" text-danger ml-3">Personal Details:-</h3>
              
              <div class="card-body">
                <div class="row ">
                    
                    <div class="col-md-3">
                       <div class="form-group">
				            <label>Student Name:</label>
				            <input type="text" class="form-control " id="student_name" name="student_name" placeholder="Student Name" value="">
		                </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label> Father Name:</label>
                            <input type="text" class="form-control" id="father_name" name="father_name" placeholder="Father Nmae" value="">
                        </div>    
                    </div>
                    <div class="col-md-3">
                       <div class="form-group">
                           <label> Mother Name:</label>
                           <input type="text" class="form-control" id="mail" name="" placeholder=" Mother Name" value="">
                          </div> 
                    </div>    
                    <div class="col-md-3">
                        <form>
                      <label>Gender:</label>
                      <div>
                        <input type="radio" id="contactChoice1"
                         name="contact" value="email" checked>
                        <label for="contactChoice1">Mail</label>
                    
                        <input type="radio" id="contactChoice2"
                         name="contact" value="phone">
                        <label for="contactChoice2">Femail</label>
                    
                        <input type="radio" id="contactChoice3"
                         name="contact" value="mail">
                        <label for="contactChoice3">Other</label>
                      </div>
                      
                    </form>

                    
                    </div>
                </div>  
                <div class="row">
                    
                <div class="col-md-3">
                    <div class="form-group">
                  <label>Mandicapped:</label>
                  <select class="form-control">
                    <option>Select</option>
                    <option>###</option>
                    <option>$$$$</option>
                    <option>####</option>
                  </select>
                </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                  <label>Religion:</label>
                  <select class="form-control">
                    <option>Select</option>
                    <option>###</option>
                    <option>$$$$</option>
                    <option>####</option>
                  </select>
                </div>
                </div>
                    <div class="col-md-3">
                    <div class="form-group">
                  <label>Category:</label>
                  <select class="form-control">
                    <option>Select</option>
                    <option>###</option>
                    <option>$$$$</option>
                    <option>####</option>
                  </select>
                </div>
                </div>
                    <div class="col-md-3">
                       <div class="form-group">
                           <label>Add RF Id Number:</label>
                           <input type="text" class="form-control" id="mail" name="" placeholder="Add RF Id Number" value="">
                          </div> 
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                       <div class="form-group">
                           <label>Date Of Birth:</label>
                           <input type="date" class="form-control" id="mail" name="" placeholder="Date Of Birth" value="">
                          </div> 
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                           <label>Date In Words:</label>
                           <input type="text" class="form-control" id="mail" name="" placeholder="Date In Words" value="">
                          </div> 
                    </div>    
                    
    </div>
                </div>    
                    <div class="row m-2">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-success btn-sm">Save & Change </button>
                    </div>
                 </div>  
                 <h3 class=" text-danger ml-3">Document Details:-</h3>
                 <div class="row m-2">
                     
                  <div class="col-md-3">
                     <div class="form-group">
                           <label>Aadhar Card (Student):</label>
                           <input type="text" class="form-control" id="aadhar_card" name="aadhar_card" placeholder="Aadhar Card" value="">
                          </div>
                    </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                           <label>Aadhar Card (Father):</label>
                           <input type="text" class="form-control" id="aadhar_card" name="aadhar_card" placeholder="Aadhar Card" value="">
                          </div>
                    </div>
                <div class="col-md-3">
                <div class="form-group">
                           <label>SSMD Id:</label>
                           <input type="text" class="form-control" id="sssm_id" name="sssm_id" placeholder="SSMD Id" value="">
                          </div>
                </div>
                <div class="col-md-3">
                   <div class="form-group">
                           <label>Family Id:</label>
                           <input type="text" class="form-control" id="family_id" name="family_id" placeholder="Family Id" value="">
                          </div>
                </div>  
    </div>
    <div class="row m-2">
                  <div class="col-md-3">
                     <div class="form-group">
                           <label>Child Id:</label>
                           <input type="text" class="form-control" id="child_id" name="child_id" placeholder="Child Id" value="">
                          </div>
                    </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                           <label> Bank Name (Father):</label>
                           <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="Bank Name" value="">
                          </div>
                    </div>
                <div class="col-md-3">
                <div class="form-group">
                           <label> Account Number (Father):</label>
                           <input type="text" class="form-control" id="account_number" name="account_number" placeholder="Account Number" value="">
                          </div>
                </div>
                <div class="col-md-3">
                   <div class="form-group">
                           <label>Bank IFSC Code (Father):</label>
                           <input type="text" class="form-control" id="bank_ifsc_code" name="bank_ifsc_code" placeholder="Bank IFSC Code" value="">
                          </div>
                </div>  
    </div>
    <div class="row m-2">
                <div class="col-md-3">
                  <div class="form-group">
                           <label> Bank Name (Student):</label>
                           <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="Bank Name" value="">
                          </div>
                </div>    
                <div class="col-md-3">
                <div class="form-group">
                           <label> Account Number (Student):</label>
                           <input type="text" class="form-control" id="account_number" name="account_number" placeholder="Account Number" value="">
                          </div>
                </div>
                <div class="col-md-3">
                   <div class="form-group">
                           <label>Bank IFSC Code (Student):</label>
                           <input type="text" class="form-control" id="bank_ifsc_code" name="bank_ifsc_code" placeholder="Bank IFSC Code" value="">
                          </div>
                </div>  
    </div>
                <div class="row m-2">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-success btn-sm">Save & Change </button>
                    </div>
                 </div>
                 
                 <h3 class=" text-danger ml-3">Family Contacts:-</h3>
                 <div class="row m-2">
                     
                  <div class="col-md-3">
                     <div class="form-group">
                           <label>Father Contacts Number:</label>
                           <input type="text" class="form-control" id="father_contacts_number" name="father_contacts_number" placeholder="Father Contacts Number" value="">
                          </div>
                    </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                           <label>Father Email Id:</label>
                           <input type="text" class="form-control" id="father_email_id" name="father_email_id" placeholder="Father Email Id" value="">
                          </div>
                    </div>
                <div class="col-md-3">
                <div class="form-group">
                           <label>Mother Contacts Number:</label>
                           <input type="text" class="form-control" id="mother_contacts_number" name="mother_contacts_number" placeholder="Mother Contacts Number" value="">
                          </div>
                </div>
                <div class="col-md-3">
                   <div class="form-group">
                           <label>Mother Email Id:</label>
                           <input type="text" class="form-control" id="mother_email_id" name="mother_email_id" placeholder="Mother Email Id" value="">
                          </div>
                </div>  
    </div>
    <div class="row m-2">
                  <div class="col-md-3">
                     <div class="form-group">
                           <label>Father Occupation:</label>
                           <input type="text" class="form-control" id="father_occupation" name="father_occupation" placeholder="Father Occupation" value="">
                          </div>
                    </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                           <label>Mother Occupation:</label>
                           <input type="text" class="form-control" id="mother_occupation" name="mother_occupation" placeholder="Mother Occupation" value="">
                          </div>
                    </div>
                <div class="col-md-3">
                <div class="form-group">
                           <label>Student Contacts Number:</label>
                           <input type="text" class="form-control" id="student_contacts_number" name="student_contacts_number" placeholder="Student Contacts Number" value="">
                          </div>
                </div>
                <div class="col-md-3">
                   <div class="form-group">
                           <label>Student Email Id:</label>
                           <input type="text" class="form-control" id="student_email_id" name="student_email_id" placeholder="Student Email Id" value="">
                          </div>
                </div>  
    </div>
    <div class="row m-2">
                   
                
                <div class="col-md-3">
                   <div class="form-group">
                           <label>Student's Full Adress:</label>
                           <input type="text" class="form-control" id="student_full_adress" name="student_full_adress" placeholder="Student's Full Adress" value="">
                          </div>
                </div>  
    </div>
                <div class="row m-2">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-success btn-sm">Save & Change </button>
                    </div>
                 </div>
                 
                 
                 <h3 class=" text-danger ml-3">Document Uploads:-</h3>
                 <div class="row m-2">
                    <div class="col-md-3">
                        <lable><b>Last Passed Marksheet:</b></lable>
                        <div class="input file form-control"><input type="file" name="file" id="file"></div>  
                   </div> 
                    <div class="col-md-3">
                        <lable><b>Transfer Cretificate:</b></lable>
                        <div class="input file form-control"><input type="file" name="file" id="file"></div>  
                   </div> 
                    <div class="col-md-3">
                        <lable><b>Income Cretificate:</b></lable>
                        <div class="input file form-control"><input type="file" name="file" id="file"></div>  
                   </div> 
                    <div class="col-md-3">
                        <lable><b>Caste Of Cretificate:</b></lable>
                        <div class="input file form-control"><input type="file" name="file" id="file"></div>  
                   </div> 
                </div>   
                
                <div class="row m-2">
                    <div class="col-md-3">
                        <lable><b>DOB Cretificate:</b></lable>
                        <div class="input file form-control"><input type="file" name="file" id="file"></div>  
                   </div> 
                    <div class="col-md-3">
                        <lable><b>Student Photo:</b></lable>
                        <div class="input file form-control"><input type="file" name="file" id="file"></div>  
                   </div> 
                    <div class="col-md-1">
                        
                   </div> 
                    <div class="col-md-3">
                        <lable><b>Parents Photo:</b></lable>
                        <div class="input file form-control"><input type="file" name="file" id="file"></div>  
                   </div> 
                    <div class="col-md-1">
                        
                   </div>
                </div>  
                <div class="row m-2">
                        <div class="col-md-10 text-center">
                            <button type="submit" class="btn btn-success btn-sm">Save & Change </button>
                    </div>
                    
                    <div class=" col-md-2 text-right">
                            <button type="submit" class="btn btn-success btn-sm">Final & Submit </button>
                    </div>
                 </div>
    </div>
                 
    
    
    
    
    
    
    
    
    </div>
@endsection    