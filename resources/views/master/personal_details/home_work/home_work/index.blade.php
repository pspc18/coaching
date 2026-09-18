
@php
  $classType = Helper::classType();
  $getSection = Helper::getSection();
  $homeworkReview = Helper::homeworkReview();
@endphp
@extends('layout.app') 
@section('content')

<input type="hidden" id="session_id" value="{{ Session::get('role_id') ?? '' }}">
 <div class="content-wrapper">
    
     <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">   
    <div class="card card-outline card-orange">
         <div class="card-header bg-primary">
             
        @if(Session::get('role_id') == 3)    
        <h3 class="card-title"><i class="fa fa-flask"></i> &nbsp; My Homework</h3>
        @else
        <h3 class="card-title"><i class="fa fa-flask"></i> &nbsp; View Homework</h3>
        @endif
            
            <div class="card-tools">
        @if(Session::get('role_id') !== 3)
          <a href="{{url('homework/add')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i> add</a>
        @endif
          </div>
            
            </div>        
            
        
        @if(Session::get('role_id') == 1)
         <form id="quickForm" action="{{ url('homework/index') }}" method="post" >
                        @csrf 
                    <div class="row m-2">
                    
                    	           
                        <div class="col-md-2">
                    		<div class="form-group">
                    			<label>Class</label>
                    			<select class="form-control" id="class_type_id" name="class_type_id" >
                    			<option value="">Select</option>
                                 @if(!empty($classType)) 
                                      @foreach($classType as $type)
                                         <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                                      @endforeach
                                  @endif
                                </select>
                    	    </div>
                    	</div>
                    	<div class="col-md-2">
                    		<div class="form-group">
                    			<label>Section</label>
                    				<select class="form-control section_id" id="" name="section_id" >
                    			   <option value="">Select</option>
                                 <!--@if(!empty($getSection)) 
                                      @foreach($getSection as $type)
                                         <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                                      @endforeach
                                  @endif-->
                                </select>
                                
                    	    </div>
                    	</div>

                    	<div class="col-md-2">
                    		<div class="form-group">
                    			<label>Admission No</label>
                    				<input type="text" class="form-control" id="admissionNo" name="admissionNo" placeholder="Admission No" value="{{ $search['admissionNo'] ?? '' }}">
  
        		        </div>
        		    </div>    

            		<div class="col-md-4">
            			<div class="form-group">
            				<label>Search By Keywords</label>
            				<input type="text" class="form-control" id="name" name="name" placeholder="Ex. Name, Mobile, Email, Aadhaar, Father/ Mother Name etc." value="{{ $search['name'] ?? '' }}">
            		    </div>
            		</div>                     	
                        <div class="col-md-1 ">
                             <label class="text-white">Search</label>
                    	    <button type="submit" class="btn btn-primary" >Search</button>
                    	</div>
                   			
                    </div>
                </form>
        
             @endif
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	

        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
          <thead>
          <tr role="row">
              <th>Sr. No.</th>
                    <th>Title</th>
                    <th>Class</th>
                    <th>Subject</th>
                    <th>Homework Date</th>
                    <th>Submission Date</th>
                    <th>Action</th>
          </thead>
              @if(!empty($data))
                @php
                   $i=1
                @endphp
               
                @foreach ($data  as $item)
                
                <tr class=" {{ ( 1 == $item['view_status'])   ? 'bg-warning' : '' }} "> 
                    <td>{{ $i++ }}</td>
                    <td>{{ $item['title'] ??'' }} </td>
                    <td>{{ $item['ClassType']['name'] ??'' }} ({{ $item['Section']['name']  }})</td>
                    <td>{{ $item['Subject']['name'] ?? '' }}</td>
                    <td>{{ $item['homework_date'] ?? '' }}</td>
                    <td>{{ $item['submission_date'] ?? '' }}</td>
                    <td>
                        <button data-id='{{$item->id}}' data-homework_date='{{$item->homework_date}}' data-submission_date='{{$item->submission_date}}' 
                        data-title='{{$item->title}}' data-description='{{$item->description}}' data-content_file='{{$item->content_file}}'
                        data-class='{{ $item['ClassType']['name'] ??'' }}' data-section='{{ $item['Section']['name']  }}' data-subject='{{ $item['Subject']['name'] ?? '' }}'
                        data-create_teacher='{{ $item['Teacher']['first_name'] ?? '' }} {{ $item['Teacher']['last_name'] ?? '' }}'
                        class="btn btn-secondary viewHomework btn-xs" title="View Homework" ><i class="fa fa-eye"></i></button>
                        
                            <a href="{{ url('homework/details') }}/{{$item->id}}" class="btn btn-primary btn-xs ml-3" title=" Assignments" ><i class="fa fa-reorder"></i></a>
                        @if(Session::get('role_id') == 3)
                            <a href="javascript:;" class="btn btn-success btn-xs ml-3 homeworkId" id="homeworkId" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#uploadModal" title="Upload Assignment" ><i class="fa fa-upload"></i></a>
                        @endif
                        @if(Session::get('role_id') == 3)
                            @if(!empty($item['content_file']))
                            <!--<img src="{{ env('IMAGE_SHOW_PATH').'homework/'.$item['content_file'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}'" width="60px" height="60px">-->
                            <a href="{{ url('download_homework') }}/{{$item['id'] ?? '' }}" class="btn btn-danger  btn-xs ml-3" title="Download Homework" ><i class="fa fa-download"></i></a>
                            @endif
                        @endif                        
                        @if(Session::get('role_id') !== 3)
                            <a href="{{url('homework/edit',$item->id)}}" class="btn btn-success  btn-xs ml-3" title="Edit Homework" ><i class="fa fa-edit"></i></a>
                            <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3" title="Delete Homework"><i class="fa fa-trash-o"></i></a>
                        @endif
                    </td>
                </tr>
              @endforeach
            @endif
            </tbody>
        </table>
        </div>
        </div>
    </div>
           
</div>

            <!-- The Modal -->
            <div class="modal" id="uploadModal">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
            
                  <div class="modal-header">
                    <h4 class="modal-title">Homework Assignments</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                  </div>
                <form action="{{ url('upload/homework') }}" method="post" enctype="multipart/form-data">
                    @csrf
                  <div class="modal-body">
                      <input type="hidden" id="homework_id" name="homework_id" value="">
                    	<div class="col-md-12">
								<div class="form-group">
									<label style="color: red;">Message*</label>
									<textarea class="form-control" id="message" name="message" placeholder="Type Message"></textarea>
							    </div>
						</div>
                    	<div class="col-md-12">
								<div class="form-group">
									<label style="color: red;">Attach Document*</label>
									<input class="form-control" type="file" id="content_file" name="content_file[]" multiple> 
							    </div>
						</div>                       
                  </div>
                  <div class="modal-footer">
                                <button type="submit" class="btn btn-primary waves-effect waves-light uploadHomework">Submit</button>
                     </div>
                    </form>
                </div>
              </div>
            </div>

            <!-- The Modal -->
            <div class="modal" id="Modal_id">
              <div class="modal-dialog">
                <div class="modal-content" style="background: #555b5beb;">
            
                  <!-- Modal Header -->
                  <div class="modal-header">
                    <h4 class="modal-title text-white">Delete Confirmation</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                  </div>
            
                  <!-- Modal body -->
                  <form action="{{ url('homework/delete') }}" method="post">
                          	 @csrf
                  <div class="modal-body">
                          <input type=hidden id="delete_id" name=delete_id>
                          <h5 class="text-white">Are you sure you want to delete  ?</h5>
                  </div>
                  <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger waves-effect waves-light">Delete</button>
                     </div>
                   </form>
            
                </div>
              </div>
            </div>


          <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" >
              <div class="modal-content">
                <div class="modal-header">
                        <h5>Homework Details</h5>
                  <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">x</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                            <div id="log">
                                <p><b>Title :</b> <span id="title"></span></p>
                                <p id="description"></p>
                            </div>
                            <div class="col-md-7" id="divMain" ></div>
                        <div class="col-md-5 pl-3" style="background-color: whitesmoke;margin-top: -2%; margin-bottom: -2%;">
                           <h4>Summary</h4>
                           <hr>
                            <p><i class="fa fa-calendar-plus-o"></i> <b>Homework Date:</b> <span id="homework_date"></span></p>
                            <p><i class="fa fa-calendar-plus-o"></i> <b>Submission Date:</b> <span id="submission_date"></span></p>
                            <p><i class="fa fa-calendar-plus-o"></i> <b>Evaluation Date:</b> 12/05/2021</p>
                            <p><b>Created By:</b> <span id="create_teacher"></span></p>
                            <p><b>Evaluated By:</b> Shyam Sir</p>
                            <p><b>Class:</b> <span id="classes"></span></p>
                            <p><b>Section:</b> <span id="sections"></span></p>
                            <p><b>Subject:</b> <span id="subject"></span></p>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
          
          
</div>
</div>
</div>
</section>
  
<script>
  $('.homeworkId').click(function() {
  var homework_id = $(this).data('id'); 
  
  $('#homework_id').val(homework_id); 
  } );


$(document).on('click', ".uploadHomework", function () {
    if( !$('#message').val() ) { 
        $("#message").attr('required','true');
        toastr.error('The Message field is required!'); 
    }     
    if( !$('#content_file').val() ) { 
        $("#content_file").attr('required','true');
        toastr.error('The Document field is required!'); 
    } 
});

$(document).on('click', ".viewHomework", function() {

    var session_id = $('#session_id').val();
        $('#myModal').modal('toggle');      
        
    var homework_date = $(this).data('homework_date');
    var submission_date = $(this).data('submission_date');
    var title = $(this).data('title');
    var description = $(this).data('description');
    var classes = $(this).data('class');
    var sections = $(this).data('section');
    var subject = $(this).data('subject');
    var create_teacher = $(this).data('create_teacher');

    $('#homework_date').html(homework_date);
    $('#submission_date').html(submission_date);
    $('#title').html(title);
    $('#description').html(description);
    $('#classes').html(classes);
    $('#sections').html(sections);
    $('#subject').html(subject);
        if(create_teacher !== "") { 
            $('#create_teacher').html(create_teacher); 
        }else{
            $('#create_teacher').html('Admin');
        }     

}); 


  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
</script>  
 <script>


var support = (function () {
	if (!window.DOMParser) return false;
	var parser = new DOMParser();
	try {
		parser.parseFromString('x', 'text/html');
	} catch(err) {
		return false;
	}
	return true;
})();

var textToHTML= function (str) {

	// check for DOMParser support
	if (support) {
		var parser = new DOMParser();
		var doc = parser.parseFromString(str, 'text/html');
		return doc.body.innerHTML;
	}

	// Otherwise, create div and append HTML
	var dom = document.createElement('div');
	dom.innerHTML = str;
	return dom;

};

 var myValue9 = document.getElementById("log").innerText;

document.getElementById("divMain").innerHTML= textToHTML(myValue9);
  
document.getElementById("log").innerText ="";
  
  
</script>
<script>

// Calculate Results
function calculateResults(){

// console.log("calculating");

// Declare UI Variables

const amount = document.getElementById('amount');
const interest = document.getElementById('interest');
const years = document.getElementById('years');
const monthlyPayment = document.getElementById('monthly-payment');
const totalPayment = document.getElementById('total-payment');
const totalInterest = document.getElementById('total-interest');

// Turn amount into decimal and store it into variable
const principal = parseFloat(amount.value);
const calculatedInterest = parseFloat(interest.value) /100 / 12;
const calculatedPayment =  parseFloat(years.value) * 12;

// Compute monthly payments
const x = Math.pow(1 + calculatedInterest, calculatedPayment);
const monthly = (principal * x * calculatedInterest)/(x-1);


// Check if value is finite

if(isFinite(monthly)){
	monthlyPayment.value = monthly.toFixed(2);
	totalPayment.value = (monthly * calculatedPayment).toFixed(2);
	totalInterest.value = ((monthly * calculatedPayment) - principal).toFixed(2);

} else{
	showError("Please check your numbers")
}

e.preventDefault();
}

// Function to show error
function showError(error){
	// create div
	const errorDiv = document.createElement('div');

	// Get card and heading in order to add new div to DOM. Parent element
	const card = document.querySelector('.card');
	const heading = document.querySelector('.heading');


	// Give div a class name
	errorDiv.className = 'alert alert-danger';

	// Create text and append div
	errorDiv.appendChild(document.createTextNode(error));

	// Insert error above heading. Insert above will take in the parent element which is the card in this case and for the parameters
	// It will take in the element you want to put in and the element you want to put it before, in this case errorDiv and the heading

	card.insertBefore(errorDiv, heading);

	// Clear error after 3 seconds

	setTimeout(clearError, 3000);

}


// Create clear error
function clearError(){
	document.querySelector('.alert').remove();
}
</script>
@endsection 