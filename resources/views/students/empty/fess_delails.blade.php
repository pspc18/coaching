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

<div class="card m-2">
             <div class="card-header text-danger ">
                <h2><b>Fees Delails:-</b></h2>
              </div>
              
              <div class="row m-2">
		<div class="col-md-2">
			<div class="form-group">
				<label>Tution Fee/Year:</label>
					<input type="text" class="form-control " id="tution_fess" name="tution_fess" placeholder="" value="1000">
		    </div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<label>Discount Type:</label>
				<select class="form-control">
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
		<div class="col-md-2">
			<div class="form-group">
				<label>Discount Amount:</label>
				<select class="form-control">
                <option value="0">Select</option>
                <option value="1">....</option>
                <option value="18">.....</option>
                <option value="20">.....</option>
                <option value="34">.....</option>
                <option value="35">.....</option>
                <option value="36">.....</option>
                <option value="37">.....</option>
                <option value="38">.....</option>
                <option value="39">.....</option>
                <option value="40">.....</option>
                <option value="40">.....</option>
                <option value="40">.....</option>
                </select>
		    </div>
		</div>
			<div class="col-md-2">
			<div class="form-group">
				<label>TotleAmountAfterDiscount:</label>
				<input type="text" class="form-control " id="totle_amount_after_dicount" name="totle_amount_after_dicount" placeholder="" value="1000">
		    </div>
		</div>
		<div class="col-md-2">
	    	<div class="form-group">
				<label>Tution Fee Balance/Year:</label>
				<input type="text" class="form-control " id="sibling_name" name="sibling_name" placeholder="" value="1000">
		    </div>
		</div>
	    
	     	<div class="col-md-2">
	    	<div class="form-group">
				<label>Tution Fee Paid Amount:</label>
				<input type="text" class="form-control " id="sibling_name" name="sibling_name" placeholder="" value="0">
		    </div>
		</div>
	</div>

  <div class="row m-2">
		<div class="col-md-2">
			<div class="form-group">
				<label>Exam Fee/Year:</label>
					<input type="text" class="form-control " id="tution_fess" name="tution_fess" placeholder="" value="1200">
		    </div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<label>Discount Type:</label>
				<select class="form-control">
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
		<div class="col-md-2">
			<div class="form-group">
				<label>Discount Amount:</label>
				<select class="form-control">
                <option value="0">Select</option>
                <option value="1">....</option>
                <option value="18">.....</option>
                <option value="20">.....</option>
                <option value="34">.....</option>
                <option value="35">.....</option>
                <option value="36">.....</option>
                <option value="37">.....</option>
                <option value="38">.....</option>
                <option value="39">.....</option>
                <option value="40">.....</option>
                <option value="40">.....</option>
                <option value="40">.....</option>
                </select>
		    </div>
		</div>
			<div class="col-md-2">
			<div class="form-group">
				<label>TotleAmountAfterDiscount:</label>
				<input type="text" class="form-control " id="totle_amount_after_dicount" name="totle_amount_after_dicount" placeholder="" value="1200">
		    </div>
		</div>
		<div class="col-md-2">
	    	<div class="form-group">
				<label>Exam Fee Balance/Year:</label>
				<input type="text" class="form-control " id="exam_fee_balance" name="exam_fee_balance" placeholder="" value="1200">
		    </div>
		</div>
	    
	     	<div class="col-md-2">
	    	<div class="form-group">
				<label>Exam Fee Paid Amount:</label>
				<input type="text" class="form-control " id="exam_fee_paid_amount" name="exam_fee_paid_amount" placeholder="" value="0">
		    </div>
		</div>
	</div>
	
	
	<div class="row m-2">
		<div class="col-md-2">
			<div class="form-group">
				<label>Bus Fee/Year:</label>
					<input type="text" class="form-control " id="bus_fees" name="bus_fees" placeholder="" value="1200">
		    </div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<label>Discount Type:</label>
				<select class="form-control">
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
		<div class="col-md-2">
			<div class="form-group">
				<label>Discount Amount:</label>
				<select class="form-control">
                <option value="0">Select</option>
                <option value="1">....</option>
                <option value="18">.....</option>
                <option value="20">.....</option>
                <option value="34">.....</option>
                <option value="35">.....</option>
                <option value="36">.....</option>
                <option value="37">.....</option>
                <option value="38">.....</option>
                <option value="39">.....</option>
                <option value="40">.....</option>
                <option value="40">.....</option>
                <option value="40">.....</option>
                </select>
		    </div>
		</div>
			<div class="col-md-2">
			<div class="form-group">
				<label>TotleAmountAfterDiscount:</label>
				<input type="text" class="form-control " id="totle_amount_after_dicount" name="totle_amount_after_dicount" placeholder="" value="1200">
		    </div>
		</div>
		<div class="col-md-2">
	    	<div class="form-group">
				<label>Bus Fee Balance/Year:</label>
				<input type="text" class="form-control " id="bus_fee_balance" name="bus_fee_balance" placeholder="" value="1200">
		    </div>
		</div>
	    
	     	<div class="col-md-2">
	    	<div class="form-group">
				<label>Bus Fee Paid Amount:</label>
				<input type="text" class="form-control " id="bus_fee_paid_amount" name="bus_fee_paid_amount" placeholder="" value="0">
		    </div>
		</div>
	</div>
	
	
	<div class="row m-2">
		<div class="col-md-2">
			<div class="form-group">
				<label>Computer Fee/Year:</label>
					<input type="text" class="form-control " id="computer_fees" name="computer_fees" placeholder="" value="1500">
		    </div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<label>Discount Type:</label>
				<select class="form-control">
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
		<div class="col-md-2">
			<div class="form-group">
				<label>Discount Amount:</label>
				<select class="form-control">
                <option value="0">Select</option>
                <option value="1">....</option>
                <option value="18">.....</option>
                <option value="20">.....</option>
                <option value="34">.....</option>
                <option value="35">.....</option>
                <option value="36">.....</option>
                <option value="37">.....</option>
                <option value="38">.....</option>
                <option value="39">.....</option>
                <option value="40">.....</option>
                <option value="40">.....</option>
                <option value="40">.....</option>
                </select>
		    </div>
		</div>
			<div class="col-md-2">
			<div class="form-group">
				<label>TotleAmountAfterDiscount:</label>
				<input type="text" class="form-control " id="totle_amount_after_dicount" name="totle_amount_after_dicount" placeholder="" value="1500">
		    </div>
		</div>
		<div class="col-md-2">
	    	<div class="form-group">
				<label>ComputerFeeBalance/Year:</label>
				<input type="text" class="form-control " id="computer_fee_balance" name="computer_fee_balance" placeholder="" value="1500">
		    </div>
		</div>
	    
	     	<div class="col-md-2">
	    	<div class="form-group">
				<label>ComputerFeePaidAmount:</label>
				<input type="text" class="form-control " id="computer_fee_paid_amount" name="computer_fee_paid_amount" placeholder="" value="0">
		    </div>
		</div>
	</div>
	
	
	<div class="row m-2">
		<div class="col-md-3">
			
		</div>
		<div class="col-md-3">
			
		</div>
		
			<div class="col-md-3">
			<div class="form-group">
				<label>Admission fees (One Time):</label>
				<input type="text" class="form-control " id="totle_amount_after_dicount" name="totle_amount_after_dicount" placeholder="1000" value="">
		    </div>
		</div>
		<div class="col-md-3">
	    	<div class="form-group">
				<label>Grand Totle:</label>
				<input type="text" class="form-control " id="computer_fee_balance" name="computer_fee_balance" placeholder="0" value="">
		    </div>
		</div>
	    
	</div>

    <div class="row m-2">
        <div class="col-md-12 text-center">
        <button type="submit" class="btn btn-success btn-sm">Save & Change </button>
        </div>
    </div>
        </div>
    
</div>












@endsection      