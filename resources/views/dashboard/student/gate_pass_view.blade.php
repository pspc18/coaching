@extends('layout.app')
@section('content')

<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-user-secret"></i> &nbsp;{{ __('View Gate Pass') }} </h3>
                            <div class="card-tools">
                           
                                <a href="{{url('dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a>

                            </div>
                        </div>

                        <div class="row mb-2 m-2">
                            <div class="col-md-12">
                                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                    <thead class="bg-primary">
                                        <tr role="row">
                                            <th>{{ __('messages.Sr.No.') }}</th>
                                            <th>{{ __('Student Name') }}</th>
                                            <th>{{ __('Father Name') }}</th>
                                            <th>{{ __('Father Mobile') }}</th>
                                            <th>{{ __('Reciver  Name') }}</th>
                                            <th>{{ __('Reciver Mobile') }}</th>
                                            <th>{{ __('Relation') }}</th>
                                            <th>{{ __('Date') }}</th>

                                            <!--<th>{{ __('messages.Action') }}</th>-->
                                    </thead>
                                    <tbody>

                                        @if(!empty($data))
                                        @php
                                        $i=1
                                        @endphp
                                        @foreach ($data as $item)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $item['student_name'] ?? '' }}</td>
                                            <td>{{ $item['father_name'] ?? '' }}</td>
                                            <td>{{ $item['father_mobile'] ?? '' }}</td>
                                            <td>{{ $item['reciver_name'] ?? '' }}</td>
                                            <td>{{ $item['reciver_mobile'] ?? '' }}</td>
                                            <td>{{ $item['relation'] ?? '' }}</td>
                                            <td>{{date('d-m-Y', strtotime($item['iessu_date'] ?? ''))}} {{date('h:i A', strtotime($item['iessu_date'] ?? ''))}}</td>

                                            <!--<td>
                                                <a href="{{url('gate_pass_print') }}/{{$item->admissionNo}}" class="btn btn-success  btn-xs ml-3" title="Gate Pass Print" target="_blank"><i class="fa fa-print"></i></a>
                                                <a href="{{url('gate_pass_edit') }}/{{$item->id}}" class="btn btn-primary  btn-xs ml-3" title="Edit Complaint"><i class="fa fa-edit"></i></a>
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


                    <script>
                        $('.deleteData').click(function() {
                            var delete_id = $(this).data('id');
                            $('#delete_id').val(delete_id);
                        });
                    </script>

                    <div class="modal" id="Modal_id">
                        <div class="modal-dialog">
                            <div class="modal-content" style="background: #555b5beb;">
                                <div class="modal-header">
                                    <h4 class="modal-title text-white">{{ __('messages.Delete Confirmation') }}</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                                </div>
                                <form action="{{ url('gate_pass_delete') }}" method="post">
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
                </div>
            </div>
        </div>
    </section>
</div>
@endsection