@php
$getUser = Helper::getUser();
@endphp
@extends('student_login.layout.app')
@section('title', 'Edit Leave')
@section('page_title', 'EDIT LEAVE')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])
@section('content')
<section class="common-page">
 <div class="common-box m-2 border-0">
     <div class="leave-form-box p-3">                 
                <form id="quickForm" action="{{ url('updateLeaveStudent') }}/{{$data['id']}}" method="post">
                @csrf
              
                       
                               <div class="form-group">
                    
                			<label style="color:red;">Subject*</label>
            			<input class="leave-input @error('subject') is-invalid @enderror" type="input" id="subject" name="subject" value="{{$data['subject'] ?? ''}}">
                             @error('subject')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div> 
                        <div class="form-group">
                			<label style="color:red;">From Date*</label>
            				<input class="leave-input @error('from_date') is-invalid @enderror" type="date" id="from_date" name="from_date" value="{{$data['from_date'] ?? ''}}">
                             @error('from_date')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div>                     	
                        <div class="form-group">
                			<label style="color:red;">To Date*</label>
            				<input class="leave-input @error('to_date') is-invalid @enderror" type="date" id="to_date" name="to_date" value="{{$data['to_date'] ?? ''}}">
                             @error('to_date')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div>                     	
                        <div class="form-group">
                    			<label style="color:red;">Reason*</label>
                    			<textarea class="leave-input @error('reason') is-invalid @enderror" type="text" name="reason" id="reason" placeholder="Reason" >{{$data['reason'] ?? ''}}</textarea>
                             @error('reason')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror 
                    	</div>                      	
             <button type="submit" class="btn-leave-send">UPDATE</button>
                </form>
            </div>          
  </div>
        
</section>
@endsection      