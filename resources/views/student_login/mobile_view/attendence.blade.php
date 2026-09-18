@extends('layout.app') 
@section('content')
@php
$attendanceType = Helper::attendanceType();
  $classType = Helper::classType();
  $getPermission = Helper::getPermission();
@endphp
<link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="https://adminlte.io/themes/v3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">


 <div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                    <div class="card-header bg-primary">
                        <h3 class="card-title"><i class="fa fa-calendar-minus-o"></i> &nbsp;{{ __('Academic Attendance Calendar') }}</h3>
                    </div>      
                    
             
            
            
            <div class="col-md-12 mt-2 col-12">
                <div class="flex_items">
                    <div class="instructions">
                        <span class="Present instruction_btn">P</span> Present asdsa
                    </div>
                    <div class="instructions">
                        <span class="Absent instruction_btn">A</span> Absent
                    </div>
                    <div class="instructions">
                        <span class="Holiday instruction_btn">H</span> Holiday
                    </div>
                   
                    <div class="instructions touch">
                        <span class="Event instruction_btn">E</span> Event
                    </div>
                    <div class="instructions touch">
                        <span class="Exam instruction_btn">EX</span> Exam
                    </div>
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
        padding: 0px 8px;
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
            display: block;
            vertical-align: top;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
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
            font-size: 12px;
        }
        
        .week-symbols {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 2px;
            margin-bottom: 5px;
        }
        
        @media only screen and (max-width: 600px) {
          .week-symbols {
            padding: 0px;
          }
          
          .calendar-date {
              height: auto !important;
              min-height: 42px;
              aspect-ratio: 1 / 1;
              font-size: 12px !important;
          }
          
          .week-symbol {
            width: 100% !important;
            height: 22px !important;
            font-size: 12px !important;
          }
          
          .flex_items {
           display: none;
          }
          
          .calendar-dates{
              gap: 3px !important;
              padding: 0;
              margin: 0;
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
              padding: 0 4px;
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
            box-sizing: border-box;
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
              background-color: #e83e8c;
              color: white;
            }
        .Exam {
            background-color: #0028ff;
            color: white;
        }
        .Holiday {
            background-color: #ffc107;
            color: black;
        }
        
        
        .calendar_footer{
            margin-top:10px;
            border-top:1px solid grey;
            background: #002c54;
            color: white;
            padding: 2px 6px 6px 6px;
            position: relative;
            bottom: auto;
            width: 100%;
            box-sizing: border-box;
        }
        


    </style>
    
   
<script>
$(document).ready(function() {
    var id = "{{Session::get('id')}}";

    function generateCalendar(containerId, year, month, attendanceData, name, total) {
        var container = $("#" + containerId);
        var daysInMonth = new Date(year, month, 0).getDate();
        var firstDayOfWeek = new Date(year, month - 1, 1).getDay(); 
        var monthName = new Date(year, month - 1, 1).toLocaleString('default', { month: 'long' });

        var weekSymbols = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];

        var calendarHTML = '<div class="calendar col-md-2 col-12">';
        calendarHTML += '<div class="calendar-header">' + monthName + ' ' + year + '</div>';
        calendarHTML += '<div class="week-symbols">';

        // Week symbols
        weekSymbols.forEach(function(symbol) {
            calendarHTML += '<div class="week-symbol">' + symbol + '</div>';
        });

        calendarHTML += '</div><div class="calendar-body">';
        calendarHTML += '<div class="calendar-dates">';

        let sundayCount = 0; // Sunday counter

        // Empty cells before 1st day
        for (var i = 0; i < firstDayOfWeek; i++) {
            calendarHTML += '<div class="calendar-date empty"></div>';
        }

     // Date cells
for (var day = 1; day <= daysInMonth; day++) {
    var dateObj = new Date(year, month - 1, day); 
    var dayOfWeek = dateObj.getDay(); // 0 = Sunday

    var dateString = year + '-' + month.toString().padStart(2, '0') + '-' + day.toString().padStart(2, '0');

    var attendanceClass = attendanceData[dateString] ? attendanceData[dateString] : '';

    // अगर Sunday है और attendanceData नहीं है तो ही Sunday mark करो
    if (dayOfWeek === 0 && !attendanceData[dateString]) {
        attendanceClass += ' sunday';
        sundayCount++; // Sunday count बढ़ाओ (केवल बिना attendance वाले Sundays)
    }

    calendarHTML += '<div class="calendar-date ' + attendanceClass + '">' + day + '</div>';
}

        calendarHTML += '</div>';

        // Footer counts (Sunday को Holiday count में जोड़ो)
        var holidayCount = (total["Holiday"] ?? 0) + sundayCount;

        calendarHTML += '<div class="calendar_footer">';
        calendarHTML += '<span class="Present padding_footer">P: ' + (total["Present"] ?? 0) + '</span>';
        calendarHTML += ' <span class="Absent padding_footer">A: ' + (total["Absent"] ?? 0) + '</span> ';
        calendarHTML += ' <span class="Holiday padding_footer">H: ' + holidayCount + '</span> ';
        calendarHTML += ' <span class="Event padding_footer">E: ' + (total["Event"] ?? 0) + '</span>';
        calendarHTML += ' <span class="Exam padding_footer">EX: ' + (total["Exam"] ?? 0) + '</span></div>';
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
                month: month
            }
        });
    }

    function processMonthsSequentially(admission_id, name) {
        var startYear = 2025; // Session Start Year
        var endYear = 2026;   // Session End Year

        // April से दिसंबर -> startYear
        var months1 = Array.from({ length: 9 }, (_, i) => i + 4); // [4..12]

        // जनवरी से मार्च -> endYear
        var months2 = Array.from({ length: 3 }, (_, i) => i + 1); // [1..3]

        var containerId = "calendarContainer";

        // पहले startYear (April–Dec)
        months1.reduce((promise, month) => {
            return promise.then(() => {
                return admissionData(month, admission_id, name).done(response => {
                    generateCalendar(containerId, startYear, month, response.data, name, response.total);
                });
            });
        }, Promise.resolve())
        .then(() => {
            // फिर endYear (Jan–Mar)
            return months2.reduce((promise, month) => {
                return promise.then(() => {
                    return admissionData(month, admission_id, name).done(response => {
                        generateCalendar(containerId, endYear, month, response.data, name, response.total);
                    });
                });
            }, Promise.resolve());
        });
    }

    // Start processing
    processMonthsSequentially(id, '...');
});
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
