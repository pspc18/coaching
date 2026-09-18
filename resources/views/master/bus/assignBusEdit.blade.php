@php
$busRoute = Helper::busRoute();
$bus = Helper::bus();
@endphp

@extends('layout.app')
@section('content')


<div class="content-wrapper">

  <section class="content pt-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card card-outline card-orange">
            <div class="card-header bg-primary">
              <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('bus.Edit Assigned Bus') }} </h3>
              <div class="card-tools">
                <a href="{{url('assignBusRoute')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>{{ __('View') }} </a>
                <a href="{{url('busDashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('Back') }} </a>
              </div>

            </div>
            <div class="card-body">
              <form id="quickForm" action="{{ url('busAssignEdit') }}/{{$data['id']}}" method="post">
                @csrf
                <div class="row">

                  <div class="col-md-3">
                    <div class="form-group">
                      <label style="color:red;">{{ __('bus.Bus Route') }}*</label>
                      <select class="form-control @error('route_id') is-invalid @enderror" id="route_id" name="route_id" value="{{old('route_id')}}">
                        <option value="">Select</option>
                        @if(!empty($busRoute))
                        @foreach($busRoute as $type)
                        <option value="{{ $type->id ?? ''  }}" {{ ( $type->id == $data['route_id'] ? 'selected' : '' ) }}>{{ $type->name ?? ''  }}</option>
                        @endforeach
                        @endif
                      </select>
                      @error('route_id')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label style="color:red;">{{ __('bus.Bus') }}*</label>
                      <select class="form-control @error('bus_id') is-invalid @enderror bus_id" id="bus_id" name="bus_id">
                        <option value="">Select</option>
                        @if(!empty($dataview))
                        @foreach($dataview as $type)
                        <option value="{{ $type['bus']['id'] }}" {{ ( $type['bus']['id'] == $data['bus_id'] ? 'selected' : '' ) }}>{{ $type['bus']['name'] ?? ''  }}</option>
                        @endforeach
                        @endif
                      </select>
                      @error('bus_id')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                      @enderror
                    </div>
                  </div>

                </div>
                <div class="row m-2">
                  <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary ">{{ __('messages.Update') }}</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>


<script>
  $('#route_id').on('change', function(e) {
    var basurl = "{{ url('/') }}";
    var route_id = $(this).val();
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
      },
      url: basurl + '/busData/' + route_id,
      success: function(data) {
        if (data != '') {
          $(".bus_id").html(data);
        } else {
          $(".bus_id").html(data);
          alert('Bus Not Found');
        }
      }
    });
  });
</script>
@endsection