@php
$role = Helper::getrole();
@endphp
@extends('layout.app') 
@section('content')
<div class="content-wrapper">
	<!--<div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
                <div class="col-sm-8">
                <h3 class="m-2">View Users</h3>
                </div>
                <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                <li class="pr-2"><a href="{{url('add_user')}}" class="btn btn-primary  btn-xs"><i class="fa fa-plus"></i> Add User</a></li>
                </ol>
                </div>
            </div>
        <hr class="bg-primary" style="margin-top:-12px;">
        </div>
    </div>-->
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-child"></i> &nbsp; View Holiday</h3>
							<div class="card-tools"> <a href="{{url('holiday/add')}}" class="btn btn-primary  btn-sm" title="Add "><i class="fa fa-plus"></i> Add </a> </div>
						</div>
						<div class="card-body">
						    
                           						    
						    
							<table id="example1" class="table table-bordered table-striped  dataTable">
								<thead>
									<tr role="row">
										<th>Sr. No.</th>
										<th>Name</th>
									
								</thead>
								<tbody id="user_list_show"> 
								@if(!empty($data)) 
    								@php 
    								    $i=1 
    								@endphp 
								@foreach ($data as $item)
									<tr>
										<td>{{ $i++ }}</td>
										
								        <td>{{ $item['name'] ?? '' }} </td>
									
									
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

@endsection