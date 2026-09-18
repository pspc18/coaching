@php
$getLibrary = Helper::getLibrary();
$getPermission = Helper::getPermission();
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
                            <h3 class="card-title"><i class="fa fa-book"></i> &nbsp;{{ __('library Student View') }} </h3>
                            <div class="card-tools">
                                <a href="{{url('library_assign')}}" class="btn btn-primary  btn-sm {{($getPermission->add == 1) ? '' : 'd-none'}}"><i class="fa fa-plus"></i>{{ __('common.Add') }} </a>
                                <a href="{{url('library_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
                            </div>
                        </div>

                       <form id="quickForm" action="{{ url('library_student_view') }}" method="post">
                         @csrf
                        <div class="row m-2">
                            
                           	<div class="col-md-2">
									<label> Filter Users: </label>
								<select class="form-control" id="filter" name="filter">
                                    <option value="">Filter</option>
                                    <option value="active"  {{ ("active" == $search['filter']) ? 'selected' : '' }} >Active Users</option>
                                    <option value="expired" {{ ("expired" == $search['filter']) ? 'selected' : '' }} >Membership Expired Users</option>
                                    <option value="expired_last_15_days" {{ ("expired_last_15_days" == $search['filter']) ? 'selected' : '' }} >Expired Last 15 Days</option>
                                    <option value="expired_yesterday" {{ ("expired_yesterday" == $search['filter']) ? 'selected' : '' }} >Expired Yesterday</option>
                                    <option value="expiring_today"  {{ ("expiring_today" == $search['filter']) ? 'selected' : '' }} >Expiring Today</option>
                                    <option value="expiring_3_days" {{ ("expiring_3_days" == $search['filter']) ? 'selected' : '' }} >Expiring in 3 days</option>
                                    <option value="expiring_7_days" {{ ("expiring_7_days" == $search['filter']) ? 'selected' : '' }} >Expiring in 7 days</option>
                                    <option value="expiring_15_days" {{ ("expiring_15_days" == $search['filter']) ? 'selected' : '' }} >Expiring in 15 days</option>
                                    <option value="new_student_today" {{ ("new_student_today" == $search['filter']) ? 'selected' : '' }} >New Student Today</option>
                                    <option value="new_student_yesterday" {{ ("new_student_yesterday" == $search['filter']) ? 'selected' : '' }} >New Student Yesterday</option>
                                    <option value="new_student_this_month" {{ ("new_student_this_month" == $search['filter']) ? 'selected' : '' }} >New Student This Month</option>
                                    <option value="new_student_last_month" {{ ("new_student_last_month" == $search['filter']) ? 'selected' : '' }} >New Student Last Month</option>
                                </select>
								
								</div>
								
                                <div class="col-md-1 ">
                                  <label class="text-white">{{ __('common.Search') }}</label>
                                  <button type="submit" class="btn btn-primary">{{ __('common.Search') }}</button>
                                </div>
                              </div>
                            </form>


                        <div class="row ">
                            <div class="col-md-12">
                                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline">
                                    <thead>
                                        <tr role="row">
                                            <th>{{ __('Admission No.') }}</th>
                                            <th>{{ __('library.Student Name') }}</th>
                                            <th>{{ __('Mobile') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Admission Date') }}</th>
                                            <th>{{ __('Renew Date') }}</th>
                                            @if($getPermission->edit == 1)
                                            <th>{{ __('library.Status') }}</th>
                                            @endif
                                            @if($getPermission->edit == 1 || $getPermission->deletes == 1 || $getPermission->download == 1)
                                            <th>{{ __('messages.Action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody id="student_list_show">
                                        @if(!empty($data))
                                        @php
                                        $i=1;
                                       
                                        @endphp

                                        @foreach ($data as $item)
                                         
                                        <tr>
                                            <td>{{ $item['admissionNo'] ?? '' }}</td>
                                            <td>{{ $item['first_name'] ?? '' }}</td>
                                            <td>{{ $item['mobile'] ?? '' }}</td>
                                            <td>{{ $item['email'] ?? '' }}</td>
                                            <td>{{ date('d-M-Y', strtotime($item['created_at'])) }}</td>
                                            @php
                                            $plans = \App\Models\library\LibraryPlan::Select('library_plans.*','library_time_slots.study_time')
                                                     ->leftjoin('library_time_slots','library_time_slots.id','library_plans.library_time_slot_id')
                                                     ->where('library_assign_id',$item->id)->where('library_plans.status',0)->get();
                                            
                                            @endphp
                                            <td>
                                                @if(!empty($plans))
                                                   @foreach ($plans as $time)  
                                                    <span class="badge {{ date('Y-m-d') <= $time['renew_date']  ? 'badge-active' : 'badge-expired' }} ">{{$time->study_time ?? ''}} (Valid: {{ date('d-M-Y', strtotime($time['renew_date'])) }}) <br></span>
                                                   @endforeach
                                                @endif
                                            </td>
                                            @if($getPermission->edit == 1)
                                            <td>
                                                  @if($item->status==1)
                                                	<button data-toggle="modal"  data-id="{{ $item->id }}" data-name="1" class="btn btn-success btn-sm btn-soft-success waves-effect waves-light student_status " style ="display:inline">Active</button>
               							    	@else
                                                	<button data-toggle="modal" data-id="{{ $item->id }}" data-name="0" class="btn btn-danger btn-sm btn-soft-danger waves-effect waves-light student_status" style ="display:inline">Inactive</button>
            								    @endif
                                            </td>
                                            @endif
                                            @if($getPermission->edit == 1 || $getPermission->deletes == 1 || $getPermission->download == 1)
                                            <td>
                                                <a href="#" class="btn btn-info btn-flat btn-xs ViewDetail" data-toggle="modal" data-library_assign_id='{{ $item['id'] ?? '' }}' data-admission_no='{{ $item['admissionNo'] ?? '' }}' data-name='{{ $item['first_name'] ?? '' }}' data-target="#exampleModalLong" data-id="3031" title="View Detail"><i class="fa fa-eye" aria-hidden="true"></i> </a>
                                                    <form id="myForm" action="{{ url('library_student_renew') }}" method="get">
                                                        <input type="hidden" class="form-control"  name="library_assign" value="{{ $item['id'] }}">
                                                        <button type="submit" onclick="submitForm()" class="btn btn-xs btn-secondary" >ReNew</button>
                                                    </form>
                                                    
                                                @if($getPermission->download == 1)
                                                <a href="{{ url('library_id_card') }}/{{ $item['id'] ?? '' }}" class="btn btn-success  btn-xs " title="Id Card" target="_blank"><i class="fa fa-address-card"></i></a>
                                                @endif
                                                @if($getPermission->edit == 1)
                                                <a href="{{ url('library_student_edit') }}/{{ $item['admission_id'] ?? '' }}" class="btn btn-primary  btn-xs" title="Edit Student"><i class="fa fa-edit"></i></a>
                                                <a href="{{ url('library_student_plans_manage') }}/{{ $item['admission_id'] ?? '' }}" class="btn btn-primary  btn-xs"><i class="fa fa-user"></i> User Plans </a>
                                                @endif
                                                @if($getPermission->deletes == 1)
                                                <a href="javascript:;" data-admission_id="{{ $item->admission_id }}" data-id='{{ $item['id'] ?? '' }}' data-admission_id='{{ $item['admission_id'] ?? '' }}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger btn-xs " title="Delete Floor"><i class="fa fa-trash-o"></i></a>
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach

                                        @else
                                        <tr>
                                            <td colspan="12" class="text-center">{{ __('library.No Book Found') }} !</td>
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
<!-- Modal -->
<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"><i class="text-muted fa fa-user" aria-hidden="true"></i> <spam class="admissionNo"> </spam> - <spam class="student_name"> </spam> </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="row">
            <div class="col-md-4 text-center user-modal image">
                <img src="https://sjcs.rusoft.in/schoolimage/default/user_image.jpg" alt="Default Photo" title="Default Photo" class="img-thumbnail">
            </div>
             <div class="col-md-8">
            <table class="pull-left col-md-8 display-grid ">
                <tbody>
                    
                    <tr>
                        <td class="h5 teach padding-right"><strong>Mobile : </strong></td>
                        <td class="h5 teach mobile"></td>
                    </tr>
                    <tr>
                        <td class="h5 teach padding-right"><strong>Email : </strong></td>
                        <td class="h5 teach email"></td>
                    </tr>
                    <tr>
                        <td class="h5 teach padding-right"><strong>Gender : </strong></td>
                        <td class="h5 teach gender_name"> </td>
                    </tr>
                    <tr>
                        <td class="h5 teach padding-right"><strong>ID Proof : </strong></td>
                        <td class="h5 teach id_proof"></td>
                    </tr>
                    <tr>
                        <td class="h5 teach padding-right"><strong>ID Number : </strong></td>
                        <td class="h5 teach id_number"></td>
                    </tr>
                </tbody>
                <tbody class="open_info">
                    <tr>
                        <td class="h5 teach padding-right"><strong>Father Name : </strong></td>
                        <td class="h5 teach father_name"></td>
                    </tr>
                    <tr>
                        <td class="h5 teach padding-right " ><strong>Parent/Guardian Mobile : </strong></td>
                        <td class="h5 teach padding-right father_mobile"></td>
                    </tr>
                    <tr> 
                        <td class="h5 teach padding-right "><strong>Date of Birth : </strong></td>
                        <td class="h5 teach dob"></td>
                    </tr>
                    <tr>
                        <td class="h5 teach padding-right"><strong>Blood Group : </strong></td>
                        <td class="h5 teach"></td>
                    </tr>
                    <tr>
                        <td class="h5 teach padding-right"><strong>Nationality : </strong></td>
                        <td class="h5 teach"></td>
                    </tr>
                    <tr>
                        <td class="h5 teach padding-right "><strong>Address : </strong></td>
                        <td class="h5 teach address"> </td>
                    </tr>
                 <!--   <tr>
                        <td class="h5 teach padding-right"><strong>State : </strong></td>
                        <td class="h5 teach">Bihar</td>
                    </tr>
                    <tr>
                        <td class="h5 teach padding-right"><strong>City : </strong></td>
                        <td class="h5 teach">Madhubani</td>
                    </tr>-->
                   <!-- <tr>
                        <td class="h5 teach padding-right"><strong>Pincode : </strong></td>
                        <td class="h5 teach">0</td>
                    </tr>-->

                </tbody>
            </table>
            </div>
            </div>
      </div>
     
    </div>
  </div>
</div>
     
<div class="modal" id="Modal_id">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #555b5beb;">

            <div class="modal-header">
                <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>

            <form action="{{ url('library_student_delete') }}" method="post">
                @csrf
                <div class="modal-body">
                    <input type=hidden id="delete_id" name="delete_id">
                    <input type=hidden id="admission_id" name="admission_id">
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



   <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg_color" style="background: #555b5beb; color:white;">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">{{ __('common.Status Change Confirmation') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <!-- Modal body -->
            <form action="{{ url('library_student_status')}}" method="post">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="status_id" name="status_id" />
                    <input type="hidden" id="student_id" name="student_id" />
                    <input type="hidden" id="admission_id" name="admission_id" />
                    
                    <h5 class="text-black">
                        {{ __('common.Are you sure you want to Change Status ?') }} 
                    </h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form"
                        data-bs-dismiss="modal">
                        {{ __('Close') }}
                    </button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">
                        {{ __('yes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(".deleteData").click(function() {
            var delete_id = $(this).data('id');
            var admission_id = $(this).data('admission_id');
            $("#delete_id").val(delete_id);
            $("#admission_id").val(admission_id);
        })
    })
    
    
     $('.student_status').click(function() {
    var student_id = $(this).data('id'); 
    var status_id = $(this).data('name');
    var admission_id = $(this).data('admission_id');
  
    $('#admission_id').val(admission_id); 
    $('#status_id').val(status_id); 
    $('#student_id').val(student_id); 
   $('#exampleModalCenter').modal('show');
  } );
  
  $(document).ready(function() {
        $(".ViewDetail").click(function() {
            var student_name = $(this).data('name');
              var library_assign_id = $(this).data('library_assign_id');
            var admissionNo = $(this).data('admission_no');
            $(".student_name").html(student_name);
            $(".admissionNo").html(admissionNo);
            student_detail(library_assign_id);
        })
    });
     function student_detail(library_assign_id) {
		
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: '/library_student_detail/' + library_assign_id,
			success: function(data) {
			    $('.image').attr('src', data.image);
			    $(".mobile").html(data.mobile);
			    $(".email").html(data.email);
			    $(".gender_name").html(data.gender_name);
			    $(".id_proof").html(data.id_proof);
			    $(".id_number").html(data.id_number);
			    $(".father_name").html(data.father_name);
			    $(".father_mobile").html(data.father_mobile);
			    $(".dob").html(data.dob);
			    $(".address").html(data.address);
			    
			}
		});
	};
</script>


@endsection