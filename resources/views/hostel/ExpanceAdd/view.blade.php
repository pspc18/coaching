@php
    $getHostel = Helper::getHostel();
//dd($data);
@endphp
@extends('layout.app') 
@section('content')
<style>
    .top{
        margin-top: -12px;
    }
</style>
<div class="content-wrapper">

	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-credit-card"></i> &nbsp;{{ __('hostel.Hostel View Expense') }}</h3>
							
							
							<div class="card-tools"> 
					
							    <a href="{{url('hostelExpensesAdd')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i>{{ __('common.Add') }}</a>
							    <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
							</div>
							
						</div>
						<div class="card-body">
                        <form id="quickForm" action="{{ url('hostelExpensesView') }}" method="post" >
                        @csrf 
                    <div class="row m-2">
                   <div class="col-md-2 mb-2 top">
                    		<div class="form-group">
                    			<label>{{ __('hostel.From Date') }}</label>
            				<input type="date" class="form-control" id="from_date" name="from_date"  value="{{ $_POST['from_date'] ?? '' }}" >
                            </div>
                    	</div>
                    	<div class="col-md-2 top">
                            <div class="form-group ">
                                <label>{{ __('hostel.To Date') }}</label>
            				<input type="date" class="form-control" id="to_date" name="to_date"  value="{{ $_POST['to_date'] ?? '' }}">
                			</div> 
                        </div>
            		<div class="col-md-3 top">
            			<div class="form-group">
            				<label>{{ __('common.Search By Keywords') }}</label>
            				<input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Expenses. Name, ') }}" value="{{ $search['name'] ?? '' }}">
            		    </div>
            		</div>                     	
                        <div class="col-md-1 top">
                             <label class="text-white">Search</label>
                    	    <button type="submit" class="btn btn-primary" >{{ __('common.Search') }}</button>
                    	</div>
                    			
                    </div>
                     </form>
                            <div class="col-md-12" id="">

                        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                            <thead class="bg-primary">
                                <tr role="row">
                                    <th>{{ __('common.SR.NO') }}</th>
                                    <th>{{ __('hostel.Expense Head') }}</th>
                                    <th>{{ __('hostel.Expense Date') }}</th>
                                    <th>{{ __('hostel.Expense Name') }}</th>
                                  <th>{{ __('hostel.Expences Amount') }}</th>
                                    <th>{{ __('hostel.Expences By') }}</th>
                                    <th>{{ __('common.Action') }}</th>
                              
                                </tr>
                            </thead>
                            <tbody>

                                @if(!empty($data))
                                @php
                                    $i=1;
                                   $total = 0;
                                @endphp

                                @foreach ($data  as $item)
                              
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $item['expense_head'] ?? '' }} </td>
                                    <td>{{ date('d-M,Y', strtotime($item['expense_date'])) ?? '' }}</td>
                                    <td>{{ $item['expense_name'] ?? '' }}</td>
                                    <td >{{ $item['expense_amount'] ?? '' }}</td>
                                    <td >{{ $item['first_name'] ?? '' }}{{ $item['last_name'] ?? '' }}</td>
                                    <td>
                                        <a href="{{ url('hostelExpensesPrint') }}/{{ $item['id'] }}" class="btn btn-success  btn-xs ml-3 tooltip1" title1="Edit Expense" target="_blank"><i class="fa fa-print"></i></a> 
                                        <a href="{{ url('hostelExpensesEdit') }}/{{ $item['id'] }}" class="btn btn-primary  btn-xs ml-3 tooltip1" title1="Edit Expense" ><i class="fa fa-edit"></i></a> 
                                        <a href="javascript:;" data-id='{{ $item['id'] ?? '' }}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger btn-xs ml-1 tooltip1" title1="Delete Expense"><i class="fa fa-trash-o"></i></a>
                                    </td>                                    
                                </tr>
                               
                               @php
                               $total += $item['expense_amount'] ;
                               @endphp
                                @endforeach
                           
                             
                             <tfoot>
                              
                                  <tr>
                                        <th class="text-white">{{ __('hostel.Total') }}</th>
                                        <th> </th>
                                        <th> </td>
                                        <th> <b>{{ __('hostel.Total Amount') }}</b></th>
                                        <th> <b id="total_amt">₹ {{$total ?? ''}}</b></th>
                                        <th></th>   
                                        <th></th>   
                                  </tr>    
                              
                               
                            
                               
                            </tfoot>
                              @endif
                            </tbody>
                        </table>
        
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
			<form action="{{ url('hostelExpensesDelete') }}" method="post"> 
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
$('.deleteData').click(function() {
	var delete_id = $(this).data('id');
	$('#delete_id').val(delete_id);
});


</script>


@endsection      