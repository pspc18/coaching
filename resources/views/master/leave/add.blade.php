@php
  $feesType = Helper::feesType();

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
							<h3 class="card-title"><i class="fa fa-id-badge"></i> &nbsp;{{ __('master.Leave Applications') }}</h3>
							<div class="card-tools"> <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a> </div>
						</div>
    

    <div class="row">
        <div class="col-md-12 pl-2">
                <div class="row m-2">
                    <div class="col-md-12">
                            <h5>{{ __('master.Applied leave list') }}</h5><hr>
                	</div>
                    <div class="col-md-12">
                       <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                          <thead class="bg-primary">
                          <tr role="row">
                              <th>{{ __('messages.Sr.No.') }}</th>
                             
                                <th>{{ __('master.Status') }}</th>
                             
                              <th>{{ __('Student Name') }}</th>
                              <th>{{ __('messages.Subject') }}</th>
                              <th>{{ __('Date') }}</th>
                              <th>{{ __('master.Reason') }}</th>
                              <!--<th>Action</th>-->
                              
                              
                              
                          </thead>
                          <tbody id="">
                           @if(!empty($dataview))
                                @php
                                        $i=1
                                    @endphp
                                    @foreach ($dataview  as $item)
                                    @if($item['status']=="2")
                                    @php
                                        $width="w-50";
                                        $bg="bg-yellow";
                                    @endphp
                                    @else
                                    @php
                                     $bg="bg-white";
                                        $width="w-100";
                                    @endphp
                                    @endif
                                 
                                <tr class="{{$bg}}">

                                        <td>{{ $i++ }}</td>
                                      
                                        <td >
                                            <a data-id="{{ $item['id'] ?? '' }}" style="{{  $item['status'] == 1 ? 'display:none'   : ''  }}" data-status="1" class="btn btn-xs btn-success reminder_status {{$width}}" data-text="Activate"> &nbsp; {{ __('messages.Approve') }} &nbsp;</a>
                                            <a data-id="{{ $item['id'] ?? '' }}" style="{{  $item['status'] == 0 ? 'display:none'   : ''  }}" data-status="0" class="btn btn-xs btn-danger reminder_status {{$width}}" data-text="Deactivate">{{ __('messages.Deny') }}</a>                                                               
                                        </td>
                                        
                                        <td>{{ $item['first_name'] ?? ''}} {{ $item['last_name'] ?? ''}}</td>
                                        <td>{{ $item['subject'] ?? '' }}</td>
                                        <td>{{date('d-m-Y', strtotime($item['from_date'])) ?? '' }} / {{date('d-m-Y', strtotime($item['to_date'])) ?? '' }}</td>
                                        
                                        <td>{{ $item['reason'] ?? '' }}</td>
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
</div>
</div>
</section>
</div>

<script>
    $(document).on('click', ".reminder_status", function () {
var id = $(this).data("id");
 var basurl = "{{ url('/') }}";
   var status = $(this).data("status");
    if(confirm('Are you sure ?')){
        $.ajax({
            url: basurl + '/leaveStatus',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { status: status, id: id },
            success: function (response) {
                // alert(JSON.stringify(response));
                if (response == 0) {
                  location.reload(true);
                }else if (response == 1) {
                  location.reload(true);
                }
                else {
                    alert("Internal Servasaser Error");
                }
            },
        });
    }

});

</script>


<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
</script>

<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <div class="modal-header">
        <h4 class="modal-title text-white">Delete Confirmation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('leave_delete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              <input type=hidden id="delete_id" name=delete_id >
              <h5 class="text-white">Are you sure you want to delete  ?</h5>
      </div>
      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">Delete</button>
         </div>
       </form>
    </div>
  </div>
</div>

<style>
    .w-50 {
  width: 47% !important;
}
</style>
@endsection      