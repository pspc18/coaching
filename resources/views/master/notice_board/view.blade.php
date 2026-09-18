@php
$role = Helper::roleType();
$liverole = Session::get('role_id');
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
							<h3 class="card-title"><i class="fa fa-envelope"></i> &nbsp;{{ __('master.View Notices') }}</h3>
							<div class="card-tools"> 
							@if(Session::get('role_id') != 3)
							    <a href="{{url('notice_board/add')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(9)->add ? '' : 'd-none' }}" title="Add "><i class="fa fa-plus"></i>{{ __('common.Add') }}   </a> 
							    <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm" title="Back "><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}   </a> 
							    @else 
							      
                                <a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs "><i class="fa fa-arrow-left"></i> <span class="" >{{ __('Back') }} </span></a>
                                
							@endif
							</div>
						</div>

                        <div class="row m-2">
                            <div class="col-12" id="accordion">
                                    @if(!empty($data))
                                    @php
                                       $i=1
                                    @endphp
                                    @foreach ($data  as $item) 
                                    @php
                                    $roles = $item->role_id;
                                    $checkRole  = explode (",", $roles);
                                    
                                    @endphp
                                     
                                    @if(in_array($liverole, $checkRole))
                                    <div class="card card-warning card-outline">
                                            <a class="d-block w-100"id="_{{ $item['id'] ?? '' }}" data-toggle="collapse" href="#_{{ $item['id'] ?? '' }}">
                    						<div class="card-header">
                    							<h3 class="card-title">{{ $i++ }}. {{ $item['title'] ?? '' }}</h3>
                    							<div class="card-tools"> 
                    							@if(Session::get('role_id') != 3)
                    							    <a href="{{url('notice_board/edit')}}/{{($item->id)}}" class="tooltip1" title1="Edit Notice "><i class="fa fa-pencil"></i></a> &nbsp;
                    							    <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData tooltip1" title1="Delete Notice"><i class="fa fa-remove text-danger"></i></a> 
                    							@endif
                    							</div>
                    						</div>                                                
                                            </a>
                                            <div id="_{{ $item['id'] ?? '' }}" class="collapse" data-parent="#accordion">
    <div class="card-body">
        <div class="row">
            {{-- Right Side: Details First --}}
            <div class="col-md-3">
                <p class="mb-1">
                    <i class="fa fa-calendar text-primary"></i> 
                    <strong>{{ __('master.From Date') }}:</strong> 
                    {{ date('d-m-Y', strtotime($item['from_date'])) ?? '' }}
                </p>
                <p class="mb-1">
                    <i class="fa fa-calendar text-success"></i> 
                    <strong>{{ __('master.To Date') }}:</strong> 
                    {{ date('d-m-Y', strtotime($item['to_date'])) ?? '' }}
                </p>
                <p class="mb-1">
                    <i class="fa fa-user text-info"></i> 
                    <strong>Created By:</strong> Super Admin
                </p>

                <h6 class="mt-3 mb-1">
                    <i class="fa fa-users text-warning"></i> 
                    <strong>Message To:</strong>
                </h6>

                @if(!empty($checkRole))
                    @foreach($checkRole as $role_id)
                        @foreach($role as $roleName)
                            @if($roleName->id == $role_id)
                                <span class="badge bg-secondary mb-1">
                                    <i class="fa fa-user"></i> {{ $roleName->name ?? '-' }}
                                </span><br>
                            @endif
                        @endforeach
                    @endforeach
                @endif
            </div>

            {{-- Left Side: Message (Shown Last) --}}
            <div class="col-md-9 mt-2">
                <div class="alert alert-light border p-3 shadow-sm">
                    {!! html_entity_decode($item['message'] ?? '', ENT_QUOTES, 'UTF-8') !!}
                </div>
            </div>
        </div>
    </div>
</div>

                                        </div>
                                   
                                    @endif
                                    @endforeach
                                     @else
                                       <div class="card card-warning card-outline">
                                           <h4 class="text-center mb-0">No Notice Here !!</h4>
                                        </div>
                                    @endif
                            </div>
                        </div>              
                                
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
			<form action="{{ url('notice_board/delete') }}" method="post"> 
			    @csrf
				<div class="modal-body">
					<input type=hidden id="delete_id" name=delete_id>
					<h5 class="text-white">{{ __('common.Are you sure you want to delete') }}  ?</h5> </div>
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

<script>
   $(window).on("load", function(){
 
  setTimeout(
    function() {
      $( "#_"+data_id ).trigger("click");
     }, 100);
      $('html, body').animate({
            scrollTop: $("#_"+data_id).offset().top
          }, 1500);
 
});
</script>

@endsection