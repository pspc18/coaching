@php
$classType = Helper::classType();
$getgenders = Helper::getgender();
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
                            <h3 class="card-title"><i class="fa fa-bus"></i> &nbsp;{{ __('bus.Assign Bus to Students') }} </h3>
                            <div class="card-tools">
                                <a href="{{url('assignBusRoute')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a>
                            </div>

                        </div>
                        <form id="quickForm" action="#" method="post">
                            @csrf
                            <div class="row m-2">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ __('messages.Class') }}</label>
                                        <select class="form-control" id="class_type_id" name="class_type_id">
                                            <option value="">{{ __('messages.Select') }}</option>
                                            @if(!empty($classType))
                                            @foreach($classType as $type)
                                            <option value="{{ $type->id ?? ''  }}">{{ $type->name ?? ''  }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label>{{ __('bus.Search By Name') }}</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('bus.Search By Name') }}">
                                    </div>
                                </div>
                                <div class="col-md-1 ">
                                    <label for="">&nbsp;</label><br>
                                    <button type="button" class="btn btn-primary" onclick="SearchValue()">{{ __('messages.Search') }}</button>
                                </div>
                            </div>
                        </form>


                        <form id="quickForm" action="{{ url('assignBus') }}/{{$dataview['bus_id'] ?? '' }}" method="post" id="assign_form">
                            @csrf
                            <input type="hidden" name="route_id" value="{{ $dataview['route_id'] ?? '' }}">
                            <input type="hidden" name="bus_id" value="{{ $dataview['bus_id'] ?? '' }}">
                            <div class="row m-2">
                                <div class="col-md-4">
                                    <table id="" class="table table-bordered table-striped dataTable dtr-inline ">

                                        <tbody class="">
                                            <tr class="text-center bg-light">
                                                <td>
                                                    <h5 class="text-white">{{ $dataview['busRoute']['name'] ?? '' }}</h5>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            {{ __('bus.Bus Name') }}
                                                        </div>
                                                        <div class="col-md-8">
                                                            <i class="fa fa-bus"></i> &nbsp; {{ $dataview['bus']['name'] ?? '' }}
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            {{ __('bus.Bus No.') }}
                                                        </div>
                                                        <div class="col-md-8">
                                                            <i class="fa fa-clipboard"></i> &nbsp; {{ $dataview['bus']['bus_no'] ?? '' }}
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            {{ __('Bus Owner') }}
                                                        </div>
                                                        <div class="col-md-8">
                                                            <i class="fa fa-address-book-o"></i> &nbsp; {{ $dataview['bus']['bus_owmer_name'] ?? '' }}
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            {{ __('bus.Driver No.') }}
                                                        </div>
                                                        <div class="col-md-8">
                                                            <i class="fa fa-phone"></i> &nbsp; {{ $dataview['bus']['owner_no'] ?? '' }}
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-md-8">
                                    <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                                        <thead class="bg-primary">
                                            <tr role="row">
                                                <th><input type="checkbox" id="select_all" class="checkbox"> &nbsp;{{ __('messages.All') }} </th>
                                                <th>{{ __('messages.Adm.No.') }}</th>
                                                <th>{{ __('messages.Name') }}</th>
                                                <!-- <th>{{ __('messages.Class') }}</th> -->
                                                <th>{{ __('messages.F Name') }}</th>
                                                <!-- <th>{{ __('messages.Gender') }}</th> -->
                                                <th>{{ __('messages.Mobile') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="student_list_show">


                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-4">&nbsp;</div>
                                <div class="col-md-8 text-center"><button type="submit" class="btn btn-primary">{{ __('messages.submit') }}</button></div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>





<script>
    function SearchValue() {
        var name = $('#name').val();
        var basurl = "{{ url('/') }}";
        var class_type_id = $('#class_type_id :selected').val();
        if (class_type_id > 0 || name != '') {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: basurl + '/assign_bus_search_data',
                data: {
                    class_type_id: class_type_id,
                    name: name
                },
                //dataType: 'json',
                success: function(data) {


                    $('.student_list_show').html(data);

                }
            });
        } else {
            alert('Please put a value in minimum one column !');
        }
    };
</script>

<script>
    //select all checkboxes
    $("#select_all").change(function() {
        $(".checkbox").prop('checked', $(this).prop("checked"));
    });

    //".checkbox" change 
    $('.checkbox').change(function() {
        if (false == $(this).prop("checked")) {
            $("#select_all").prop('checked', false);
        }
        if ($('.checkbox:checked').length == $('.checkbox').length) {
            $("#select_all").prop('checked', true);
        }
    });
</script>
@endsection