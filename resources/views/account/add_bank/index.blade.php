@php
$getPermission = Helper::getPermission();
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
							<h3 class="card-title"><i class="fa fa-bank"></i> &nbsp;{{ __('accounts.View Accounts') }}</h3>
							@if($getPermission->add == 1)
							<div class="card-tools">
							     <a href="{{url('bank/account/add')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i>{{ __('common.Add') }} </a> 
							     <!--<a href="{{url('account_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> Back</a>--> 
							 </div>
							 @endif
						</div>
				
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline" style="white-space: nowrap;">
          <thead>
          <tr role="row">
                      
                      
                      <th>{{ __('common.SR.NO') }}</th>
                            <th>{{ __('accounts.Account Holder Name') }}</th>
                            <th>{{ __('accounts.Account Number') }} </th>
                            <th>{{ __('accounts.Bank Name') }}</th>
                            <th>{{ __('accounts.Bank Branch Name') }} </th>
                            <th>{{ __('accounts.Bank IFSC Code') }}</th>
                            <th>{{ __('accounts.Uplode QR.Code') }}</th>
                            @if($getPermission->edit == 1 || $getPermission->deletes == 1)
                                <th>{{ __('common.Action') }}</th>
                            @endif
                           
                             
                      
                  </thead>
                  <tbody>
                      
                      @if(!empty($data))
                        @php
                           $i=1
                        @endphp
                        @foreach ($data  as $item)
                        <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $item['name']  }}</td>
                                <td>{{ $item['account_number']  }}</td>
                                <td>{{ $item['bank_name']  }}</td>
                                <td>{{ $item['branch_name']  }}</td>
                                <td>{{ $item['ifsc_code']  }}</td>
                                <td><img src="{{ env('IMAGE_SHOW_PATH').'/uplode_qr/'.$item['uplode_qr'] }}" width="120px" height="60px"></td>
                                @if($getPermission->edit == 1 || $getPermission->deletes == 1)
                                <td>
                                    @if($getPermission->edit == 1)
                                    <a href="{{url('bank/account/edit',$item->id)}}" class="btn btn-primary  btn-xs tooltip1" title1="Edit"><i class="fa fa-edit"></i></a> 
                                    @endif
                                    @if($getPermission->deletes == 1)
                                    <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3 tooltip1" title1="Delete"><i class="fa fa-trash-o"></i></a>                                   
                                    @endif
                                </td>
                                @endif
                                
                      </tr>
                      @endforeach
                @endif
            </tbody>
                  </table>
                  
              </div>
              
            </div>
           
        </div>
        
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
        <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('account_delete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              
            
            
              <input type=hidden id="delete_id" name=delete_id>
              <h5 class="text-white">{{ __('common.Are you sure you want to delete') }}?</h5>
           
      </div>

      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('common.Delete') }}</button>
         </div>
       </form>
		</div>
	</div>
		</div>
	</div>
		</div>

	</section>
</div>>




@endsection 