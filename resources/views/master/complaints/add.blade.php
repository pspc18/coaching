
@extends('layout.app') 
@section('content')

                                                                    
<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-book-reader"></i> &nbsp; {{ __('master.View Complaints') }}</h3>
							<div class="card-tools"> <a href="{{url('complaint_add')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i> {{__('common.Add') }} </a> </div>
						</div>
				
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline text-nowrap">
          <thead>
          <tr role="row" class="text-nowrap">
              <th>{{__('common.SR.NO') }}</th>
                    <th>{{__('common.Subject') }}</th>
                    <th>{{__('master.Description') }}</th>
                  
<!--                     <th>Action</th>
-->          </thead>
          <tbody>
              
              @if(!empty($data))
                @php
                   $i=1
                @endphp
                @foreach ($data  as $item)
                <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $item['subject'] ?? '' }}</td>
                        <td>{{ $item['description'] ?? '' }}</td>
                      
                       <!--<td>
                                    <a href="{{url('complaint_edit') }}/{{$item->id}}" class="btn btn-primary  btn-xs ml-3" title="Edit Complaint"><i class="fa fa-edit"></i></a> 
												    <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3" title="Delete Book"><i class="fa fa-trash-o"></i></a> 
												</td>-->
              </tr>
              @endforeach
            @endif
            </tbody>
        </table>
        </div>
        </div>

</div>


@endsection

