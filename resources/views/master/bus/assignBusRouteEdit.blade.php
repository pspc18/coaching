@php
$busRoute = Helper::busRoute();
$bus = Helper::bus();
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
            <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('bus.Edit Route & Bus') }} </h3>
            <div class="card-tools">
                         <a href="{{url('assignBusRoute')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>View</a>
                         <a href="{{url('busDashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('Back') }} </a>

            </div>
            
            </div>                 
        	<form id="form-submit-edit" action="{{ url('assignBusRouteEdit') }}/{{($data->id)}}" method="post"> 

                @csrf
               	<div class="row mb-2 m-2">
		    <div class="col-md-6">
				<div class="form-group">
            			<label style="color:red;">{{ __('bus.Route') }}*</label>
        				<select class="form-control @error('route_id') is-invalid @enderror" id="route_id" name="route_id">
        				        <option value="">Select</option>
                         @if(!empty($busRoute)) 
                              @foreach($busRoute as $type)
                                 <option value="{{ $type->id ?? ''  }}" {{ ( $type->id == $data['route_id'] ? 'selected' : '' ) }}>{{ $type->name ?? ''  }}</option>
                              @endforeach
                          @endif
                        </select>
                                      			
                	</div>
                	</div>
             
		    <div class="col-md-6">
				<div class="form-group">
            			<label style="color:red;">{{ __('bus.Bus') }}*</label>
        				<select class="form-control @error('bus_id') is-invalid @enderror" id="bus_id" name="bus_id">
        				        <option value="">Select</option>
                         @if(!empty($bus)) 
                              @foreach($bus as $type)
                                 <option value="{{ $type->id ?? ''  }}" {{ ( $type->id == $data['bus_id'] ? 'selected' : '' ) }}>{{ $type->name ?? ''  }}</option>
                              @endforeach
                          @endif
                        </select>
                                         			
                	</div>  
                </div>
                </div>
 
                 <div class="col-md-12 text-center">
			<button type="submit" class="btn btn-primary btn-submit">{{ __('messages.Update') }}</button><br><br>
		</div>
    	</form>
             </div>
        </div>
    </div>
    </section>
</div>>
@endsection      