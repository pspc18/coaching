@php
$getMessageType = Helper::getMessageType();
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
                    <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('master.School Desk Description') }} </h3>
                    <div class="card-tools">
                    <!--<a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add </a>-->
                    <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}  </a>
                    </div>
                    
                    </div>                 
                <form id="form-submit-edit" action="{{ url('school_desk') }}" method="post">
                    @csrf
                    <div class="row m-2">
             
                          
                        	<div class="col-md-12 pt-2">
                                <div class="form-group">
                                    <label class="text-danger"><b>{{ __('master.Description') }}*</b></label>
                                    <textarea type="text" class="form-control @error('email_content') is-invalid @enderror" id="editor1" name="description" placeholder="{{ __('master.Description') }}">{{ $data->description ??  old('description') }}</textarea>
                                    
                                </div>
                            </div>
                            
                                              
                    </div>                        
        
                    <div class="row m-2 pb-2">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary btn-submit">{{ __('common.Update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
</section>
</div>  

<script>
$(document).ready(function(){
    var editor1 = new RichTextEditor("#editor1");
});
</script>
 @endsection    