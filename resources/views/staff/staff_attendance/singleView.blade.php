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
                        <h3 class="card-title"><i class="fa fa-calendar-minus-o"></i> &nbsp;{{Session::get('first_name') ?? ''}}'s MonthWise Attendance</h3>
                        <div class="card-tools">
                            @if(Session::get('role_id') !== 3)
                                <a href="{{url('staff_attendance_add')}}" class="btn btn-primary {{($getPermission->add == 1) ? '' : 'd-none'}} btn-sm" ><i class="fa fa-plus"></i>{{ __('common.Add') }}  </a>
                                <a href="{{url('studentsDashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a>
                            @endif
                        </div>
                    </div>      
                    
             
            
            
            <div class="col-md-12 mt-2">
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
                        <span class="Leave instruction_btn">L</span> Leave
                    </div>
                    <div class="instructions touch">
                        <span class="Event instruction_btn">E</span> Event
                    </div>
                </div>
            </div>
            
            <div class="row" id="calendarContainer">
            </div>
            
            <!--<button onclick="downloadCSV()">Download CSV</button>-->
              </div>
                    
          </div>
            </div>
        </div>
      </div>
    </section>
</div>


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
        padding: 0px 16px;
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
        width: 500px;
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
            display: inline-block;
            vertical-align: top;
            width: 100%;
            /*max-width: 200px;*/
            text-align: center;
            padding:0px;
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
          
           .calendar-date {
                width: 20px !important;
                height: 20px !important;
                font-size: 10px !important;
              }
          
          .week-symbol {
            width: 20px !important;
            height: 20px !important;
            font-size: 10px !important;
          }
          
          .calendar-dates{
              gap: 4px !important;
              padding: 0px;
          }
          
          .padding_footer {font-size: 11px;}
          
          .instructions{
              font-size: 11px;
          }
          
          .instruction_btn{
            width: 20px;
            height: 20px;
            margin-right: 8px;
            margin-left: 5px;
            font-size: 10px;
          }
          
          #calendarContainer{
              height:500px;
          }
        }
        
        .week-symbol {
            width: 25px;
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
            height:195px;
        }
        .calendar-dates {
            display: grid;
            grid-template-columns: repeat(7, 1fr); /* 7 columns for each day of the week */
            gap: 2px;
            text-align: center;
        }
        .calendar-date {
            width: 25px;
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
        .Event {
            background-color: coral;
            color: white;
        }
        .Holiday {
            background-color: #ffc107;
            color: black;
        }
        .Leave {
            background-color: #17a2b8;
            color: white;
        }
        
        .calendar_footer{
            margin-top:10px;
            border-top:1px solid grey;
            background: #002c54;
            color: white;
            padding: 2px 6px 6px 6px;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        


    </style>
    
   
    <script>
$(document).ready(function() {
    var id = "{{Session::get('teacher_id')}}";

    function generateCalendar(containerId, year, month, attendanceData, name, total) {
        var container = $("#" + containerId);
        var daysInMonth = new Date(year, month, 0).getDate();
        var firstDayOfWeek = new Date(year, month - 1, 1).getDay(); // Adjusted to get the correct first day
        var monthName = new Date(year, month - 1, 1).toLocaleString('default', { month: 'long' });

        var weekSymbols = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];

        var calendarHTML = '<div class="calendar col-md-2 col-6">';
        calendarHTML += '<div class="calendar-header">' + monthName + ' ' + year + '</div>';
        calendarHTML += '<div class="week-symbols">';

        // Add week symbols
        weekSymbols.forEach(function(symbol) {
            calendarHTML += '<div class="week-symbol">' + symbol + '</div>';
        });

        calendarHTML += '</div><div class="calendar-body">';
        calendarHTML += '<div class="calendar-dates">';

        // Fill in the days before the first day of the month
        for (var i = 0; i < firstDayOfWeek; i++) {
            calendarHTML += '<div class="calendar-date empty"></div>';
        }

        // Create date cells
        for (var day = 1; day <= daysInMonth; day++) {
            var dateString = year + '-' + month.toString().padStart(2, '0') + '-' + day.toString().padStart(2, '0');
            var attendanceClass = attendanceData[dateString] ? attendanceData[dateString] : '';
            calendarHTML += '<div class="calendar-date ' + attendanceClass + '">' + day + '</div>';
        }

        calendarHTML += '</div>';
        calendarHTML += '<div class="calendar_footer">';
        calendarHTML += '<span class="Present padding_footer">P: ' + total["Present"] + '</span>';
        calendarHTML += ' <span class="Absent padding_footer">A: ' + total["Absent"] + '</span> ';
        calendarHTML += ' <span class="Leave padding_footer">L: ' + total["Leave"] + '</span> ';
        calendarHTML += ' <span class="Holiday padding_footer">H: ' + total["Holiday"] + '</span> ';
        calendarHTML += ' <span class="Event padding_footer">E: ' + total["Event"] + '</span></div>';
        calendarHTML += '</div></div>';
        container.append(calendarHTML);
    }

    function admissionData(month, admission_id, name) {
        return $.ajax({
            headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            type: 'post',
            url: '/getAttendanceDates',
            data: {
                admission_id: admission_id,
                month: month,
                user:'Teacher'
            }
        });
    }

    function processMonthsSequentially(admission_id, name) {
        var currentYear = 2025;
        var months = Array.from({ length: 12 }, (_, i) => i + 1); // [1, 2, 3, ..., 12]
        var containerId = "calendarContainer";

        // Chain AJAX calls sequentially
        months.reduce((promise, month) => {
            return promise.then(() => {
                return admissionData(month, admission_id, name).done(response => {
                    generateCalendar(containerId, currentYear, month, response.data, name, response.total);
                });
            });
        }, Promise.resolve());
    }

    // Start processing
    processMonthsSequentially(id, '...');
});


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
 .In {
            background-color: green;
            color: white; 
        }
</style>
@endsection 