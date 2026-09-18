@extends('layout.app')
@section('content')
@php
$getCounters = Helper::getCounters();
@endphp
<div class="content-wrapper">

    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row" >
                 <div class="col-12 col-md-12">
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-hospital-o"></i> &nbsp; {{ __('fees.Fees Counter') }}</h3>
                            <div class="card-tools">
                            </div>
                    
                        </div>               
                    </div>
                </div>
                
                @if(!empty(Helper::SidebarSubPerm(11)))
                        @foreach(Helper::SidebarSubPerm(11) as $sub_sidebar)
                        <div class="col-md-3 col-6">
                            <a href="{{url($sub_sidebar['url'] ?? '')}}" class="small-box-footer">
                                <div class="small-box bg-{{$sub_sidebar['bg_color'] ?? ''}}">
                                    <div class="inner">
                                        <h4 class="mobile_text_title"> @if(Session::get('locale') == 'hi'){{$sub_sidebar['hindi_name'] ?? ''}} @else {{$sub_sidebar['name'] ?? ''}} @endif </h4>
                                        <p>{{ __('common.Enter') }}</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa {{$sub_sidebar['ican'] ?? ''}}"></i>
                                    </div>
                                    <div class="text-center small-box-footer">{{ __('common.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                 @endif
                
             </div>
             
            <!--<div class="row" style="margin-top:5%;">
                <div class="col-12 col-md-12">
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-money"></i> &nbsp;{{ __('fees.Fees Management') }}</h3>
                            <div class="card-tools">
                               <a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add User</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>-->
        </div>
            <div class="row" style="margin-top:5%;">
             <!--<div class="col-12 col-md-12">
                <div class="card card-outline card-orange">
                    <div class="card-header bg-primary">
                        <h3 class="card-title"><i class="fa fa-hospital-o"></i> &nbsp; {{ __('Library Fees Management') }}</h3>
                        <div class="card-tools">
                            <a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add User</a>
                        </div>
                
                    </div>               
                </div>
            </div>-->
        </div>

        </div>
    </section>
</div>



 <div class="modal fade" id="staticBackdrop" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Counter Authentication</h5>
       
            <button type="button" style="visibility:hidden" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        
      </div>
      <div class="modal-body">
          
           <table  class="text-center table table-bordered table-striped dtr-inline ">
               <thead>
                   <th colspan="50">Counter </th>
                   <th colspan="50">Password </th>
               </thead>
               <tbody>
                    <td colspan="50" class="p-3"> 
                        @if(!empty($getCounters))
                            <select  name="counter_name" id="counter_id" class="form-control" required>
                                 
                                 <option value=""> Select</option>
                             @foreach($getCounters as $item)
                             <option value="{{$item->id ?? '' }}"> {{ $item->name}}</option>
                             @endforeach
                             
                            </select>
                        @endif
                    </td>
                   <td colspan="50" class="p-3"><input type="password" id="password" name="password" required/></td>
                     <i class="toggle-password fa fa-eye" onclick="togglePasswordVisibility()"></i>
               </tbody>
               </table>
    
      </div>
      <div class="modal-footer">
        <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
        <button type="button" class="btn btn-primary" id="get_in">Get In</button>
            <a class="text-white"  href="{{ URL::previous() }}">
                <button type="button" class="btn btn-danger">
                    Back
                </button>
            </a>
      </div>
    </div>
  </div>
</div>
  
  <button style="visibility:hidden" type="button" class="btn btn-primary" id="modal_id" data-toggle="modal" data-target="#staticBackdrop">
  Open Modal
</button>
  <style>
      /* Assuming you have Font Awesome for the eye icon */
.toggle-password {
 position: absolute;
  right: 18%;
  top: 54%;
  transform: translateY(-50%);
  cursor: pointer;
}

  </style>
  <script>
  
    function togglePasswordVisibility() {
  const passwordInput = document.getElementById('password');
  const eyeIcon = document.querySelector('.toggle-password');

  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    eyeIcon.classList.remove('fa-eye');
    eyeIcon.classList.add('fa-eye-slash');
  } else {
    passwordInput.type = 'password';
    eyeIcon.classList.remove('fa-eye-slash');
    eyeIcon.classList.add('fa-eye');
  }
}

    
    $( document ).ready(function() {
        
        var session_counter = "{{ Session::get('counter_id')}}";
        if(session_counter == '')
        {
            // $("#modal_id").trigger("click");
        }
});
     
     
     
      $("#get_in").click(function() {
        var password =$("#password").val();
        var counter_id =$("#counter_id").val();
     
     
        var basurl = "{{ url('/') }}";
            $.ajax({
                 type: "post",
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
        	    url: basurl+'/check_authentication',
        	     data:{counter_id:counter_id,password,password},
        	    success: function(result){
        	     
                if(result == "DONE")
                {
                    $('.close').trigger('click');
                }
                else
                {
                   	toastr.error("Wrong Credentials");
                }
 	        	 
 
 	       	  }
        	});
      });
     
       
</script>
@endsection