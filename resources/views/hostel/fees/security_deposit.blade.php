@php
$getPaymentMode = Helper::getPaymentMode();
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
							<h3 class="card-title"><i class="fa fa-users"></i> &nbsp; {{ __('Hostel Security Deposit View') }}</h3>
							<div class="card-tools"> 
							    <a href="{{url('hostel/fees/security_deposite_add')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i>Add </a> 
							    <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
							</div>
						</div>
						
						
                            
                       <div class="row m-2">
                                <div class="col-md-3">
                        			<label>{{ __('hostel.Select Student') }}<font style="color:red"><b>*</b></font></label>
                        <form id="studentDetailsForm" method="post" action="{{ url('hostel/fees/security_deposite') }}">
                           @csrf
                           <select name="student_details" id="student_details" class="form-control select2 ">
                              <option>{{__('common.Select') }}</option>

                              @if(!empty($allstudents))
                              @foreach($allstudents as $value)
                              <option value="{{ $value->admission_id }}" {{ ( $value->admission_id == $search['student_details'] ?? '' ) ? 'selected' : '' }}>{{ $value->first_name ?? ''}} {{ $value->last_name ?? ''}} {{$value->room_name ?? ''}}</option>
                              @endforeach
                              @endif
                           </select>
                        </form>
                            	</div>    
                               
                               
                                
                            </div> 
                      
                              <div class="row m-2">

                                <div class="col-md-12">
                                    
                                    <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                                        <thead>
                                            <tr role="row">
                                                <th>{{ __('common.SR.NO') }}</th> 
                                                <th>{{ __('common.Name') }}</th> 
                                                <th>{{ __('common.Mobile') }}</th> 
                                               
                                                <!--<th>{{ __('messages.Hostel') }}</th>-->
                                                <!--<th>{{ __('messages.Building') }}</th>-->
                                                <!--<th>{{ __('messages.Floor') }}</th>-->
                                                <!--<th>{{ __('messages.Room') }}</th>                                           -->
                                                                          
                                                <th>{{ __('common.Fathers Name') }}</th>                                           
                                                <th>{{ __('hostel.Deposit On') }}</th>                                           
                                                                                      
                                                <th>{{ __('hostel.Security Deposit') }}</th>                                           
                                                <th>{{ __('hostel.Payment Mode') }}</th>                                           
                                                <th>{{ __('Transaction Id') }}</th>                                           
                                                <th>{{ __('Other') }}</th>                                           
                                                <th>{{ __('hostel.Status') }}</th>                                           
                                                <th>{{ __('common.Action') }}</th>                                           
                                               
                                            </tr>
                                        </thead>
                                        <tbody id="student_list_show">
             @php
                                                    $total_security=0;
                                                    $total_mess=0;
                                            @endphp
                                            @if(!empty($data))
                                            
                                            @foreach($data as $key =>$item)
                                            
                                             @php
                                                    
                                                    $total_security += $item->security_deposit; 
                                                    $total_mess += $item->mess_security_deposite; 
                                            @endphp
                                          
                                            <tr>
                                                 <td>{{$key+1 }}</td>  
                                                <td style=" text-transform: capitalize;">{{$item->first_name ?? ''}}</td>
                                                <td>{{$item->mobile ?? ''}}</td>
                                                <!--<td>{{$item->hostel_name ?? ''}}</td>-->
                                                <!--<td>{{$item->building_name ?? ''}}</td>-->
                                                <!--<td>{{$item->floor_name ?? ''}}</td>-->
                                                <!--<td>{{$item->room_name ?? ''}}</td>-->
                                                <!--<td>{{$item->bad_name ?? ''}}</td>-->
                                                <td style=" text-transform: capitalize;">{{$item->father_name ?? ''}}</td>
                                                <td>{{date('d-m-Y', strtotime($item->date)) ?? ''}}</td>
                                                <td>{{$item->security_deposit ?? ''}} /-</td>
                                                <td>
                                                
                                                  @if(!empty($getPaymentMode))
                                    @foreach($getPaymentMode as $value)
                                 {{ $value->id == $item->payment_mode_id ?  $value->name : '' }}
                                    @endforeach
                                    @endif
                                                
                                                </td>
                                                <td>{{$item->transaction_id ?? ''}}</td>
                                                <td>{{$item->pay_remark ?? ''}}</td>
                                                <td class="text-{{ $item->status == 0 ? 'success' : 'danger'}}">{{$item->status == 0 ? 'Paid' : 'Refunded' }}</td>
                                                <td>
                                                    
                                                  
                                                        <a href="{{ url('securityDepositPrint') }}/{{ $item->id }}" target="blank" class=" btn btn-success btn-xs ml-1" title="Print Security Deposit"><i class="fa fa-print"></i></a>
                                                       
                                                        <button type="submit" class="paid_data btn btn-{{ $item->status == 0 ? 'danger' : 'success'}}" 
                                                                                data-id="{{$item->id ?? ''}}"
                                                                                data-status="{{$item->status ?? ''}}"
                                                                                data-hostel_assign_id="{{$item->hostel_assign_id ?? ''}}"
                                                                                data-toggle="modal"
                                                                               
                                                                                data-target="#myPaidModal" {{ $item->status == 0 ? '' : 'disabled'}}>
                                                                            {{ $item->status == 0 ? 'Refund' : 'Paid'}}
                                                        </button>
                                                    <a href="javascript:;" data-id='{{ $item['id'] ?? '' }}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger btn-xs ml-1" title="Delete Security "><i class="fa fa-trash-o"></i></a>
                                                    
                                                </td>
                                          </tr>
                                        
                            @endforeach                                            
                                        @endif
                                  
                                           
                                        </tbody>
                                         <tfoot>
                
                <tr>
                   
                  <td colspan="5" class="text-right"><b>Total : </b></td>
                  <td><b>{{$total_security}} /-</b></td>
                  <td colspan="4"><b>{{$total_mess}} /-</b></td>
                  </tr>
            </tfoot>
                                    </table>
                                </div>
                                
                                
                                <div class="modal" id="myPaidModal">
                                    <div class="modal-dialog">
                                        <div class="modal-content" style="background: #555b5beb;">
                                            <div class="modal-header">
                                                <h4 class="modal-title text-white">{{ __('hostel.Are You Sure You Want To Refund') }}</h4>
                                                <button type="button" class="btn-close" data-dismiss="modal" ><i class="fa fa-times" aria-hidden="true"></i></button>
                                            </div>
                                                <form action="{{url('hostel/fees/security_deposite_refund')}}" method="post">
              	                                    @csrf
                                                
                                                        <input type="hidden" id="security_status" name="security_status" />
                                                        <input type="hidden" id="security_id" name="security_id" />
                                                        <input type="hidden" id="hostel_assign_id_1" name="hostel_assign_id" />
                                                      
                                                
        
                                                    <div class="text-center p-3">
                                                        <button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('hostel.Refund') }}</button>
                                                         <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-dismiss="modal">{{ __('common.Close') }}</button>
                                                       
                                                    </div>
                                                </form>
                                        </div>
                                    </div>
                                </div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<!-- The Modal -->
<div class="modal" id="Modal_id">
	<div class="modal-dialog">
		<div class="modal-content" style="background: #555b5beb;">
			<div class="modal-header">
				<h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
			<form action="{{ url('hostel/fees/security_deposite_delete') }}" method="post"> 
			    @csrf
				<div class="modal-body">
					<input type=hidden id="delete_id" name=delete_id>
					<h5 class="text-white">{{ __('common.Are you sure you want to delete') }}  ?</h5> </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
					<button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('common.Delete') }}</button>
				</div>
			</form>
		</div>
	</div>
</div> 

<script>

 

 $(document).ready(function() {

      $('#student_details').change(function() {
         var student_details = $(this).val();
         if (student_details != '') {
            $('#studentDetailsForm').trigger('submit');
         } else {
            window.location.href = 'fees';
         }
      })
   });

    </script>
    
<script>

$('.deleteData').click(function() {
	var delete_id = $(this).data('id');
	$('#delete_id').val(delete_id);
});



    $(document).ready(function(){
        $('.paid_data').click(function(){
            var id = $(this).data("id");
            var status = $(this).data("status");
            var hostel_assign_id = $(this).data("hostel_assign_id");
         


        
            $("#security_id").val(id);
            $("#security_status").val(status);
            $("#hostel_assign_id_1").val(hostel_assign_id);
        });
        
        
        
        
                       $('#payment_mode_id').on('change', function() {
    
    if(this.value == 16 || this.value == 17){
        $('.transaction_slip').removeClass('d-none');
    }else{
        $('.transaction_slip').addClass('d-none');
    }
  
        
});
        
   
    });
</script>


                        

@endsection      