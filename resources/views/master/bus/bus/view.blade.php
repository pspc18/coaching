@extends('layout.app')
@section('content')
<div class="content-wrapper">

  <section class="content pt-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">

          <div class="card card-outline card-orange">
            <div class="card-header bg-primary">
              <h3 class="card-title"><i class="fa fa-bus"></i> &nbsp; {{ __('bus.Bus List') }}</h3>
              <div class="card-tools">
                <a href="{{url('busAdd')}}" class="btn btn-primary btn-sm {{ Helper::permissioncheck(20)->add ? '' : 'd-none' }}"><i class="fa fa-plus"></i> {{ __('messages.Add') }}</a>
                <a href="{{url('busDashboard')}}" class="btn btn-primary btn-xs ms-2"><i class="fa fa-arrow-left"></i> {{ __('messages.Back') }}</a>
              </div>
            </div>

            <!-- Main content -->
            <section class="content">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-12">
                    <div class="card-body">

                      <!-- ✅ Responsive wrapper for table -->
                      <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline">
                          <thead class="bg-primary">
                            <tr role="row">
                              <th>{{ __('messages.Sr.No.') }}</th>
                              <th>{{ __('bus.Bus Name') }}</th>
                              <th>{{ __('bus.Bus No.') }}</th>
                              <th>{{ __('bus.Bus Owner Name') }}</th>
                              <th>{{ __('bus.Bus Owner Contact No.') }}</th>
                              <th>{{ __('bus.Bus Rigistration No.') }}</th>
                              <th>{{ __('bus.Bus Photo') }}</th>
                              <th>{{ __('messages.Action') }}</th>
                            </tr>
                          </thead>
                          <tbody>
                            @if(!empty($data))
                            @php $i=1 @endphp
                            @foreach ($data as $item)
                            <tr>
                              <td>{{ $i++ }}</td>
                              <td>{{ $item['name'] }}</td>
                              <td>{{ $item['bus_no'] }}</td>
                              <td>{{ $item['bus_owmer_name'] }}</td>
                              <td>{{ $item['owner_no'] }}</td>
                              <td>{{ $item['bus_rigistration_no'] }}</td>
                              <td>
                                <img src="{{ env('IMAGE_SHOW_PATH').'/bus_photo/'.$item['bus_photo'] }}" width="120px" height="60px">
                              </td>
                              <td>
                                <a href="{{url('busEdit',$item->id)}}" class="btn btn-primary btn-xs {{ Helper::permissioncheck(20)->edit ? '' : 'd-none' }}"><i class="fa fa-edit"></i></a>
                                <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger btn-xs ml-1 {{ Helper::permissioncheck(20)->delete ? '' : 'd-none' }}"><i class="fa fa-trash"></i></a>
                              </td>
                            </tr>
                            @endforeach
                            @endif
                          </tbody>
                        </table>
                      </div>
                      <!-- ✅ End Responsive wrapper -->

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
      <form action="{{ url('busDelete') }}" method="post">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="delete_id" name="delete_id">
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
