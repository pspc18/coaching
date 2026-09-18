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
							<h3 class="card-title"><i class="fa fa-book"></i> &nbsp;{{ __('library.View Library Books') }}  </h3>
							<div class="card-tools"> 
							@if($getPermission->download == 1)
                                <button type="button" class="btn btn-primary  btn-sm" id="btnDownload"><i class="fa fa-download"></i> {{ __('library.Barcode') }}  </button> 
                            @endif
                            @if($getPermission->add == 1)
                                 <a href="{{url('library_book_add')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i> {{ __('common.Add') }}  </a> 
							@endif  
							    <a href="{{url('library_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}  </a> 
							</div>
						</div>
						
						<div class="col-md-12">
                    <form id="quickForm" action="{{ url('library_book_view') }}" method="post" >
                        @csrf 
                <div class="row m-2">
            	<div class="col-md-3">
            		<div class="form-group">
            			<label>{{ __('library.Form Date') }}</label>
            		    <input type="date" class="form-control" name="form_date" id="form_date" />
            	    </div>
            	</div>
            	<div class="col-md-3">
            		<div class="form-group">
            			<label>{{ __('library.To Date') }}</label>
            		    <input type="date" class="form-control" name="to_date" id="to_date" />
            	    </div>
            	</div>
        		<div class="col-md-3">
        			<div class="form-group">
        				<label>{{ __('common.Search By Keywords') }}</label>
        				<input type="text" class="form-control" id="name" name="name" placeholder="{{ __('common.Ex. Name, Mobile, Email, Aadhaar etc.') }}" value="{{ $search['name'] ?? '' }}">
        		    </div>
        		</div> 
                <div class="col-md-1 ">
                     <label for="" class="text-white">{{ __('common.Select') }}</label>
                     <div class="btn-group">
                        <button type="submit" class="btn btn-primary">{{ __('common.Search') }}</button>
                        <!--@if($getPermission->download == 1)-->
                        <!--<button type="submit" name="barcode_download" value="barcode_download" class="btn btn-success">{{ __('library.Barcode') }}</button>-->
                        <!--@endif-->
                    </div>
            	    
            	</div>
            			
            </div>
        </form>
        </div>
                        
                            <div class="row m-2">
                                <div class="col-md-12">
                                    <table id="example1" class="table table-bordered table-responsive table-striped dataTable dtr-inline">
                                        <thead>
                                            <tr role="row">
                                                <th>{{ __('common.SR.NO') }}</th>
                                                <th>{{ __('library.catname') }}</th>
                                                <th>{{ __('library.Book Name') }}</th>
                                                <th>{{ __('library.Authour Name') }}</th>
                                                <th>{{ __('library.Publisher Name') }}</th>
                                                <th>{{ __('library.Date') }} </th>
                                                <th>{{ __('library.Edition') }} </th>
                                                <th>{{ __('library.Brand') }} </th>
                                                <th>{{ __('Almari No.') }} </th>
                                                <th>{{ __('library.Quantity') }} </th>
                                                <th>MRP.</th>
                                                <th>Scan Bar Code</th>
                                                <th>{{ __('library.Image') }}</th>
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
                                                <td>{{ $item['catname'] ?? '' }}</td>
                                                <td>{{ $item['name'] ?? '' }}</td>
                                                <td>{{ $item['author'] ?? '' }}</td>
                                                <td>{{ $item['publisher'] ?? '' }}</td>
                                                <td>{{date('d-m-Y', strtotime($item['date'])) ?? '' }}</td>
                                                <td>{{ $item['edition'] ?? '' }}</td>
                                                <td>{{ $item['brand'] ?? '' }}</td>
                                                <td>{{ $item['almari_no'] ?? '' }}</td>
                                                <td>{{ $item['quantity'] ?? '' }}</td>
                                                <td>{{ $item['mrp'] ?? '' }}</td>
                                            @php
                                              $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
                                            @endphp
                                                <td class="text-center"> 
                                                @if(!empty($item->book_code))
                                                <img src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($item['book_code'], $generatorPNG::TYPE_CODE_128)) }}"><br><small>{{ $item['book_code'] ?? '' }}</small>
                                                @endif
                                                </td>
                                                <td> 
                                                 @if($item->image)
                                                    <img src="{{ env('IMAGE_SHOW_PATH').'Book_img/'.$item['image'] }}" class="img-fluid" style="width:50px; height:50px;" >
                                                @else
                                                <img src="{{asset('schoolimage/library/book_image.png')}}" class="img-fluid" style="max-width:80px" alt="avatar.png">
                                                @endif
                                                </td>
                                                 @if($getPermission->edit == 1 || $getPermission->deletes == 1)
                                                <td class="p-2">
                                                    @if($getPermission->edit == 1)
                                                        <a href="{{ url('library_book_edit') }}/{{ $item['id'] ?? '' }}" class="btn btn-primary ml-3 btn-xs" title="Edit Student" ><i class="fa fa-edit"></i></a>  
                                                    @endif
                                                    @if($getPermission->deletes == 1)
                                                        <button data-id="{{ $item['id'] ?? '' }}"  class="deleteData btn btn-danger btn-xs ml-3" ><i class="fa fa-trash"></i></button> 
                                                    @endif
                                                </td>   
                                                @endif
                                            </tr>
                                            @endforeach
                                            
                                            @else
                                            <tr><td colspan="12" class="text-center">{{__('library.No Book Found') }} !</td></tr>
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
        <h4 class="modal-title text-white">{{__('common.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('library_book_delete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              <input type="hidden" id="delete_id" name="delete_id">
              <h5 class="text-white">{{__('common.Are you sure you want to delete') }}  ?</h5>
      </div>
      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{__('common.Close') }}</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">{{__('common.Delete') }}</button>
         </div>
       </form>
    </div>
  </div>
</div>

<div class="modal" id="barcodeModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
          <div class="text-right w-100">
        <button type="button" class="btn-close" data-dismiss="modal" onclick="closeModal()"><i class="fa fa-times" aria-hidden="true"></i></button>
</div>
        
      </div>        
        
        <div class="row m-2 p-3 scrollable-container" id="capture">
			@php
				$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
			@endphp                                
            @foreach($data as $item)
                @if(!empty($item->book_code))
                <div class="col-md-3 text-center p-3">
                <img src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($item['book_code'], $generatorPNG::TYPE_CODE_128)) }}"><br><small>{{ $item['book_code'] ?? '' }}</small>
                </div>
                @endif                                
            @endforeach
        </div>
    </div>
  </div>
</div>
                        
<script>
    function reloadPage() {
      location.reload();
    }
  </script>

<script>
$(document).ready(function(){
    $( "#example1" ).on( "click", ".deleteData", function() {
        var delete_id = $(this).data('id');
        $("#delete_id").val(delete_id);
        $("#Modal_id").modal('show');
    })
})

function closeModal(){
    $("#barcodeModal").hide();
}

function download(url){
  var a = $("<a style='display:none' id='js-downloder'>")
  .attr("href", url)
  .attr("download", "BookBarcodes.png")
  .appendTo("body");

  a[0].click();

  a.remove();
   $("#barcodeModal").toggle('hide');
}

function saveCapture(element) {
  html2canvas(element).then(function(canvas) {
    download(canvas.toDataURL("image/png"));
  })
}

$('#btnDownloadBarcode').click(function(){
    var element = document.querySelector("#capture");
    saveCapture(element)
})
$('#btnDownload').click(function(){
    $("#barcodeModal").toggle('modal');
})
</script>
<style>
    .scrollable-container {
       
        max-height: 400px; /* You can adjust this value as needed */
        max-width:100%; /* You can adjust this value as needed */
        overflow-y: auto; /* Enable vertical scrolling */
        border: 1px solid #ccc; /* Optional: Add a border for better visualization */
    }
</style>

@endsection      