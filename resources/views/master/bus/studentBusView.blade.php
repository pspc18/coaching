@php
  $classType = Helper::classType();
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
                    <h3 class="card-title"><i class="fa fa-bus"></i> &nbsp; {{ __('bus.Student Bus assign view') }}</h3>
                    <div class="card-tools">
                        @if(Session::get('role_id') !== 3)
                    <a href="{{url('assignBusRoute')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(20)->add ? '' : 'd-none' }}" ><i class="fa fa-plus"></i>{{ __('messages.Add') }}  </a>
                    <a href="{{url('busDashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}</a>
                    @endif
                    </div>
                    
                    </div>                          
                        <div class="card-body">
                            
                 
                            <table id="example1" class="table table-bordered table-striped  dataTable dtr-inline w-100">
                                <thead class="bg-primary">
                                    <tr role="row">
                                      <th>{{ __('messages.Sr.No.') }}</th>
                                      <th>{{ __('bus.Bus') }}</th>
                                      <th>{{ __('bus.Bus No.') }}</th>
                                      <th>{{ __('bus.Bus Driver') }}</th>
                                      <th>{{ __('bus.Driver Mobile No') }}</th>
                                      <!--<th>Student Name</th>-->
                                      <th>{{ __('messages.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($data))
                                    @php
                                       $i=1;
                                       //dd($data);
                                    @endphp
                                    @foreach ($data  as $item)
                                    <tr>
                                        <input  type="hidden" class="form-control" id="name_{{$item->id}}" value="{{ $item['busId']['name']  }}">
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $item['busId']['name'] ?? '' }}</td>
                                        <td>{{ $item['busId']['bus_no'] ?? '' }}</td>
                                        <td>{{ $item['busId']['bus_owmer_name'] ?? '' }}</td>
                                        <td>{{ $item['busId']['owner_no'] ?? '' }}</td>
                                        <td class="d-flex">
                                        @if(Session::get('role_id') == 3)
                                            <a href="javascript:;" data-id="{{ $item['id'] ?? '' }}" data-bus_name="{{ $item['busId']['name'] ?? '' }}" data-company="{{ $item['busId']['bus_company'] ?? '' }}" data-modal="{{ $item['busId']['bus_model_no'] ?? '' }}"
                                            data-bus_no="{{ $item['busId']['bus_no'] ?? '' }}" data-driver_name="{{ $item['busId']['bus_owmer_name'] ?? '' }}" data-driver_mobile="{{ $item['busId']['owner_no'] ?? '' }}" data-capacity="{{ $item['busId']['capacity_bus'] ?? '' }}"
                                            data-bs-toggle="modal" data-bs-target="#myModal"  class="btn btn-primary btn-xs viewBus {{ Helper::permissioncheck(20)->view ? '' : 'd-none' }}" title="View Bus Detail"><i class="fa fa-navicon"></i></a>
                                        @else
                                            <a href="javascript:;" data-id="{{ $item['id'] ?? '' }}" data-bs-toggle="modal" data-bs-target="#myModal"  class="btn btn-primary btn-xs viewBus mr-3 {{ Helper::permissioncheck(20)->delete ? '' : 'd-none' }}" title="View Bus Detail"><i class="fa fa-navicon"></i></a>
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
    </section>
</div>

<!--<script>
  $('.viewBus').click(function() {
  var bus_name = $(this).data('bus_name');
  var bus_owmer_name = $(this).data('id');


  $('#bus_name').html(bus_name);
  $('#bus_owmer_name').html(bus_owmer_name);

  } );
</script>-->
<script>
function deleteData(){
  var delete_id = $(".deleteData").data('id'); 
  $('#delete_id').val(delete_id); 
}
 </script>
 
<div class="modal fade mt-5" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content">
            <div class="modal-header">
                
                <h4>{{ __('bus.Bus Details') }}<span class="FeesGroup_name"></span> <span class="FeesType_name"></span></h4>   
                      <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">x</button>
            </div>
    
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        
                        <p>{{ __('bus.Bus Name') }}: <b id="bus_name"> {{ $item['busId']['name'] ?? '' }}</b></p>
                        <p>{{ __('bus.Bus Model No.') }}:<b> {{ $item['busId']['bus_model_no'] ?? '' }}</b></p>
                        <p>{{ __('bus.Driver Name') }}: <b id="bus_owmer_name"> {{ $item['busId']['bus_owmer_name'] ?? '' }}</b></p>
                        <p>{{ __('bus.Bus Capacity') }}:<b> {{ $item['busId']['capacity_bus'] ?? '' }}</b></p>
                    </div>
                    <div class="col-md-6">
                        <p>{{ __('bus.Bus Company') }}:<b> {{ $item['busId']['bus_company'] ?? '' }}</b></p>
                        <p>{{ __('bus.Bus No.') }}:<b> {{ $item['busId']['bus_no'] ?? '' }}</b></p>
                        <p>{{ __('bus.Driver Mobile No') }}:<b> {{ $item['busId']['owner_no'] ?? '' }}</b></p>
                    </div>                
                </div>
                <div class="row">
                            <table id="example1" class="table table-bordered table-striped  dataTable dtr-inline w-100">
                                <thead>
                                    <tr role="row">
                                      <th>{{ __('messages.Sr.No.') }}</th>
                                      <th>{{ __('messages.Ad. No.') }}</th>
                                      <th>{{ __('Student Name') }}</th>
                                      <th>Class</th>
                                      <th>{{ __('messages.Mobile') }}</th>
                          @if(Session::get('role_id') == 1)
                                      <th>{{ __('messages.Action') }}</th>
                             @endif
                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                    <form action="{{ url('busLateMessage') }}" method="post">
                                        @csrf
                                            @if(!empty($stulistbus))
                                            @php
                                               $i=1;
                                            @endphp
                                            @foreach ($stulistbus  as $item)
                                            
                                            <tr>
                                                <input type="hidden" name="admission_id[]" id="admission_id" value="{{ $item->admission_id ?? '' }}">
                                                <input  type="hidden" class="form-control" id="name_{{$item->id}}" value="{{ $item['busId']['name']  }}">
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $item['admissionNo'] ?? '' }}</td>
                                                <td>{{ $item['first_name'] ?? '' }}</td>
                                                <td>{{ $item['class_name'] ?? '' }}</td>
                                                <td>{{ $item['studentId']['mobile'] ?? '' }}</td>
                                               @if(Session::get('role_id') == 1)
                                                <td>
                                                    <a href="{{ url('busAssignEdit') }}/{{$item['id'] ?? '' }}"class=" btn btn-primary btn-xs ml-3" title="Edit Assigned Bus"><i class="fa fa-edit"></i></a>
                                                    <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" onClick="deleteData()" class="deleteData btn btn-danger  btn-xs ml-3" title="Delete Assigned Bus"><i class="fa fa-trash"></i></a>      
                                                </td>
                                                @endif
                                            </tr>
                                            @endforeach
                                            @endif
                                            
                                            
                                                <div class="col-md-12 text-center">
                                                    <button type="submit" class="btn btn-success btn-xs "><i class="fa fa-whatsapp"></i> Send Bus Late Message</button>
                                                </div>

                                    </form>
                                </tbody>
                            </table>                    
                </div>
            </div>
        
            <div class="modal-footer">
              <button type="button" class="btn btn-primary waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
        

  
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('messages.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="bus_assign_delete" method="post">
              	 @csrf
      <div class="modal-body">

              <input type=hidden id="delete_id" name=delete_id>
              <h5 class="text-white">{{ __('messages.Are you sure you want to delete') }}  ?</h5>
           
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