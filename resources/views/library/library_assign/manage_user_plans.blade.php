@php
$getLibrary = Helper::getLibrary();
//$getLibraryCabinAll = Helper::getLibraryCabinAll();
$getPermission = Helper::getPermission();
//dd($search);
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
                            <h3 class="card-title"><i class="fa fa-book"></i> &nbsp;{{ __('User Plan') }} </h3>
                            <div class="card-tools">
                                <a href="{{url('library_student_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="col-md-12 p-0">
                                <h5 class="bg-primary text-white mb-0 p-2" style="border-radius:4px;">#{{ $student->first_name ?? '' }} --- {{ $student->admissionNo ?? '' }}</h5>
                            </div>
                            <div class="row mt-3">
                            <div class="col-md-12">
                                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline">
                                    <thead>
                                        <tr role="row">
                                            <th>{{ __('Invoice No.') }}</th>
                                            <th>{{ __('Study Time') }}</th>
                                            <th>{{ __('Seat Number') }}</th>
                                            <th>{{ __('Renewal Date') }}</th>
                                            @if($getPermission->edit == 1 || $getPermission->deletes == 1 || $getPermission->download == 1)
                                            <th>{{ __('messages.Action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody id="student_list_show">
                                        @if(!empty($data))
                                        @foreach ($data as $item)
                                        <tr>
                                            <td>{{ $item['invoice_no'] ?? '' }}</td>
                                            <td>{{ $item['study_time'] ?? '' }} <br> {{ (int)$item->study_hour ?? '' }} Hours</td>
                                            <td><button class="btn btn-success w-50 btn-xs" type="button">S-{{ $item['library_cabin'] ?? '' }}</button></td>
                                            <td>{{ date('d-M-Y', strtotime($item['renew_date'])) }}</td>
                                            @if($getPermission->edit == 1 || $getPermission->deletes == 1 || $getPermission->download == 1)
                                            <td>
                                                @if($getPermission->edit == 1)
                                                <a href="{{ url('change_user_plan') }}/{{ $item['id'] ?? '' }}/{{ $item->admission_id ?? '' }}">
                                                    <button class="btn btn-info btn-xs">Change Plan</button>
                                                </a>
                                                @endif
                                                @if($getPermission->deletes == 1)
                                                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#Modal_id" title="Delete Floor">
                                                    <button type="button" data-admission_id="{{ $item->admission_id }}" data-id='{{ $item['id'] ?? '' }}' class="deleteData btn btn-danger btn-xs">Delete Plan</button>
                                                </a>
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach

                                        @else
                                        <tr>
                                            <td colspan="12" class="text-center">{{ __('library.No Book Found') }} !</td>
                                        </tr>
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
  
<div class="modal" id="Modal_id">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #555b5beb;">

            <div class="modal-header">
                <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>

            <form action="{{ url('student_plan_delete') }}" method="post">
                @csrf
                <div class="modal-body">
                    <input type=hidden id="delete_id" name="delete_id">
                    <input type=hidden id="admission_id" name="admission_id">
                    <h5 class="text-white">{{ __('common.Are you sure you want to delete') }} ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('common.Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(".deleteData").click(function() {
            var delete_id = $(this).data('id');
            var admission_id = $(this).data('admission_id');
            $("#delete_id").val(delete_id);
            $("#admission_id").val(admission_id);
        });
    });
</script>


@endsection