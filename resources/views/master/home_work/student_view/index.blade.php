
@php
$classType = Helper::classType();
$homeworkReview = Helper::homeworkReview();
$getPermission = Helper::getPermission();
$actionPermission = Helper::actionPermission();

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
      
        <h3 class="card-title"><i class="fa fa-flask"></i> &nbsp;{{ __('homework.My Homework') }} </h3>
            
            <div class="card-tools">
         <a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs "><i class="fa fa-arrow-left"></i> <span class="" >{{ __('Back') }} </span></a>                   
      
          </div>
            
            </div>        
     <div class="row mb-2 m-2">
		    <div class="col-md-12">	

        <table id="" class="table table-bordered table-striped  dtr-inline">
          <thead>
          <tr role="row">
              <th style="padding:0;">{{ __('messages.Sr.No.') }}</th>
                  
                
                  <th>{{ __('master.Title') }}</th>
                    <th>{{ __('messages.Subject') }}</th>
                    <th>{{ __('homework.Submission Date') }}</th>
                    <th>{{ __('messages.Action') }}</th>
          </thead>
              @if(!empty($data))
                @php
                   $i=1
                @endphp
               
                @foreach ($data  as $item)
                @php
                    $userName = DB::table('users')->whereNull('deleted_at')->where('id',$item->user_id)->first();
                   
                @endphp
                <tr class=" {{ ( 1 == $item['view_status'])   ?  : '' }} "> 
                    <td>{{ $i++ }}</td>
                   
                      <td>{{ $item['title'] ??'' }} </td>
                  
                    <td>{{ $item['Subject']['name'] ?? '' }}</td>
                    <td>{{date('d-m-Y', strtotime($item['submission_date'])) ?? '' }}</td>
                    <td class="actionTd">
                        <button data-id='{{$item->id}}'  data-submission_date="{{date('d-m-Y', strtotime($item['submission_date'])) ?? '' }}" 
                        data-title='{{$item->title}}' data-description='{{$item->description}}' data-content_file='{{ env('IMAGE_SHOW_PATH').'homework/'.$item['content_file'] }}'
                        data-class='{{ $item['ClassType']['name'] ??'' }}' data-subject='{{ $item['Subject']['name'] ?? '' }}'
                        data-create_teacher='{{ $item['Teacher']['first_name'] ?? '' }} {{ $item['Teacher']['last_name'] ?? '' }}'
                       
                        class="btn btn-secondary viewHomework btn-xs" title="View Homework" ><i class="fa fa-eye"></i></button>
                            <a href="javascript:;" class="btn btn-success btn-xs ml-3 homeworkId" id="homeworkId" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#uploadModal" title="Upload Assignment" ><i class="fa fa-upload"></i></a>
                        <a href="{{ url('homework/details') }}/{{$item->id}}" class="btn btn-primary btn-xs ml-3" title=" Assignments" ><i class="fa fa-reorder"></i></a>    
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
              <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content">
            
                  <div class="modal-header">
                    <h4 class="modal-title">{{ __('homework.Homework Assignments') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                  </div>
                <form action="{{ url('upload/homework') }}" method="post" enctype="multipart/form-data">
                    @csrf
                  <div class="modal-body">
                      <input type="hidden" id="homework_id" name="homework_id" value="">
                    	<div class="col-md-12">
								<div class="form-group">
									<label style="color: red;">{{ __('messages.Message') }}*</label>
									<textarea class="form-control" id="message" name="message" placeholder="Type Message"></textarea>
							    </div>
						</div>
                    	<div class="col-md-12">
								<div class="form-group">
									<label style="color: red;">{{ __('messages.Attach Document') }}*</label>
									<input class="form-control" type="file" id="content_file" name="content_file[]" multiple> 
							    </div>
						</div>                       
                  </div>
                    <div class="modal-footer">
                            <button type="submit" class="btn btn-primary waves-effect waves-light uploadHomework">{{ __('messages.submit') }}</button>
                    </div>
                    </form>
                </div>
              </div>
            </div>

       <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered homework-modal" role="document">
        <div class="modal-content" >
            <div class="modal-header">
                <h5 class="modal-title">{{ __('homework.Homework Details') }}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <div class="row" id="downloadLeaflet">
                    <div class="col-md-12 col-12">
                        <p><b>{{ __('messages.Title') }} :</b> <span id="title"></span></p>
                        <p><b>{{ __('messages.Subject') }}:</b> <span id="subject"></span></p>
                        <p><b>{{ __('homework.Submission Date') }}:</b> <span id="submission_date"></span></p>
                        <p><b>{{ __('homework.Created By') }}:</b> <span id="create_teacher"></span></p>
                    </div>
                    <br>
                    <div class="col-md-12 col-12">
                        <hr>
                        <p><b>Description :</b> <span id="description"></span></p>
                       
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              
                <button type="button" class="btn btn-primary"  id="downloadBtnImage"><i class="fa fa-download"></i></button>
                  <button type="button" class="btn btn-default" data-bs-dismiss="modal">{{ __('messages.Close') }}</button>
            </div>
        </div>
    </div>
</div>

          
          
</div>
</div>
</div>
</section>

<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
<script src="{{URL::asset('public/assets/school/js/jquery-ui.min.js')}}"></script>
<script src="{{URL::asset('public/assets/school/js/html2canvas.js')}}"></script>
<script src="{{URL::asset('public/assets/school/js/jspdf.js')}}"></script>

  <script>
      function downloadImageLeaflet() {
        const leafletElement = document.getElementById('downloadLeaflet');
        html2canvas(leafletElement, {
            useCORS: true,
            scrollX: 0,
            scrollY: 0,
            dpi: window.devicePixelRatio * 1000, // Set higher DPI value for better image quality
            scale: 5
        }).then((canvas) => {
            const imgData = canvas.toDataURL('image/jpeg', 2.0); // Use JPEG format with highest quality
            // Create a temporary link element
            const link = document.createElement('a');
            link.href = imgData;
            link.download = 'Homework.png';

            // Trigger the download
            link.click();
        });
    }

 
    const downloadBtnImage = document.getElementById('downloadBtnImage');
    downloadBtnImage.addEventListener('click', downloadImageLeaflet);
</script>
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
    var submission_date = $(this).data('submission_date');
    var title = $(this).data('title');
    var description = $(this).data('description');
    var classes = $(this).data('class');
    var subject = $(this).data('subject');
 
    var content_file = $(this).data('content_file');
    var create_teacher = $(this).data('create_teacher');

    $('#submission_date').html(submission_date);
    $('#title').html(title);
    $('#description').html(description);
    $('#classes').html(classes);
    $('#subject').html(subject);
    $('#hw_file').attr('src',content_file);
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




<style>
    .actionTd a{
        margin-left: 0px !important;
    }
   @media (min-width: 576px) {
  .homework-modal {
    max-width: 80%;
    margin: 1.75rem auto;
  }
   
}
</style>


@endsection 