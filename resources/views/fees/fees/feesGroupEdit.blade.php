@php

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
            <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('fees.Edit Fees Group') }} </h3>
            <div class="card-tools">
      
            <a href="{{url('feesGroup')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-eye"></i> {{ __('messages.View') }} </a>
            <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('Back') }} </a>
            </div>
            
            </div>                 
                <form id="quickForm" action="{{ url('feesGroupEdit') }}/{{$data['id']}}" method="post">
                @csrf
                	<div class="row mb-2 m-2">
		    <div class="col-md-4">
				<div class="form-group">
                			<label style="color:red;"{{ __('fees.Add Fees Group') }}>{{ __('messages.Name') }}*</label>
                			<input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" placeholder="Name"value="{{ $data['name'] ?? '' }}">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror                    			
                    	</div>
                    	</div>
                        	
                    <div class="col-md-4" style="align-content: flex-end;">
                    <div class="form-group">
                    <label for="refund_fees">Refund Fees :</label>
                    <!-- Checkbox for Refund Fees -->
                    <input type="checkbox"    id="refund_fees_value" value="yes"  @if(old('fees_refund', $data->fees_refund) === 'yes') checked @endif  onchange="updateRefundFees(this)" >
                       </div>                    	
                        </div>
                        <input type="hidden" id="fees_refund" name="fees_refund" value="{{ $data['fees_refund'] ?? '' }}">
                        </div>
 
                
                          <div class="col-md-12 text-center">
			<button type="submit" class="btn btn-primary">{{ __('messages.Update') }}</button><br><br>
		</div>
    	</form>
             </div>
        </div>
    </div>
    </section>
</div>
<script>
    function updateRefundFees(checkbox) {
        if (checkbox.checked) {
            document.getElementById('fees_refund').value = 'yes';
        } else {
            document.getElementById('fees_refund').value = 'no';
        }
    }
</script>
@endsection      