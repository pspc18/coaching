@php

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
                    <h3 class="card-title"><i class="fa fa-image"></i> {{ __('master.Edit Uniform') }}</h3>
                    <div class="card-tools">
                    <a href="{{url('uniform_add')}}" class="btn btn-primary  btn-sm" title="View Gallery"><i class="fa fa-eye"></i> {{ __('common.View') }} </a>
                    <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}</a>
                    </div>
                    
                    </div>        
                <form id="form-submit-edit" action="{{ url('uniform_edit') }}/{{$data['id']}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row m-2">
        
                          <div class="col-md-3">
                            <div class="form-group">
                               <label>{{ __('master.Uniform Image') }}</label>
                               <input type="file" class="form-control" value="{{ $data['uniform_image'] ?? ''}}" id="uniform_image" name="uniform_image">
								
                            </div>
                        </div>
                          <div class="col-md-3">
                            <img src="{{ env('IMAGE_SHOW_PATH').'uniform_image/'.$data['uniform_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/no_image.png' }}'" width="160px" height="160px" alt="uniform" />
                        </div>
                        
                        <div class="col-md-12 pt-2">
                                <div class="form-group">
                                    <label class="text-danger"><b>{{ __('master.Description') }}*</b></label>
                                    <textarea type="text" class="form-control " id="editor1" name="description" placeholder="{{ __('master.Description') }}">{{ $data['description'] ??  old('description') }}</textarea>
                               
                                </div>
                            </div>
                        </div>
                        <div class="row m-2">
                     
                         <div class="col-md-12 mt-3 mb-3 text-center">
                            <button type="submit" class="btn btn-primary btn-submit">{{ __('common.Submit') }}</button>
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