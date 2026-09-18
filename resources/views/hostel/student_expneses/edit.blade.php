@php
$getPaymentMode = Helper::getPaymentMode();
$allstudents = Helper::allstudents();
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
							<h3 class="card-title"><i class="fa fa-users"></i> &nbsp; {{ __('Hostel Student Expenses Edit') }}</h3>
							<div class="card-tools"> 
							    <a href="{{url('student_expenses')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i> View </a> 
							     <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}  </a>  
							</div>
						</div>
                        <form id="studentDetailsForm" method="post" action="{{ url('student_expenses_edit') }}/{{ $expense->id }}" enctype="multipart/form-data">
                           @csrf
                            <div class="row m-2">
                                <div class="col-md-4">
                        			<label>{{ __('hostel.Select Student') }}<font style="color:red"><b>*</b></font></label>
                            
                                   <select name="hostel_assign_id" id="hostel_assign_id" class="form-control select2 " required>
                                      <option value="">{{ __('common.Select') }}</option>
                                      @if(!empty($allstudents))
                                      @foreach($allstudents as $value)
                                      <option value="{{ $value->id }}" {{ $value->id == $expense['hostel_assign_id'] ? 'selected' : '' }}  >{{ $value->first_name ?? ''}} {{'[Father Name : '}}{{ $value->father_name ?? 'N/A'}}{{' ]'}}</option>
                                      @endforeach
                                      @endif
                                   </select>
                       
                            	</div>   
                            	<div class="col-md-2">
                            		<div class="form-group">
                            			<label>{{ __('hostel.Expense Name') }}</label>
                            			<input type="text" class="form-control" id="expense_name" name="expense_name" placeholder="{{ __('hostel.Expense Name') }}" value="{{ $expense['expense_name'] ?? '' }}"> 
                            	    </div>
                            	</div>
                            	
                            	<div class="col-md-2">
									<div class="form-group">
										<label>Expense Date</label>
										<input type="date" class="form-control " id="expense_date" name="expense_date" value="{{ $expense['expense_date'] ?? '' }}">
									</div>
								</div>
								
								
                               <div class="col-md-2">
                            		<div class="form-group">
                            			<label>{{ __('common.Amount') }}</label>
                            			<input type="text" class="form-control" id="amount" name="amount" placeholder="{{ __('common.Amount') }}" value="{{ $expense['expense_amount'] ?? '' }}" onkeypress="javascript:return isNumber(event)" required> 
                            	    </div>
                            	</div> 
                            	
                            	<div class="col-md-2">
									<div class="form-group">
										<label>Payment Mode</label>
										<select class="form-control" id="payment_mode" name="payment_mode">
											<option value="">Select</option>
											@if(!empty($getPaymentMode))
											@foreach($getPaymentMode as $value)
											<option value="{{$value->id}}" {{ ($value->id == $expense['payment_mode']) ? 'selected' : '' }}>{{ $value->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
									</div>
								</div>
								
								<div class="col-md-3">
									<div class="form-group">
										<label>{{ __('hostel.Expense Bill') }}</label>
										<input type="file" class="form-control" id="expense_bill" name="expense_bill"  accept="image/png, image/jpg, image/jpeg" value="{{ $expense['expense_bill'] ?? '' }}">
									</div>
								</div>
								
								<div class="col-md-3">
									<div class="form-group">
									   <img src="{{ env('IMAGE_SHOW_PATH').'student_expence_bill/'.$expense['expense_bill'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" width="100px" height="100px" alt="">
									</div>
								</div>
                            
                               
                                <div class="col-md-12 text-center">
                            	    <div class="form-group">
                            	        <label style="visibility:hidden">{{ __('common.Submit') }}</label>
                            			<button type="submit" class="btn btn-primary">{{ __('common.Submit') }}</button>
                            	    </div>                    
                            	</div>
                            </div> 
                        </form>   
				</div>
			</div>
		</div>
	</section>
</div>
@endsection      