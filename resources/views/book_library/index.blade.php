@php
    $getLibrary = Helper::getLibrary();
    $getLibraryCabinAll = Helper::getLibraryCabinAll();
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
							<h3 class="card-title"><i class="fa fa-book"></i> &nbsp;  View Library Books</h3>
							<!--<div class="card-tools"> -->
       <!--                             <a href="{{url('library_book_add')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i>{{ __('messages.Add') }}  </a> -->
							<!--    <a href="{{url('library_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}  </a> -->
							<!--</div>-->
						</div>
                        
                       
                            <div class="row m-2">
              
                            </div> 
                      
                        
                            <div class="row m-2">
                                <div class="col-md-12">
                                    <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                        <thead>
                                            <tr role="row">
                                                <th>{{ __('messages.Sr.No.') }}</th>
                                              <th>Category</th>
                                                <th>Book Name</th>
                                                <th>Authour Name</th>
                                                <th>Publisher Name</th>
                                                <th>Date</th>
                                                <th>Edition </th>
                                                <th>Brand </th>
                                                <th>Quantity </th>
                                                <th>Scan QR Code </th>
                                                <th>Image</th>
                                                
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
                                                
                                                <td>{{ $item['catname'] ?? '' }}</td>
                                                <td>{{ $item['name'] ?? '' }}</td>
                                                <td>{{ $item['author'] ?? '' }}</td>
                                                <td>{{ $item['publisher'] ?? '' }}</td>
                                                <td>{{date('d-m-Y', strtotime($item['date'])) ?? ''}}</td>
                                                <td>{{ $item['edition'] ?? '' }}</td>
                                                <td>{{ $item['brand'] ?? '' }}</td>
                                                <td>{{ $item['quantity'] ?? '' }}</td>
												@php
													$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
												@endphp
                                                <td> <img src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($item['book_code'], $generatorPNG::TYPE_CODE_128)) }}"></td>
                                                <td> 
                                                 @if($item->image)
                                                    <img src="{{ env('IMAGE_SHOW_PATH').'Book_img/'.$item['image'] }}" class="img-fluid" style="width:50px; height:50px;" >
                                                @else
                                                    <img src="{{asset('schoolimage/library/k.png')}}" class="img-fluid" style="max-width:80px" alt="avatar.png">
                                                @endif
                                                                                                  
                                                </td>
                                                                                  
                                            </tr>
                                            @endforeach
                                            
                                            @else
                                            <tr><td colspan="12" class="text-center">No Book Found !</td></tr>
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
                       <!-- <div class="modal" id="Modal_id">
                          <div class="modal-dialog">
                            <div class="modal-content" style="background: #555b5beb;">
                        
                              <div class="modal-header">
                                <h4 class="modal-title text-white">Delete Confirmation</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                              </div>
                        
                              <form action="{{ url('library_book_delete') }}" method="post">
                                      	 @csrf
                              <div class="modal-body">
                                      <input type=hidden id="delete_id" name="delete_id">
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
                        


<script>
$(document).ready(function(){
    $(".deleteData").click(function(){
        var delete_id = $(this).data('id');
        $("#delete_id").val(delete_id);
    })
})
</script>-->
@endsection      