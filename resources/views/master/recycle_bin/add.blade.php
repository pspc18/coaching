@php
  $classType = Helper::classType();
  $getsubject = Helper::getSubject();
    
@endphp

@extends('layout.app') 
@section('content')
    
    
<div class="content-wrapper">
    <!--<div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
                <div class="col-sm-8">
                <h3 class="m-2">Add Homework</h3>
                </div>
                <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                <li class="pr-2"><a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-home"></i> Home</a></li>
                <li class="pl-2"><a href="{{url('recycle bin/dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i> Back</a></li>
                </ol>
                </div>
            </div>
        <hr class="bg-primary" style="margin-top:-12px;">
        </div>
    </div-->
    
    
    <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">   
        <div class="card card-outline card-orange">
               <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-address-book-o"></i> &nbsp;{{ __('master.Recycle Bin') }}</h3>
            <div class="card-tools">
            <a href="{{url('recycle_bin/view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>{{ __('common.View') }}  </a>
            <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a>
           
            </div>
            
            </div>   
             <div class="card-body">
                 <form id="quickForm" action="{{ url('recycle_bin/add') }}" method="post" enctype="multipart/form-data">
                      @csrf
                <div class="row"> 
                          		 
            		<div class="col-md-3">
        			    <div class="form-group">
        				<label style="color:red;">{{ __('master.Resent') }}*</label>
        				
        					<input type="text" class="form-control @error('resent') is-invalid @enderror" id="resent" name="resent"  placeholder=" {{ __('master.Resent') }}" value="{{old('resent')}}">
        				   
                         @error('resent')
        					<span class="invalid-feedback" role="alert">
        						<strong>{{ $message }}</strong>
        					</span>
        				 @enderror
        		        </div>
        		    </div>
            	
                    </div>
              <div class="row m-2">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary ">{{ __('common.submit') }}</button>
                    </div>
                </div>
                </form>
                </div>                 
            </div> 
            </div> 
            </div> 
            </div> 
            </div> 
            </section>
        </div>
    
    
    
    
    
    
    
   <div class="ml-3"></div> 
@endsection                