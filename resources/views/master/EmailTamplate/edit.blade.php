@php
  $classType = Helper::classType();
    
@endphp

@extends('layout.app') 
@section('content')
    <link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/dist/css/skins/_all-skins.min.css">

<link rel="stylesheet" href="https://adminlte.io/themes/AdminLTE/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">

    
<div class="content-wrapper">
           
    <div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
                <div class="col-sm-6">
                <h1 class="m-2">Marks Management</h1>
                
                </div>
                <div class="col-sm-6">
                   <ol class="breadcrumb float-sm-right">
                   <li class="breadcrumb-item"><a href="{{url('dashboard')}}" data-widget="remove" data-toggle="tooltip" title="Remove">Home</a></li>
                  <li class="breadcrumb-item active"><a href="{{url('homework/dashboard')}}">Back</a></li>
                </ol>
                </div>
            </div>
            <hr class="bg-primary">
        </div>
    </div>
        <div class="card m-2">
             <div class="card-body">
                 <form id="quickForm" action="{{ url('homework/add') }}" method="post">
                      @csrf
                <div class="row"> 
               	  <div class="col-md-6">
        			    <div class="form-group">
        				<label>Titel</label>
        					<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" readonly name="name"value="{{$data['name'] ? : old('name')}}">
                             @error('name')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				 @enderror
        		        </div>
        		    </div>
        		    <div class="col-md-6">
        			    <div class="form-group">
        				<label>Subject</label>
        					<input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject"value="{{$data['subject'] ? : old('subject')}}">
                             @error('subject')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				 @enderror
        		        </div>
        		    </div>
        		   <div class="col-md-6">
        			    <div class="form-group">
        				<label>Subject</label>
        				<textarea id="editor1" name="editor1" rows="10" cols="80" >                                            This is my textarea to be replaced with CKEditor.
                    </textarea>
        				
        		        </div>
        		    </div>
               
              </div>
              <div class="row m-2">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-info ">Submit</button>
                    </div>
                </div>
                </form>
                </div>                 
            </div> 
        </div>
    </div>       
    <script>
        
    </script>
    <script>
  $(function () {
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace('editor1')
    //bootstrap WYSIHTML5 - text editor
    $('.textarea').wysihtml5()
  })
</script>
@endsection                