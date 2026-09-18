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
            <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('master.Edit Sessions') }} </h3>
            <div class="card-tools">
                     <a href="{{url('session_add')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>{{ __('common.View') }} </a>
                     <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}</a>
              </div>
            
            </div>                 
             <form id="form-submit-edit" action="{{url('sessions_edit')}}/{{$data['id']}}" method="post" > 
               @csrf
              	<div class="row mb-2 m-2">
		    <div class="col-md-6">
				<div class="form-group">
        				<label style="color:red;">{{ __('master.From Year') }}* </label>
        				<input type="text" class="form-control @error('from_year') is-invalid @enderror" id="year" name="from_year" placeholder="From Year" value="{{ $data->from_year ??  '' }}" maxlength="4" onkeypress="javascript:return isNumber(event)">
            		       
                        </select>
        		    </div>
    		    </div>
                      <div class="col-md-6">
				<div class="form-group">
        				<label style="color:red;">{{ __('master.To Year') }}* </label>
        				<input type="text" class="form-control @error('to_year') is-invalid @enderror" id="to_year" name="to_year" placeholder="To Year" value="{{ $data->to_year ??  '' }}" maxlength="2" onkeypress="javascript:return isNumber(event)">
            		      
                        </select>
        		    </div>
    		    </div>
                                     	
                </div>
 
                 <div class="col-md-12 text-center">
			<button type="submit" class="btn btn-primary btn-submit">{{ __('common.Update') }}</button><br><br>
		</div>
    	</form>
             </div>
        </div>
    </div>
    </section>
</div>>
@endsection      