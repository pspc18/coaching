@extends('layout.app') 
@section('content')


<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-cloud-download"></i> &nbsp;{{ __('download.Other Downloads') }}</h3>
							<div class="card-tools"> <a href="{{url('download_center')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a> </div>
						</div>
				
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
          <thead class="bg-primary">
          <tr role="row">

                                        <th>{{ __('messages.Sr.No.') }}</th>
                                        <th>{{ __('messages.Content Title') }}</th>
                                        @if(Session::get('role_id') == 1 || Session::get('role_id') == 2)
                                            <th>{{ __('Class') }}</th>
                                        @endif

                                       
                                        <th>{{ __('messages.Type') }}</th>
                                        <th>{{ __('messages.Date') }}</th>
                                         <th>{{ __('Link') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('messages.Action') }}</th>

                                </thead>
                                <tbody>
                      
                                    @if(!empty($data))
                                    @php
                                       $i=1
                                    @endphp
                                    @foreach ($data  as $item)
                                    @if($item->content_type =="Other Downloads")
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $item['content_title']  }}</td>
                                        @if(Session::get('role_id') == 1 || Session::get('role_id') == 2)
                                           <td>{{ $item['class_name']  }}</td>
                                        @endif

                                       
                                        <td>{{ $item['content_type']  }}</td>
                                        <td>{{date('d-m-Y', strtotime($item['upload_date'])) ?? '' }}</td>
                                          <td>   @if(($item['video_link'] ?? '') != '')
                                        <a class='text-primary' target='_blank'href="{{$item['video_link'] ?? '' }}" >Click to View</a>
                                        @endif</td>
                                            <td>{{ $item['description'] ?? ''  }}</td>
                                        <td>
                                        
                                                <a href="{{ url('download') }}/{{$item['id'] ?? '' }}" class="ml-2 tooltip1 {{ Helper::permissioncheck(12)->print ? '' : 'd-none' }}" title1="Download"><i class="fa fa-download text-success"></i></a>
                                              
                                                <a href="{{ url('download') }}/{{$item['id'] ?? '' }}" class="ml-2 tooltip1 {{ Helper::permissioncheck(12)->print ? '' : 'd-none' }}" title1="Download"><i class="fa fa-download text-success"></i></a>
                                               
                                                <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData ml-4 tooltip1 {{ Helper::permissioncheck(12)->delete ? '' : 'd-none' }}" title1="Delete"><i class="fa fa-trash-o text-danger"></i></a>
                                               
                                        </td>
                                        
                                    </tr>
                                    @endif
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
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

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title text-white">Delete Confirmation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
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


<div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoModalLabel">Demo Video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe width="100%" height="315" src="https://www.youtube.com/watch?v=SFgUg9y0b8U" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div> 
<script>
    $( ".change_src" ).on( "click", function() {
        
        var src = $(this).attr('data-src');
 $('iframe').attr('src',src)
});
</script>    
@endsection