@php
$classType = Helper::classType();
$getAttendanceStatus= Helper::getMarkableAttendanceStatus();

@endphp
@extends('layout.app')
@section('content')

<input type="hidden" id="session_id" value="{{ Session::get('role_id') ?? '' }}">
<div class="content-wrapper">

    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-calendar-check-o"></i> &nbsp;{{ __('student.Fill Students Attendance') }}</h3>
                            <div class="card-tools">
                                <a href="{{url('studentsAttendanceView')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(3)->view ? '' : 'd-none' }}"><i class="fa fa-eye"></i>{{ __('common.View') }}</a>
                                <a href="{{url('studentsDashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}</a>
                            </div>
                        </div>
                        <form id="quickForm" action="{{ url('studentsAttendanceAdd') }}" method="post">
                            @csrf
                            <div class="row m-2">
                               
                                <div class="col-md-2 col-4">
                                    <div class="form-group">
                                        <label>{{ __('common.Class') }}</label>
                                        <select class="form-control @error('class_type_id') is-invalid @enderror" id="class_type_id" name="class_type_id">
                                            @if(Session::get('role_id') != 2)
                                            <option value="">{{ __('common.Select') }}</option>
                                            @endif
                                            @if(!empty($classType))
                                            @foreach($classType as $type)
                                            <option value="{{ $type->id ?? ''  }}">{{ $type->name ?? ''  }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                        @error('class_type_id')
                                          <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-2 col-4">
                                    <div class="form-group">
                                        <label class="text-danger">{{ __('common.Date') }}*</label>
                                        <input class="form-control @error('date') is-invalid @enderror date_" type="date" max="{{ date('Y-m-d') }}" id="date1" name="date" value="{{date('Y-m-d')}}">
                                        @error('date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                             
                                <div class="col-md-1 col-3">
                                     <div class="form-group">
                                    <label for="" class="text-white">{{ __('common.Search') }}</label>
                                        <button type="button" class="btn btn-primary" onclick="SearchValue()"> <i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                                    </div> 
                                    
                                </div>
                                <div class="col-md-2 col-6 mt-4">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
                                     Academic Calender
                                    </button>
                                </div>
                            </div>
                        </form>

                        <form id='myForm' action="{{ url('studentsAttendanceAdd') }}" method="post">
                            @csrf
                            <div class="row m-2" id='attendance_wrapper'>
                                
                                
                                  <div class="col-md-9 col-12"></div>
                                   <div class="col-md-3 col-12">
                                    <label>Select For Everyone</label>
                                  
                                    
                                    <select id="selectForAll" class="form-control" onchange="setForAll(this.value)">
                                        <option value="">-- Select --</option>
                                        @foreach($getAttendanceStatus as $attendance_status)
                                            <option value="{{ $attendance_status->id }}">
                                                {{ $attendance_status->name }}
                                            </option>
                                        @endforeach
                                    </select>


                                       
                                       </div>
                                
                                
                            </div>


                            <div class="col-md-12 overflow_scroll">
                                <table id='student_list_show' class="table table-bordered table-striped border  dtr-inline  student_data">
                                    <thead>
                                        <tr role="row" class="colored_tr">
                                            <th>#  <input type="checkbox" id="checkAll"> <!-- master checkbox --> </th>
                                            <th>{{ __('student.Admission No.') }}</th>
                                            <th>{{ __('common.Name') }}</th>
                                            <th>{{ __('Father') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>

                                    </thead>
                                    <tbody class="student_list_show" >

                                    </tbody>
                                </table>
                            </div>

                            <div class="row m-2 student_data">
                                <div class="col-md-12 text-center"><button type="submit" class="btn btn-primary" id="saveAttendance">{{ __('student.Submit Attendance') }}</button></div>
                            </div>
                        </form>

                    </div>
                </div>

                
            </div>
        </div>
    </section>
</div>

<!-- Loading screen modal -->
<div class="modal" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="w-100">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only text-white">Loading...</span>
                </div>
                <p class="mt-2 text-white loading_text">Loading...</p>
            </div>
        </div>
    </div>
</div>


<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Academic Calender</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
            
                <table class="table table-bordered" style="font-size:13px">
                    <thead>
                        <tr>
                            <!-- <th scope="col">Sr. No</th> -->
                            <th scope="col" style="width: 54px">Date</th>
                            <th scope="col">Day</th>
                            <th scope="col">Event</th>
                        </tr>
                    </thead>
                    <tbody id="calendarTableBody">
                        <!-- Table rows will be dynamically added here -->
                    </tbody>
                </table>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <a href="{{url('add_weekend')}}" target="_blank" class="btn btn-primary  btn-sm mt-1 ml-2"><i class="fa fa-plus"></i> Add</a>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>


<!-- The Modal -->
<style>
    /* Centering the loader */
    .loader {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1050;
        /* Make sure this is higher than the modal backdrop */
    }
    
    .overflow_scroll{
        overflow: scroll;
    }
</style>
<script>
    var academicCalender = [];

    function AcademicCalender() {
        $.ajax({
            url: 'academic_calendar',
            method: 'GET',
            success: (data) => {
                academicCalender = data.data;
                loadAcademic();
            },
            error: (xhr, status, error) => {
                console.error('Error fetching academic calendar:', error);
            }
        });
    }

    AcademicCalender();

    function loadAcademic() {
        $('#today_event').html('');
        var dateOfEvent = '';
        var attendaceStatus = '';
        var checkDate = false;

        var filteredDates = academicCalender.map(function(event, index) {
            var eventDate = new Date(event.date);
            var currentDate = new Date($('#date1').val());
            var formattedDate2 = currentDate.toISOString().split('T')[0];

            let isToday = event.date === formattedDate2;
            let shouldAutoSubmit = isToday && event.is_attendance_submitted == 0;

            if (isToday) {
                $('#today_event').html("<span class='text-primary'>Event/Schedule On Selected Date Is </span>" + event.event_schedule);
            }

            if (shouldAutoSubmit) {
                checkDate = true;
                attendaceStatus = event.attendance_status;
                dateOfEvent = event.date;
            }

            var day = eventDate.getDate();
            var monthIndex = eventDate.getMonth();
            var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                              'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var formattedDate = day + '-' + monthNames[monthIndex];

            return {
                srno: index + 1,
                date: formattedDate,
                day: event.day,
                event: event.event_schedule,
                checkDate: shouldAutoSubmit
            };
        });

      

        function populateTable() {
            var tableBody = document.getElementById('calendarTableBody');
            tableBody.innerHTML = '';
            filteredDates.forEach(function(event) {
                var row = `<tr class="${event.checkDate ? 'bg-primary blink' : ''}">
                    <td>${event.date}</td>
                    <td>${event.day}</td>
                    <td>${event.event}</td>
                </tr>`;
                tableBody.innerHTML += row;
            });
        }

        populateTable();

    }

    function showLoading() {
        $('#loadingModal').modal('show');
    }

    function hideLoading() {
        $('#loadingModal').modal('hide');
    }
           $('#saveAttendance').click(function(e) {
                if ($('.student-check:checked').length === 0) {
                    toastr.error('Please select at least one student!');
                    return false; // stop AJAX
                }
                showLoading();
                
                    e.preventDefault(); // prevent form submit
                
                    var formData = $('#myForm').serialize(); // serializes arrays like attendance_status[1], admission_id[]
                    var URL = "{{ url('studentsAttendanceAdd') }}";
                
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        type: 'POST',
                        url: URL,
                        data: formData,
                        success: function(response) {
                            if(response.success){
                                hideLoading();
                                toastr.success('Attendance saved successfully!');
                            location.reload();
                            }
                        },
                        error: function(xhr) {
                            hideLoading();
                            if(xhr.status === 422){
                                toastr.error(xhr.responseJSON.error);
                            } else {
                                toastr.error('Something went wrong!');
                            }
                        }
                    });
                });



function SearchValue() {
    var class_type_id = $('#class_type_id').val();
    var custom_date = $('#date1').val();
    var URL = "{{ url('/') }}/SearchValueAtten";

    if (!class_type_id || class_type_id == '') {
        toastr.error('Please select a Class!');
        return;
    }

    $('#loadingModal').modal('show');

    $.ajax({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        type: 'POST',
        url: URL,
        data: {
            custom_date: custom_date,
            class_type_id: class_type_id
        },
        success: function(data) {
            $('.student_list_show').html(data);
            $('#loadingModal').modal('hide');
        },
        error: function() {
            toastr.error('Something went wrong!');
            $('#loadingModal').modal('hide');
        }
    });
}

</script>

<script>
    $(function() {
        //Initialize Select2 Elements
        $('.select2').select2()
        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })
    })
</script>



<style>
    .colored_tr th {
        padding: 5px;
    }

    .colored_tr {
        background: #673ab7;
        font-size: 14px;
        color: white;
        position: sticky;
        top: 0;
    }

    @keyframes blink {
        0% {
            background-color: transparent;
            /* Start with transparent background */
        }

        50% {
            background-color: #007bff;
            /* Blink to primary color */
        }

        100% {
            background-color: transparent;
            /* Back to transparent */
        }
    }

    /* Apply animation to elements with bg-primary class */
    .blink {
        animation: blink 1s infinite;
        /* Use the blink animation infinitely */
    }
</style>

@endsection
