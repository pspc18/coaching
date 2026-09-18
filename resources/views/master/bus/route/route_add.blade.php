@php
$getPermission = Helper::getPermission();
@endphp
@extends('layout.app')
@section('content')

<div class="content-wrapper">

    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-4 pr-0 {{($getPermission->add == 1) ? '' : 'd-none'}}">
                    <div class="card card-outline card-orange mr-1">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-road"></i> &nbsp; {{ __('bus.Add Route') }}</h3>
                            <div class="card-tools">

                            </div>

                        </div>
                        <form id="form-submit" action="{{ url('busRouteAdd') }}" method="post">
                            @csrf
                            <div class="row m-2">
                                <div class="col-md-12">
                                    <label style="color:red;">{{ __('messages.Name') }}*</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" placeholder="{{ __('messages.Name') }}">
                                   
                                </div>
                                <div class="col-md-12">
                                    <label>{{ __('messages.Description') }}</label>
                                    <textarea class="form-control" type="text" name="description" id="description" placeholder="{{ __('messages.Description') }}"></textarea>
                                </div>
                            </div>

                            <div class="row m-2">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-submit">{{ __('messages.submit') }} </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="{{($getPermission->add == 1) ? 'col-md-8 pl-0' : 'col-md-12 pl-0'}}">
                    <div class="card card-outline card-orange ml-1">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-road"></i> &nbsp;{{ __('bus.View Route') }} </h3>
                            <div class="card-tools">
                                <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>-->
                                <a href="{{url('busDashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a>
                            </div>

                        </div>
                        <div class="row m-2">
                            <div class="col-md-12">
                            </div>
                            <div class="col-md-12">
                                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                    <thead class="bg-primary">
                                        <tr role="row">
                                            <th>{{ __('messages.Sr.No.') }}</th>
                                            <th>{{ __('messages.Name') }}</th>
                                            @if($getPermission->edit == 1 || $getPermission->deletes == 1)
                                            <th>{{ __('messages.Action') }}</th>
                                            @endif



                                    </thead>
                                    <tbody id="">

                                        @if(!empty($dataview))
                                        @php
                                        $i=1
                                        @endphp
                                        @foreach ($dataview as $item)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $item['name'] ?? '' }}</td>
                                             @if($getPermission->edit == 1 || $getPermission->deletes == 1)
                                            <td>
                                                @if($getPermission->edit == 1)
                                                <a href="{{url('busRouteEdit',$item->id)}}" class="btn btn-primary  btn-xs tooltip1 {{ Helper::permissioncheck(20)->edit ? '' : 'd-none' }}" title1='Edit Student Registration'><i class="fa fa-edit"></i></a>
                                                @endif
                                                @if($getPermission->deletes == 1)
                                                <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-primary  btn-xs ml-3 tooltip1 {{ Helper::permissioncheck(20)->delete ? '' : 'd-none' }}" title1="Delete Student Registration"><i class="fa fa-trash-o"></i></a>
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
                <h4 class="modal-title text-white">{{ __('messages.Delete Confirmation') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>

            <form action="{{ url('busRouteDelete') }}" method="post">
                @csrf
                <div class="modal-body">
                    <input type=hidden id="delete_id" name=delete_id>
                    <h5 class="text-white">{{ __('messages.Are you sure you want to delete') }} ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('messages.Close') }}</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('messages.Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection