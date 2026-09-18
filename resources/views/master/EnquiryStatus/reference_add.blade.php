@php
   $tabType = $type ?? 'reference';
@endphp

@extends('layout.app')
@section('content')
<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-sm-12">
                    <div class="card card-primary card-outline card-outline-tabs">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link {{ $tabType=='reference'?'active':'' }}" href="{{ url('enquiry_status_add?type=reference') }}"><b>Reference</b></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $tabType=='response'?'active':'' }}" href="{{ url('enquiry_status_add?type=response') }}"><b>Response</b></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $tabType=='calling_purpose'?'active':'' }}" href="{{ url('enquiry_status_add?type=calling_purpose') }}"><b>Calling Purpose</b></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $tabType=='visiting_purpose'?'active':'' }}" href="{{ url('enquiry_status_add?type=visiting_purpose') }}"><b>Visiting Purpose</b></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $tabType=='complaint_type'?'active':'' }}" href="{{ url('enquiry_status_add?type=complaint_type') }}"><b>Complaint Type</b></a>
                                </li>
                            </ul>
                        </div>

                        <div class="row">
                            <div class="col-md-4 pr-0">
                                <div class="card card-outline card-orange mr-1">
                                    <div class="card-header bg-primary">
                                        <h3 class="card-title"><i class="fa fa-user-circle-o"></i> &nbsp;{{ ucfirst(str_replace('_',' ',$tabType)) }}</h3>
                                    </div>
                                    <form id="form-submit" action="{{ url('enquiry_status_add?type='.$tabType) }}" method="POST">
                                        @csrf
                                        <div class="row m-2">
                                            <div class="col-md-12">
                                                <label class="text-danger">Name*</label>
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="{{ old('name') }}">
                                            </div>
                                        </div>
                                        <div class="row m-2">
                                            <div class="col-md-12 text-center">
                                                <button type="submit" class="btn btn-primary btn-submit">Submit</button><br>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="col-md-8 pl-0 col-md-12 pl-0}}">
                                <div class="card card-outline card-orange ml-1">
                                    <div class="card-header bg-primary">
                                        <h3 class="card-title"><i class="fa fa-user-circle-o"></i> &nbsp;View {{ ucfirst(str_replace('_',' ',$tabType)) }}</h3>
                                        <div class="card-tools">
                                            <a href="{{ url('reception_file') }}" class="btn btn-primary btn-sm">
                                                      <i class="fa fa-arrow-left"></i> {{ __('common.Back') }}
                                            </a>
    </div>
                                    </div>

                                    <div class="row m-2">
                                        <div class="col-md-12" style="overflow-x:auto;">
                                            <table class="table table-bordered table-striped dataTable dtr-inline">
                                                <thead>
                                                    <tr>
                                                        <th>SR.NO</th>
                                                        <th>Name</th>
                                                        
                                                            <th>Action</th>
                                                      
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $i=1 @endphp
                                                    @foreach ($data as $item)
                                                        <tr>
                                                            <td>{{ $i++ }}</td>
                                                            <td>{{ $item->name }}</td>
                                                            <td>
                                                               
                                                                    <a href="{{ url('enquiry_status_edit/'.$item->id) }}" class="btn btn-primary btn-xs {{ Helper::permissioncheck(28)->edit ? '' : 'd-none' }}"><i class="fa fa-edit"></i></a>
                                                               
                                                                    <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger btn-xs ml-3 {{ Helper::permissioncheck(28)->delete ? '' : 'd-none' }}"><i class="fa fa-trash-o"></i></a>
                                                              
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
<script>
    $('.deleteData').click(function() {
        var delete_id = $(this).data('id');
        $('#delete_id').val(delete_id);
    });
</script>

<!-- Delete Modal -->
<div class="modal" id="Modal_id">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #555b5beb;">
            <div class="modal-header">
                <h4 class="modal-title text-white">Delete Confirmation</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <form action="{{ url('enquiry_status_delete') }}" method="post">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="delete_id" name="delete_id">
                    <h5 class="text-white">Are you sure you want to delete?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
