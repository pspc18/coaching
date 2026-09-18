@php
$role = Helper::getrole();
$sidebar = Helper::getSiderbar();
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
							<h3 class="card-title"><i class="fa fa-indent"></i> &nbsp; View Sidebar Permission</h3>
							<div class="card-tools"> <a href="{{url('side_permis_add')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add </a> </div>
						</div>
						<div class="card-body">
						   
						    
							<table id="example1" class="table table-bordered table-striped  dataTable">
								<thead>
									<tr role="row">
										<th>Sr. No.</th>
										<th>Role</th>
										<th>Permissions </th>
										<th>Action</th>
								</thead>
								<tbody id="user_list_show"> 
								@if(!empty($data)) 
    								@php 
    								    $i=1 
    								@endphp 
								@foreach ($data as $item)
									<tr>
										<td>{{ $i++ }}</td>
										<td>{{ $item['roleName']['name'] ?? '' }}</td>
								        <td>{{ $item['sidebar_id'] ?? '' }}
								        
                                    @if(!empty($sidebar))
                                    @foreach($sidebar as $data)
                                        {{ $data->name ?? ''  }},
                                    @endforeach
                                    @endif								        
								        
								        </td>
										<td> <a href="{{url('side_permis_edit',$item['id'])}}" class="btn btn-primary  btn-xs" title="Edit SideBar"><i class="fa fa-edit"></i></a> <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3" title="Delete SideBar"><i class="fa fa-trash-o"></i></a> </td>
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
});

</script>

<!-- The Modal -->
<div class="modal" id="Modal_id">
	<div class="modal-dialog">
		<div class="modal-content" style="background: #555b5beb;">
			<div class="modal-header">
				<h4 class="modal-title text-white">Delete Confirmation</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
			<form action="{{ url('side_permis_delete') }}" method="post"> @csrf
				<div class="modal-body">
					<input type=hidden id="delete_id" name=delete_id>
					<h5 class="text-white">Are you sure you want to delete  ?</h5> </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-danger waves-effect waves-light">Delete</button>
				</div>
			</form>
		</div>
	</div>
</div> @endsection