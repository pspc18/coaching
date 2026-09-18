@php


@endphp

@extends('layout.app')
@section('content')


<div class="content-wrapper students_search">
<input type="hidden" id="value">
<input type="hidden" id="value2">
    <section class="content pt-3">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-home"></i> &nbsp;Student Academic Performance</h3>
                            
                            <div class="card-tools">
                                <!--<a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add User</a>-->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
 
         
                <div class="row">
                <div class="col-12 col-md-9">
                    
                        <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fa fa-chart-bar"></i>
               Average Subjects Score
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
              <!-- /.card-header -->
                      <div class="card-body">
                <div class="row">
                  <div class="col-6 col-md-2 text-center">
                    <input type="text" class="knob" value="30" data-width="90" data-height="90" data-fgColor="#9c7bbc"
                           data-readonly="true">

                    <div class="knob-label">English</div>
                  </div>
                  <div class="col-6 col-md-2 text-center">
                    <input type="text" class="knob" value="60" data-width="90" data-height="90" data-fgColor="#3c4bbc"
                           data-readonly="true">

                    <div class="knob-label">Hindi</div>
                  </div>
                  <div class="col-6 col-md-2 text-center">
                    <input type="text" class="knob" value="80" data-width="90" data-height="90" data-fgColor="#7c8dbc"
                           data-readonly="true">

                    <div class="knob-label">Sanskrit</div>
                  </div>
                  <div class="col-6 col-md-2 text-center">
                    <input type="text" class="knob" value="60" data-width="90" data-height="90" data-fgColor="#3c7dbc"
                           data-readonly="true">

                 <div class="knob-label">Mathematic</div>
                  </div>
           
                </div>
                <!-- /.row -->
              </div>
               
                </div>
                
                            <!-- BAR CHART -->
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">Bar Chart</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fa fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="chart">
                  <canvas id="examChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
                
                
                
                
                </div>
                <div class="col-12 col-md-3">
                  <div class="row">
        <div class="col-md-12">
                         <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="fa fa-chart-bar"></i>
               Attendance
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
              <!-- /.card-header -->
                <div class="card-body">
                    <canvas id="pieChart1"></canvas>
                </div>
            </div>
        </div>
   

    </div>
                </div>
             
                  
                  
                 

        
  </div>
              
              <script>
  $(function () {
    /* jQueryKnob */

    $('.knob').knob({
      /*change : function (value) {
       //console.log("change : " + value);
       },
       release : function (value) {
       console.log("release : " + value);
       },
       cancel : function () {
       console.log("cancel : " + this.value);
       },*/
      draw: function () {

        // "tron" case
        if (this.$.data('skin') == 'tron') {

          var a   = this.angle(this.cv)  // Angle
            ,
              sa  = this.startAngle          // Previous start angle
            ,
              sat = this.startAngle         // Start angle
            ,
              ea                            // Previous end angle
            ,
              eat = sat + a                 // End angle
            ,
              r   = true

          this.g.lineWidth = this.lineWidth

          this.o.cursor
          && (sat = eat - 0.3)
          && (eat = eat + 0.3)

          if (this.o.displayPrevious) {
            ea = this.startAngle + this.angle(this.value)
            this.o.cursor
            && (sa = ea - 0.3)
            && (ea = ea + 0.3)
            this.g.beginPath()
            this.g.strokeStyle = this.previousColor
            this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false)
            this.g.stroke()
          }

          this.g.beginPath()
          this.g.strokeStyle = r ? this.o.fgColor : this.fgColor
          this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false)
          this.g.stroke()

          this.g.lineWidth = 2
          this.g.beginPath()
          this.g.strokeStyle = this.o.fgColor
          this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false)
          this.g.stroke()

          return false
        }
      }
    })
    /* END JQUERY KNOB */

    //INITIALIZE SPARKLINE CHARTS
    var sparkline1 = new Sparkline($('#sparkline-1')[0], { width: 240, height: 70, lineColor: '#92c1dc', endColor: '#92c1dc' })
    var sparkline2 = new Sparkline($('#sparkline-2')[0], { width: 240, height: 70, lineColor: '#f56954', endColor: '#f56954' })
    var sparkline3 = new Sparkline($('#sparkline-3')[0], { width: 240, height: 70, lineColor: '#3af221', endColor: '#3af221' })

    sparkline1.draw([1000, 1200, 920, 927, 931, 1027, 819, 930, 1021])
    sparkline2.draw([515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921])
    sparkline3.draw([15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21])

  })

</script>    
                  
              <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>     
                  <script>
                       // First Pie Chart
    var ctx1 = document.getElementById('pieChart1').getContext('2d');
    var pieChart1 = new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: ['Red', 'Blue', 'Yellow'],
            datasets: [{
                data: [300, 50, 100],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    
                  </script>
                  
                  
                  
  <script>
        const ctx = document.getElementById('examChart').getContext('2d');
        const examChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Test 1', 'Test 2', 'Test 3'],
                datasets: [
                    {
                        label: 'Passed',
                        data: [30, 40, 50],
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    },
                    {
                        label: 'Failed',
                        data: [15, 10, 5],
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    },
                    {
                        label: 'Not Attempted',
                        data: [5, 10, 15],
                        backgroundColor: 'rgba(255, 206, 86, 0.6)',
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
       


@endsection