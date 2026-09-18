@php 
$bookCategory = Helper::getBookCategory(); 
@endphp 
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
    <div class="col-md-6">
        <div class="card card-orange card-outline">
            <div class="card-header bg-primary">
                <h3 class="card-title">Add Time Slot</h3>
                <div class="card-tools"> <a href="{{url('library_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i><span class="Display_none_mobile">{{ __('messages.Back') }}</span> </a> </div>
            </div>

             <form action="{{ url('library/time_slot') }}" method="post">
                            @csrf
                <div class="card-body">
                    <div class="bootstrap-timepicker">
                        <div class="form-group">
                           <label>Start Time</label>
                                <div class="input-group date"  data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input @error('start_time') is-invalid @enderror" data-target="#timepicker" data-toggle="datetimepicker" id="timepicker" name="start_time"/>
                                    <div class="input-group-append" data-target="#timepicker" data-toggle="datetimepicker">
                                         <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                    </div>
                                    @error('start_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                        </div>
                    </div>
                   <div class="bootstrap-timepicker">
                        <div class="form-group">
                           <label>End Time</label>
                                <div class="input-group date" id="timepicker_end1" data-target-input="nearest">
                                    <input type="text" class="form-control datetimepicker-input @error('end_time') is-invalid @enderror" data-target="#timepicker_end" data-toggle="datetimepicker" id="timepicker_end" name="end_time"/>
                                    <div class="input-group-append" data-target="#timepicker_end" data-toggle="datetimepicker">
                                         <div class="input-group-text"><i class="fa fa-clock"></i></div>
                                    </div>
                                    @error('end_time')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                        </div>
                    </div>
                  
                    <div class="form-group">
                        <label >Study Time</label>
                        <input type="text" class="form-control @error('study_time') is-invalid @enderror" id="study_time" name="study_time" placeholder="10AM to 1PM "/>
                        @error('study_time')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Study Hour (Hr)</label>
                        <input type="text" class="form-control @error('study_hour') is-invalid @enderror" id="study_hour" name="study_hour" placeholder="Enter Study Hour (Hr)" />
                        @error('study_hour')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                      <div class="form-group">
                    <label >1 Month Fee Amount</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                Rs.
                            </span>
                        </div>
                             <input class="form-control @error('amount') is-invalid @enderror" type="text" name="amount" id="amount" style="height: 38px;"> 
                                <div class="input-group-append">
                                    <div class="input-group-text">.00</div>
                                </div>
                            @error('amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                   
                  
                </div>

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
             <div class="card-header bg-primary">
                <h3 class="card-title">Time Slot</h3>
            </div>
            <form action="{{ url('not_assign_time_slots') }}" method="POST">
                @csrf
             <div class="card-body">
                @if(!empty($data))
                    @foreach($data as $type)
                        <div href="#" class="list-group-item p-2">
    						<h3 class="list-group-item-text" >{{$type->study_time ?? ''}}({{$type->study_hour ?? ''}} Hrs)</h3>
                            <input type="hidden" name="slot_id[]" value="{{ $type->id ?? '' }}">
    						<dd style="margin-left: 29px;">
                				@if(!empty($data))
                                    @foreach($data as $type2)
                                        @if($type->id != $type2->id)
                                            <input type="checkbox" {{ in_array($type2->id, explode(',', $type->not_assign_time_slot_id)) ? 'checked' : '' }} name="not_assign_time_slot_id[]" id="time{{$type2->id}}" value="slot/{{ $type->id}}/{{$type2->id}}">
                                            <label for="time{{$type2->id}}"> {{$type2->study_time ?? ''}} / {{$type2->study_hour ?? ''}} Hours</label><br>
                            			@endif	 
                				    @endforeach
                                @endif
                            </dd>	
        				</div>
				    @endforeach
                @endif
                
                <div class="col-md-12 mt-3 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            </form>
        </div>
        
    </div>

    <div class="col-md-6">
        <div class="card ">
            <div class="card-header bg-primary">
                <h3 class="card-title">Time Slot</h3>
            </div>
            <div class="card-body">
                 @if(!empty($data))
                    @foreach($data as $type)
                        <a href="#" class="list-group-item p-2">
        						<h5 class="list-group-item-heading">{{$type->study_time ?? ''}}</h5>
        						<p class="list-group-item-text" >{{$type->study_hour ?? ''}} Hours <span style="color:#000">|</span>
        						<span style="color:#F03; font-weight:600; font-size:14px;">	Fee: Rs. {{$type->amount ?? ''}} / Month </span><span style="color:#000">|</span><span style="color:#F03; font-weight:600; font-size:14px;"> Filled Seats: 0 </span> <span style="color:#000">|</span> <span style="color:#03ad9d">Available Seats: 0</span></p> 
        				</a>
				 @endforeach
                    @endif
            </div>
        </div>
    </div>
</div>

        </div>
    </section>
</div>


<script>
  $(function () {
    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })
    $('#timepicker_end').datetimepicker({
      format: 'LT'
    })

    //Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox()

    //Colorpicker
    $('.my-colorpicker1').colorpicker()
    //color picker with addon
    $('.my-colorpicker2').colorpicker()

    $('.my-colorpicker2').on('colorpickerChange', function(event) {
      $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
    })

    $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    })

  })
  // BS-Stepper Init
  document.addEventListener('DOMContentLoaded', function () {
    window.stepper = new Stepper(document.querySelector('.bs-stepper'))
  })

  // DropzoneJS Demo Code Start
  Dropzone.autoDiscover = false

  // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
  var previewNode = document.querySelector("#template")
  previewNode.id = ""
  var previewTemplate = previewNode.parentNode.innerHTML
  previewNode.parentNode.removeChild(previewNode)

  var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
    url: "/target-url", // Set the url
    thumbnailWidth: 80,
    thumbnailHeight: 80,
    parallelUploads: 20,
    previewTemplate: previewTemplate,
    autoQueue: false, // Make sure the files aren't queued until manually added
    previewsContainer: "#previews", // Define the container to display the previews
    clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
  })

  </script>
  
  
  
  <script>
  
  $( document ).ready(function() {
      
      $( "#timepicker_end" ).on( "blur", function() {
                var startTime = $( "#timepicker" ).val();
                var endTime = $( "#timepicker_end" ).val();
                console.log(startTime)
                console.log(endTime)

                if (startTime && endTime) {
                    var start = moment(startTime, 'LT');
                    var end = moment(endTime, 'LT');
                    if (end.isBefore(start)) {
                        end.add(1, 'day'); // Add 1 day to the end time if it is before the start time (crossing midnight)
                    }
                    var duration = moment.duration(end.diff(start));
                    var hours = duration.asHours();
                    $('#study_time').val(startTime + ' to ' + endTime);
                    $('#study_hour').val(hours.toFixed(2));
                }
                
            }); 
    });
     
  </script>
@endsection
