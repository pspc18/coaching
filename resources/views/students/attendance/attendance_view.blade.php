@extends('layout.app') 
@section('content')
@php
$attendanceType = Helper::attendanceType();
  $classType = Helper::classType();
  $getPermission = Helper::getPermission();
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
                    <div class="row m-2">
                      
                        <div class="col-md-1 col-3">
            			<div class="form-group">
            				<label>{{ __('Year') }} </label>
            				<select class="form-control " id="year" name="year">
                               <option  value='2023' {{'2023' == $search['year'] ? "selected" : "" }}>2023</option>
                                <option  value='2024' {{'2024' == $search['year'] ? "selected" : "" }}>2024</option>
                                <option  value='2025'{{'2025' == $search['year'] ? "selected" : "" }}>2025</option>
                                <option  value='2026'{{'2026' == $search['year'] ? "selected" : "" }}>2026</option>
                                <option  value='2027'{{'2027' == $search['year'] ? "selected" : "" }}>2027</option>
                                <option  value='2028'{{'2028' == $search['year'] ? "selected" : "" }}>2028</option>
                                <option  value='2029'{{'2029' == $search['year'] ? "selected" : "" }}>2029</option>
                                <option  value='2030'{{'2030' == $search['year'] ? "selected" : "" }}>2030</option>
                                
                                </select> 
            		    </div>
            		</div>
                        <div class="col-md-2 col-4">
                			<div class="form-group">
                				<label>{{ __('common.Month') }} </label>
                				<select class="form-control" id='date__' name="date">
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
            		    @if(Session::get('role_id') == 3)
                            <input type='hidden' class="form-control" id="class_type_id" name="class_type_id"  value="{{Session::get('class_type_id')}}"/>
                    	@else
                    	
                       <div class="col-md-2 col-4">
                    		<div class="form-group">
                    			<label>{{ __('common.Class') }}</label>
                    			<select class="form-control" id="class_type_id" name="class_type_id" >
                    			  
                    			    
                    			<option value="">{{ __('common.Select') }}</option>
                                
                                 @if(!empty($classType)) 
                                      @foreach($classType as $type)
                                         <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['class_type_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                      @endforeach
                                  @endif
                                </select>
                    	    </div>
                    	</div> 		
                    @endif
                    
                  
                </div>
                @else
                    <p class="text-center text-danger mt-2">You are not yet authorized for viewing attendance .... please contact your administrator</p>
                @endif
            
            
            <div class="col-md-12">
                <div class="flex_items">
                    <div class="instructions">
                        <span class="Present instruction_btn">P</span> Present
                    </div>
                    <div class="instructions">
                        <span class="Absent instruction_btn">A</span> Absent
                    </div>
                    <div class="instructions">
                        <span class="Holiday instruction_btn">H</span> Holiday
                    </div>
                    
                    <div class="instructions">
                        <span class="Event instruction_btn">E</span> Event
                    </div>
                    <div class="instructions">
                        <span class="Exam instruction_btn">E</span> Exam
                    </div>
                </div>
            </div>
            
            <div class="col-md-12 flex_centered">
                <div class="month_box" id="month_box" style="display:none;">
                    <p id="month_year"></p>
                </div>
            </div>
            
            <div id="calendarContainer">
            </div>
            
            <!--<button onclick="downloadCSV()">Download CSV</button>-->
              </div>
                    
          </div>
            </div>
        </div>
      </div>
    </section>
</div>



<script>
        $(document).ready(function() {
            // Function to generate a calendar with attendance marks
           
        class_type_id

         
            
        });
    </script>
<style>
    .flex_items{
        display: flex;
        align-items: center;
        justify-content: center;
        background: #002c54;
        padding: 10px;
        border-radius: 4px;
        box-shadow: 0px 4px 6px #9d9d9d;
        color:white;
    }
    
    #calendarContainer{
        height: 400px;
        overflow-y: scroll;
        overflow-x: hidden;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        padding: 0px 12px;
    }
    
    .flex_centered{
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .month_box p{
        margin-bottom: 0px;
    }
    .month_box{
        margin-top: 10px;
        width: 100%;
        max-width: 500px;
        border-radius: 4px;
        text-align: center;
        font-size: 29px;
        font-weight: 600;
        text-shadow: 4px 4px 5px #858585;
    }
    
    .instructions{
        display:flex;
        align-items:center;
    }
    
    .instruction_btn{
        padding: 10px;
        width: 40px;
        height: 40px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        margin-left: 10px;
        color:white;
    }



        /* Calendar styles */
        .container {
            text-align: center; /* Center align the calendars */
        }
        .calendar {
            border: 1px solid #ccc;
            margin-bottom: 20px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            text-align: center;
            padding:0px;
            height:auto;
        }
        .calendar-header {
            background-color: #002c54;
            color:white;
            padding: 3px;
            text-align: center;
            font-size:13px;
        }
        
        .padding_footer{
            padding: 3px 4px;
            font-size: 13px;
        }
        
        .week-symbols {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        @media only screen and (max-width: 600px) {
          .week-symbols {
            padding: 0px;
          }

          .col-6 {
              flex: 0 0 100%;
              max-width: 100%;
          }

          #calendarContainer {
              padding: 0px 4px;
              overflow-x: hidden;
          }

          .calendar {
              height: auto;
              margin-bottom: 12px;
              max-width: 100%;
          }
          
           .calendar-date {
                height: 16px !important;
                line-height: 16px !important;
                font-size: 9px !important;
              }
          
          .week-symbol {
            height: 16px !important;
            line-height: 16px !important;
            font-size: 9px !important;
          }
          
          .calendar-dates{
              gap: 1px !important;
              padding: 0px;
              grid-template-columns: repeat(7, minmax(0, 1fr));
          }

          .calendar-body {
              padding: 0px 2px;
              height: auto;
          }
          
          .padding_footer {font-size: 11px;}
          
          .instructions{
              font-size: 11px;
          }
          
          .instruction_btn{
            width: 18px;
            height: 18px;
            margin-right: 6px;
            margin-left: 4px;
            font-size: 9px;
          }
          
          .calendar_footer{
              padding: 2px 4px 4px 4px;
          }

          .month_box{
              font-size: 20px;
          }
        }
        
        .week-symbol {
            min-width: 0;
            width: 100%;
            box-sizing: border-box;
            height: 25px;
            line-height: 23px; /* Adjust line-height to vertically center text */
            margin: 1px;
            border: 1px solid #ccc;
            background-color: skyblue; /* Added background color */
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
        }
        .calendar-body {
            padding: 1px;
            text-align: center;
            height:auto;
        }
        .calendar-dates {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr)); /* 7 columns for each day of the week */
            gap: 2px;
            text-align: center;
            width: 100%;
        }
        .calendar-date {
            min-width: 0;
            width: 100%;
            box-sizing: border-box;
            height: 25px;
            line-height: 25px;
            border: 1px solid #ccc;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size:12px;
        }
        .Absent {
            background-color: red;
            color: white;
        }
        .Present {
            background-color: green;
            color: white;
        }
        .In {
            background-color: green;
            color: white;
        }
        .Out {
            background-color: green;
            color: white;
        }
        .Event {
            background-color: coral;
            color: white;
        }
        .Holiday {
            background-color: #ffc107;
            color: black;
        }
       .Exam {
            background-color: #0028ff;
            color: white;
        }
        
        
        .calendar_footer{
            margin-top:10px;
            border-top:1px solid grey;
            background: #002c54;
            color: white;
            padding: 2px 6px 6px 6px;
            position: relative;
            width: 100%;
            box-sizing: border-box;
        }


    </style>
    
   
<script>
$(document).ready(function() {
    var month = '';

    function generateCalendar(containerId, year, month, attendanceData, name, total, admissionNo) {
        var container = $("#" + containerId);
        var daysInMonth = new Date(year, month + 1, 0).getDate();
        var firstDayOfWeek = new Date(year, month, 1).getDay();
        var monthName = new Date(year, month, 1).toLocaleString('default', { month: 'long' });

        var weekSymbols = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
        var calendarHTML = '<div class="calendar">';
        calendarHTML += '<div class="calendar-header">' + name + ' ' + admissionNo + '</div>';
        calendarHTML += '<div class="week-symbols">';

        // Week symbols
        weekSymbols.forEach(function(symbol) {
            calendarHTML += '<div class="week-symbol">' + symbol + '</div>';
        });

        calendarHTML += '</div><div class="calendar-body">';
        calendarHTML += '<div class="calendar-dates">';

        // âœ… Sunday count variable
        var sundayCount = 0;

        // Date cells loop
        for (var day = 1; day <= daysInMonth; day++) {
            var dateObj = new Date(year, month, day);
            var dayOfWeek = dateObj.getDay(); // 0 = Sunday

            var dateString = year + '-' + (month + 1).toString().padStart(2, '0') + '-' + day.toString().padStart(2, '0');

            var attendanceClass = attendanceData[dateString] ? attendanceData[dateString] : '';

            // à¤…à¤—à¤° Sunday à¤¹à¥ˆ à¤”à¤° attendanceData à¤¨à¤¹à¥€à¤‚ à¤¹à¥ˆ à¤¤à¥‹ à¤¹à¥€ Sunday mark à¤•à¤°à¥‹
            if (dayOfWeek === 0 && !attendanceData[dateString]) {
                attendanceClass += ' sunday';
                sundayCount++;
            }

            calendarHTML += '<div class="calendar-date ' + attendanceClass + '">' + day + '</div>';
        }

        calendarHTML += '</div>';
 var holidayCount = (total["Holiday"] ?? 0) + sundayCount;
        // âœ… Footer à¤®à¥‡à¤‚ Sunday count à¤­à¥€ à¤¦à¤¿à¤–à¤¾à¤“
        calendarHTML += '<div class="calendar_footer">' +
            '<span class="Present padding_footer">P: ' + total["Present"] + '</span>' +
            ' <span class="Absent padding_footer">A: ' + total["Absent"] + '</span> ' +
            ' <span class="Holiday padding_footer">H: ' + holidayCount + '</span> ' +
            ' <span class="Event padding_footer">E: ' + total["Event"] + '</span> ' +
            ' <span class="Exam padding_footer">EX: ' + total["Exam"] + '</span> ' +
            '</div>';

        calendarHTML += '</div></div>';
        container.append(calendarHTML);

        $('#month_year').html(monthName + " " + year);
        $('#month_box').show();
    }

    $("#date__").on("change", function() {
        $('#class_type_id').trigger('change');
    });
    $("#year").on("change", function() {
        $('#class_type_id').trigger('change');
    });

    function admissionData(admission_id, name, admissionNo) {
        var year = $('#year').val();
        $.ajax({
            headers: { 'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content') },
            type: 'post',
            url: '/getAttendanceDates',
            data: {
                admission_id: admission_id,
                month: month,
                year: year
            },
            success: function(response) {
                console.log(response.data)
                generateCalendar("calendarContainer", year, month - 1, response.data, name, response.total, admissionNo);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    }

    var allStudents = @json($allStudents);

    function filterStudents(classTypeId, admissionNo) {
        return allStudents.filter(student => {
            const classMatch = (classTypeId === "" || student.class_type_id == classTypeId);
            const admissionMatch = (admissionNo === "" || student.admission_no == admissionNo);
            return classMatch && admissionMatch;
        });
    }

    function displayStudents(students) {
        month = parseInt($('#date__').val());
        $('#calendarContainer').html('');
        var studentsList = $('#studentsList');
        studentsList.empty();

        if (students.length === 0) {
            studentsList.append('<p>No students found.</p>');
            return;
        }

        students.forEach(student => {
            admissionData(student.id, student.first_name, student.admissionNo);
        });
    }

    $('#class_type_id').change(function() {
        var selectedClassTypeId = $(this).val();
        var admissionNo = $('#admissionNo').val();

        if (admissionNo == "" && selectedClassTypeId == "") {
            $('#calendarContainer').html('');
            $('#month_box').hide();
            toastr.error("Please Select Class");
            return;
        }

        var filteredStudents = filterStudents(selectedClassTypeId, admissionNo);

        if (filteredStudents.length == 0) {
            toastr.info("Students Not Found");
            $('#calendarContainer').html('');
            $('#month_box').hide();
            return;
        }

        if (selectedClassTypeId != '') {
            displayStudents(filteredStudents);
        } else {
            toastr.error('Please Select Class')
        }
    });

});
</script>


<script>



  
function downloadCSV() {
  let csv = [];
  const rows = document.querySelectorAll("table tr");

  for (const row of rows.values()) {
    const cells = row.querySelectorAll("td, th");
    const rowText = Array.from(cells).map((cell) => cell.innerText);
    csv.push(rowText.join(","));
  }
  const csvFile = new Blob([csv.join("\n")], {
    type: "text/csv;charset=utf-8;"
  });
  saveAs(csvFile, "Students Attendance.csv");
}

</script>        
<style>
.calendar-date.sunday {
    background-color: #ffc107;
  color: black;
    font-weight: bold;
}


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

.sticky-col {
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
}

.paddingTable{
    padding-bottom:20px;    
}
.paddingTable th,td{
    padding:10px;
}
</style>
@endsection 

