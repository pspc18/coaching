@extends('layout.app') 
@section('content')
@php
$attendanceType = Helper::attendanceType();
  $classType = Helper::classType();

@endphp


 <div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                     <div class="card-header bg-primary">
                         <h3 class="card-title"><i class="fa fa-calendar-minus-o"></i> &nbsp;{{ __('student.View Students Attendance') }}</h3>
                    <div class="card-tools">
                        @if(Session::get('role_id') !== 3)
                        <a href="{{url('studentsAttendanceAdd')}}" class="btn btn-primary {{ Helper::permissioncheck(3)->add ? '' : 'd-none' }} btn-sm" ><i class="fa fa-plus"></i>{{ __('common.Add') }}  </a>
                 
                        <a href="{{url('studentsDashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a>
                        @endif
                    </div>
                    
                    </div>      
                    
                    @if(count($classType) > 0)
                    <form id="quickForm" action="{{ url('studentsAttendanceView2') }}"  method="post">
                        @csrf 
                  
                    <div class="row m-2">
                         <div class="col-md-1 col-6">
                			<div class="form-group">
                				<label>{{ __('Year') }} </label>
                				<select class="form-control select2" id='year' name="year">
                                   <option  value='2023' {{'2023' == date('Y') ? "selected" : "" }}>2023</option>
                                    <option  value='2024' {{'2024' == date('Y') ? "selected" : "" }}>2024</option>
                                    <option  value='2025'{{'2025' == date('Y') ? "selected" : "" }}>2025</option>
                                    <option  value='2026'{{'2026' == date('Y') ? "selected" : "" }}>2026</option>
                                    <option  value='2027'{{'2027' == date('Y') ? "selected" : "" }}>2027</option>
                                    <option  value='2028'{{'2028' == date('Y') ? "selected" : "" }}>2028</option>
                                    <option  value='2029'{{'2029' == date('Y') ? "selected" : "" }}>2029</option>
                                    <option  value='2030'{{'2030' == date('Y') ? "selected" : "" }}>2030</option>
                                    
                                    </select> 
                		    </div>
                		</div>
                        <div class="col-md-2 col-6">
            			<div class="form-group">
            				<label>{{ __('common.Month') }} </label>
            				<select class="form-control select2" id='date__' name="date">
                                <option value=''>--{{ __('student.Select Month') }}--</option>
                                <option {{$search['date'] == 1 ? "selected" : "" }} value='01'>Janaury</option>
                                <option {{$search['date'] == 2 ? "selected" : "" }} value='02'>February</option>
                                <option {{$search['date'] == 3 ? "selected" : "" }} value='03'>March</option>
                                <option {{$search['date'] == 4 ? "selected" : "" }} value='04'>April</option>
                                <option {{$search['date'] == 5 ? "selected" : "" }} value='05'>May</option>
                                <option {{$search['date'] == 6 ? "selected" : "" }} value='06'>June</option>
                                <option {{$search['date'] == 7 ? "selected" : "" }} value='07'>July</option>
                                <option {{$search['date'] == 8 ? "selected" : "" }} value='08'>August</option>
                                <option {{$search['date'] == 9 ? "selected" : "" }} value='09'>September</option>
                                <option {{$search['date'] == 10 ? "selected" : "" }} value='10'>October</option>
                                <option {{$search['date'] == 11 ? "selected" : "" }} value='11'>November</option>
                                <option {{$search['date'] == 12 ? "selected" : "" }} value='12'>December</option>
                                </select> 
                		    </div>
                		</div>       
                		@if(Session::get('role_id') != 3)
                            <div class="col-md-2 col-6">
                        		<div class="form-group">
                    			<label>{{ __('common.Class') }}</label>
                    			<select class="form-control select2" id="class_type_id" name="class_type_id" >
                    			    @if(Session::get('role_id') != 2)
                    			    
                    			<option value="">{{ __('common.Select') }}</option>
                                 @endif
                                 @if(!empty($classType)) 
                                      @foreach($classType as $type)
                                         <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['class_type_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                      @endforeach
                                  @endif
                                </select>
                        	    </div>
                        	</div>
                         @endif
              	
                    <div class="col-md-1 col-6 text-center">
                    	 <button type="button" class="btn btn-primary mt-4" onclick="SearchValue()">{{ __('common.Search') }}</button>
                    </div>
                    </div>
                    </form>
                        @else
                        <p class="text-center text-danger mt-2">You are not yet authorized for viewing attendance .... please contact your administrator</p>
                          @endif
 
                         <div class="col-md-12 col-6 text-right">
                            <button id="toggleInOut" class="btn btn-sm btn-primary mt-4">Show/Hide In/Out</button>
                                          	</div>
                    
                      <table id="table" class="table table-bordered table-striped border table-responsive dataTable paddingTable">
                          <thead class="bg-primary">
                            <tr id='days'role="row">
                            </tr>
                          </thead>
                        <tbody id='student_list'>
                            <tr></tr>
                         </tbody>
                          
                      </table>
                      <div class="table-responsive">
                      <div class="col-md-10">
                            <button class="btn btn-primary" onclick="downloadCSV()">Download CSV</button> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="btn btn-xs btn-success">&nbsp;P&nbsp;</span> Present &nbsp; <span class="btn btn-xs btn-danger">&nbsp;A&nbsp;</span> Absent &nbsp;  <span class="btn btn-xs btn-primary">&nbsp;H&nbsp;</span> Holiday  &nbsp; <span class="btn btn-xs btn-info">&nbsp;EX&nbsp;</span> Exam &nbsp; <span class="btn btn-xs btn-secondary">&nbsp;E&nbsp;</span>Event 
                            </div>    
                        </div>
                                   
                  </div>
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
<script>
function SearchValue() {
    var class_type_id = $('#class_type_id').val();
    if (!class_type_id) {
        toastr.error('Please select a Class!');
        return;
    }

    var allStudents = @json($allStudents);
    var date_array = [];

    $('#student_list').html('');
    event.preventDefault();
    $('#loadingModal').modal('show');

    var year = $('#year').val();
    var month = parseInt($('#date__').val()) - 1; // 0-indexed
    var daysInMonth = new Date(year, month + 1, 0).getDate();

    // Table headers
    var days = $('#days');
    days.html('<th>Admission No</th><th class="days_td">Name</th>');
    var row_days = '';
    for (var i = 1; i <= daysInMonth; i++) {
        var dateObj = new Date(year, month, i);
        var dayOfWeek = dateObj.toLocaleString('en', { weekday: 'short' });
        row_days += '<th class="days_">' + i + ' ' + dayOfWeek + '</th>';
    }
    row_days += '<th class="days_">Present</th>';
    row_days += '<th class="days_">Absent</th>';
    row_days += '<th class="days_">Holiday</th>';
    row_days += '<th class="days_">Event</th>';
    row_days += '<th class="days_">Exam</th>';
    days.append(row_days);

    // Render student rows
    var container = $('#student_list');
    var count = 0;

    allStudents.forEach(function(item) {
        if (parseInt(item.class_type_id) !== parseInt(class_type_id)) return;

        var row = '<tr class="stu_tr" id="' + item.id + '"><td>' + item.admissionNo + '</td>' +
                  '<td class="sticky-col second-col" title="' + item.first_name + (item.last_name ?? '') + '">' +
                  item.first_name + (item.last_name ?? '') + '</td>';

        var row2 = '';
        var array_d = [];
        for (var i = 1; i <= daysInMonth; i++) {
            var newDate = new Date(year, month, i);
            var dayOfWeek = newDate.getDay();
            var newclass = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(i).padStart(2, '0');
            array_d.push(newclass);

            if (dayOfWeek === 0) {
                // Sunday pre-filled Holiday
                row2 += '<td class="' + newclass + '_' + item.id + ' text-center holiday-cell" data-status="holiday">' +
                        '<span class="p-1 bg-primary">H</span></td>';
            } else {
                row2 += '<td class="' + newclass + '_' + item.id + ' text-center" data-status=""></td>';
            }
        }

        var row3 = '';
        row3 += '<td class="persent_' + item.id + '" data-status="present"></td>';
        row3 += '<td class="absent_' + item.id + '" data-status="absent"></td>';
        row3 += '<td class="holiday_' + item.id + '" data-status="holiday"></td>';
        row3 += '<td class="event_' + item.id + '" data-status="event"></td>';
        row3 += '<td class="exam_' + item.id + '" data-status="exam"></td>';

        container.append(row + row2 + row3);

        date_array[count] = { 'id': item.id, 'date': array_d };
        count++;
    });

    // Divide into AJAX slots
    var result = [];
    function divideIntoSlots(number) {
        var slots = Math.ceil(number / 15);
        var start = 0;
        for (var i = 0; i < slots; i++) {
            var slotValue = Math.min(15, number);
            var end = start + slotValue - 1;
            result.push({ 'from': start, 'to': end });
            start = end + 1;
            number -= slotValue;
        }
    }
    divideIntoSlots(date_array.length);

    var loop = 0;
    fetchData();

    function fetchData() {
        if (loop < result.length) {
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'post',
                url: "{{ url('/') }}/studentsAttendanceViewTable",
                data: { data: JSON.stringify(date_array), loop: result[loop] },
                success: function(response) {
                    $.each(response.data, function(index, item) {
                        $.each(item, function(index2, item2) {
                            var cell = $('.' + item2.date + '_' + item2.admission_id);
                            var existing = cell.data('attendance') || {};

                            var time12h = convertTo12HrFormat(item2.time + '');
                            var out_time = convertTo12HrFormat(item2.out_time + '');

                            // P/A/H/E/EX status
                            var statusText = '';
                            if (item2.attendance_status_id == 1) { // Present
                                statusText = '<span class="p-1 bg-success existing">P</span>';
                                existing.in_time = time12h;
                                cell.attr('data-status', 'present');
                            } else if (item2.attendance_status_id == 2) { // Present with Out
                                statusText = '<span class="p-1 bg-success existing">P</span>';
                                existing.in_time = time12h;
                                existing.out_time = out_time;
                                cell.attr('data-status', 'present');
                            } else if (item2.attendance_status_id == 3) { // Absent
                                statusText = '<span class="p-1 bg-danger existing">A</span>';
                                cell.attr('data-status', 'absent');
                            } else if (item2.attendance_status_id == 5) { // Holiday
                                statusText = '<span class="p-1 bg-primary existing">H</span>';
                                cell.attr('data-status', 'holiday');
                            } else if (item2.attendance_status_id == 6) { // Event
                                statusText = '<span class="p-1 bg-secondary existing">E</span>';
                                cell.attr('data-status', 'event');
                            } else if (item2.attendance_status_id == 7) { // Exam
                                statusText = '<span class="p-1 bg-info existing">EX</span>';
                                cell.attr('data-status', 'exam');
                            }

                            cell.html(statusText);

                            // In/Out times only if P (1) or P with Out (2)
                            if(item2.attendance_status_id == 1 || item2.attendance_status_id == 2) {
                                var timeText = '';
                                if (existing.in_time || existing.out_time) {
                                    timeText += '<div style="text-align:center; font-size:11px;">';
                                    if (existing.in_time) timeText += `<span class="text-success in_out">In: ${existing.in_time}</span><br>`;
                                    if (existing.out_time) timeText += `<span class="text-info in_out">Out: ${existing.out_time}</span>`;
                                    timeText += '</div>';
                                    cell.append(timeText);
                                }
                            }

                            cell.data('attendance', existing);
                        });
                    });

                    loop++;
                    if(loop < result.length){
                        fetchData();
                    } else {
                        $('#loadingModal').modal('hide');
                        countStatuses(); // Run after all AJAX
                    }
                }
            });
        }
    }

    function countStatuses() {
        const table = document.getElementById('table');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let row of rows) {
            let present = 0, absent = 0, holiday = 0, event = 0, exam = 0;
            const rowId = row.id;

            for (let i = 2; i < row.cells.length; i++) {
                const cell = row.cells[i];
                const status = cell.getAttribute('data-status');

                if (status === 'present') present++;
                else if (status === 'absent') absent++;
                else if (status === 'holiday') holiday++;
                else if (status === 'event') event++;
                else if (status === 'exam') exam++;
            }

            if (document.querySelector(`.persent_${rowId}`)) document.querySelector(`.persent_${rowId}`).innerText = present;
            if (document.querySelector(`.absent_${rowId}`)) document.querySelector(`.absent_${rowId}`).innerText = absent;
            if (document.querySelector(`.holiday_${rowId}`)) document.querySelector(`.holiday_${rowId}`).innerText = holiday;
            if (document.querySelector(`.event_${rowId}`)) document.querySelector(`.event_${rowId}`).innerText = event;
            if (document.querySelector(`.exam_${rowId}`)) document.querySelector(`.exam_${rowId}`).innerText = exam;
        }
    }

    function convertTo12HrFormat(time24h) {
        if (!time24h || time24h === "null") return "";
        var timeArray = time24h.split(':');
        var hours = parseInt(timeArray[0]);
        var minutes = parseInt(timeArray[1]);
        var period = hours < 12 ? 'AM' : 'PM';
        if (hours === 0) hours = 12;
        else if (hours > 12) hours -= 12;
        return hours + ':' + (minutes < 10 ? '0' : '') + minutes + ' ' + period;
    }
}


</script>

<script>
// Toggle In/Out times
$('#toggleInOut').click(function() {
    $('td span.in_out, td span.in_out').toggle(); // In/Out times

});




  
function downloadCSV() {
    var month = $('#date__ option:selected').text();
    var classtype = $('#class_type_id option:selected').text();
    let csv = [];
     var pageTitle = document.title;
    // Add month and class type to the first row
    csv.push(pageTitle);
    csv.push(`Month: ${month}, Class: ${classtype}`);

    const rows = document.querySelectorAll("table tr");

    for (const row of rows.values()) {
        const cells = row.querySelectorAll("td, th");
        const rowText = Array.from(cells).map((cell) => cell.innerText);
        csv.push(rowText.join(","));
    }
    
    const csvFile = new Blob([csv.join("\n")], {
        type: "text/csv;charset=utf-8;"
    });

    saveAs(csvFile, "Attendance_" + month + "_" + classtype + ".csv");
}

</script>        
<style>
.view {
  margin: auto;
  width: 100%;
}

.wrapper {
  position: relative;
  overflow: auto;
  border: 1px solid black;
  white-space: nowrap;
}
.in_out {
    display: none;
}

/*.sticky-col {
  position: -webkit-sticky;
  position: sticky;
  background-color: white;
}

.first-col {
  width: 100px;
  min-width: 100px;
  max-width: 100px;
  left: 0px;
}

.second-col {
  width: 150px;
  min-width: 150px;
  max-width: 150px;
  left: 100px;
}*/

.stu_tr {
    position: relative;
    overflow: auto;
    border: 1px solid black;
    white-space: nowrap;
    width: 150px;
    min-width: 150px;
    max-width: 150px;
    left: -1px;
}
.second-col {
    width: 35px;
    min-width: 89px;
    max-width: 97px;
    left: 0px;
}
.sticky-col {
    position: -webkit-sticky;
    position: sticky;
    background-color: white;
}
.days_td {
    position: -webkit-sticky;
    position: sticky;
    background-color: #002c54;
    width: 35px;
    min-width: 124px;
    max-width: 97px;
    left: 0px;
}
#days {
    position: -webkit-sticky;
    position: sticky;
    background-color: #002c54;
    width: 35px;
    min-width: 89px;
    max-width: 97px;
    left: 2px;
}

.paddingTable{
    padding-bottom:20px;    
}
.paddingTable th,td{
    padding:10px;
}

.downloadCSV{
    width:150px;
    background:#002c54;
    color:white;
    border-radius: 8px;
}
table.dataTable > thead > tr > th:not(.sorting_disabled), table.dataTable > thead > tr > td:not(.sorting_disabled) {
  padding-right: 4px;
}
.table td, .table th {
  padding: 4px;
  vertical-align: middle;
  border-top: 1px solid #dee2e6;
}
table td {
  height: 30px !important;
  padding: 2px !important;
  
}
</style>
@endsection 