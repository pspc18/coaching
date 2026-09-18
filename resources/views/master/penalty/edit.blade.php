@extends('layout.app') 
@section('content')

     <div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-edit"></i> &nbsp; View Penalty Form</h3>
							<div class="card-tools"> <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> Back</a> </div>
						</div>
        <form id="quickForm" action="{{ url('penalty/edit') }}/{{$data['id']}}" method="post" enctype="multipart/form-data">   
         @csrf
       <div class="row">
           <div class="col-md-3">
	    	<div class="form-group">
				<label style="color:red;">Class*</label>
				<input type="text" class="form-control @error('class') is-invalid @enderror" id="class" name="class" placeholder=" Class" value="{{$data['class'] ?? ''}}">
				 @error('dob')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
		    </div>
		</div>
      
	    
	    <div class="col-md-3">
	    	<div class="form-group">
				<label style="color:red;">Student Section*</label>
				<input type="text" class="form-control @error('student_section') is-invalid @enderror" id="student_section" name="student_section" placeholder="Student Section" value="{{$data['student_section'] ?? ''}}">
				 @error('mobile')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
		    </div>
		</div>
		<div class="col-md-3">
	    	<div class="form-group">
				<label>Student Name</label>
				<input type="text" class="form-control" id="name" name="name" placeholder=" Name" value="{{$data['name'] ?? ''}}">
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
				<input type="text" class="form-control @error('student_no') is-invalid @enderror" id="student_no" name="student_no" placeholder="Student Roll No." value="{{$data['student_no'] ?? ''}}">
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
				<input type="text" class="form-control @error('pelanty_amount') is-invalid @enderror" id="pelanty_amount" name="pelanty_amount" placeholder="Pelanty Amount" value="{{$data['pelanty_amount'] ?? ''}}">
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
				<input type="text" class="form-control @error('pelanty_reason') is-invalid @enderror" id="pelanty_reason" name="pelanty_reason" placeholder="Pelanty Reason" value="{{$data['pelanty_reason'] ?? ''}}">
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
				<input type="text" class="form-control @error('pelanty_remark') is-invalid @enderror" id="pelanty_remark" name="pelanty_remark" placeholder="Pelanty Remark" value="{{$data['pelanty_remark'] ?? ''}}">
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
            <button type="submit" class="btn btn-info ">Submit</button>
        </div>
    </div>
    </form>
	</div>
			</div>
		</div>
	</section>
</div>      
@endsection