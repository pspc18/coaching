@php
  $classType = Helper::ClassType();
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
                    <h3 class="card-title"><i class="nav-icon fas fa fa-leanpub"></i> &nbsp;{{__('Add Exam Term') }}</h3>
                    <div class="card-tools">
                    <a href="{{url('view/exam_term')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(8)->view ? '' : 'd-none' }}" title="View Users"><i class="fa fa-eye"></i> {{ __('common.View') }} </a>
                    <a href="{{url('view/exam_term')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
                    </div>
                    
                    </div>        
                <form id="quickForm" action="{{ url('add/exam_term') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row m-2">
                       <div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('Exam Term Name') }}*</label>
            				<input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('Exam Term Name') }}" value="{{old('name')}}">
                             @error('name')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
            		    </div>
            		</div>
                    
        		
		        </div>

                <div class="row m-2 pb-2">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary ">{{ __('common.Submit') }}</button>
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