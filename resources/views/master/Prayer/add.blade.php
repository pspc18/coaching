@php
   $getRole = Helper::roleType();
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
                            <h3 class="card-title"><i class="fa fa-envelope"></i> &nbsp;  {{ __('master.Add Prayer') }}</h3>
                            <div class="card-tools">
                                <a href="{{url('prayer_view')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(9)->view ? '' : 'd-none' }}" title="View Notice Bord"><i class="fa fa-eye"></i> {{ __('common.View') }} </a>
                                <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}</a>
                            </div>
                        </div>        
                        <form id="form-submit" action="{{ url('prayer_add') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row m-2">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"style="color:red;">{{ __('common.Name') }}*</label>
                                        <input class="form-control @error('name') is-invalid @enderror"id="name" name="name" placeholder="{{ __('common.Name') }}" type="text"  />
                                       
                                    </div>
                                  
                                </div>
                                </div>
                                <div class="row m-2">

                                <div class="col-md-12">
                                <div class="form-group">
                                        <label style="color:red;">{{ __('master.Prayer') }}*</label>
                                        <textarea   class="form-control @error('prayer') is-invalid @enderror"id="editor1" name="prayer"> </textarea>
                                     
                                    </div>
                                  
                                </div>
                                
                                </div> 
                                <div class="row m-2">
                             	<div class="col-md-12 text-center">
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