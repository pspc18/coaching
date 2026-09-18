
@extends('layout.app') 
@section('content')

@php
$classType = Helper::classType();

@endphp
<div class="content-wrapper" >

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">    
         
        <div class="col-md-4 pr-0 {{ Session::get('role_id') == 3 ? 'd-none' : '' }}">
            <div class="card card-outline card-orange mr-1">
             <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-cloud-download"></i> &nbsp;Edit Content</h3>
            <div class="card-tools">
           
            </div>
            
            </div>                 

                <form id="form-submit-edit" action="{{ url('upload/content_edit') }}/{{$upload_data['id']}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row m-2">
                    	<div class="col-md-12">
									<div class="form-group">
										<label>{{ __('common.Class') }}</label>
										<select class="form-control select2 " id="class_search_id" name="class_search_id">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($classType))
											@foreach($classType as $type)
											<option value="{{ $type->id ?? ''  }}" {{$type->id==$upload_data["class_type_id"] ? "selected" : ""}} >{{ $type->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
									</div>
								</div>
                        <div class="col-md-12">
                         
                			<label style="color:red;">{{ __('master.Content Title') }}*</label>
                			<input class="form-control @error('content_title') is-invalid @enderror" type="text" name="content_title" id="content_title" placeholder="{{ __('master.Content Title') }}" value="{{ $upload_data['content_title'] ?? ''}}">
                            @error('content_title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror                    			
                    	</div>
                        <div class="col-md-12">
                			<label style="color:red;">{{ __('master.Content Type') }}*</label>
            				<select class="  form-control @error('content_type') is-invalid @enderror" id="content_type" name="content_type">
                                <option value="" >Select</option>
                                <option name="Assignments" {{ ( "Assignments" == $upload_data['content_type'] ? 'selected' : '' ) }} value="Assignments">Assignments</option>
                                <option name="Study Material" {{ ( "Study Material" == $upload_data['content_type'] ? 'selected' : '' ) }} value="Study Material">Study Material</option>
                                <option name="Syllabus" {{ ( "Syllabus" == $upload_data['content_type'] ? 'selected' : '' ) }} value="Syllabus">Syllabus</option>
                                <option name="Other Downloads" {{ ( "Other Downloads" == $upload_data['content_type'] ? 'selected' : '' ) }} value="Other Downloads">Other Downloads</option>
                            </select>
                             @error('content_type')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div>  
                        <div class="col-md-12">
                			<label style="color:red;">{{ __('master.Upload Date') }}*</label>
                			<input class="form-control @error('upload_date') is-invalid @enderror" type="date" name="upload_date" id="upload_date" value="{{$upload_data['upload_date'] ?? ''}}">
                                @error('upload_date')
                        			<span class="invalid-feedback" role="alert">
                        				<strong>{{ $message }}</strong>
                        			</span>
                    		    @enderror                			
                    	</div>                   
                    	 <div class="col-md-12">
                    			<label>{{ __('Video Link') }}</label>
                    			<input class="form-control" type="text" name="video_link" id="video_link" placeholder="{{ __('Video Link') }}" value="{{$upload_data['video_link'] ?? old('video_link')}}">
                      	
                    	</div> 
                        <div class="col-md-12">
                    			<label>{{ __('master.Description') }}</label>
                    			<textarea class="form-control" type="text" name="description" id="description" placeholder="{{ __('master.Description') }}" value="{{$upload_data['description'] ?? ''}}">{{$upload_data['description'] ?? ''}}</textarea>
                    	</div> 
                        <div class="col-md-12">
                			<label >{{ __('master.Content File') }}</label>
                                <input type="file" class="input file form-control @error('content_file') is-invalid @enderror" name="content_file" id="content_file" value="{{$upload_data['content_file'] ?? ''}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_error"></p>
                                @error('content_file')
                        			<span class="invalid-feedback" role="alert">
                        				<strong>{{ $message }}</strong>
                        			</span>
                    		    @enderror
                                                
                    	</div>                    	
                </div>
 
                <div class="row m-2">
                    <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary btn-submit">{{ __('master.Submit') }} </button>
                    </div>
                </div>
                </form>
            </div>          
        </div>
        
    <div class="col-md-8 pl-0">
            <div class="card card-outline card-orange ml-1">
             <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-cloud-download"></i> &nbsp;{{ __('master.Content List') }} </h3>
            <div class="card-tools">
         
            <a href="{{url('upload/content')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> {{ __('master.Add') }} </a>
            </div>
            
            </div>                 
                <div class="row m-2">
                    <div class="col-md-12">
                	</div>
                    <div class="col-md-12">
                       <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                          <thead class="bg-primary">
                          <tr role="row">
                              <th>{{ __('master.Sr.No.') }}</th>
                              <th>{{ __('master.Content Title') }}</th>
                              <th>{{ __('master.Content Type') }}</th>
                              <th>{{ __('master.Date') }}</th>
                              <th>{{ __('Description') }}</th>
                              <th>{{ __('master.Action') }}</th>
                              
                              
                              
                          </thead>
                          <tbody id="">
                          
                          @if(!empty($dataview))
                                @php
                                   $i=1
                                @endphp
                                @foreach ($dataview  as $item)
                                <tr>
                                        <td>{{ $i++ }}<!--<img src="{{ env('IMAGE_SHOW_PATH').'download_center/'.$item['content_file'] }}" width="60px" height="60px">--></td>
                                        <td>{{ $item['content_title'] ?? '' }}</td>
                                        <td>{{ $item['content_type'] ?? '' }}</td>
                                        <td>{{ $item['upload_date'] ?? '' }}</td>
                                              <td>{{ $item['description'] ?? '' }}</td>
                                        <td>
                                           
                                            

                                            @if(($item['video_link'] ?? '') != '')
                                            <a class='text-primary tooltip1' target='_blank'href="{{$item['video_link'] ?? '' }}" title1="Play Video"><i class="fa fa-play-circle-o text-dark"></i></a>
                                            @endif

                                            @if(($item['content_file'] ?? '') != '')
                                           <a href="{{ url('download/'.$item['id']) }}"
   class="ml-2 tooltip1 {{ Helper::permissioncheck(12)->print ? '' : 'd-none' }}"
   title1="Download">
    <i class="fa fa-download text-success"></i>
</a>
                                            @endif

                                            <a href="{{ url('upload/content_edit') }}/{{$item['id'] ?? '' }}" class="ml-2 tooltip1 {{ Helper::permissioncheck(12)->edit ? '' : 'd-none' }}" title1="Edit"><i class="fa fa-edit text-primary"></i></a>
                                            
                                            <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData ml-2 tooltip1 {{ Helper::permissioncheck(12)->delete ? '' : 'd-none' }}" title1="Delete"><i class="fa fa-trash-o text-danger"></i></a>
                                           
                                        </td>
                                    </tr>
                           @endforeach
                        @endif
                          </tbody>
                          </table>
                	</div>
                </div>
            </div>          
        </div>
    </div>
</div>
</section>
</div>

<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
</script>

<!-- The Modal -->
<div class="modal" id="Modal_id">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #555b5beb;">

            <div class="modal-header">
                <h4 class="modal-title text-white">Delete Confirmation</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>

            <form action="{{ url('upload_delete') }}" method="post">
                 @csrf
                <div class="modal-body">
                    <input type=hidden id="delete_id" name=delete_id>
                    <h5 class="text-white">Are you sure you want to delete  ?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $('#content_file').change(function(e){
            $('#image_error').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "mp4" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_error').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_error').html("");
            }
        }else{
            $('#image_error').html("Image Size File");
            $(this).val('');
        }
        });
    });
    </script>
    
    <style>
    #image_error{
        font-weight: bold;
    font-size: 14px;
    }
    </style>
@endsection      