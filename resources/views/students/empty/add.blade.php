@extends('layout.app') 
@section('content')

<div class="content-wrapper">
           
        <div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
            <div class="col-sm-6">
                <h1 class="m-2">Student Admission Management</h1>
                
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                <li class="breadcrumb-item active"><a href="{{url('students_dashboard')}}">Student</a></li>
                </ol>
                
            </div>
            </div>
            <hr class="bg-danger">
        </div>
</div>

<div class="card m-2">
             <div class="card-header text-danger">
                <h2><b>Admission Delails:-</b></h2>
              </div>
              
              <div class="row m-2">
		<div class="col-md-3">
			<div class="form-group">
				<label>Admission Type:</label>
				<select class="form-control" id="admission_type" name="admission_type" value="">
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
				<label>Date Of Admission:</label>
				<input type="date" class="form-control " id="date_of_admission" name="date_of_admission" value="">
		    </div>
		</div>
		<div class="col-md-3">
	    	<div class="form-group">
				<label>Admission No.</label>
				<input type="text" class="form-control " id="admission" name="admission" placeholder="Admission No" value="">
		    </div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				<label>Section:</label>
				<select class="form-control" id="section" name="section" value="">
                <option value="0">A</option>
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
	</div>
 
	
	<div class="row m-2">
	    <div class="col-md-3">
	    	<div class="form-group">
				<label> School Name:</label>
				<input type="text" class="form-control " id="school_name" name="school_name" placeholder="School Name" value="">
		    </div>
		</div>
	    
		 <div class="col-md-3">
	    	<div class="form-group">
				<label>Scholar No.</label>
				<input type="text" class="form-control " id="scholar" name="scholar" placeholder="Scholar No." value="">
		    </div>
		</div>
		 <div class="col-md-3">
			<div class="form-group">
				<label>Previous Class:</label>
				<select class="form-control" id="previous_class" name="previous_class" value="">
                <option value="0"> Previous Class</option>
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
				<label>Class:</label>
				<select class="form-control" id="classs" name="classs" value="">
                <option value="0">NURSERY</option>
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
	</div> 
             
    <div class="row m-2">
        <div class="col-md-12 text-center">
        <button type="submit" class="btn btn-success btn-sm">Submit </button>
        </div>
    </div>
        </div>
    
    
</div>












@endsection      