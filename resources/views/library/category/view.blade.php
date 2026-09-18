@php
    $getLibrary = Helper::getLibrary();
    $getPermission = Helper::getPermission();
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">

	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-book"></i> &nbsp; {{ __('library.View Books Category') }} </h3>
							<div class="card-tools"> 
                                    <a href="{{url('book_category_add')}}" class="{{($getPermission->add == 1) ? '' : 'd-none'}} btn btn-primary  btn-sm"><i class="fa fa-plus"></i>{{ __('common.Add') }}  </a> 
							    <a href="{{url('library_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
							</div>
						</div>
                        

                          
                        
                            <div class="row m-2">
                                <div class="col-md-12">
                                    <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                        <thead>
                                            <tr role="row">
                                                <th>{{ __('common.SR.NO') }}</th>
                                               
                                                <th>{{ __('library.Book Name') }}</th>
                                              
                                               @if($getPermission->edit == 1 || $getPermission->deletes == 1)
                                                <th>{{ __('common.Action') }}</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody id="student_list_show">
            
                                            @if(!empty($data))
                                            @php
                                                $i=1
                                            @endphp
            
                                            @foreach ($data  as $item)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $item['name'] ?? '' }}</td>
                                                
                                            @if($getPermission->edit == 1 || $getPermission->deletes == 1)
                                                <th>
                                                    @if($getPermission->edit == 1)
                                                        <a href="{{ url('book_category_edit') }}/{{ $item['id'] ?? '' }}" class="btn btn-primary ml-3 btn-xs" title="Edit Book Category" ><i class="fa fa-edit"></i></a>  
                                                    @endif
                                                    @if($getPermission->deletes == 1)
                                                        <a href="javascript:;" data-id='{{ $item['id'] ?? '' }}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger btn-xs ml-3" title="Delete Book Category"><i class="fa fa-trash-o"></i></a> 
                                                    @endif
                                                </th>   
                                            @endif
                                            </tr>
                                            @endforeach
                                            
                                            @else
                                            <tr><td colspan="12" class="text-center">{{ __('library.No Book Found') }} !</td></tr>
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



<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('book_category_delete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              <input type=hidden id="delete_id" name=delete_id value="">
              <h5 class="text-white">{{ __('common.Are you sure you want to delete') }}  ?</h5>
      </div>
      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('common.Delete') }}</button>
         </div>
       </form>
    </div>
  </div>
</div>


                  
<script>
$(document).ready(function(){
    $(".deleteData").click(function(){
        var delete_id = $(this).data('id');
        $("#delete_id").val(delete_id);
    })
})
</script>
@endsection      