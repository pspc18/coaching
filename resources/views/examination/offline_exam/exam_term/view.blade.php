@php
  $classType = Helper::classType();
  $getsubject = Helper::getSubject();
  $date = date('Y-m-d');
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
                       						    
							<h3 class="card-title"><i class="nav-icon fas fa fa-leanpub"></i> &nbsp;{{ __(' View Exam Term') }}</h3>
					
							<div class="card-tools"> 
							    <a href="{{url('add/exam_term')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(8)->add ? '' : 'd-none' }}"><i class="fa fa-plus"></i>{{ __('common.Add') }} </a> 
							    <a href="{{url('examination_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
                            </div>
						</div>
						<div class="card-body">

                    <form id="quickForm" action="{{ url('view/exam') }}" method="post" >
                        @csrf 
                    <div class="row">

            		<div class="col-md-5">
            			<div class="form-group">
            				<label>{{ __('common.Search By Keywords') }}</label>
            				<input type="text" class="form-control" id="name" name="name" placeholder="{{ __('common.Search By Keywords') }}" value="{{ $search['name'] ?? '' }}">
            		    </div>
            		</div>                     	
                        <div class="col-md-1 ">
                             <label for="" class="text-white">{{ __('common.Search') }}</label>
                    	    <button type="submit" class="btn btn-primary">{{ __('common.Search') }}</button>
                    	</div>
                    			
                    </div>
                </form> 
                
							<table id="example1" class="table table-bordered table-striped  dataTable">
								<thead>
									<tr role="row">
										<th>{{ __('common.SR.NO') }}</th>
										<th>{{ __('Exam Term') }} </th>
										<th>{{ __('common.Action') }}</th>
								</thead>
								<tbody> 
								@if(!empty($data)) 
    								@php 
    								    $i=1 
    								@endphp 
								@foreach ($data as $item)
								  
									<tr>
									
										<td>{{ $i++ }}</td>
										<td>{{ $item['name'] ?? ''}}</td>

							           <td>
                                            <a href="{{url('edit/exam_term') }}/{{$item->id}}" class="btn btn-primary  btn-xs ml-3 tooltip1 {{ Helper::permissioncheck(8)->edit ? '' : 'd-none' }}" title1="Edit Exam Term"><i class="fa fa-edit"></i></a> 
											<a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3 tooltip1 {{ Helper::permissioncheck(8)->delete ? '' : 'd-none' }}" title1="Delete Exam Term"><i class="fa fa-trash-o"></i></a> 
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




<!-- The Modal -->
<div class="modal" id="Modal_id">
	<div class="modal-dialog">
		<div class="modal-content" style="background: #555b5beb;">
			<div class="modal-header">
				<h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
			<input type="hidden" id="exam_id1" >
			<form action="{{ url('delete/exam_term') }}" method="post"> 
			    @csrf
				<div class="modal-body">
					<input type=text id="delete_id" name=delete_id>
					<h5 class="text-white">{{ __('common.Are you sure you want to delete') }} ?</h5> </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{__('common.Close') }}</button>
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
<script>
$(document).on('click', ".startExam", function () {
        toastr.error('You Cannot Attempt This Exam Now !');        
});
$(document).on('click', ".oldData", function () {
        toastr.error('You Already Attempted This Exam !');        
});
</script>
@endsection