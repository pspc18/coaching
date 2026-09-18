@php
$student_count = Helper::getCount('admissions','id','count');
$user_count = Helper::getCount('users','id','count');
$noticeBoard = Helper::noticeBoard();
$task = Helper::task();
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper" >

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-book"></i> &nbsp; Library Admin Dashboard</h3>
                    <div class="card-tools">
                        <!--<a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add User</a>-->
                    </div>
            
                </div>               
            </div>
            </div>
        </div>
        
        <div class="row">
            
            <div class="col-6 col-sm-4 col-md-3">
                <form id="myForm" action="{{ url('library_student_view') }}" method="post">
                    @csrf  
                        <div class="info-box mb-3 text-dark">
                            <input type="hidden" class="form-control" id="filter" name="filter" value="active_user">
                            <span class="info-box-icon bg-success elevation-1"><i class="fa fa-users"></i></span>
                            <div class="info-box-content">
                                <button type="submit" class="bg-white" style="border:hidden;text-align:left"> <span class="info-box-text">Active Users</span></button>
                                <span class="info-box-number">{{\App\Models\library\LibraryAssign::countData()['active_user'] }} </span>
                            </div>
        
                        </div>
                </form>

            </div> 
            <div class="col-6 col-sm-4 col-md-3 ">
                <form id="myForm" action="{{ url('library_student_view') }}" method="post">
                    @csrf
                    <input type="hidden" class="form-control" id="filter" name="filter" value="expired_last_15_days">
                    <div class="info-box mb-3 text-dark">
                       <span class="info-box-icon bg-danger elevation-1"><i class="fa fa-users"></i></span>
                        <div class="info-box-content">
                        <button type="submit" class="bg-white" style="border:hidden;text-align:left"> <span class="info-box-text">Expired Last 15 Days</span></button>
                        <span class="info-box-number">{{\App\Models\library\LibraryAssign::countData()['expired_last_15_days'] }}</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <form id="myForm" action="{{ url('library_student_view') }}" method="post">
                    @csrf
                    <input type="hidden" class="form-control" id="filter" name="filter" value="expiring_7_days">
                        <div class="info-box mb-3 text-dark">
                            <span class="info-box-icon bg-danger elevation-1"><i class="fa fa-users"></i></span>
                            <div class="info-box-content">
                                <button type="submit" class="bg-white" style="border:hidden;text-align:left"> <span class="info-box-text">Expiring Last 7 Days</span></button>
                                <span class="info-box-number">{{\App\Models\library\LibraryAssign::countData()['expiring_7_days'] }} </span>
                            </div>
                        </div>
                </form>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <form id="myForm" action="{{ url('library_student_view') }}" method="post">
                    @csrf
                    <input type="hidden" class="form-control" id="filter" name="filter" value="expired_yesterday">
                    <div class="info-box mb-3 text-dark">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fa fa-users"></i></span>
                        <div class="info-box-content">
                            <button type="submit" class="bg-white" style="border:hidden;text-align:left"> <span class="info-box-text">Expired Yesterday</span></button>
                        <span class="info-box-number">{{\App\Models\library\LibraryAssign::countData()['expired_yesterday'] }}</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <form id="myForm" action="{{ url('library_student_view') }}" method="post">
                    @csrf
                    <input type="hidden" class="form-control" id="filter" name="filter" value="expiring_today">
                        <div class="info-box mb-3 text-dark">
                            <span class="info-box-icon bg-danger elevation-1"><i class="fa fa-users"></i></span>
                            <div class="info-box-content">
                                <button type="submit" class="bg-white" style="border:hidden;text-align:left"> <span class="info-box-text">Expiring Today</span></button>
                            <span class="info-box-number">{{\App\Models\library\LibraryAssign::countData()['expiring_today'] }}</span>
                            </div>
                        </div>
                </form>
            </div>  
            
             
            <div class="col-12 col-sm-6 col-md-3">
                <form id="myForm" action="{{ url('library_student_view') }}" method="post">
                    @csrf
                    <input type="hidden" class="form-control" id="filter" name="filter" value="new_student_today">
                        <div class="info-box mb-3 text-dark">
                            <span class="info-box-icon bg-success elevation-1"><i class="fa fa-users"></i></span>
                            <div class="info-box-content">
                                <button type="submit" class="bg-white" style="border:hidden;text-align:left"> <span class="info-box-text">New Student Today</span></button>
                            <span class="info-box-number">{{\App\Models\library\LibraryAssign::countData()['new_student_today'] }} </span>
                            </div>
                        </div>
                </form>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <form id="myForm" action="{{ url('library_student_view') }}" method="post">
                    @csrf
                    <input type="hidden" class="form-control" id="filter" name="filter" value="new_student_yesterday">
                    <div class="info-box mb-3 text-dark">
                        <span class="info-box-icon bg-success elevation-1"><i class="fa fa-users"></i></span>
                        <div class="info-box-content">
                        <button type="submit" class="bg-white" style="border:hidden;text-align:left"> <span class="info-box-text">New Student Yesterday</span></button>
                        <span class="info-box-number">{{\App\Models\library\LibraryAssign::countData()['new_student_yesterday'] }}  </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <form id="myForm" action="{{ url('library_student_view') }}" method="post">
                    @csrf
                    <input type="hidden" class="form-control" id="filter" name="filter" value="new_student_this_month">
                    <div class="info-box mb-3 text-dark">
                        <span class="info-box-icon bg-success elevation-1"><i class="fa fa-users"></i></span>
                        <div class="info-box-content">
                            <button type="submit" class="bg-white" style="border:hidden;text-align:left"> <span class="info-box-text">New Student This Month</span></button>
                        <span class="info-box-number">{{\App\Models\library\LibraryAssign::countData()['new_student_this_month'] }} </span>
                        </div>
                    </div>
                </form>
            </div>
             <div class="col-12 col-sm-6 col-md-3">
                <form id="myForm" action="{{ url('library_student_view') }}" method="post">
                    @csrf
                    <input type="hidden" class="form-control" id="filter" name="filter" value="new_student_last_month">
                    <div class="info-box mb-3 text-dark">
                        <span class="info-box-icon bg-success elevation-1"><i class="fa fa-users"></i></span>
                        <div class="info-box-content">
                        <button type="submit" class="bg-white" style="border:hidden;text-align:left"> <span class="info-box-text">New Student Last Month</span></button>
                        <span class="info-box-number"> {{\App\Models\library\LibraryAssign::countData()['new_student_last_month'] }}</span>
                        </div>
                    </div>
                </form>
            </div>
            
            
            <div class="col-12 col-sm-6 col-md-3">
                <a href="{{ url('expenseView') }}">
                <div class="info-box mb-3 text-dark">
                    <span class="info-box-icon bg-success elevation-1"><i class="fa fa-credit-card"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">TOTAL EXPENSES</span>
                    <span class="info-box-number"><i class="fa fa-rupee"></i> {{\App\Models\Expense::totalExpense() }} </span>
                    </div>
                </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 ">
                <a href="{{ url('library_assign') }}" class="btn btn-info btn-block btn-flat"><i class="fa fa-bell"></i>  Enroll New Student</a>
               <a href="{{ url('library_student_view') }}" class="btn btn-info btn-block btn-flat"><i class="fa fa-bell"></i>   Old Student</a>
               <!--<a href="{{ url('library_student_view') }}" class="btn btn-info btn-block btn-flat"><i class="fa fa-bell"></i>   Old Student</a>-->
                <a href="{{ url('book_locker') }}" class="btn btn-info btn-block btn-flat"><i class="fa fa-unlock-alt"></i> Locker Assign</a>
			</div>
			<div class="col-lg-3 col-md-4 col-sm-4 col-xs-6 ">
                <a href="{{ url('library_student_renew') }}" class="btn btn-info btn-block btn-flat"><i class="fa fa-bell"></i>  Renew Membership</a>
                 <a href="{{ url('library/student/report') }}" class="btn btn-info btn-block btn-flat"><i class="fa fa-bell"></i>  Student Report</a>
                <a href="{{ url('library/fees/view') }}" class="btn btn-info btn-block btn-flat"><i class="fa fa-bell"></i>  Collection Report</a>
			</div>
                    
        </div>        
        <h5 class="mb-2">Collection Report</h5>
        <div class="row">
            <div class="col-12 col-sm-6 col-md-2">
                <div class="info-box mb-3 text-dark">
                    <span class="info-box-icon bg-success elevation-1"><i class="fa fa-inr"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Today </span>
                    <span class="info-box-number"> {{\App\Models\FeesDetail::LibraryCollection()['today'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <div class="info-box mb-3 text-dark">
                    <span class="info-box-icon bg-success elevation-1"><i class="fa fa-inr"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Yesterday </span>
                    <span class="info-box-number"> {{\App\Models\FeesDetail::LibraryCollection()['yesterday'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <div class="info-box mb-3 text-dark">
                    <span class="info-box-icon bg-success elevation-1"><i class="fa fa-inr"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">This Month </span>
                    <span class="info-box-number"> {{\App\Models\FeesDetail::LibraryCollection()['this_month'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <div class="info-box mb-3 text-dark">
                    <span class="info-box-icon bg-success elevation-1"><i class="fa fa-inr"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Last Month </span>
                    <span class="info-box-number"> {{\App\Models\FeesDetail::LibraryCollection()['last_month'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <div class="info-box mb-3 text-dark">
                    <span class="info-box-icon bg-success elevation-1"><i class="fa fa-inr"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Collection </span>
                    <span class="info-box-number"> {{\App\Models\FeesDetail::LibraryCollection()['collection'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <a href="{{ url('library_due_amount') }}">
                    <div class="info-box mb-3 text-dark">
                        <span class="info-box-icon bg-success elevation-1"><i class="fa fa-inr"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Dues</span>
                        <span class="info-box-number"> {{\App\Models\FeesDetail::LibraryCollection()['due_amount'] }}</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="row">
             <div class="col-md-6">
                <div class="card">
                <div class="card-header ui-sortable-handle">
                    <h3 class="card-title">
                        Facilities
                    </h3>
                </div>
                
                <div class="card-body">
					<span class="label label-success"> Fully AC</span>

					<span class="label label-success"><i class="fa fa-clock-o" aria-hidden="true"></i> 24x7 OPEN</span>

					<span class="label label-success"><i class="fa fa-wifi" aria-hidden="true"></i> Wi-Fi Facility</span>

					<span class="label label-success">RO Water</span>

					<span class="label label-success"><i class="fa fa-video-camera" aria-hidden="true"></i> CCTV Camera</span>

					<span class="label label-success"><i class="fa fa-volume-off" aria-hidden="true"></i> Noiseless Environment</span>

					<span class="label label-success"><i class="fa fa-battery-3" aria-hidden="true"></i> Power Backup</span>

					<span class="label label-success"><i class="fa fa-book" aria-hidden="true"></i> Monthly Magazines</span>

					<span class="label label-success"><i class="fa fa-newspaper-o" aria-hidden="true"></i> Daily Newspapers</span>

					<span class="label label-success"><i class="fa fa-book" aria-hidden="true"></i> NCERT Books</span>

					<span class="label label-success"><i class="fa fa-lock" aria-hidden="true"></i> Locker Facility</span>

					<span class="label label-success"><i class="fa fa-wpforms" aria-hidden="true"></i> Online Form Submission</span>

					<span class="label label-success"><i class="fa fa-table" aria-hidden="true"></i> Comfortable Seating</span>

					<span class="label label-success"><i class="fa fa-car" aria-hidden="true"></i> Parking Facility</span>

					<span class="label label-success"><i class="fa fa-plug" aria-hidden="true"></i> Separate Charging Point</span>

					<span class="label label-success"><i class="fa fa-users" aria-hidden="true"></i> For Boys &amp; Girls</span>

					<span class="label label-success"><i class="fa fa-coffee" aria-hidden="true"></i> Cafeteria</span>

					<span class="label label-success"><i class="fa fa-volume-control-phone" aria-hidden="true"></i> Discussion Room</span>

					<span class="label label-success"><i class="fa fa-fire" aria-hidden="true"></i> Heat Blower</span>

										
									
                </div>

                </div>        
            </div>
            <div class="col-md-6">
                <div class="card">
                <div class="card-header ui-sortable-handle">
                <h3 class="card-title">
                <i class="ion ion-clipboard mr-1"></i>
                To Do List
                </h3>
                <div class="card-tools" style="width:70%;">
                    <div class="row">
                        <div class="col-md-8">
                            <input type="text" class="form-control form-control-border" id="task" name="task" placeholder="Enter Task..">
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="add_task btn btn-primary float-right btn-xs"><i class="fa fa-plus"></i> Add</button>
                        </div>
                    </div>
                
                </div>
                </div>
                
                <div class="card-body">
                <ul class="todo-list ui-sortable" data-widget="todo-list">
                @if(!empty($task))
                @foreach($task as $item)
                    <li class="" id="_{{ $item->id ?? '' }}">
                    <span class="handle ui-sortable-handle">
                    <i class="fa fa-ellipsis-v"></i>
                    <i class="fa fa-ellipsis-v"></i>
                    </span>
                    <div class="icheck-primary d-inline ml-2">
                    <input type="checkbox" value="" class="task_status" data-id="{{ $item->id ?? '' }}" data-status="{{ $item->status ?? '' }}" name="task_status" id="_{{ $item->name ?? '' }}" style="{{  $item['status'] == 1 ? 'checked' : '' }}">
                    <label for="_{{ $item->name ?? '' }}"></label>
                    </div>
                    <span class="text">{{ $item->name ?? '' }}</span>
                    <small class="badge badge-primary"><i class="fa fa-clock"></i> 1 week</small>
                    <div class="tools">
                    <i class="fa fa-trash-o task_delete" data-id="{{ $item->id ?? '' }}"></i>
                    </div>
                    </li>
                @endforeach
                @endif
                </ul>
                </div>

                </div>        
            </div>
            @if(!empty($noticeBoard))
            <div class="col-md-6">
                <div class="card">
                <div class="card-header ui-sortable-handle">
                <h3 class="card-title">
                <i class="fa fa-bell-o mr-1"></i>
                Notifications
                </h3>

                </div>
                
                <div class="card-body">
                    <marquee direction="up" scrollamount="4" id="test" onMouseOver="document.all.test.stop()" onMouseOut="document.all.test.start()">
                        <ul class="todo-list ui-sortable" data-widget="todo-list">
                            @if(!empty($noticeBoard))
                            @foreach($noticeBoard as $item)
                                <li class="">
                                    <a href="{{ url('notice_board/view') }}">
                                    <span class="text text-dark">{{ $item->title ?? '' }}</span>
                                    <small class="badge badge-danger"><i class="fa fa-envelope-o"></i> New</small>
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
    </section>

</div>
<script>
  $(function () {

    /* initialize the external events
     -----------------------------------------------------------------*/
    function ini_events(ele) {
      ele.each(function () {

        // create an event object
        // it doesn't need to have a start or end
        var eventObject = {
          title: $.trim($(this).text()) // use the element's text as the event title
        }

        // store the Event Object in the DOM element so we can get to it later
        $(this).data('eventObject', eventObject)

        // make the event draggable using jQuery UI
        $(this).draggable({
          zIndex        : 1070,
          revert        : true, // will cause the event to go back to its
          revertDuration: 0  //  original position after the drag
        })

      })
    }

    ini_events($('#external-events div.external-event'))

    /* initialize the calendar
     -----------------------------------------------------------------*/
    //Date for the calendar events (dummy data)
    var date = new Date()
    var d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear()

    /* ADDING EVENTS */
    var currColor = '#3c8dbc' //Red by default
    // Color chooser button
    $('#color-chooser > li > a').click(function (e) {
      e.preventDefault()
      // Save color
      currColor = $(this).css('color')
      // Add color effect to button
      $('#add-new-event').css({
        'background-color': currColor,
        'border-color'    : currColor
      })
    })
    $('#add-new-event').click(function (e) {
      e.preventDefault()
      // Get value and make sure it is not null
      var val = $('#new-event').val()
      if (val.length == 0) {
        return
      }

      // Create events
      var event = $('<div />')
      event.css({
        'background-color': currColor,
        'border-color'    : currColor,
        'color'           : '#fff'
      }).addClass('external-event')
      event.text(val)
      $('#external-events').prepend(event)

      // Add draggable funtionality
      ini_events(event)

      // Remove event from text input
      $('#new-event').val('')
    })
  })
</script>
<script>
$(document).on('click', ".add_task", function () {
    var task = $('#task').val();
    var data = {'task': task}
        $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
        $.ajax({
            type: "POST",
            url: "/add/task",
            data: data,
            dataType: "html",
            success: function (response) {
            toastr.success('Task Added Successfully.');
            },
        });
});

$(document).on('click', ".task_status", function () {
    var id = $(this).data('id');
    var status = $(this).data('status');
		$.ajax({
			url: '/status/task',
			type: 'post',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				status: status,
				id: id
			},
			success: function() {
				toastr.success('Record Saved Successfully.');
			},
		});    
});

$(document).on('click', ".task_delete", function () {
    var task_id = $(this).data('id');
    var data = {'task_id': task_id}
        $.ajaxSetup({
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });
        $.ajax({
            type: "POST",
            url: "/delete/task",
            data: data,
            dataType: "html",
            success: function (response) {
               $("#task_li").remove();
            toastr.success('Task Deleted Successfully.');
            },
        });
});
</script>
<script>
  $(function () {
    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */

    //--------------
    //- AREA CHART -
    //--------------

    // Get context with jQuery - using jQuery's .get() method.
    var areaChartElement = $('#areaChart').get(0)
    if (!areaChartElement) {
      return
    }
    var areaChartCanvas = areaChartElement.getContext('2d')

    var areaChartData = {
      labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
      datasets: [
        {
          label               : 'Digital Goods',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : [28, 48, 40, 19, 86, 27, 90]
        },
        {
          label               : 'Electronics',
          backgroundColor     : 'rgba(210, 214, 222, 1)',
          borderColor         : 'rgba(210, 214, 222, 1)',
          pointRadius         : false,
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [65, 59, 80, 81, 56, 55, 40]
        },
      ]
    }

    var areaChartOptions = {
      maintainAspectRatio : false,
      responsive : true,
      legend: {
        display: false
      },
      scales: {
        xAxes: [{
          gridLines : {
            display : false,
          }
        }],
        yAxes: [{
          gridLines : {
            display : false,
          }
        }]
      }
    }

    // This will get the first returned node in the jQuery collection.
    new Chart(areaChartCanvas, {
      type: 'line',
      data: areaChartData,
      options: areaChartOptions
    })

    //-------------
    //- LINE CHART -
    //--------------
    var lineChartCanvas = $('#lineChart').get(0).getContext('2d')
    var lineChartOptions = $.extend(true, {}, areaChartOptions)
    var lineChartData = $.extend(true, {}, areaChartData)
    lineChartData.datasets[0].fill = false;
    lineChartData.datasets[1].fill = false;
    lineChartOptions.datasetFill = false

    var lineChart = new Chart(lineChartCanvas, {
      type: 'line',
      data: lineChartData,
      options: lineChartOptions
    })

    //-------------
    //- DONUT CHART -
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
    var donutData        = {
      labels: [
          'Chrome',
          'IE',
          'FireFox',
          'Safari',
          'Opera',
          'Navigator',
      ],
      datasets: [
        {
          data: [700,500,400,600,300,100],
          backgroundColor : ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'],
        }
      ]
    }
    var donutOptions     = {
      maintainAspectRatio : false,
      responsive : true,
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    new Chart(donutChartCanvas, {
      type: 'doughnut',
      data: donutData,
      options: donutOptions
    })

    //-------------
    //- PIE CHART -
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
    var pieData        = donutData;
    var pieOptions     = {
      maintainAspectRatio : false,
      responsive : true,
    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    new Chart(pieChartCanvas, {
      type: 'pie',
      data: pieData,
      options: pieOptions
    })

    //-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas = $('#barChart').get(0).getContext('2d')
    var barChartData = $.extend(true, {}, areaChartData)
    var temp0 = areaChartData.datasets[0]
    var temp1 = areaChartData.datasets[1]
    barChartData.datasets[0] = temp1
    barChartData.datasets[1] = temp0

    var barChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      datasetFill             : false
    }

    new Chart(barChartCanvas, {
      type: 'bar',
      data: barChartData,
      options: barChartOptions
    })

    //---------------------
    //- STACKED BAR CHART -
    //---------------------
    var stackedBarChartCanvas = $('#stackedBarChart').get(0).getContext('2d')
    var stackedBarChartData = $.extend(true, {}, barChartData)

    var stackedBarChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      scales: {
        xAxes: [{
          stacked: true,
        }],
        yAxes: [{
          stacked: true
        }]
      }
    }

    new Chart(stackedBarChartCanvas, {
      type: 'bar',
      data: stackedBarChartData,
      options: stackedBarChartOptions
    })
  })
</script>
<style>
    .label-success
{
  background-color: #1abc9c;
}
.label
{
  display: inline;
  padding: 0.5em 0.6em 0.6em;
  font-size: 85%;
  font-weight: 700;
  line-height: 2.5;
  color: #fff;
  text-align: center;
  white-space: nowrap;
  vertical-align: baseline;
  border-radius: 0.25em;
}
</style>
@endsection
  
