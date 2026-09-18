@php
$getRole = Helper::roleType();
$classType = Helper::classType();
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
                        @if(Session::get('') == 3)
                            <h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp; {{ __('Edit Whatsapp Group') }}</h3>
                        @else						    
							<h3 class="card-title"><i class="nav-icon fas fa fa-leanpub"></i> &nbsp;{{ __('Edit Whatsapp Group') }}</h3>
						@endif
							<div class="card-tools"> 
							@if(Session::get('role_id') !== 3)
							    <a href="{{url('group_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
                            @endif

                                
                            </div>
						</div>
						
                 
        <section class="content">
            <form action='{{url("group_edit")}}/{{$data->id}}' method='post' >
                @csrf
            <div class="container-fluid">
                <div class="row m-2">
                    <div class=" col-md-12 title"><h5 class="text-danger">{{ __('Select Class') }} :-</h5></div>
                    <div class="col-md-3">
                		<div class="form-group">
                			<label style="color:red;">{{ __('messages.Select') }}*</label>
                			<select class="form-control select2 @error('class_type_id') is-invalid @enderror" id="class" name="class_type_id">
                			<option value="">{{ __('messages.Select') }}</option>
                              @if(!empty($classType))
                                @foreach($classType as $value)
                                    <option value="{{ $value->id ?? ''  }}" {{$data->class_type_id == $value->id ? 'selected' : '' }}>{{ $value->name ?? ''  }}</option>
                                @endforeach
                              @endif

                            </select>
                             @error('class_type_id')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
                	    </div>
                	</div>
                    <div class="col-md-3">
                		<div class="form-group">
                			<label style="color:red;">{{ __('Group Name') }}*</label>
                		<input type='text' class='form-control @error("group_name") is-invalid @enderror' name='group_name'  value='{{$data->group_name ?? ''}}'/>
                		 @error('group_name')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
                	    </div>
                	</div>
                    <div class="col-md-3">
                		<div class="form-group">
                			<label style="color:red;">{{ __('Group Id') }}*</label>
                	<input type='text' class='form-control @error("group_id") is-invalid @enderror' name='group_id'  value='{{$data->group_id ?? ''}}'/>
                	 @error('group_id')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
                	    </div>
                	</div>
                       	
                               	
                
                    <div class="col-md-1">
                         <label class="text-white">{{ __('&nbsp;') }}</label>
                	    <button class="btn btn-primary">{{ __('Save') }}</button>
                	</div>
                </div>
      </form>
        </section>
        <section>
         
    </div>
</div>
</div>
</div>
</div>
</section>
</div>

@endsection

