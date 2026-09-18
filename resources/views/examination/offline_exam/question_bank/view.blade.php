@php
  $classType = Helper::classType();
  $getsubject = Helper::getSubject();;
@endphp
@extends('layout.app') 
@section('content')
<div class="content-wrapper">

	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-check-square-o"></i> &nbsp;{{ __('examination.View Questions') }}</h3>
							<div class="card-tools"> 
							    <a href="{{url('add/question')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(8)->add ? '' : 'd-none' }}"><i class="fa fa-plus"></i>{{ __('common.Add') }}  </a> 
                                <a href="{{url('examination_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
                            </div>
						</div>
						<div class="card-body">
						    
                    <form id="quickForm" action="{{ url('view/question') }}" method="post" >
                        @csrf 
                    <div class="row">

                        <div class="col-md-2">
                    		<div class="form-group">
                    			<label>{{ __('examination.Subject') }}</label>
                    			<select class="select2 form-control" id="subject_id" name="subject_id">
                    			<option value="">{{ __('common.Select') }}</option>
                                 @if(!empty($getsubject)) 
                                      @foreach($getsubject as $type)
                                         <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['subject_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                      @endforeach
                                  @endif
                                </select>
                    	    </div>
                    	</div>
                    	<div class="col-md-2">
                    		<div class="form-group">
                    			<label>{{ __('examination.Question Type') }}</label>
                    				<select class="select2 form-control" id="question_type_id" name="question_type_id">
                    			   <option value="">{{ __('common.Select') }}</option>
                    			   <option value="1" {{ (1== $search['question_type_id']) ? 'selected' : '' }}>Objective</option>
                    			   <option value="2" {{ (2 == $search['question_type_id']) ? 'selected' : '' }}>Descriptive</option>
                                </select>
                    	    </div>
                    	</div>

            		<div class="col-md-4">
            			<div class="form-group">
            				<label>{{ __('common.Search By Keywords') }}</label>
            				<input type="text" class="form-control" id="name" name="name" placeholder="{{ __('common.Ex. Name, Mobile, Email, Aadhaar etc.') }}" value="{{ $search['name'] ?? '' }}">
            		    </div>
            		</div>                     	
                        <div class="col-md-1 ">
                             <label for="" class="text-white">{{ __('common.Search') }}</label>
                    	    <button type="submit" class="btn btn-primary">{{ __('common.Search') }}</button>
                    	</div>
                    			
                    </div>
                </form> 						    
						    
							<table id="example1" class="table table-bordered table-striped  dataTable">
								<thead>
									<tr role="row">
										<th>{{ __('examination.Q. Id') }}</th>
										<th>{{ __('common.Subject') }}</th>
										<th>{{ __('examination.Question Type') }} </th>
										<th>{{ __('examination.Question') }}</th>
										<th>{{ __('common.Action') }}</th>
								</thead>
								<tbody> 
								@if(!empty($data)) 
    								@php 
    								    $i=1 
    								@endphp 
								@foreach ($data as $item)
									<tr>
										<td>{{ $i++ }}</td>
										<td>{{ $item['subject_name'] ?? ''}}</td>
										<td>
										    @if($item['question_type_id'] == 1)
										        Objective
										    @else
										        Descriptive
										    @endif
										</td>
										<td>{{ $item['name'] ?? ''}}</td>
							            <td class="d-flex align-items-center">
                                                <a data-question='{{$item->name ?? ''}}'  
                                                    data-subject_id='{{$item['Subject']->name ?? ''}}' data-class_type_id='{{$item['ClassType']->name ?? ''}}' 
                                                    data-opt1='{{$item->ans1 ?? ''}}'
                                                    data-opt2='{{$item->ans2 ?? ''}}' data-opt3='{{$item->ans3 ?? ''}}'
                                                    data-opt4='{{$item->ans4 ?? ''}}' data-correct_ans='{{$item->correct_ans ?? ''}}' 
                                                    class="btn btn-success questionDetail btn-xs {{ Helper::permissioncheck(8)->view ? '' : 'd-none' }}" title="View Question"><i class="fa fa-eye"></i></a> 
                                                <a href="{{url('edit/question') }}/{{$item->id}}" class="btn btn-primary  btn-xs ml-3 {{ Helper::permissioncheck(8)->edit ? '' : 'd-none' }}" title="Edit Question"><i class="fa fa-edit"></i></a> 
											    <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3 {{ Helper::permissioncheck(8)->delete ? '' : 'd-none' }}" title="Delete Question"><i class="fa fa-trash-o"></i></a> 
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
		</div>
	</section>
</div>



<div class="modal" id="myModal">
    <div class="modal-dialog modal-xl" >
      <div class="modal-content card-outline card-orange">
        <div class="modal-header">
            <h4 class="text-center" style="width:100%;">{{ __('examination.Question Details') }}</h4>   
                <button type="button" id="closeModal"class="close" data-bs-dismiss="modal" aria-hidden="true">x</button>
        </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><b>Class :</b> <span id="subject_id1"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><b>{{ __('examination.Question Type') }} :</b> {{ __('examination.Objective') }} </p>
                    </div><br>
                    <div class="col-md-12 border-bottom" id="question1">
                        <p><b>{{ __('examination.Question') }} :</b> <span id="question"></span></p>
                    </div><br>    
                    <div class="col-md-12" id="option1">
                        <p><b>{{ __('examination.Option') }} A :</b> <span id="ans1"></span></p>
                    </div>       
                    <div class="col-md-12" id="option2">
                        <p><b>{{ __('examination.Option') }} B :</b> <span id="ans2"></span></p>
                    </div>      
                    <div class="col-md-12" id="option3">
                        <p><b>{{ __('examination.Option') }} C :</b> <span id="ans3"></span></p>
                    </div>       
                    <div class="col-md-12" id="option4">
                        <p><b>{{ __('examination.Option') }} D :</b> <span id="ans4"></span></p>
                    </div>                         
                </div>

            </div>
        
            <div class="modal-footer">
              <button class="btn btn-danger" id="closeModal"class="close" data-bs-dismiss="modal">Close</button>
            </div>
      </div>
    </div>
</div>

<!-- The Modal -->
<div class="modal" id="Modal_id">
	<div class="modal-dialog">
		<div class="modal-content" style="background: #555b5beb;">
			<div class="modal-header">
				<h4 class="modal-title text-white">{{ __('messages.Delete Confirmation') }}</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
			<form action="{{ url('delete/question') }}" method="post"> 
			    @csrf
				<div class="modal-body">
					<input type=hidden id="delete_id" name=delete_id>
					<h5 class="text-white">{{ __('messages.Are you sure you want to delete') }} ?</h5> </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('messages.Close') }}</button>
					<button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('messages.Delete') }}</button>
				</div>
			</form>
		</div>
	</div>
</div> 

<script>
    
$('.deleteData').click(function() {
	var delete_id = $(this).data('id');
	$('#delete_id').val(delete_id);
});

$('.questionDetail').click(function() {
    $('#myModal').modal('toggle');
    
var question = $(this).data('question');
var class_type_id = $(this).data('class_type_id');
var subject_id1 = $(this).data('subject_id');
var answer1 = $(this).data('opt1');
var answer2 = $(this).data('opt2');
var answer3 = $(this).data('opt3');
var answer4 = $(this).data('opt4');
var correct_ans = $(this).data('correct_ans');

$('#question').html(question);
$('#subject_id1').html(subject_id1);
$('#ans1').html(answer1);
$('#ans2').html(answer2);
$('#ans3').html(answer3);
$('#ans4').html(answer4);

/*toastr.error(answer1);
toastr.error(answer2);
toastr.error(answer3);
toastr.error(answer4);*/
/*if(correct_ans == "0"){
    $('#ans1').addClass("bg-success");
    $('#ans2').removeClass("bg-success");
    $('#ans3').removeClass("bg-success");
    $('#ans4').removeClass("bg-success");
}else if(correct_ans == "1"){
    $('#ans2').addClass("bg-success");
    $('#ans1').removeClass("bg-success");
    $('#ans3').removeClass("bg-success");
    $('#ans4').removeClass("bg-success");    
}else if(correct_ans == "2"){
    $('#ans3').addClass("bg-success");
    $('#ans1').removeClass("bg-success");
    $('#ans2').removeClass("bg-success");
    $('#ans4').removeClass("bg-success");    
}else{
    $('#ans4').addClass("bg-success");
    $('#ans1').removeClass("bg-success");
    $('#ans2').removeClass("bg-success");
    $('#ans3').removeClass("bg-success");    
}*/

});

</script>
@endsection