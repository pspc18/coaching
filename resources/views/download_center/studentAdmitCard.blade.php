@extends('layout.app') 
@section('content')


<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-cloud-download"></i> &nbsp;{{ __('Admit Card') }}</h3>
							<div class="card-tools"> <a href="{{url('download_center')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a> </div>
						</div>
				
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
        <table class="table table-bordered table-striped dataTable dtr-inline ">
          <thead class="bg-primary">
          <tr role="row">

                                        <th>{{ __('messages.Sr.No.') }}</th>
                                        <th>{{ __('Exam') }}</th>
                                        <th>{{ __('Issue Date') }}</th>
                                        <th>{{ __('messages.Action') }}</th>
                                </thead>
                                <tbody>
                      
                                    @if(!empty($data))
                                    @php
                                       $i=1;
                                      
                                    @endphp
                                    @foreach ($data  as $item)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $item['exam_name'] ?? ''  }}  </td>
                                        <td>{{ date('d-M-Y h:i A', strtotime($item['created_at']))}}</td>
                                    
                                     
                                        <td>
                                            
                                            @if(!empty($fees))
                                                @php
                                                
                                                
                                                $remainingFees = $fees['total_school_fees'] - $fees['collected_school_fees'];
                                                @endphp
                                            
                                            @endif
                                            
                                            @if($remainingFees <= 0)
                                            
                                            <a href="{{url('/single_exam_admit_card')}}/{{$item['class_type_id']}}/{{$item['exam_id']}}/{{$item['admission_id']}}" target="_blank">
                                             <button form="Form_0" type="submit" class="btn btn-success btn-xs ml-3" title="View/Download">
                                                <i class="fa fa-print"></i> 
                                            </button>
                                        </a>
                                              <!--<a href="{{ url('download') }}/{{$item['admission_id'] ?? '' }}" class="ml-2" title="Download"><i class="fa fa-download text-success"></i></a>-->
                                              @else
                                              <span class='text-danger' style='font-size:12px'>Please Pay your tuition fees to download admit card <br> <a style='color:blue;cursor:pointer' class='checkout' data-admission_id="{{$item['admission_id'] ?? '' }}" 
                                               data-email={{$item['email']}}
                                               data-mobile={{$item['mobile']}}
                                              
                                              data-remaining={{$remainingFees}}>Click here for pay fees</a>
                                              
                                              </span>
                                            @endif
                                          
                                        </td>
                                      
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
</div>
        
        
<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
 </script>

<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title text-white">Delete Confirmation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('upload_delete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              
            
            
              <input type=hidden id="delete_id" name=delete_id>
              <h5 class="text-white">Are you sure you want to delete  ?</h5>
           
      </div>

      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">Delete</button>
         </div>
       </form>

    </div>
  </div>
</div>     


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>

$('.checkout').on('click', function() {
    
    var remaining = $(this).attr('data-remaining');
    var admission_id = $(this).attr('data-admission_id');
    var email = $(this).attr('data-email');
    var mobile = $(this).attr('data-mobile');
  
  
                  // console.log(result);
                   var options = {
                        "key": "{{ Config::get('app.razorpay_key') }}", 
                        "amount": remaining*100, 
                       
                        "currency": "INR",
                        "name": "Rukmani Software",
                        "description": "Live Transaction",
                        "image": "https://www.rukmanisoftware.com/public/assets/img/header-logo.png",
                        "handler": function (response){
                         afterPayment(admission_id,remaining,response.razorpay_payment_id,email,mobile);
                        }
                    };
                    
                    var rzp1 = new Razorpay(options);
                   rzp1.open();
                    
        
});


function afterPayment(admission_id,amount,transaction_id,email,mobile){
    
  var basurl = "{{ url('/') }}";
             $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: basurl+'/student_pay_submit',
                data: {
                    slip_no:10,
                    admission_id: admission_id,
                    amount: amount,
                    pay_amt: amount,
                    transaction_id: transaction_id,
                    email: email,
                    mobile: mobile,
                    payment_mode_id:9
                },
                //dataType: 'json',
                success: function(data) {
             window.location.href = basurl+'/studentAdmitCard';
                }
            });
}
</script>
  
@endsection