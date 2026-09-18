@php
$getHostel = Helper::getHostel();
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
              <h3 class="card-title"><i class="fa fa-trello"></i> &nbsp; {{ __('hostel.View Room') }}</h3>
              <div class="card-tools">
                <a href="{{url('room_add')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i>{{ __('common.Add') }}</a>
                <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
              </div>
            </div>
            <section class="content">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-12">
                    <div class="row m-2">
                      <div class="col-md-12">
                        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                          <thead class="bg-primary">
                            <tr role="row">
                              <th>{{ __('common.SR.NO') }}</th>
                              <th>{{ __('hostel.Select Hostel') }} </th>
                              <th>{{ __('hostel.Select Building') }} </th>
                              <th>{{ __('hostel.Select Floor') }}</th>
                              <th>{{ __('hostel.Room Name/No') }}</th>
                              <th>{{ __('hostel.Edit/Delete') }}</th>

                            </tr>
                          </thead>
                          <tbody id="student_list_show">

                            @if(!empty($data))
                            @php
                            $i=1;
                            @endphp

                            @foreach ($data as $item)
                            <tr>

                              <td>{{ $i++ }}</td>
                              <td>{{ $item['hostel_name']  }}</td>
                              <td>{{ $item['bildng_name']  }}</td>
                              <td>{{ $item['floor_name']  }}</td>
                              <td>{{ $item['name']  }}
                                @if($item->room_category == "1")
                                <span class="badge badge-success">AC</span>
                                @else
                                <span class="btn btn-primary">Non AC</span>
                                @endif
                              </td>


                              <td>
                                <a href="{{url('room_edit',$item->id)}}" class="btn btn-primary  btn-xs ml-3" title="Edit Student Registration"><i class="fa fa-edit"></i></a>

                                <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-primary  btn-xs ml-3" title="Delete Student Registration"><i class="fa fa-trash-o"></i></a>

                              </td>
                            </tr>
                            @endforeach

                            @else
                            <tr>
                              <td colspan="12" class="text-center">{{ __('hostel.No Student Found') }} !</td>
                            </tr>
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

                  </div>
                </div>
              </div>
            </section>
              </div>


            <div class="modal" id="Modal_id">
              <div class="modal-dialog">
                <div class="modal-content" style="background: #555b5beb;">

                  <!-- Modal Header -->
                  <div class="modal-header">
                    <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                  </div>

                  <!-- Modal body -->
                  <form action="{{ url('room_delete') }}" method="post">
                    @csrf
                    <div class="modal-body">



                      <input type=hidden id="delete_id" name=delete_id>
                      <h5 class="text-white">{{ __('common.Are you sure you want to delete') }}?</h5>

                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
                      <button type="submit" class="btn btn-primary waves-effect waves-light">{{ __('common.Delete') }}</button>
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