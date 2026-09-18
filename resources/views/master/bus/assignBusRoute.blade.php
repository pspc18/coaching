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

                <div class="col-md-4 pr-0">
                    <div class="card card-outline card-orange mr-1">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-road"></i> &nbsp;{{ __('bus.Add Bus to Route') }}</h3>
                            <div class="card-tools">

                            </div>

                        </div>
                        <form id="form-submit" action="{{ url('assignBusRoute') }}" method="post">
                            @csrf
                            <div class="row m-2">
                                <div class="col-md-12">
                                    <label style="color:red;">{{ __('bus.Route') }}*</label>
                                    <select class="form-control @error('route_id') is-invalid @enderror select2" id="route_id" name="route_id">
                                        <option value="">{{ __('messages.Select') }}</option>
                                        @if(!empty($busRoute))
                                        @foreach($busRoute as $type)
                                        <option value="{{ $type->id ?? ''  }}">{{ $type->name ?? ''  }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    
                                </div>
                                <div class="col-md-12">
                                    <label style="color:red;">{{ __('bus.Bus') }}*</label>
                                    <select class="form-control @error('bus_id') is-invalid @enderror select2" id="bus_id" name="bus_id">
                                        <option value="">{{ __('messages.Select') }}</option>
                                        @if(!empty($bus))
                                        @foreach($bus as $type)
                                        <option value="{{ $type->id ?? ''  }}">{{ $type->name ?? ''  }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                   
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

                <div class="col-md-8 pl-0">
                    <div class="card card-outline card-orange ml-1">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-road"></i> &nbsp;{{ __('bus.Bus & Route List') }}</h3>
                            <div class="card-tools">
                                <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>-->
                                <a href="{{url('busDashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}</a>
                            </div>
                        </div>
                        <div class="row m-2">
                            <div class="col-md-12">
                                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                    <thead class="bg-primary">
                                        <tr role="row">
                                            <th>{{ __('messages.Sr.No.') }}</th>
                                            <th>{{ __('bus.Bus Route') }}</th>
                                            <th>{{ __('bus.Bus Name') }}</th>

                                    </thead>
                                    <tbody id="">

                                        @if(!empty($dataview))
                                        @php
                                        $i=1
                                        @endphp
                                        @foreach ($dataview as $item)
                                        @php
                                        $busRouteAssign = Helper::busRouteAssign($item['route_id']);
                                        @endphp
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $item['busRoute']['name'] ?? '' }}</td>
                                            <td>
                                                @if(!empty($busRouteAssign))
                                                @foreach ($busRouteAssign as $type)

                                                <i class="fa fa-bus"></i>
                                                {{ $type['bus']['name'] ?? '' }}&nbsp; &nbsp;
                                                <a href="{{ url('assignBusRouteEdit') }}/{{$type['id'] ?? '' }}"><i class="fa fa-pencil text-primary" title="Edit"></i></a> &nbsp;
                                                <a href="javascript:;" data-id='{{$type->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData"><i class="fa fa-remove text-danger" title="Delete"></i></a>
                                                <a href="{{ url('assignBus') }}/{{$type['id'] ?? '' }}" title="Assign Bus to Student" class="text-success"><i class="fa fa-tag pl-2"></i> Assign Bus</a>
                                                <br>
                                                <hr class="m-0">

                                                @endforeach
                                                @endif
                                            </td>
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

            <form action="{{ url('assignBusRouteDelete') }}" method="post">
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