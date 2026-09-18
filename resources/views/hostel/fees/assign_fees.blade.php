
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    
<section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline card-orange mr-1">
                 <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-bookmark-o"></i> &nbsp; Assign Fees</h3>
                <div class="card-tools">
                <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>-->
                <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> Back</a>
                </div>
                
                </div>                 
                    <form id="quickForm" action="{{ url('assign_hostal_fees') }}/{{ $data->id }}" method="post">
                    @csrf
                    <div class="row m-2">
                        <div class="col-md-3">
                			<label style="color:red;">Name*</label>
                			<input class="form-control" type="text" placeholder="Hostel Name" value="{{ $data->first_name ?? '' }}" readonly>

                    	</div>
                        <div class="col-md-2">
                			<label style="color:red;">Mobile*</label>
                			<input class="form-control" type="text" placeholder="Mobile" value="{{ $data->mobile ?? '' }}" readonly>

                    	</div>   
                  	
                        <div class="col-md-2">
                			<label style="color:red;">Assign Date*</label>
                			<input class="form-control @error('date') is-invalid @enderror" type="date" name="date" id="date" value="{{ date('Y-m-d') }}">
                             @error('date')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
                    	</div>
                        <div class="col-md-2">
                			<label style="color:red;">Assign Amount*</label>
                			<input class="form-control @error('amount') is-invalid @enderror" type="text" name="amount" id="amount" placeholder="Assign Amount" value="{{ old('amount') }}" onkeypress="javascript:return isNumber(event)">
                             @error('amount')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
                    	</div>                    	
                    </div>
     
                    <div class="row m-2">
                        <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">Submit </button>
                        </div>
                    </div>
                    </form>
                </div>          
            </div>            
            
        </div>  
    </div>
    </section>

</div>


@endsection      