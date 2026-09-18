@php
$classType = Helper::classType();
$getAttendanceStatus= Helper::getMarkableAttendanceStatus();
@endphp
@extends('layout.app') 
@section('content')
<input type="hidden" id="session_id" value="{{ Session::get('role_id') ?? '' }}">
 <div class="content-wrapper attendance-page">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="attendance-hero mb-3">
            <div class="attendance-hero-inner">
                <div>
                    <div class="attendance-hero-kicker">Attendance Management</div>
                    <h1 class="attendance-hero-title">{{ __('staff.Fill Staff Attendance') }}</h1>
                    <p class="attendance-hero-subtitle">Search staff, set date and time, then submit attendance exactly as before.</p>
                </div>
                <div class="attendance-hero-actions">
                    <a href="{{url('staff/Attendance/view')}}" class="btn btn-light btn-sm"><i class="fa fa-eye"></i> {{ __('common.View') }}</a>
                    <a href="{{url('staff_file')}}" class="btn btn-outline-light btn-sm"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}</a>
                </div>
            </div>
        </div>
        <div class="row">
          <div class="col-12">
    <div class="card card-outline card-orange">
         <div class="card-header bg-primary flex_items_toggel">
        <h3 class="card-title"><i class="fa fa-calendar-check-o"></i> &nbsp; {{ __('staff.Fill Staff Attendance') }}</h3>
        <div class="card-tools d-none">
        </div> 
        </div>         
        <form id="quickForm" action="#" method="post" >
            @csrf 
               
            <div class="row m-2">
                <div class="col-md-5">
            		<div class="form-group">
            			<label>{{ __('common.Search By Keywords') }}</label>
            			<input type="text" class="form-control" id="searchName" name="name" placeholder="{{ __('common.Ex. Name, Mobile, Email, Aadhaar etc.') }}" value="{{ $search['name'] ?? '' }}"> 
            	    </div>
            	</div> 
                <div class="col-md-1 ">
                     <label for="" class="text-white">{{ __('common.Search') }}</label>
            	    <button type="button" class="btn btn-primary" onclick="SearchValue()">{{ __('common.Search') }}</button>
            	</div>
            	</div>
        </form>        
        <form id="form-submit" action="{{ url('staff_attendance_add') }}" method="post">
                @csrf 
                <div class="row m-2">
                    <div class="col-md-2">
                		<div class="form-group">
                			<label class="text-danger">Time*</label>
                                    <input type="time" class="form-control  @error('time') is-invalid @enderror" id="select-time"  name="time" required  {{  ( Session::get('role_id') == 2 ? 'readonly' : '' )}}>  
                                    @error('time')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror            	    
                	    </div>
                	</div>
                	<div class="col-md-2">
                		<div class="form-group">
                			<label class="text-danger">{{ __('common.Date') }}*</label>
                			<input class="form-control @error('date') is-invalid @enderror" max="{{ date('Y-m-d') }}"  type="date" id="date" name="date" value="{{date('Y-m-d')}}" {{  ( Session::get('role_id') == 2 ? 'readonly' : '' )}}>
                              	@error('date')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror            	    
                	    </div>
                	</div> 
                <div class="col-md-12">
                <div class="table-responsive">
                  <table id="example11" class="table table-bordered table-striped border dataTable dtr-inline padding_table">
                      <thead>
                  <tr role="row">
                            <th>{{ __('common.SR.NO') }}</th>
                            <th>{{ __('common.Name') }}</th>
                            <th>{{ __('common.Fathers Name') }}</th>
                            <th>{{ __('common.Mobile No.') }}</th>
                            <th>{{ __('common.Attendance') }}</th>
                        </tr>
        
                    </thead>
                    <tbody class="student_list_show">
                        @if(!empty($data))
                                @php
                              
                               $current_date =   DB::table('teacher_attendance')->where('staff_id',Session::get('staff_id'))->WhereDate('date',date('Y-m-d'))->get(['attendance_status_id'])->first();
                    
                                   $i=1;
                                  
                                @endphp
                                @foreach ($data  as $item)
                               
                                @if(Session::get('role_id') != 1)
                                    @if($item['id'] == Session::get('id'))    
                                       @php
                              
                               @endphp 
                                    <tr>
                                        <input type="" id="staff_id" name="staff_id[]" value="{{ $item['id'] ?? '' }}">
                                        <input type="hidden" id="email" name="email[]" value="{{ $item['email'] ?? '' }}">
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                                        <td>{{ $item['father_name'] ?? '' }}</td>
                                        <td>{{ $item['mobile'] ?? '' }}</td>
                                        <td style="width: 30% !important;">
                                			<select class="form-control" id="attendance_status" name="attendance_status[]" >
                                             @if(!empty($getAttendanceStatus)) 
                                                    @foreach($getAttendanceStatus as $attendance_status)
                                                        
                                                            <option value="{{ $attendance_status->id ?? '' }}" @if(!empty($current_date)) {{  ( $attendance_status->id == $current_date->attendance_status_id ? 'selected' : '' )}}   @endif >{{ $attendance_status->name ?? '' }}</option>
                                                       
                                                    @endforeach
                                                @endif
                                            </select>                                    
                                        </td>         
                                    </tr> 
                                    @endif
                                    @else
                                    <tr>
                                        <input type="hidden" id="staff_id" name="staff_id[]" value="{{ $item['id'] ?? '' }}">
                                        <input type="hidden" id="email" name="email[]" value="{{ $item['email'] ?? '' }}">
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                                        <td>{{ $item['father_name'] ?? '' }}</td>
                                        <td>{{ $item['mobile'] ?? '' }}</td>
                                        <td style="width: 30% !important;">
                                			<select class="form-control" id="attendance_status" name="attendance_status[]" >
                                             @if(!empty($getAttendanceStatus)) 
                                                    @foreach($getAttendanceStatus as $attendance_status)
                                                        
                                                            <option value="{{ $attendance_status->id ?? '' }}" >{{ $attendance_status->name ?? '' }}</option>
                                                       
                                                    @endforeach
                                                @endif
                                            </select>                                    
                                        </td>                            
                                    </tr>
                                @endif
                           @endforeach
                        @endif
                    </tbody>
                </table>
                </div>
                </div>
                <div class="col-md-12 mb-3">
                   
                @if(Session::get('role_id') == 2)
                @if(empty($current_date))
                     <div class="text-center"><button type="submit" class="btn btn-primary btn-submit" id="submitButton" >{{ __('staff.Submit Attendance') }}</button></div>
                @endif
                @else
                     <div class="text-center"><button type="submit" class="btn btn-primary btn-submit" id="submitButton" >{{ __('staff.Submit Attendance') }}</button></div>
                
                @endif
                </div>
                 </div>
            </form>                  
    </div>
</div>
</div>
</div>
</section>
        
</div>
<style>
      .padding_table thead tr{
    background: #002c54;
    color:white;
    }
        
    .padding_table th, .padding_table td{
         padding:5px;
         font-size:14px;
    }
</style>

<style>
.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 768px) {
    .padding_table th, 
    .padding_table td {
        font-size: 12px;
        padding: 6px;
        white-space:
    }

    .card-header h3 {
        font-size: 16px;
    }

    .btn {
        font-size: 12px;
        padding: 4px 8px;
    }

    label {
        font-size: 13px;
    }
}

</style>


<script>
    function SearchValue() {
        var name = $('#searchName').val();
        var basurl = "{{ url('/') }}";
        if(name != ''){
        $.ajax({
                 headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            type:'post',
            url: basurl+'/SearchValueStaffAtten',
            data: {name:name},
             //dataType: 'json',
            success: function (data) {

                $('.student_list_show').html(data);
               
            }
          });
        }else{
                toastr.error('Please put a value in search box !');
            }               
    };

$( document ).ready(function() {
    var session_id = $('#session_id').val();
    if(session_id != 1){
        var today = new Date().toISOString().split('T')[0];
        document.getElementsByName("date")[0].setAttribute('min', today);        
    }
    // Get the current time
    var currentTime = new Date();
    
    // Format the time as HH:MM
    var hours = currentTime.getHours().toString().padStart(2, '0');
    var minutes = currentTime.getMinutes().toString().padStart(2, '0');
    var currentTimeString = hours + ':' + minutes;
    
    // Set the current time as the value of the input element
    document.getElementById('select-time').value = currentTimeString;
});   


function autoStaffAttendance() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: 'autoStaffAttendance',
                type: 'POST',
                data: {
                    date: dateOfEvent,
                    attendanceStatus: attendaceStatus
                },
                success: function(response) {
                    hideLoading();
                    $('.student_data').show();
                    $('.loading_text').text('Loading ...');
                    SearchValue()

                },
                error: function(xhr, status, error) {
                    hideLoading(); // Assuming hideLoading() is defined elsewhere
                    // Handle error
                    console.error('Error occurred while updating attendance:', error);
                    console.error(xhr.responseText);
                }
            });
        }
        
</script>  

@endsection 
