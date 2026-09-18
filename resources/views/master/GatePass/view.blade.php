@php
$classType = Helper::classType();
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
                            <h3 class="card-title"><i class="fa fa-user-secret"></i> &nbsp;{{ __('master.View Gate Pass') }} </h3>
                            <div class="card-tools">
                                <a href="{{url('gate_pass_add')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(9)->add ? '' : 'd-none' }}"><i class="fa fa-plus"></i>{{ __('common.Add') }} </a>
                                <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>

                            </div>
                        </div>
            <form id="quickForm" action="{{ url('gate_pass_view') }}" method="post">
              @csrf
              <div class="row m-2">

                <div class="col-md-2">
                  <div class="form-group">
                    <label for="State" class="required">{{ __('master.Admission No.') }}</label>
                     <input type="text" class="form-control" id="admissionNo" name="admissionNo" placeholder="{{ __('master.Admission No.') }}" value="{{ $search['admissionNo'] ?? '' }}">
                  </div>
                </div>
             
                <div class="col-md-2">
                  <div class="form-group">
                    <label>{{ __('common.Class') }}</label>
                    <select class="form-control select2" id="class_type_id" name="class_type_id">
                      <option value="">{{ __('common.Select') }}</option>
                      @if(!empty($classType))
                      @foreach($classType as $type)
                      <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['class_type_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                </div>
              
              
                <div class="col-md-3">
                  <div class="form-group">
                    <label>{{ __('common.Search By Keywords') }}</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('common.Ex. Name, Mobile, Email, Aadhaar etc.') }}" value="{{ $search['name'] ?? '' }}">
                  </div>
                </div>
                <div class="col-md-1 ">
                  <label class="text-white">{{ __('common.Search') }}</label>
                  <button type="submit" class="btn btn-primary">{{ __('common.Search') }}</button>
                </div>

              </div>
            </form>

                        <div class="row mb-2 m-2">
                            <div class="col-md-12">
                                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                    <thead class="bg-primary">
                                        <tr role="row">
                                            <th>{{ __('common.SR.NO') }}</th>
                                            <th>{{ __('master.Student Name') }}</th>
                                            <th>{{ __('Class') }}</th>
                                            <th>{{ __('common.Fathers Name') }}</th>
                                            <th>{{ __('common.Fathers Mobile') }}</th>
                                            <th>{{ __('master.Reciver  Name') }}</th>
                                            <th>{{ __('master.Reciver Mobile') }}</th>
                                            <th>{{ __('master.Relation') }}</th>
                                            <th>{{ __('common.Date') }}</th>

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
                                            <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                                            <td>{{ $item['class_name'] ?? '' }}</td>
                                            <td>{{ $item['stu_father_name'] ?? '' }}</td>
                                            <td>{{ $item['f_mobile'] ?? '' }}</td>
                                            <td>{{ $item['reciver_name'] ?? '' }}</td>
                                            <td>{{ $item['reciver_mobile'] ?? '' }}</td>
                                            <td>{{ $item['relation'] ?? '' }}</td>
                                            <td>{{ date('d-m-Y', strtotime($item->created_at)) }}</td>

                                            <td>
                                                <a href="{{url('gate_pass_print') }}/{{$item->admissionNo}}" class="btn btn-success  btn-xs ml-3 tooltip1 {{ Helper::permissioncheck(9)->print ? '' : 'd-none' }}" title1="Gate Pass Print" target="_blank"><i class="fa fa-print"></i></a>
                                                <!--<a href="{{url('gate_pass_edit') }}/{{$item->id}}" class="btn btn-primary  btn-xs ml-3" title="Edit Complaint"><i class="fa fa-edit"></i></a>-->
                                                <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3 tooltip1 {{ Helper::permissioncheck(9)->delete ? '' : 'd-none' }}" title1="Delete Book"><i class="fa fa-trash-o"></i></a>
                                            </td>
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
                                    <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                                </div>
                                <form action="{{ url('gate_pass_delete') }}" method="post">
                                    @csrf
                                    <div class="modal-body">
                                        <input type=hidden id="delete_id" name=delete_id>
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
                </div>
            </div>
        </div>
    </section>
</div>
@endsection