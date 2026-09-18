@php
$getSetting=Helper::getSetting();
$busAssign=Helper::busAssign();
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    
   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-3">
            <div class="card card-outline card-orange">

                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-user-circle-o"></i> &nbsp; Profile</h3>
                    <span id="statusColor" style="display:none;">{{ $data['stu_status'] ?? '' }}</span>
                    <div class="card-tools">
                        <!--<a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-arrow-left"></i> Back</a>-->
                    </div>
                </div> 
                  <div class="card-body box-profile">
                    <div class="text-center">
                      <img class="profile-user-img img-fluid img-circle" src="{{ env('IMAGE_SHOW_PATH').'/student_image/'.$data['student_img'] ?? '' }}" style="width:105px; height:105px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
                    </div>
    
                    <h3 class="profile-username text-center">{{Session::get('name')}}</h3>

                    <div class="text-center mb-3">
                        <span id="studentStatusBadge" class="badge {{ ($data['status'] ?? 1) == 1 ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                            {{ ($data['status'] ?? 1) == 1 ? 'Active Student' : 'Inactive Student' }}
                        </span>
                    </div>
    
                    <ul class="list-group list-group-unbordered mb-3">
                      <li class="list-group-item">
                        <b>Admission No</b> <a class="float-right">{{$data['admissionNo'] ?? ''}}</a>
                      </li>
                      <li class="list-group-item">
                        <b>Roll No</b> <a class="float-right">{{$data['roll_no'] ?? ''}}</a>
                      </li>
                      <li class="list-group-item">
                        <b>Class</b> <a class="float-right">{{$data['ClassTypes']['name'] ?? ''}}</a>
                      </li>
                      <li class="list-group-item">
                        <b>Section</b> <a class="float-right">{{$data['Section']['name'] ?? ''}}</a>
                      </li> 
                      <li class="list-group-item">
                        <b>Admission</b> 
                            @if($data['admission_type_id'] == 1)
                                <a class="float-right">Regular</a>
                            @endif
                            @if($data['admission_type_id'] == 2)
                                <a class="float-right">Non</a>
                            @endif                      
                            @if($data['admission_type_id'] == 3)
                                <a class="float-right">Other</a>
                            @endif                        
                        </li>                   
                    </ul>
    
                  </div>
             
            </div>

         </div>
         
          <div class="col-12 col-md-9">
            <div class="card card-outline card-orange">

                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-address-book-o"></i> &nbsp; Personal Details</h3>
                    <div class="card-tools">
                        <a href="{{url('admission/edit',$data->id)}}" class="btn btn-primary btn-sm" title="Edit Student"><i class="fa fa-edit"></i></a>
                        <a href="{{url('Fees/add')}}" class="btn btn-primary  btn-sm" title="Collect Fees"><i class="fa fa-money"></i></a>
                        <a href="#" data-user_name="{{ $data['userName'] ?? '' }}" data-confirm_password="{{ $data['confirm_password'] ?? '' }}" data-first_name="{{ $data['first_name'] ?? '' }}" data-last_name="{{ $data['last_name'] ?? '' }}" class="btn btn-primary btn-sm loginDetail" title="Login Details"><i class="fa fa-key"></i></a>
						<a id="disableStudentBtn" data-id="{{ $data['id'] ?? '' }}" style="{{ $data['status'] == 0 ? 'display:none' : '' }}" data-status="0" class="btn btn-primary btn-sm change_status" data-bs-toggle="modal" data-bs-target="#statusModel" title="Disable Student"><i class="fa fa-thumbs-o-up"></i></a>
						<a id="enableStudentBtn" data-id="{{ $data['id'] ?? '' }}" style="{{ $data['status'] == 1 ? 'display:none' : '' }}" data-status="1" class="btn btn-primary btn-sm change_status" data-bs-toggle="modal" data-bs-target="#statusModel" title="Enable Student"><i style="color:red" class="fa fa-thumbs-o-down"></i></a>
                    </div>
                </div> 
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#profile" data-toggle="tab">Profile</a></li>
                  <li class="nav-item"><a class="nav-link" href="#fees" data-toggle="tab">Fees</a></li>
                  <li class="nav-item"><a class="nav-link" href="#exam" data-toggle="tab">Exam</a></li>
                  <li class="nav-item"><a class="nav-link" href="#document" data-toggle="tab">Documents</a></li>
                  <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a></li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content">
                  <div class="active tab-pane" id="profile">
                        <div class="card-body p-0 mb-3">
                            <table class="table table-sm table-hover border">
                                <tbody>
                                    <tr>
                                    <td>Name</td>
                                    <td>{{$data['first_name'] ?? ''}} {{$data['last_name'] ?? ''}}</td>
                                    </tr>
                                    <tr>
                                    <td>Admission Date</td>
                                    <td>{{$data['admission_date'] ?? ''}} </td>
                                    </tr>                                    
                                    <tr>
                                    <td>Date of Birth</td>
                                    <td>{{$data['dob'] ?? ''}} </td>
                                    </tr>
                                    <tr>
                                    <td>Mobile Number</td>
                                    <td>{{$data['mobile'] ?? ''}}</td>
                                    </tr>
                                    <tr>
                                    <td>Email</td>
                                    <td>{{$data['email'] ?? ''}}</td>
                                    </tr>
                                    <tr>
                                    <td>Address</td>
                                    <td>{{$data['address'] ?? ''}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="card-body p-0 mb-3">
                            <table class="table table-sm border table-hover">
                                <thead>
                                    <tr class="bg-light">
                                    <th width="39%" class="text-white">Parent / Guardian Details </th>
                                    <th></th>
                                    <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td class="col-md-4">Father Name</td>
                                    <td class="col-md-5">{{$data['father_name'] ?? ''}}</td>
                                    <td rowspan="3">
                                        <img class="profile-user-img img-fluid img-circle" src="{{ env('IMAGE_SHOW_PATH').'/father_image/'.$data['father_img'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" style="width:105px; height:105px;">
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>Father Phone</td>
                                    <td>{{$data['father_mobile'] ?? ''}}</td>
                                    </tr>
                                    <tr>
                                    <td>Father Occupation</td>
                                    <td>{{$data['null'] ?? ''}}</td>
                                    </tr>
                                    <tr>
                                    <td class="col-md-4">Mother Name</td>
                                    <td class="col-md-5">{{$data['mother_name'] ?? ''}}</td>
                                    <td rowspan="3">
                                        <img class="profile-user-img img-fluid img-circle" src="{{ env('IMAGE_SHOW_PATH').'/mother_image/'.$data['mother_img'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" style="width:105px; height:105px;">
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>Mother Phone</td>
                                    <td>{{$data['mother_mobile'] ?? ''}}</td>
                                    </tr>
                                    <tr>
                                    <td>Mother Occupation</td>
                                    <td>{{$data['null'] ?? ''}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="card-body p-0 mb-3">
                            <table class="table table-sm border table-hover">
                                <thead>
                                    <tr class="bg-light">
                                    <th width="39%" class="text-white">Transport Details</th>
                                    <th></th>
                                    <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td>Route</td>
                                    <td>{{$busAssign['busRoute']['name'] ?? ''}}</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td>Vehicle Number</td>
                                    <td>{{$busAssign['busId']['bus_no'] ?? ''}}</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td>Driver Name</td>
                                    <td>{{$busAssign['busId']['bus_owmer_name'] ?? ''}}</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td>Driver Contact</td>
                                    <td>{{$busAssign['busId']['owner_no'] ?? ''}}</td>
                                    <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>                    
                        
                        <div class="card-body p-0 mb-3">
                            <table class="table table-sm border table-hover">
                                <thead>
                                    <tr class="bg-light">
                                    <th width="39%" class="text-white">Hostel Details</th>
                                    <th></th>
                                    <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td>Hostel</td>
                                    <td>{{$data['section_id'] ?? ''}}</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td>Room No</td>
                                    <td>{{$data['section_id'] ?? ''}}</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                    <td>Room Type</td>
                                    <td>{{$data['section_id'] ?? ''}}
                                    </td><td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> 

                  </div>
                 
                  <div class="tab-pane" id="fees">

                        <div class="card-body p-0 mb-3">
                            <table class="table table-sm table-hover border">
                                <thead>
                                    <tr>
                                        <th>Sr. No.</th>
                                        <th>Fees Type</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Payment Mode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                @if(!empty($feesDetail))
                                @php
                                   $i=1;
                                   $totalPadeFees= 0;
                                @endphp
                                @foreach ($feesDetail  as $item)
                                
                                    <tr>
                                    <td>{{ $i++}}</td>
                                    <td>{{$item['FeesType']['name'] ?? ''}}</td>
                                    <td>{{$item['total_amount'] ?? ''}}</td>
                                    <td>{{$item['date'] ?? ''}}</td>
                                    <td>{{$item['PaymentMode']['name'] ?? ''}}</td>
                                    </tr>
                                    @php
                                    $totalPadeFees +=$item['total_amount']; 
                                    @endphp
                                    @endforeach
                                    <tr>
                                        <td></td>
                                        <td>
                                            <h5>Total Paid Fees</h5>
                                        </td>
                                        <td > <h5>{{ $totalPadeFees }}</h5> </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
  
                  </div>
                
             <!--   {!! QrCode::size(150)->generate(url('student_detail/'.$data['id'])) !!}-->

                  <div class="tab-pane" id="exam">

                  </div>

                  <div class="tab-pane" id="fees">
  
                  </div>
                 

                  <div class="tab-pane" id="exam">

                  </div>
                  
                </div>
               
              </div>
              </div>
              </div>
            </div>
            
          </div>
    </section>

</div>

<style>
.card-header .nav-pills .nav-link {
  color: #db5b06;
}
</style> 
<script>
/*$( document ).ready(function() {
    var statusColor = $('#statusColor').data();
    alert(statusColor);
    if(statusColor = 0){
        $(".card").addClass("bg-danger");
    }
});*/
  $(document).on('click', ".loginDetail", function() {
      $('#Modal_id').modal('toggle');
        userName = $(this).data("user_name");
        confirm_password = $(this).data("confirm_password");
        first_name = $(this).data("first_name");
        last_name = $(this).data("last_name");
        
        $('#userName').html(userName);
        $('#confirm_password').html(confirm_password);
        $('#first_name').html(first_name);
        $('#last_name').html(last_name);
  });
  
  $(document).on('click', ".change_status", function() {
      $('#statusModel').modal('show');
        id = $(this).data("id");
        status = $(this).data("status");
  });
$(document).on('click', ".change_status1", function() {
	$.ajax({
    		headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    		},
    		type: 'post',
    		url: '{{ url('stu_status') }}',
    		data: {
    			status: status,
    			id: id
    		},
    		success: function(response) {
    		    const isActive = String(status) === '1';
    		    $('#studentStatusBadge')
    		        .toggleClass('bg-success', isActive)
    		        .toggleClass('bg-danger', !isActive)
		        .text(isActive ? 'Active Student' : 'Inactive Student');

		    $('#disableStudentBtn').toggle(isActive);
		    $('#enableStudentBtn').toggle(!isActive);
		    toastr.success('Status Changed Successfully !');
		},
		error: function() {
		    toastr.error('Student status could not be changed. Please try again.');
    		},
	});
});
</script>
                        <!-- The Modal -->
                        <div class="modal" id="statusModel">
                          <div class="modal-dialog">
                            <div class="modal-content" style="background: #555b5beb;">
                        
                              <div class="modal-header">
                                <h4 class="modal-title text-white">Status Change Confirmation</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                              </div>
                        
                              <form action="#" method="post">
                              <div class="modal-body">
                                      <h5 class="text-white">Are you sure you want to Change Status  ?</h5>
                              </div>
                              <div class="modal-footer">
                                        <button type="button" class="btn btn-primary waves-effect waves-light change_status1" data-bs-dismiss="modal">Yes</button>
                                        <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">No</button>
                                            
                                 </div>
                               </form>
                            </div>
                          </div>
                        </div>
                        
                        
<!-- The Modal -->
<div class="modal" id="Modal_id">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Login Details</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
				<div class="modal-body">
					<h4 class="text-center"><span id="first_name"></span> <span id="last_name"></span></h4> 
					<div class="row border-bottom">
					    <div class="col-md-4"><b>User Type</b></div>
					    <div class="col-md-4"><b>Username</b></div>
					    <div class="col-md-4"><b>Password</b></div>
				    </div>
				    <div class="row border-bottom">
					    <div class="col-md-4">Student</div>
					    <div class="col-md-4" id="userName"></div>
					    <div class="col-md-4" id="confirm_password"></div>
					</div>
					<div class="row mt-3">
					    <div class-"col-md-12"><b class=" text-primary">Login Url: &nbsp; https://www.school.rukmanisoftware.com/student/login</b></div>
				    </div>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
				</div>
		</div>
	</div>
</div>                        
                        
                        
@endsection
  
