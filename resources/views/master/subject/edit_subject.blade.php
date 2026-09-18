@php
   $getCountry = Helper::getCountry();
    $getState = Helper::getState();
    $getCity = Helper::getCity();

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
							<h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('master.Edit Subject') }}</h3>
							
							<div class="card-tools">
                                        <!--<a onclick="history.back()" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>-->
                                         <a href="{{url('add_subject')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-eye"></i> {{ __('View') }}</a>
                                         <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}</a>
          
                        </div>
                           </div>                      
                          <form id="quickForm" action="{{ url('edit_subject') }}/{{($data->id)}}" method="post" >
                                @csrf
                        		<div class="row mb-2 m-2">
                        		    <div class="col-md-4">
                        				<div class="form-group">
                        					<label class="text-danger">{{ __('common.Subject') }}*</label>
                        					<input type="text" class="form-control @error('subject') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="subject" name="subject" placeholder="{{ __('common.Subject') }}" value="{{$data->name}}">
                        					@error('subject')
                        						<span class="invalid-feedback" role="alert">
                        							<strong>{{ $message }}</strong>
                        						</span>
                        					@enderror
                        				</div>
                        			</div>
                        			<div class="col-md-4">
                                    	<div class="form-group">
                                			<label>{{ __('common.Class') }}</label>
                                			<select class="form-control select2" id="class_type_id" name="class_type_id" >
                                			<option value="">{{ __('common.Select') }}</option>
                                             @if(!empty(Helper::classType())) 
                                                  @foreach(Helper::classType() as $type)
                                                  @if($type->id != 11 && $type->id != 12)
                                                     <option value="{{ $type->id ?? ''  }}" {{ ( $type->id == $data['class_type_id'] ? 'selected' : '' ) }}>{{ $type->name ?? ''  }}</option>
                                                  @endif
                                                  @endforeach
                                              @endif
                                            </select>
                                	    </div>
                                </div>
                        
                        		</div>
        <div class="col-md-12 text-center">
			<button type="submit" class="btn btn-primary">{{ __('common.Update') }}</button><br><br>
		</div>
    	</form>
            </div>
        </div>
    </div>
    </section>
</div>>

@endsection