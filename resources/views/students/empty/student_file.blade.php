@extends('layout.app') 
@section('content')
    
    
     <div class="content-wrapper">
           
        <div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
            <div class="col-sm-6">
                <h1 class="m-2">Student Management</h1>
                
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active"><a href="students">Student</a></li>
                </ol>
                
            </div>
            </div>
            <hr class="bg-primary">
        </div>
</div>

<div class="card">
              <div class="card-header">
                <h3 class="card-title">DataTable with default features</h3>
              </div>
           
    <div class="row m-2">
        
		<div class="col-md-3">
			<div class="form-group">
				<label>Rigistration:<sup style="color:red;">*</sup> </label>
				<input type="text" class="form-control " id="rigistration" name="rigistration" value="">
		    </div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label for="Department">Student New Old:</label>
				<select name="data[Royaltycard][spl_files1]" id="spl_files1" class="form-control">
                <option value="0">Select</option>
                <option value="1">#</option>
                <option value="18">#</option>
                <option value="20">#</option>
                <option value="34">#</option>
                <option value="35">#</option>
                <option value="36">#</option>
                <option value="37">#</option>
                <option value="38">#</option>
                <option value="39">#</option>
                <option value="40">#</option>
                </select>
		    </div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label for="Department">Class:<sup style="color:red;">*</sup></label>
				<select name="data[Royaltycard][spl_files1]" id="spl_files1" class="form-control">
                <option value="0">Select</option>
                <option value="1">Frist Class</option>
                <option value="18">Secound Class</option>
                <option value="20">Third Class</option>
                <option value="34">Forth Class</option>
                <option value="35">Five Class</option>
                <option value="36">Six Class</option>
                <option value="37">Sevent Class</option>
                <option value="38">Eeight Class</option>
                <option value="39">Nine Class</option>
                <option value="40">ten Class</option>
                <option value="40">Elven Class</option>
                <option value="40">twelve Class</option>
                </select>
		    </div>
		</div>
		<div class="col-md-3">
	    	<div class="form-group">
				<label>Student Name:</label>
				<input type="text" class="form-control " id="student_name" name="student_name" value="">
		    </div>
		</div>
	</div>
	
	
	<div class="row m-2">
	    <div class="col-md-3">
	    	<div class="form-group">
				<label>Father's Name:</label>
				<input type="text" class="form-control " id="father_name" name="father_name" value="">
		    </div>
		</div>
	    
	     <div class="col-md-3">
	    	<div class="form-group">
				<label>Mother's Name:</label>
				<input type="text" class="form-control " id="mather_name" name="mather_name" value="">
		    </div>
		</div>
		 <div class="col-md-3">
	    	<div class="form-group">
				<label>Father's Contact No.1:<sup style="color:red;">*</sup></label>
				<input type="text" class="form-control " id="father_name" name="father_name" value="">
		    </div>
		</div>
		 <div class="col-md-3">
	    	<div class="form-group">
				<label>Father's Contact No.2:</label>
				<input type="text" class="form-control " id="father_name" name="father_name" value="">
		    </div>
		</div>
	</div>    
	<div class="row m-2">
	    <div class="col-md-3">
	    	<div class="form-group">
				<label>Date Of  Birth:<sup style="color:red;">*</sup></label>
				<input type="date" class="form-control " id="date_of_birth" name="date_of_birth" value="">
		    </div>
		</div>
	    
	     <div class="col-md-3">
	    	<div class="form-group">
				<label>DOB In Word:</label>
				<input type="text" class="form-control " id="dob_in_word" name="dob_in_word" value="">
		    </div>
		</div>
		 <div class="col-md-3">
                <div class="form-group">
                  <label>Gender:</label>
                  <select class="form-control select2bs4" style="width: 100%;">
                    <option>Select</option>
                    <option>Male</option>
                    <option>Female</option>
                    <option>Other</option>
                  </select>
                </div>
              </div>
		 <div class="col-md-3">
	    	<div class="form-group">
				<label>Date Of Admission:</label>
				<input type="date" class="form-control " id="date_of_admission" name="date_of_admission" value="">
		    </div>
		</div>
	</div>
	
		<div class="row m-2">
		    
		     <div class="col-md-3">
                <div class="form-group">
                  <label>Admission Type:</label>
                  <select class="form-control select2bs4" style="width: 100%;">
                    <option>Select</option>
                    <option>Regular</option>
                    <option>Non</option>
                    <option>Other</option>
                  </select>
                </div>
              </div>
	    <div class="col-md-3">
                <div class="form-group">
                  <label>Admission Scheme:</label>
                  <select class="form-control select2bs4" style="width: 100%;">
                    <option>NON-RTE</option>
                    <option>Regular</option>
                    <option>Other</option>
                  </select>
                </div>
        </div>
		
	    
	     <div class="col-md-3">
                <div class="form-group">
                  <label>Fee Category:</label>
                  <select class="form-control select2bs4" style="width: 100%;">
                    <option>Olo Student</option>
                    <option>Regular</option>
                    <option>Other</option>
                  </select>
                </div>
        </div>
		
		 <div class="col-md-3">
	    	<div class="form-group">
				<label>Bus No.:</label>
				<input type="text" class="form-control " id="bus_no" name="bus_no" value="">
		    </div>
		</div>
	</div>
	
	<div class="row m-2">
		    
		     <div class="col-md-3">
                <div class="form-group">
                  <label>Hostel:</label>
                  <select class="form-control select2bs4" style="width: 100%;">
                    <option>NO</option>
                    <option>..</option></option>
                    <option>#</option>
                    <option>#</option>
                  </select>
                </div>
              </div>
	    <div class="col-md-3">
                <div class="form-group">
                  <label>Library:</label>
                  <select class="form-control select2bs4" style="width: 100%;">
                    <option>No</option>
                    <option>...</option>
                    <option>....</option>
                    <option>...</option>
                    <option>....</option>
                  </select>
                </div>
        </div>
		
	    
	     <div class="col-md-3">
	    	<div class="form-group">
				<label>Rigistration Fees:</label>
				<input type="text" class="form-control " id="rigistration_fees" name="rigistration_fees" value="">
		    </div>
		</div>
		 <div class="col-md-3">
	    	<div class="form-group">
				<label>SMS Contact No.:</label>
				<input type="text" class="form-control " id="sms_contact_no" name="sms_contact_no" value="">
		    </div>
		</div>
	</div>
	
	<div class="row m-2">
	    <div class="col-md-3">
	    	<div class="form-group">
				<label>Students Address:</label>
				<input type="text" class="form-control " id="student_address" name="student_address" value="">
		    </div>
		</div>
	    
	     <div class="col-md-3">
	    	<div class="form-group">
				<label>Village/City:</label>
				<input type="text" class="form-control " id="village" name="village" value="">
		    </div>
		</div>
		 <div class="col-md-3">
	    	<div class="form-group">
				<label>Block:</label>
				<input type="text" class="form-control " id="block" name="block" value="">
		    </div>
		</div>
		 <div class="col-md-3">
	    	<div class="form-group">
				<label>District:</label>
				<input type="text" class="form-control " id="district" name="district" value="">
		    </div>
		</div>
	</div>  
	
	<div class="row m-2">
	    <div class="col-md-3">
	    	<div class="form-group">
				<label>State:</label>
				<input type="text" class="form-control " id="state" name="state" value="">
		    </div>
		</div>
	    
	     <div class="col-md-3">
	    	<div class="form-group">
				<label>Pin Code:</label>
				<input type="code" class="form-control " id="pin_code" name="pin_code" value="">
		    </div>
		</div>
		
	</div>

	<div class="row m-3">
	<div class="form-check">
        <input type="checkbox" class="form-check-input" id="exampleCheck1">
        <label class="form-check-label" for="exampleCheck1">Check For Message</label>
    </div>
	</div>
    <div class="row m-2">
        <div class="col-md-12 text-center">
        <button type="submit" class="btn btn-info ">Submit</button>
        </div>
    </div>

</div>
    
</div>  
                  

@endsection    
    