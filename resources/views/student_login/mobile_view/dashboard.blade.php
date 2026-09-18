@extends('layout.app') 
@section('content')
@php
$getSetting=Helper::getSetting();
$getUser=Helper::getUser();
$noticeBoard = Helper::noticeBoard();
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
<div class="mobile_view">
          
  <div class="card text-center border-bottom" style="width: 96%; margin: auto; background: white;box-shadow:0 0 1px rgba(0, 0, 0, .125), 0 15px 10px rgba(0, 0, 0, .2);">
    <div class="d-flex  p-2">
        <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] ?? '' }}" 
             alt="" class="brand_img me-2" 
             onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/no_image.png' }}'" 
             width="50px" height="50px" style="border-radius:50%;">
        <div class="ml-2">
            <h6 class="m-0 fw-bold p-0" style="color:orange;">{{$getSetting->name ?? '' }}</h6>
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
                <a href="{{ url('notice_board_student/0')}}" class="text-dark">
                    <div class="dashboard-card bg-green text-white">
                        <i class="fa fa-envelope"></i>
                    </div>
                    <p class="mt-2">Notice Board</p>
                </a>
            </div>
            
            <div class="col-3 mt-3">
                <a href="{{ url('AttendanceView_student') }}" class="text-dark">
                    <div class="dashboard-card bg-purple text-white">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <p class="mt-2">Attendance</p>
                </a>
            </div>
            
            <div class="col-3 mt-3">
                <a href="{{ url('teachers_student/index') }}" class="text-dark">
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
        let ctx = document.getElementById('myChart').getContext('2d');
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

<style>
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


.bg-blue { background-color: #007bff; }
.bg-green { background-color: #28a745; }
.bg-orange { background-color: #fd7e14; }
.bg-red { background-color: #dc3545; }
.bg-purple { background-color: #6f42c1; }
.bg-teal { background-color: #20c997; }
.bg-pink { background-color: #e83e8c; }
.bg-yellow { background-color: #ffc107; }
</style>
@endsection