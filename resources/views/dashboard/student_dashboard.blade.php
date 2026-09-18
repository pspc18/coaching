@extends('layout.student_app') 
@section('content')
@php
$getSetting=Helper::getSetting();
$getUser=Helper::getUser();
$noticeBoard = Helper::noticeBoard();



    
$fee_assigned = DB::table('fees_assigns')->where('admission_id',Session::get('id'))->whereNull('deleted_at')->first();
$fee_collected = DB::table('fees_collect')->where('admission_id',Session::get('id'))->whereNull('deleted_at')->first();
$fee_pending = (($fee_assigned->total_amount ?? 0)-($fee_assigned->total_discount ?? 0))-($fee_collected->amount ?? 0);
$home_work= DB::table('homeworks')->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))
        ->whereDate('submission_date', '>=', date("Y-m-d"))->whereDate('homework_issue_date', '<=', date("Y-m-d"))->where('class_type_id',Session::get('class_type_id'))->whereNull('deleted_at')->orderBy('id', 'DESC')->count();
@endphp
@php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

$months = [];
$monthValues = [];
for ($month = 1; $month <= 12; $month++) {
    $monthName = Carbon::create(null, $month, 1)->format('F');
    $totalAmount = DB::table('fees_detail')->where('session_id', Session::get('session_id')) ->where('branch_id', Session::get('branch_id'))->where('admission_id',$getUser->id ) ->whereIn('status', [0,1])->whereMonth('date', $month) ->whereYear('date', date('Y'))->sum('total_amount');
    $months[] = $monthName;
    $monthValues[] = $totalAmount;
}


$years = [];
$yearValues = [];
$startYear = 2020; 
$endYear = Carbon::now()->year;

for ($year = $startYear; $year <= $endYear; $year++) {
    $totalAmount = DB::table('fees_detail') ->where('session_id', Session::get('session_id')) ->whereIn('status', [0,1])->where('admission_id',$getUser->id )->whereYear('date', $year)->sum('total_amount');
     $years[] = $year;
    $yearValues[] = $totalAmount;
}


$today = Carbon::today();
$startOfMonth = $today->copy()->startOfMonth();
$days = [];
$dayValues = [];

for ($date = $startOfMonth; $date->lte($today); $date->addDay()) {
    $day = $date->format('d-m-Y');
    $totalAmount = DB::table('fees_detail')->where('session_id', Session::get('session_id')) ->where('admission_id',$getUser->id ) ->where('branch_id', Session::get('branch_id')) ->whereIn('status', [0,1])->whereDate('date', $date->format('Y-m-d'))->sum('total_amount');
     $days[] = $day;
    $dayValues[] = $totalAmount;
}
@endphp
<div class="content-wrapper desktop">
   <section class="content pt-3">

      <div class="container-fluid">
      <div class="row ">
          <div class="col-md-12">
         <div class="student-card">
             <div class="row tshadow">
    <!-- Left Side Photo -->
    <div class="col-md-3">
    <div class="student-photo">
      <img src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$getUser['image'] }}" alt="Student Photo">
      <h3>{{$getUser['first_name'] ?? ''}}</h3>
    </div>
    </div>

    <!-- Right Side Info -->
    <div class="col-md-9">
    <div class="student-info">
      <table>
        <tr>
          <td>{{ __('student.Admission No.')  }}</td>
          <td>{{$getUser['admissionNo'] ?? ''}}</td>
          <td>{{ __('messages.Date Of  Birth')  }}</td>
          <td>@if(!empty($getUser['dob'])) {{ date('d-m-Y', strtotime($getUser['dob']))  ?? '' }} @else - @endif</td>
        </tr>
        <tr>
          <td>{{ __('messages.Fathers Name')  }}</td>
          <td>{{$getUser['father_name'] ?? ''}}</td>
          <td>Class</td>
          <td>{{$getUser['ClassTypes']['name'] ?? ''}}</td>
        </tr>
        <tr>
          <td>Mobile</td>
          <td>{{$getUser['mobile'] ?? ''}}</td>
         <td>{{ __('Blood Group')  }}</td>
                                    <td>{{$getUser->blood_group ?? ''}}</td>
                            
        </tr>
        <tr>
          <td>Gender</td>
          <td>{{$getUser['Gender']['name'] ?? ''}}</td>
          <td>Aadhar Number</td>
          <td>{{$getUser->aadhaar ?? ''}}</td>
        </tr>
      </table>
    </div>
    </div>
    </div>
  </div>
</div>
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          
          

      <div class="col-12 col-sm-6 col-md-4">

      <div class="row">
          
    <div class="col-md-12" id="calendarElement">

    </div>

 
      @if(count($noticeBoard) > 0) 
<div class="col-md-12">
<div class="card card-dark">
<div class="card-header">
   <h3 class="card-title"><i class="fa fa-bell"> {{ __('Notice Board') }}</i> </h3>
  
</div>
<div class="">
   <marquee direction="up" scrollamount="4" id="newnotic" onMouseOver="document.all.newnotic.stop()"
       onMouseOut="document.all.newnotic.start()">
       <ul class="todo-list ui-sortable" data-widget="todo-list">
          @if(!empty($noticeBoard))
           @foreach($noticeBoard as $item)
        
           <li class="">
             <a target='blank' href="{{ url('notice_board/view') }}/{{$item->id}}">
                  <span class="text font-weight-bold"> {!! html_entity_decode($item->title ?? '', ENT_QUOTES, 'UTF-8') !!} </span><br>
                  <span class="text text-dark"> {!! html_entity_decode($item->message ?? '', ENT_QUOTES, 'UTF-8') !!} </span>
                   <small class="badge badge-danger"><i class="fa fa-envelope-o"></i>
                       New</small>
               </a>
           </li>
           @endforeach
           @endif
       </ul>
   </marquee>
</div>

</div>

</div>
@endif
      </div>
      </div>
    </div>
    
        <div class="row">
         </div> 
    </div>
    </section>
</div>
        <div class="mobile_view">
          
  <div class="card text-center border-bottom" style="width: 96%; margin: auto; background: white;box-shadow:0 0 1px rgba(0, 0, 0, .125), 0 15px 10px rgba(0, 0, 0, .2);">
    <div class="d-flex  p-2">
        <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] ?? '' }}" 
             alt="" class="brand_img me-2" 
             onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/no_image.png' }}'" 
             width="50px" height="50px" style="border-radius:50%;">
        <div class="ml-2">
            <h5 class="m-0 fw-bold p-0" style="color:orange;">{{$getSetting->name ?? '' }}</h5>
            <p class="m-0 text-dark small" style="text-align: left;">{{$getSetting->gmail ?? '' }}</p>
        </div>
    </div>
</div>

    <!-- Dashboard Grid -->
    <div class="container py-4">
        <div class="row g-3 text-center">
            <!-- Icon Card Example -->
           <div class="col-3 mt-3">
                <a href="{{ url('fees_history')}}" class="text-dark">
                    <div class="dashboard-card bg-blue text-white">
                        <i class="fa fa-money"></i>
                    </div>
                    <p class="mt-2">Fees History</p>
                </a>
            </div>
            
            <div class="col-3 mt-3">
                <a href="{{ url('notice_board/view/0')}}" class="text-dark">
                    <div class="dashboard-card bg-green text-white">
                        <i class="fa fa-envelope"></i>
                    </div>
                    <p class="mt-2">Notice Board</p>
                </a>
            </div>
            
            <div class="col-3 mt-3">
                <a href="{{ url('studentsAttendanceView') }}" class="text-dark">
                    <div class="dashboard-card bg-purple text-white">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <p class="mt-2">Attendance</p>
                </a>
            </div>
            
            <div class="col-3 mt-3">
                <a href="{{ url('teachers/index') }}" class="text-dark">
                    <div class="dashboard-card bg-orange text-white">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <p class="mt-2">My Teachers</p>
                </a>
            </div>
            
            <div class="col-3 mt-3">
                <a href="{{ url('my_exams') }}" class="text-dark">
                    <div class="dashboard-card bg-teal text-white">
                        <i class="fa fa-book"></i>
                    </div>
                    <p class="mt-2">My Exam</p>
                </a>
            </div>
            
            <div class="col-3 mt-3">
                <a href="{{ url('student_homework') }}" class="text-dark">
                    <div class="dashboard-card bg-red text-white">
                        <i class="fa fa-tasks"></i>
                    </div>
                    <p class="mt-2">Home Work</p>
                </a>
            </div>
            
            <div class="col-3 mt-3">
                <a href="{{ url('timetable') }}" class="text-dark">
                    <div class="dashboard-card bg-pink text-white">
                        <i class="fa fa-calendar-plus-o"></i>
                    </div>
                    <p class="mt-2">Time Table</p>
                </a>
            </div>
            
            <div class="col-3 mt-3">
                <a href="{{ url('student_subject_view') }}" class="text-dark">
                    <div class="dashboard-card bg-yellow text-white">
                        <i class="fa fa-book"></i>
                    </div>
                    <p class="mt-2">Subjects</p>
                </a>
            </div>
            
            <div class="col-3 mt-3">
                <a href="{{ url('download_center') }}" class="text-dark">
                    <div class="dashboard-card bg-green text-white">
                        <i class="fa fa-download"></i>
                    </div>
                    <p class="mt-2">Download Center</p>
                </a>
            </div>
            
            <div class="col-3 mt-3">
                <a href="{{ url('gallery_view') }}" class="text-dark">
                    <div class="dashboard-card bg-blue text-white">
                        <i class="fa fa-image"></i>
                    </div>
                    <p class="mt-2">Gallery</p>
                </a>
            </div>
            
            <div class="col-3 mt-3">
                <a href="{{ url('rule_view') }}" class="text-dark">
                    <div class="dashboard-card bg-orange text-white">
                        <i class="fa fa-check-square"></i>
                    </div>
                    <p class="mt-2">Rules</p>
                </a>
            </div>
            <!-- Add more icons here -->
        </div>
    </div>


<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="card card-danger">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h3 class="card-title collection">
                            <i class="fa fa-money mr-1"></i> My Fees Deposit Chart
                        </h3>
                        <div class="card-tools">
      
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button id="yearly" class="btn btn-primary btn-xs">Yearly</button>
                        <button id="monthly" class="btn btn-primary btn-xs">Monthly</button>
                        <button id="7_days" class="btn btn-primary btn-xs">Days</button>
                    </div>
                </div>
                <div class="card-body p-0" id="chart-container">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>





    <!-- Bottom Navigation -->
   
     


</div>

<script>
$(document).ready(function() {
    // Convert PHP arrays to JS
    let monthlyData = {
        val1: @json($months),
        val2: @json($monthValues)
    };

    let weeklyData = {
        val1: @json($days),
        val2: @json($dayValues)
    };

    let yearlyData = {
        val1: @json($years),
        val2: @json($yearValues)
    };

    chartData(monthlyData);

    $('#7_days').click(function() {
        resetChart();
        chartData(weeklyData);
        $('.collection').html(`<i class="fa fa-money mr-1"></i> Weekly Fee Deposit`);
    });

    $('#monthly').click(function() {
        resetChart();
        chartData(monthlyData);
        $('.collection').html(`<i class="fa fa-money mr-1"></i> Monthly Fee Deposit`);
    });

    $('#yearly').click(function() {
        resetChart();
        chartData(yearlyData);
        $('.collection').html(`<i class="fa fa-money mr-1"></i> Yearly Fee Deposit`);
    });

    function resetChart() {
        $('#myChart').remove();
        $('#chart-container').append('<canvas id="myChart"></canvas>');
    }

    function chartData(val) {
        let chartElement = document.getElementById('myChart');
        if (!chartElement) {
            return;
        }
        let ctx = chartElement.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: val.val1,
                datasets: [{
                    label: 'Fee Amount',
                    data: val.val2,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });
    }
});
</script>

<script>
var currentYear;
var currentMonth;

document.addEventListener('DOMContentLoaded', function() {
    var now = new Date();
    currentYear = now.getFullYear();
    currentMonth = now.getMonth();

    // Fetch events and render calendar
    updateCalendar();
});

function updateCalendar() {
    getEvents().then(function(events) {
        renderCalendar(currentYear, currentMonth, events);
    }).catch(function(error) {
        console.error('Error fetching events:', error);
    });
}

function renderCalendar(year, month, events) {
    var monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    var daysInMonth = new Date(year, month + 1, 0).getDate(); // Get the number of days in the current month

    // Display month and year
    document.getElementById('monthYear').textContent = monthNames[month] + ' ' + year;

    var firstDayOfMonth = new Date(year, month, 1).getDay(); // Get the day of the week of the first day of the month (0 = Sunday)
    var calendarBody = document.getElementById('calendarBody');
    calendarBody.innerHTML = '';

    var date = 1;
    var eventsByDate = {};

    // Organize events by date for quick lookup
    events.forEach(function(event) {
        var eventDate = new Date(event.date);
        if (eventDate.getFullYear() === year && eventDate.getMonth() === month) {
            var day = eventDate.getDate();
            if (!eventsByDate[day]) {
                eventsByDate[day] = [];
            }
            eventsByDate[day].push({
                event: event.event,
                attendanceStatus: event.attendance_status
            });
        }
    });

    // Create rows and cells for the calendar
    for (var i = 0; i < 6; i++) { // 6 weeks maximum
        var row = calendarBody.insertRow();

        for (var j = 0; j < 7; j++) { // 7 days (columns) per row
            var cell = row.insertCell();
            if (i === 0 && j < firstDayOfMonth) {
                // Empty cells before the first day of the month
                continue;
            }
            if (date > daysInMonth) {
                // Stop if all days of the month have been displayed
                break;
            }
            cell.textContent = date;

            // Add 'today' class to cell if it's today's date
            if (date === (new Date()).getDate() && month === (new Date()).getMonth() && year === (new Date()).getFullYear()) {
                cell.classList.add('today');
            }

            // Add event information to cell if there are events on this date
            if (eventsByDate[date]) {
                cell.classList.add('event-cell'); // Add class for event cell
                var eventList = document.createElement('ul');
                eventsByDate[date].forEach(function(event) {
                    var eventItem = document.createElement('li');
                    eventItem.textContent = event.event;
                    eventList.appendChild(eventItem);

                    // Color the cell based on attendance status
                    switch(event.attendanceStatus) {
                        case 1:
                            cell.classList.add('bg-success'); // Color for 'Present'
                            break;
                        case 3:
                            cell.classList.add('bg-danger');// Color for 'Absent'
                            break;
                        case 5:
                             cell.classList.add('bg-warning'); // Color for 'holiday'
                            break;
                        case 9:
                           cell.classList.add('bg-info'); // Color for 'leave'
                            break;
                        case 10:
                            cell.style.backgroundColor = 'coral'; // Color for 'event'
                            break;
                        default:
                            cell.style.backgroundColor = ''; // Default color
                            break;
                    }
                });
                cell.appendChild(eventList);
            }

            // Add event listener to each date cell
            cell.addEventListener('click', function() {
                var clickedDate = this.textContent;
                var clickedMonth = month;
                var clickedYear = year;
                toastr.info('Clicked Date : ' + clickedDate + ' ' + monthNames[clickedMonth] + ' ' + clickedYear);
                // You can perform additional actions here based on the clicked date
                // For example, display more information, open a modal, etc.
            });

            date++;
        }
        if (date > daysInMonth) {
            // Stop creating rows if all days of the month have been displayed
            break;
        }
    }
}

function prevMonth() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    updateCalendar();
}

function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    updateCalendar();
}

function getEvents() {
    return new Promise(function(resolve, reject) {
        $.ajax({
            headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            type: 'get',
            url: 'getEvents',
            success: function(response) {
                resolve(response.data);
            },
            error: function(error) {
                reject(error);
            }
        });
    });
}
</script>
<style>

body {
    background-color: #f7f7f7;
    font-family: Arial, sans-serif;
}

.dashboard-card {
    width: 65px;
    height: 65px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: auto;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
}

/* Custom background colors */
.bg-blue { background-color: #007bff; }
.bg-green { background-color: #28a745; }
.bg-orange { background-color: #fd7e14; }
.bg-red { background-color: #dc3545; }
.bg-purple { background-color: #6f42c1; }
.bg-teal { background-color: #20c997; }
.bg-pink { background-color: #e83e8c; }
.bg-yellow { background-color: #ffc107; }

.navbar .fa-bars {
    font-size: 20px;
}



.event-cell {
    background-color: #999; /* Light blue background for event cells */
    color: #fff; /* Light blue background for event cells */
 
    position: relative;
}

/*.event-cell::after {*/
/*    content: 'ðŸŽ‰'; */
/*    position: absolute;*/
/*    right: 5px;*/
/*    bottom: 5px;*/
/*    font-size: 0.8em;*/
/*    color: #ff4500; */
/*}*/

.card-header .nav-pills .nav-link {
  color: #db5b06;
}
.card-dark:not(.card-outline) > .card-header {
    background-color: #002c54 !important;
    border-top: 3px solid #fd7e14;
    border-radius: 5px 5px 0px 0px;
}


.calendar td {
    cursor: pointer;
    border: 1px dotted grey;
}
.calendar {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 5px;
        }
        .month-year {
            text-align: center;
            width: 150px;
        }
      
        td.today {
            background-color: #007bff;
            color: #fff;
        }
      
        .btn-container {
            display: flex;
            justify-content: center; /* Center align child elements horizontally */
            align-items: center; /* Center align child elements vertically */
            margin-bottom: 10px;
        }
        .btn1 {
            display: inline-block;
            padding: 0px 9px 5px;
            font-size: 24px;
            cursor: pointer;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            margin: 0 1px; /* Add spacing between buttons */
        }
        .btn1:hover {
            background-color: #0056b3;
        }
          #calendarTable ul {
            list-style-type: none;
            padding:0px;
            font-size:10px;
        }
           #calendarTable td {
            text-align: center;
          
            padding: 0px;
            border-bottom: 1px solid #ddd;
        }
       
        .mobile_view{
        display:none;
        }
    @media only screen and (max-width: 600px){
        body{
            background-color: ghostwhite;
        }
        .main-header{
            box-shadow: 0 5px 5px rgba(0, 0, 0, 0.1);
        }
        .desktop{
            display:none;
        }

       .mobile_view{
        display:block;
        }
        .fees_detail{
            background-color: #1f2d3d;
             /*border-radius: 10px;*/
             color: white;
             font-size:13px;
             margin-top:40px;
 
        }
        .fees_detail a{
            /*display:flex;*/
            /*justify-content:space-around;*/
            color: white;
            /*padding: 13px;*/
            text-align:center;
            /*height:100px;*/
        }
        .fees_detail span{
            margin:auto 0px;
            border-radius:40px;
        }
        .section-heading-container{
            width: 100%;
            padding-right: 0px; 
            padding-left: 0px; 
            margin-right: auto; 
            margin-left: auto;
        }
        .section-heading{
            font-size:23px !important;
            background-color:#ffffff;
            padding: 10px 0px 10px 0px;
            box-shadow: 0 5px 5px rgba(0, 0, 0, 0.1);
            /*border-radius:10px;*/
          
        }
       .info-box .info-box-icon{
           height:35px;
           width:35px;
       }
        .academic_info{
            font-size:10px;
        }
        .fees_detail span i{
            font-size: 30px;
            padding: 10px;
        }
        .info-box{
            box-shadow: none !important;
            border-radius: 1.25rem;
            /*background-color: #ffffff !important;*/
            background-color: #f8f8ff !important;
            padding: .5rem;
            height: 80px !important;
            width:80px;
            display:block;
            margin-left: 5px;
        }
        .info-box-icon{
            margin: 0px auto;
        }
        .info-box p{
            text-align:center;
            font-size: 10px;
            font-weight:bold;
            margin-top: 5px;
        }
        .info-box .info-box-icon i{
            font-size:22px !important;
            color: black;
        }
        .elevation-1{
            background-color:#f8f8ff !important;
        }
        #carouselExampleIndicators{
            display:block !important;
            height: 70px ;
          
        }
        #carouselExampleIndicators img {
            height: 100px;
        }
        p{
            margin: 0 !important;
        }
        .carousel-indicators{
            top:90px;
        }
        /*.carousel-item{*/
        /*    transition: transform .9s ease !important;*/
        /*  transition: transform .9s ease, -webkit-transform .6s ease !important;*/
        /*}*/
        }
        
            .student-card {
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 15px;
      align-items: flex-start;
      background: #fff;
      box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
    }
    .student-photo {
      text-align: center;
      margin: 15px;
    }
    .student-photo img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
    }
    .student-photo h3 {
      margin-top: 10px;
      font-size: 16px;
      font-weight: bold;
    }
    .student-info {
      flex: 1;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    td {
      border: 1px solid #ddd;
      padding: 8px 12px;
      font-size: 14px;
    }
    td:nth-child(odd) {
      background: #f0f0ff;
      font-weight: bold;
      width: 25%;
    }
    
    .student-photo img {
  width: 100px;
  height: 115px;
  max-width: 100px;
  padding: 5px;
  border: 2px solid #dadada;
  background: white;
  border-radius: 12px;
  object-fit: cover;
  object-position: top;
}

.tshadow {
  border: 1px solid #e9e9e9;
  margin-bottom: 15px;
  padding: 12px 15px;
}
    </style>
    

@endsection
 
