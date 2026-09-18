@php
$classType = Helper::classType();
$getState = Helper::getState();
$getcitie = Helper::getCity();
$getPermission = Helper::getPermission();
$getCountry = Helper::getCountry();
$getMonths = Helper::getMonth();
@endphp
@extends('layout.app')
@section('content')

<div class="content-wrapper">

  <section class="content pt-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12 col-md-12">
          <div class="card card-outline card-orange">
            <div class="card-header bg-primary flex_items_toggel">
              <h3 class="card-title"><i class="fa fa-calendar"></i> &nbsp;{{ __('View Weekend Calendar') }}</h3>
              <div class="card-tools">
                <a href="{{url('add_weekend')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(9)->add ? '' : 'd-none' }}"><i class="fa fa-plus"></i><span class="Display_none_mobile">{{ __('common.Add') }} </span></a>
                <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i><span class="Display_none_mobile">{{ __('common.Back') }} </span></a>
              </div>

            </div>


           <form id="quickForm" action="{{ url('view_weekend') }}" method="post">
    @csrf
    <div class="row m-2">
        <div class="col-md-2">
            <div class="form-group">
                <label>{{ __('Month Type') }}</label>
                <select class="form-control select2" id="month_id" name="month_id">
                    <option value="">{{ __('All') }}</option>
                    @if(!empty($getMonths))
                        @foreach($getMonths as $month)
                            <option value="{{ $month->id }}" 
                                {{ $month->id == ($search['month_id'] ?? '') ? 'selected' : '' }}>
                                {{ $month->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <label class="text-white">{{ __('common.Search') }}</label>
                <button type="submit" class="btn btn-primary">{{ __('common.Search') }}</button>
            </div>
        </div>
    </div>
</form>

<div class="row m-2">
    <div class="col-12" style="overflow-x:scroll;">
        <table class="table table-bordered table-striped dataTable dtr-inline">
            <thead class="bg-primary">
                <tr>
                    <th>{{ __('common.SR.NO') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Day') }}</th>
                    <th>{{ __('Month') }}</th>
                    <th>{{ __('Event/Schedule') }}</th>
                    <th>{{ __('Attendance Status') }}</th>
                    <th>{{ __('Publish') }}</th>
                    <th>{{ __('common.Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @if($data->count())
                    @foreach($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                            <td>{{ $item->day ?? '-' }}</td>
                            <td>{{ $item->month_name ?? '' }}</td>
                            <td>{{ $item->event_schedule ?? '-' }}</td>
                            <td>{{ $item->attendance_status ?? '-' }}</td>
                            <td>  
                               <label class="switch1">
                                    <input type="checkbox" class="toggle-status" 
                                           data-id="{{ $item->id }}" 
                                            {{ $item['publish'] == 1 ? 'checked' : '' }}>
                                    <span class="slider1">
                                        <span class="on">✔ </span>
                                        <span class="off">✖ </span>
                                    </span>
                                </label>

                        </td>
                            <td>
                                @if($getPermission->deletes == 1)
                                    @php
                                        $eventDate = \Carbon\Carbon::parse($item->date);
                                    @endphp
                                    @if($eventDate->isFuture())
                                        <a href="{{ url('edit_weekend', $item->id) }}" target="_blank">
                                            <i class="fa fa-edit text-primary"></i>
                                        </a>
                                        <a href="javascript:;" data-id="{{ $item->id }}" 
                                           data-bs-toggle="modal" data-bs-target="#Modal_id" 
                                           class="deleteData" title="Delete">
                                           <i class="fa fa-trash-o text-danger"></i>
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="12" class="text-center pt-2">
                            @if(!empty($search['month_id']))
                                <a href="{{ url('print_weekend', $search['month_id']) }}" 
                                   target="_blank" class="btn btn-primary">
                                    <i class="fa fa-print"></i>
                                </a>
                            @else
                                <button class="btn btn-primary" onclick="alert('Choose month')">
                                    <i class="fa fa-print"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @else
                    <tr>
                        <td colspan="12" class="text-center pt-2">No Data Found</td>
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
            </section>
    </div>

            <!-- The Modal -->
            <div class="modal" id="Modal_id">
              <div class="modal-dialog">
                <div class="modal-content" style="background: #555b5beb;">

                  <!-- Modal Header -->
                  <div class="modal-header">
                    <h4 class="modal-title text-white">Delete Confirmation</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                  </div>

                  <!-- Modal body -->
                  <form action="{{ url('weekend_delete') }}" method="post">
                    @csrf
                    <div class="modal-body">

                      <input type="hidden" id="delete_id" name=delete_id>
                      <h5 class="text-white">Are you sure you want to delete ?</h5>

                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-danger waves-effect waves-light">Delete</button>
                    </div>
                  </form>

                </div>
              </div>
            </div>


        
   
        
<!--<div id="profileImgModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">

    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <img id="profileImg" src="" width="100%" height="100%">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>        
-->        
            <script>
            $(document).on('change', '.toggle-status', function(){
    let checkbox = $(this);
    let weekendcalendar = checkbox.data('id');
    let status = checkbox.is(':checked') ? '1' : '0';
    
    $.ajax({
        url: "{{ url('Status_weekend') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            weekendcalendar_id: weekendcalendar,
            status: status
        },
        success: function(res){
            if(res.success){
                             toastr.success('Status Updated Successfully!');

            } else {
                // error aaya toh rollback
                checkbox.prop('checked', !checkbox.prop('checked'));
            }
        }
    });
});


              $('.deleteData').click(function() {
                var delete_id = $(this).data('id');

                $('#delete_id').val(delete_id);
              });
              
           /*     $('.profileImg').click(function(){
                    var profileImgUrl = $(this).data('img');
                    if(profileImgUrl != ''){
                        $('#profileImgModal').modal('toggle');
                        $('#profileImg').attr('src',profileImgUrl);
                    }
                });*/
                
                
                

             
    	function SearchValue() {
		var basurl = "{{ url('/') }}";
		var month_type = $('#month_type :selected').val();
		if (class_search_id > 0 ) {
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
				},
				type: 'post',
				url: basurl + '/weekendSearch',
				data: {
					month_type: month_type
				},
				//dataType: 'json',
				success: function(data) {
                    $('.student_list_show').addClass('fadeinout');
					$('.student_list_show').html(data);
                    setTimeout(function() {
                         $('.student_list_show').removeClass('fadeinout');
                    }, 2000);
				}
			});
		} else {
			toastr.error('Please put a value in one column !');
		}
	};          
            </script>
          
 <style>
    .switch1 {
  position: relative;
  display: inline-block;
   width: 71px;
  height: 19px;
}

.switch1 input {
  display: none;
}

.slider1 {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: -6px;
  background-color: #dc3545;
  transition: .4s;
  border-radius: 30px;
  text-align: center;
  font-size: 8px;
  line-height: 22px;
  color: white;
}

.slider1:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 2px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

.off {
  position: absolute;
  width: 100%;
  font-size: 9px;
  color: #ff0b0b;
  font-weight: bold;
}
.on {
  position: absolute;
  width: 100%;
  font-size: 9px;
  color: #28a745;
  font-weight: bold;
}
.on {
  left: 24px;
  display: none;
}

.off {
  right: 24px;

}

input:checked + .slider1 {
  background-color: #28a745; /* हरा (On) */
}

input:checked + .slider1:before {
  transform: translateX(47px);
}

input:checked + .slider1 .on {
  display: block;
}

input:checked + .slider1 .off {
  display: none;
}

</style>           
            @endsection