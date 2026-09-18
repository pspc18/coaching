@extends('layout.app') 
@section('content')
 <div class="content-wrapper">

    <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
				<div class="card-header bg-primary">
					<h3 class="card-title"><i class="fa fa-shirtsinbulk"></i> &nbsp;Today's Student's Birthday List</h3>
					<div class="card-tools"> 
							@if(Session::get('role_id') !== 3)
							    <a href="{{url('send_message_terminal')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
                            @endif

                                
                            </div>
				</div>   
				
			
                <div class="card-body">
                <table  class="table table-bordered table-striped dataTable dtr-inline ">
                  <thead>
                  <tr role="row">
                      <th><input type="checkbox" name="checkbox" id="select_all_student" /> {{ __('messages.Select') }}</th>
                      <!--<th>{{ __('messages.S.NO') }}</th>-->
                            <th>{{ __('Student Name') }}</th>
                            <th>{{ __('messages.Class') }}</th>
                            <th>{{ __('messages.F Name') }}</th>
                            
                            <th>{{ __('messages.Mobile') }}</th>
                            <th>{{ __('messages.E-Mail') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th >{{ __('Status') }}</th>
                            <!--<th>Joining Date</th>-->
                           
                    </tr>
                             
                      
                  </thead>
                     <form action="{{url('send_wishes')}}" method='post'>
                @csrf
                  <tbody>
                      
                      @if(!empty($data))
                     
                        @php
                           $i=1
                        @endphp
                        @foreach ($data  as $item)
                        
                        @php
                        //dd($item);
                        $old = DB::table('birthday_wishes') ->where('session_id',Session::get('session_id'))
                                ->where('branch_id',Session::get('branch_id'))->where('role_id', $item['role_id'])->where('admission_id', $item['id'])->whereNull('deleted_at')->first();
                       // dd($item);
                        @endphp
                     
                        
                      
                        <tr>
                                <td class='text-danger'>
                                     @if(!empty($old))
                                       @if($old->status ==1)
                                     
                                   <a href="{{url('resend_message')}}">Click Here</a>  For Resend Wishes
                                     
                                     @else
                                     
                                     
                                     @endif
                                    @else
                                    <input type="checkbox" name="checkbox_student[]" class="email_list_student count" value="{{ $item['id']  }}"/>
                                    @endif
                                    
                                   <input type="hidden" name="mobile_student[]"  value="{{ $item['mobile']  }}"/>
                                   <input type="hidden" name="first_name_student[]"  value="{{ $item['first_name']  }}"/>
                                    <input type="hidden" name="role_id_student[]"  value="{{ $item['role_id']  }}"/>
                                </td>
                                <!--<td>{{ $i++ }}</td>-->
                                <td>{{ $item['first_name']  }} {{ $item['last_name']  }} </td>
                                <td>{{ $item['class_name']  }}</td>
                                <td>{{ $item['father_name']  }}</td>
                                
                                <td>{{ $item['mobile']  }}</td>
                                <td>{{ $item['email']  }}</td>
                               <td>{{date('d/m/Y', strtotime($item['dob']))}}</td>
                                <td class='text-danger'>    @if(!empty($old))
                                    @if($old->status ==1)
                                  
                                   {{ $old->failed_message ?? '' }}
                                 @else
                                 
                                <span style='color:green !important'> Sent</span>
                                  @endif
                                    @else
                                  Pending
                                    @endif</td>
                                <!--<td>{{ $item['joining_date']  }}</td>-->
                                 
                              
                      </tr>
                     
                      @endforeach
                @endif
            </tbody>
                  </table>
                  
                  
                  
                  
                  
              </div>
              
            </div>
           
        </div>
        
        
            
                      <div class="col-12">
            <div class="card card-outline card-orange">
				<div class="card-header bg-primary">
					<h3 class="card-title"><i class="fa fa-shirtsinbulk"></i> &nbsp;Today's User's Birthday List</h3>
			
				</div>   
				
			
                <div class="card-body">
               
                  
                  
                  
                   <table  class="table table-bordered table-striped dataTable dtr-inline ">
                  <thead>
                  <tr role="row">
                      <th><input type="checkbox" name="checkbox" id="select_all_user" /> {{ __('messages.Select') }}</th>
                      <!--<th>{{ __('messages.S.NO') }}</th>-->
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('messages.F Name') }}</th>
                            
                            <th>{{ __('messages.Mobile') }}</th>
                            <th>{{ __('messages.E-Mail') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <!--<th>Joining Date</th>-->
                           
                    </tr>
                             
                      
                  </thead>
                
                  <tbody>
                      
                      @if(!empty($data2))
                     
                        @php
                           $i=1
                        @endphp
                        @foreach ($data2  as $item)
                         @php
                        $old = DB::table('birthday_wishes')->where('role_id', $item['role_id'])->where('user_id_sender', $item['id'])->whereNull('deleted_at')->first();
                        
                        @endphp
                       
                        <tr>
                                <td class='text-danger'>
                                     @if(!empty($old))
                                     
                                     @if($old->status ==1)
                                     
                                   <a href="{{url('resend_message')}}">Click Here</a>  For Resend Wishes
                                     
                                     @else
                                     
                                     
                                     @endif
                                     @else
                                      <input type="checkbox" name="checkbox_user[]" class="email_list_user count" value="{{ $item['id']  }}"/>
                                     @endif
                                   
                                    <input type="hidden" name="mobile_user[]"  value="{{ $item['mobile']  }}"/>
                                    <input type="hidden" name="first_name_user[]"  value="{{ $item['first_name']  }}"/>
                                    <input type="hidden" name="role_id_user[]"  value="{{ $item['role_id']  }}"/>
                                </td>
                                <!--<td>{{ $i++ }}</td>-->
                                <td>{{ $item['first_name']  }} {{ $item['last_name']  }}</td>
                                <td>{{ $item['father_name']  }}</td>
                                
                                <td>{{ $item['mobile']  }}</td>
                                <td>{{ $item['email']  }}</td>
                                <td>{{date('d/m/Y', strtotime($item['dob']))}}</td>
                                <td class='text-danger'>@if(!empty($old))
                                  @if($old->status ==1)
                                  
                                   {{ $old->failed_message ?? '' }}
                                 @else
                                 
                               <span style='color:green !important'> Sent</span>
                                  @endif
                                  
                                    @else
                                  Pending
                                    @endif</td>
                                <!--<td>{{ $item['joining_date']  }}</td>-->
                                 
                              
                      </tr>
                     
                      @endforeach
                @endif
            </tbody>
                  </table>
              </div>
              
            </div>
           
        </div>
        
        <div class="col-12 d-none">
            <div class="card card-outline card-orange">
				<div class="card-header bg-primary">
					<h3 class="card-title"><i class="fa fa-shirtsinbulk"></i> &nbsp;Today's Marriage Anniversary List</h3>
					<div class="card-tools"> 
							@if(Session::get('role_id') !== 3)
							    <a href="{{url('send_message_terminal')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
                            @endif

                                
                            </div>
				</div>   
				
			
                <div class="card-body">
                    <table  class="table table-bordered table-striped dataTable dtr-inline ">
                        <thead>
                  <tr role="row">
                      <th><input type="checkbox" name="checkbox" id="select_all_marriageAnniversary" /> {{ __('messages.Select') }}</th>
                      <!--<th>{{ __('messages.S.NO') }}</th>-->
                            <th>{{ __('Father Name') }}</th>
                            <th>{{ __('messages.Class') }}</th>
                            <th>{{ __('messages.F Name') }}</th>
                            
                            <th>{{ __('messages.Mobile') }}</th>
                            <th>{{ __('messages.E-Mail') }}</th>
                            <th>{{ __('Marriage Anniversary Date') }}</th>
                            <th >{{ __('Status') }}</th>
                            <!--<th>Joining Date</th>-->
                           
                    </tr>
                             
                      
                  </thead>
                  
                        <tbody>
                      
                      @if(!empty($data3))
                     
                        @php
                           $i=1
                        @endphp
                        @foreach ($data3  as $item)
                        
                        @php
                        //dd($item);
                        $old = DB::table('birthday_wishes')->where('role_id', $item['role_id'])->where('admission_id', $item['id'])->whereNull('deleted_at')->first();
                        
                        @endphp
                     
                        
                      
                        <tr>
                                <td class='text-danger'>
                                     @if(!empty($old))
                                       @if($old->status ==1)
                                     
                                   <a href="{{url('resend_message')}}">Click Here</a>  For Resend Wishes
                                     
                                     @else
                                     
                                     
                                     @endif
                                    @else
                                    <input type="checkbox" name="checkbox_student[]" class="email_list_student count" value="{{ $item['id']  }}"/>
                                    @endif
                                    
                                   <input type="hidden" name="mobile_student[]"  value="{{ $item['mobile']  }}"/>
                                   <input type="hidden" name="first_name_student[]"  value="{{ $item['first_name']  }}"/>
                                    <input type="hidden" name="role_id_student[]"  value="{{ $item['role_id']  }}"/>
                                </td>
                                <!--<td>{{ $i++ }}</td>-->
                                <td>{{ $item['father_name']  }} </td>
                                <td>{{ $item['class_name']  }}</td>
                                <td>{{ $item['father_name']  }}</td>
                                
                                <td>{{ $item['mobile']  }}</td>
                                <td>{{ $item['email']  }}</td>
                               <td>{{date('d/m/Y', strtotime($item['marriage_anniversary']))}}</td>
                                <td class='text-danger'>    @if(!empty($old))
                                    @if($old->status ==1)
                                  
                                   {{ $old->failed_message ?? '' }}
                                 @else
                                 
                                <span style='color:green !important'> Sent</span>
                                  @endif
                                    @else
                                  Pending
                                    @endif</td>
                                <!--<td>{{ $item['joining_date']  }}</td>-->
                                 
                              
                      </tr>
                     
                      @endforeach
                @endif
            </tbody>
                    </table>
                  
                  
                  
                  
                  
              </div>
              
            </div>
           
    </div>
                    
        
        <div class="col-12 text-center">
            
         
                 <button class="btn btn-success" id="submit" >Send Wishes</button>
            </form>
           </div>
      </div>
      </div>
    </section>
</div>

<script>
   
    var clicked = 0 ;
    var clicked_1 = 0 ;
    $("#select_all_student").click(function(){
        clicked = 0;
        var array = [];
  if($("#select_all_student").prop('checked') == false){
   
    $( ".email_list_student" ).each(function( index ) {
              $(this).prop("checked", false) ;
                //  $("input:text").remove();
              
});

}
else if ($("#select_all_student").prop('checked') == true)
{
  $( ".email_list_student" ).each(function( index ) {
 
                  $(this).prop("checked", true) ;
                  
                
                array.push($(this).val())
                
                });
                
            clicked = array.l 
}

}); 


$("#select_all_marriageAnniversary").click(function(){
        clicked = 0;
        var array = [];
  if($("#select_all_marriageAnniversary").prop('checked') == false){
   
    $( ".email_list_student" ).each(function( index ) {
              $(this).prop("checked", false) ;
                //  $("input:text").remove();
              
});

}
else if ($("#select_all_marriageAnniversary").prop('checked') == true)
{
  $( ".email_list_student" ).each(function( index ) {
 
                  $(this).prop("checked", true) ;
                  
                
                array.push($(this).val())
                
                });
                
            clicked = array.l 
}

}); 



 $("#select_all_user").click(function(){
        clicked = 0;
        var array = [];
  if($("#select_all_user").prop('checked') == false){
   
    $( ".email_list_user" ).each(function( index ) {
              $(this).prop("checked", false) ;
                //  $("input:text").remove();
              
});

}
else if ($("#select_all_user").prop('checked') == true)
{
  $( ".email_list_user" ).each(function( index ) {
 
                  $(this).prop("checked", true) ;
                  
                
                array.push($(this).val())
                
                });
                
            clicked = array.l 
}

}); 


 $("#submit").click(function (e) {
     
      
    if($('.count:checked').length == 0) {
          e.preventDefault();
          alert("There is no selected value for send wishes");
    }
    
   
    
});
</script>

<script>
   
</script>



@endsection