<script>
var currentYear;
var currentMonth;

$(document).ready(function(){
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

            // Mark Sundays with a red color
            if (j === 0) { // 0 = Sunday
                cell.style.backgroundColor = '#f83d3d';
                cell.style.color = 'white';
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
                var clickedMonth = month; // Assuming 'month' is defined elsewhere
                var clickedYear = year;   // Assuming 'year' is defined elsewhere

                var ul = this.querySelector('ul');
                if (ul) {
                    var ulTextContent = ul.textContent;
                    document.getElementById('modalContent').textContent = ulTextContent;

                    var infoModal = new bootstrap.Modal(document.getElementById('infoModal'));
                    infoModal.show();

                    setTimeout(function() {
                        infoModal.hide();
                    }, 3000);
                }
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
                    zIndex: 1070,
                    revert: true, // will cause the event to go back to its
                    revertDuration: 0  //  original position after the drag
                })

            })
        }

        ini_events($('#external-events div.external-event'))

        /* initialize the calendar
         -----------------------------------------------------------------*/
        //Date for the calendar events (dummy data)
        var date = new Date()
        var d = date.getDate(),
            m = date.getMonth(),
            y = date.getFullYear()

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
                'border-color': currColor
            })
        })
        
        
        function sendCalenderData(){
            var value1 = $('#value').val();
            var value2 = $('#value2').val();
            let text = value2;
            const myArray = text.split(",");
                let date = myArray[1];
           $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
		            $.ajax({
                     	url:'/school_calender_add',
					type:'post',
				  data: {
				date: date,
                message: value1,
              
            },
               
                success: function(result) {
                   // var result = JSON.parse(result);
                
                    if (result) {

                      //  toastr.success(result.msg);
                        //	$('.todoList').html(result)
                        	alert("done");
                      
                    } else {
                       // toastr.error(result.msg);
                    }
                }
            })
        }
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
                'border-color': currColor,
                'color': '#fff'
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
$( document ).ready(function() {
    
 
    
 $(window).on("load", function(){
     
                $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "POST",
                url: "/calender_view",
                dataType: "json",
         success:function(response){ 
              
              $.each(response.data,function (key, item){
                  var myArray = [
                      "bg-danger",
                      "bg-warning",
                      "bg-primary",
                      "bg-info",
                      "bg-success",
                        ];
                        
                        var randomItem = myArray[Math.floor(Math.random()*myArray.length)];
      var value = $.trim(""+item.date);
          $(".fc-daygrid-day").each(function() {
    
            var data=  $(this).data("date");
           
            if(data == value)
            {
                $(this).find(".fc-daygrid-day-events").append("<div class='mt-1 fc-event-title fc-sticky "+randomItem+"'>"+item.message+"</div>");
                
            }
            })
          
            })
    }
            });
 });

  $(".fc-daygrid-day").each(function() {
       
var data=  $(this).data( "date" );
if(data == "2022-08-01")
{
    $(this).find(".fc-daygrid-day-events").text("dfsdfsfsfsfsf");
}
});
});
</script>






<div class="card card-dark">
    <div class="card-header">
       <h3 class="card-title"><i class="fa fa-calendar" aria-hidden="true"> {{ __('Calender') }}</i> </h3>
    
    </div>
    <div class="">
        <div class="calendar">
            <div class="btn-container">
                <div style="display:flex">
                <button class="btn1" onclick="prevMonth()">&#8249;</button>
                <div class="month-year pt-2" id="monthYear"></div>
                <button class="btn1" onclick="nextMonth()">&#8250;</button>
                </div>
            </div>
           
            <table id="calendarTable">
                <thead>
                    <tr>
                        <th>Sun</th>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                    </tr>
                </thead>
                <tbody id="calendarBody"></tbody>
            </table>
            <div class="col-md-12 mt-2">
                <div class="flex_items">
                    <div class="instructions">
                        <span class="Present instruction_btn">Present</span>
                    </div>
                    <div class="instructions">
                        <span class="Absent instruction_btn">Sunday</span>
                    </div>
                    <div class="instructions">
                        <span class="Holiday instruction_btn">Holiday</span>
                    </div>
                    <!--<div class="instructions">-->
                    <!--    <span class="Leave instruction_btn">L</span> Leave-->
                    <!--</div>-->
                    <div class="instructions touch">
                        <span class="Event instruction_btn">Event</span>
                    </div>
                    <div class="instructions touch">
                        <span class="Leave instruction_btn">Exam</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<div id="infoModal" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header "style='background-color:#6639b5c4;color:white'>
        <h5 class="modal-title">Event Details</h5>
       
      </div>
      <div class="modal-body">
        <p id="modalContent"></p>
      </div>
    </div>
  </div>
</div>


<style>
    .event-cell {
    background-color: #999; /* Light blue background for event cells */
    color: #fff; /* Light blue background for event cells */
 
    position: relative;
}


.card-header .nav-pills .nav-link {
  color: #db5b06;
}

.table td{
    border:1px dashed grey;
}

.calendar td {
    cursor: pointer;
    border: 1px dotted grey;
}
.calendar {
            /*max-width: 600px;*/
            margin: 0 auto;
            /*background-color: #fff;*/
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 5px;
        }
        .month-year {
            text-align: center;
            width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th,
       
        table th {
            background-color: #007bff;
            color: #fff;
            text-align: center;
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
            display:none;
        }
           #calendarTable td {
            text-align: center;
          
            padding: 0px;
            border-bottom: 1px solid #ddd;
        }
        .flex_items {
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}
.Present {
  background-color: green;
  color: white;
  padding:2px;
}
.Absent {
  background-color: red;
  color: white;
  padding:2px;
}
.Holiday {
  background-color: #ffc107;
  color: white;
  padding:2px;
}
.Leave {
  background-color: #999999;
  color: white;
  padding:2px;
}
.Event {
  background-color: coral;
  color: white;
  padding:2px;
}
       
</style>
