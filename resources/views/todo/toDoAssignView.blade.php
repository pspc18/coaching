@php

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
        <h3 class="card-title"><i class="fa fa-money"></i> &nbsp;{{ __('To Do List View') }}</h3>
        <div class="card-tools">
                <a href="{{url('/')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
        </div>
        
        </div>  
     
      

    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
          <thead>
          <tr role="row">
              <th>{{ __('Sr.No') }}</th>
                     <th>User Name</th>
                    <th>Title</th>
                    <th>Deadline</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Description</th>
                       @if(Session::get('role_id') == 1)
                    <th>Action</th>
                   @endif
          </thead>
       <tbody>
           
           @if(!empty($data))
           @foreach($data as $key=> $task)
           <tr>
               <td>{{$key+1}}</td>
               
               <td>{{$task->first_name ?? ''}} {{$task->last_name ?? ''}} <span class='text-danger'>[{{$task->role_name ?? ''}}]</span></td>
               <td>{{$task->name}}</td>
               
               <td> @if($task->deadline != '')
               
               {{ \Carbon\Carbon::parse($task->deadline)->format('d/m/Y')}}
               @endif
               </td>
               
               
               <td>{{$task->priority}}</td>
               
                 @if(Session::get('role_id') == 1)
                   <td>@if($task->status == 0)
               Pending
               @elseif($task->status == 1)
               Working
               @elseif($task->status == 2)
               Completed
               @elseif($task->status == 3)
               Verified & Completed
               @endif
               </td>
               
               
               @else
               <td>
                   
                   @if($task->status == '3')
                   Verified & Completed
                   @else
                <select class="form-control taskStatus" data-id="{{$task->id ?? ''}}" name='status' required>
                    <option value="0" {{ 0 == $task->status ? 'selected' : '' }}>Pending</option>
                    <option value="1" {{ 1 == $task->status ? 'selected' : '' }}>Working</option>
                    <option value="2" {{ 2 == $task->status ? 'selected' : '' }}>Completed</option>
                    
                    <!--<option value="3" {{ 3 == $task->status ? 'selected' : '' }}>Verified by Admin</option>-->
                  </select>
                  
                  @endif
                 </td>
                 @endif
             
               <td>{{$task->description ?? ''}}</td>
               @if(Session::get('role_id') == 1)
               <td>
                   <a href="{{ url('to_do_assign_edit') }}/{{ $task->id }}"><button class="btn btn-xs btn-primary"><i class="fa fa-edit"></i></button></a>
                   <button class="btn btn-xs btn-danger deletebtn" data-id="{{ $task->id }}" data-toggle="modal" data-target="#deleteModal">
                       <i class="fa fa-trash"></i>
                   </button>
               </td>
               @endif
               
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
</div>
</section>
</div>
        
        


<div class="modal" id="myModal">
    <div class="modal-dialog modal-xl" >
      <div class="modal-content">
        <div class="modal-header">
            
            <h4 class="text-center" style="width:100%;">{{ __('common.Class') }}: <span id="class_type_id1"></span> (<span id="section_id1"></span>) &nbsp; &nbsp; {{ __('common.Name') }}: <span id="first_name"></span></h4>   
                  <button type="button" id="closeModal"class="close" data-bs-dismiss="modal" aria-hidden="true">x</button>
        </div>
        <form action="{{ url('editFeesDetails') }}" method="post">
            @csrf
              <input type="hidden" id="admission_id" name="admission_id">
            <div class="modal-body">
                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                  <thead>
                  <tr role="row">
                      <th>{{ __('common.SR.NO') }}</th>
                            <th>{{ __('library.Fees Type') }}</th>
                            <th>{{ __('library.Paid Amount') }}</th>
                             <th>{{ __('common.Action') }}</th>
                  </thead>
                  <tbody  id="tbody">
                      
                  
                    </tbody>
                </table>

            </div>
        
            <div class="modal-footer">
              <button type="submit" class="btn btn-danger" id="closeModal"class="close" data-dismiss="modal">{{ __('common.Submit') }}</button>
            </div>
        </form>
      </div>
    </div>
</div>

<div class="modal fade" id="deleteModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Delete Confirmation</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form action="{{ url('to_do_assign_delete') }}" method="POST">
            @csrf
        <!-- Modal body -->
        <div class="modal-body">
          <input type="hidden" id="delete_task_id" name="id">
          <p class="mb-0">Are you sure you want to delete this?</p>
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
        </form>
        
      </div>
    </div>
  </div>

 
<script>
    $(document).ready(function(){
        $('.deletebtn').click(function(){
            var delete_id = $(this).data('id');
            $('#delete_task_id').val(delete_id);
        }); 
    });
    
      $('.taskStatus').change(function(){
    
    var id = $(this).data('id');
    var value = $(this).val();
    
                $.ajax({
                 type: "post",
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
        	    url: 'taskStatusChange',
        	     data:{id:id,value:value},
        	    success: function(result){
        	     
       toastr.success('Status Changed Successfully');
 	       	  }
        	});
        	});

    
             $('.feesDetail').click(function() {
                 count=2;
	var first_name = $(this).data('first_name');
	var class_type_id = $(this).data('class_type_id');
	var section_id = $(this).data('section_id');
	var admission_id = $(this).data('admission_id');

// 	$('#first_name').html(first_name);
// 	$('#class_type_id1').html(class_type_id);
// 	$('#section_id1').html(section_id);
// 	$('#admission_id').val(admission_id);
   
            $.ajax({
                 type: "post",
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
        	    url: 'getFeesDetail',
        	     data:{admission_id:admission_id},
        	    success: function(result){
        	     
        	  jQuery('#tbody').html(result)
        	   $('#myModal').modal('toggle');
        	         	$('#first_name').html(first_name);
                        $('#class_type_id1').html(class_type_id);
                    	$('#section_id1').html(section_id);
 	                    $('#admission_id').val(admission_id);
 	        	 
 	        	   $(".editable").each(function () {
        //Reference the Label.
        var label = $(this);
 
        //Add a TextBox next to the Label.
        label.after("<input type = 'text' style = 'display:none;width:100px;' />");
 
        //Reference the TextBox.
        var textbox = $(this).next();
 
        //Set the name attribute of the TextBox.
        
        textbox[0].name = this.id.replace("n"+count, "amount[]");
    count++;
        //Assign the value of Label to TextBox.
        textbox.val(label.html());
 
        //When Label is clicked, hide Label and show TextBox.
        label.click(function () {
            $(this).hide();
            $(this).next().show();
        });
 
        //When focus is lost from TextBox, hide TextBox and show Label.
        textbox.focusout(function () {
            $(this).hide();
            $(this).prev().html($(this).val());
            $(this).prev().show();
        });
    });
 	       	  }
        	});
        
             });
</script>
@endsection 