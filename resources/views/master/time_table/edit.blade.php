@extends('layout.app') 
@section('content')

                                                                   
<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('master.Edit Time Period') }}</h3>
							
							<div class="card-tools">
                                        <a href="{{url('time_periods')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
                        </div>
                           </div>                      
                          <form id="form-submit-edit" action="{{ url('edit_periods') }}/{{($data->id)}}" method="post" >
                                @csrf
                        	<div class="row m-2">
                             
                                <div class="col-md-4">
                                    <label class="text-danger">{{ __('master.From Time') }}*</label>
                                    <input type="time" class="form-control @error('from_time') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)"
                                        id="from_time"
                                        name="from_time"
                                       
                                        value="{{date("H:i", strtotime($data['from_time']))}}"
                                    />
                                    
                                </div>
                           
                             
                                <div class="col-md-4">
                                    <label class="text-danger">{{ __('master.To Time') }}*</label>
                                    <input type="time" class="form-control @error('to_time') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)"
                                        id="to_time"
                                        name="to_time"
                                       
                                         value="{{date("H:i", strtotime($data['to_time']))}}"
                                    />
                                   
                                </div>
                                <div class="col-md-4">
                                    <label class="text-danger">Period Name*</label>
                                    <select name="period_name" class="form-control @error('period_name') is-invalid @enderror">
                                        <option value="" >Select Period Name</option>
                                        <option value="First" {{ ($data['period_name'] == "First") ? 'selected' : '' }}>First</option>
                                        <option value="Second" {{ ($data['period_name'] == "Second") ? 'selected' : '' }} >Second</option>
                                        <option value="Third" {{ ($data['period_name'] == "Third") ? 'selected' : '' }} >Third</option>
                                        <option value="Fourth" {{ ($data['period_name'] == "Fourth") ? 'selected' : '' }} >Fourth</option>
                                        <option value="Fifth" {{ ($data['period_name'] == "Fifth") ? 'selected' : '' }} >Fifth</option>
                                        
                                        <option value=" 🍴 Lunch Break" {{ ($data['period_name'] == "🍴 Lunch Break") ? 'selected' : '' }} >🍴 Lunch Break</option>
                                        
                                        <option value="Sixth" {{ ($data['period_name'] == "Sixth") ? 'selected' : '' }} >Sixth</option>
                                        <option value="Seventh" {{ ($data['period_name'] == "Seventh") ? 'selected' : '' }} >Seventh</option>
                                        <option value="Eighth" {{ ($data['period_name'] == "Eighth") ? 'selected' : '' }} >Eighth</option>
                                        <option value="Ninth" {{ ($data['period_name'] == "Ninth") ? 'selected' : '' }} >Ninth</option>
                                        <option value="Tenth" {{ ($data['period_name'] == "Tenth") ? 'selected' : '' }} >Tenth</option>
                                        <option value="Eleventh" {{ ($data['period_name'] == "Eleventh") ? 'selected' : '' }} >Eleventh</option>
                                        <option value="Twelfth" {{ ($data['period_name'] == "Twelfth") ? 'selected' : '' }} >Twelfth</option>
                                        
                                    </select>
                                     
                                     
                                   
                                </div>
                            </div>
        <div class="col-md-12 text-center">
			<button type="submit" onclick="timeCheck()" class="btn btn-primary btn-submit">{{ __('messages.Update') }}</button><br><br>
		</div>
    	</form>
            </div>
        </div>
    </div>
    </section>
</div>

<script>

$("#form-submit-edit").submit(function(e){
     
  

  var element = document.getElementById("from_time").value;
  var element1 = document.getElementById("to_time").value;
  
  if (element == "") {
  alert("Please Enter Time");
    return false;  
  }

  else {
  
 
 
  // get input time
  var time = element.split(":");
  var hour = time[0];
  if(hour == '00') {hour = 24}
  var min = time[1];
  
   var inputTime = hour+"."+min;
  
  var time1 = element1.split(":");
  var hour1 = time1[0];
  if(hour1 == '00') {hour1 = 24}
  var min1 = time1[1];
  
  var inputTime1 = hour1+"."+min1;
 
  
  var totalTime = inputTime1 - inputTime;
  
 
  if ((Math.abs(totalTime)) > 0.29000000000000004) {
  
  } 
  else {
   
      e.preventDefault();
    alert("Less Time");
  }
    }
  });
   
</script>

@endsection