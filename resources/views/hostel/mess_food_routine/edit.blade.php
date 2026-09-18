@php
$all_class = Helper::classType();
$getSection = Helper::getSection();
//dd($data);
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
                            <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('hostel.Edit Head Expencese') }}
                            </h3>
                            <div class="card-tools">
                                <a href="{{url('messFoodRoutineAdd')}}" class="btn btn-primary  btn-sm"><i
                                        class="fa fa-eye"></i>{{ __('View') }} </a>
                                 <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 

                            </div>
                        </div>

                        <form id="quickForm" action="{{ url('messFoodRoutineEdit') }}/{{$data['id']}}" method="post">
                            @csrf
                          <div class="row m-2">
                                <div class="col-md-3">
                        			<label style="color:red;">{{ __('hostel.Routine name') }}*</label>
                    				<input type="text" name="name" id="name" class="form-control" Placeholder="{{ __('hostel.Routine name') }}" value="{{$data['name'] ?? ''}}">
                            	</div>    
                                <div class="col-md-3">
                        			<label style="color:red;">{{ __('hostel.Routine Time Frome') }}*</label>
                                    <input type="time" name="frome_time" id="frome_time" class="form-control" value="{{isset($data['frome_time']) ? date('h:i A', strtotime($data['frome_time'])) : ''}}" >
                            	</div>                 	
                                <div class="col-md-3">
                        			<label style="color:red;">{{ __('hostel.Routine Time To') }}*</label>
                                   <input type="time" name="to_time" id="to_time" class="form-control" value="{{isset($data['to_time']) ? date('h:i A', strtotime($data['to_time'])) : ''}}" >

                            	</div>                 	
                              
                            </div>



                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary ">{{ __('common.Update')
                                    }}</button><br><br>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </section>
</div>



@endsection