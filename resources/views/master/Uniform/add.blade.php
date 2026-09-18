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
                    <h3 class="card-title"><i class="fa fa-image"></i>{{ __('master.Add Uniform') }} </h3>
                    <div class="card-tools">
                    <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm" title="View Gallery"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
                    </div>
                    
                    </div>        
                <form id="form-submit" action="{{ url('uniform_add') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row m-2">
        
                          <div class="col-md-3">
                            <div class="form-group">
                               <label>{{ __('master.Uniform Image') }}</label>
                               <input type="file" class="form-control @error('uniform_image') is-invalid @enderror" value={{old('uniform_image')}} id="uniform_image" name="uniform_image[]" multiple>
                           
                            </div>
                        </div>
						<div class="col-md-12 pt-2">
                                <div class="form-group">
                                    <label class="text-danger"><b>{{ __('master.Description') }}*</b></label>
                                    <textarea type="text" class="form-control" id="editor1" name="description" placeholder="{{ __('master.Description') }}"></textarea>
                                  
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


     
        <div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-support"></i> &nbsp; {{__('master.View Uniform') }}</h3>
							<div class="card-tools"> 
                            </div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="card-body">
									<table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
										<thead class="bg-primary">
											<tr>
												<th>{{__('common.SR.NO') }}</td>
											
												<td>{{__('common.Photo') }}</td>
												<td>{{__('master.Description') }}</td>
												<td>{{__('common.Action') }}</td>
											</tr>
										</thead>
										<tbody> 
										@if(!empty($data)) 
										    @php 
										        $i=1 
										    @endphp 
										    @foreach ($data as $item)
											<tr>
												<td>{{ $i++ }}</td>
												
												<td>
												<!--<img src="{{ env('IMAGE_SHOW_PATH').'uniform_image/'.$item['uniform_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/no_image.png' }}'" width="60px" height="60px">-->
											 <a href="javascript:;" class="imageData2 ml-3" title="" data-bs-toggle="modal" data-bs-target="#Modal_id2" data-image-src="{{ env('IMAGE_SHOW_PATH').'uniform_image/'.$item['uniform_image'] }}">
                                                <img src="{{ env('IMAGE_SHOW_PATH').'uniform_image/'.$item['uniform_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/no_image.png' }}'" width="60px" height="60px">
                                             </a>
												</td>

												<td>{!! html_entity_decode($item->description ?? '') !!}</td>
											<td>
												    <a href="{{url('uniform_edit') }}/{{$item->id}}" class="btn btn-primary  btn-xs ml-3 tooltip1 {{ Helper::permissioncheck(9)->edit ? '' : 'd-none' }}" title1="Edit Sports"><i class="fa fa-edit"></i></a> 
												    <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3 tooltip1 {{ Helper::permissioncheck(9)->delete ? '' : 'd-none' }}" title1="Delete Sports"><i class="fa fa-trash-o"></i></a> 
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
            </div>

</section>
</div>


<script>
    $('.deleteData').click(function() {
	var delete_id = $(this).data('id');
	$('#delete_id').val(delete_id);
});

    $(document).ready(function() {
        $('.imageData2').click(function() {
            var imageSrc = $(this).data('image-src');
            $('#modalImage').attr('src', imageSrc);
        });
    });
</script>

<div class="modal fade" id="Modal_id2" tabindex="-1" aria-labelledby="Modal_id2_Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #ffffff;color: black;">
            <div class="modal-header">
                <h4 class="modal-title">Uniform Image</h4>
                <a type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </a>
            </div>
            <div class="modal-body">
                <img id="modalImage" src="" width="100%">
            </div>
        </div>
    </div>
</div>
<div class="modal" id="Modal_id">
	<div class="modal-dialog">
		<div class="modal-content" style="background: #555b5beb;">
			<div class="modal-header">
				<h4 class="modal-title text-white">Delete Confirmation</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
			<form action="{{ url('uniform_delete') }}" method="post"> 
			    @csrf
				<div class="modal-body">
					<input type=hidden id="delete_id" name=delete_id>
					<h5 class="text-white">Are you sure you want to delete  ?</h5> </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-danger waves-effect waves-light">Delete</button>
				</div>
			</form>
		</div>
	</div>
</div>


<script>
$(document).ready(function(){
    var editor1 = new RichTextEditor("#editor1");
});
</script>
@endsection