@php
  $classType = Helper::classType();
  $getsubject = Helper::getSubject();
@endphp
@extends('layout.app')
@section('content')



<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="nav-icon fas fa fa-tag"></i> &nbsp;&nbsp;Edit :: {{$data2->name ?? ''}} </h3>
                    <div class="card-tools">
                    <!--<a href="{{url('view/exam')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i> View </a>-->
                    <a href="{{url('view/exam')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}  </a>
                    </div>
                    
                    </div>        
                <form id="quickForm" action="{{ url('assign/exam') }}/{{ $data->exam_id }}/{{$data->class_type_id }}" method="post" >
                    @csrf
                    
            <input type="hidden" name="id" value="{{$data->id}}" />
                    <div class="row m-2">

                       <div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('messages.Class') }}*:</label>
            				<select class="form-control select2 @error('class_type_id') is-invalid @enderror" id="class_type_id" name="class_type_id" value="{{old('class_type_id')}}">
                            <option value="" >{{ __('messages.Select') }}</option>
                             @if(!empty($classType)) 
                                  @foreach($classType as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $data->class_type_id) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
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
		        </div>

                <div class="row m-2 pb-2">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary ">{{ __('examination.Assign') }}</button>
                    </div>
                </div>
                
            
            </form>
</div>
</div>
</div>
</div>
</section>
</div>

@endsection