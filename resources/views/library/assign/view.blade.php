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
							<h3 class="card-title"><i class="fa fa-book"></i> &nbsp; {{ __('library.All Assign Books') }}</h3>
							<div class="card-tools"> 
        
							    <a href="{{url('library_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
							</div>
						</div>
                        

                          <form id="quickForm" action="{{ url('book_assign_view') }}" method="post">
              @csrf
              <div class="row m-2">
                <div class="col-md-2 mb-2 top">
                    		<div class="form-group">
                    			<label>{{ __('expense.From Date') }}</label>
            				<input type="date" class="form-control" id="from_date" name="from_date"  value="{{ $_POST['from_date'] ?? '' }}" >
                            </div>
                    	</div>
                    	<div class="col-md-2 top">
                            <div class="form-group ">
                                <label>{{ __('expense.To Date') }}</label>
            				<input type="date" class="form-control" id="to_date" name="to_date"  value="{{ $_POST['to_date'] ?? '' }}">
                			</div> 
                        </div>
         
                
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>{{ __('common.Search By Keywords') }}</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Ex. Name, Book Code, Book Name, etc.') }}" value="{{ $search['name'] ?? '' }}">
                          </div>
                        </div>
                        <div class="col-md-1 ">
                          <label class="text-white">{{ __('common.Search') }}</label>
                          <button type="submit" class="btn btn-primary">{{ __('common.Search') }}</button>
                        </div>
        
                      </div>
                    </form>
                        
                            <div class="row m-2">
                                <div class="col-md-12">
                                    <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                          
                                       <thead>
                                        <tr role="row">
                                            <th>{{ __('library.Id') }}</th>
                                            <th>{{ __('library.Student Name') }}</th>
                                             <th>{{ __('library.Student Mobile') }}</th>
                                            <th>{{ __('library.Book Name') }}</th>
                                            <th>{{ __('Book Code') }}</th>
                                            
                                            <th>{{ __('library.Brand') }} </th>
                                            <th>{{ __('common.Date') }} </th>
                                           
                                        </tr>
                                    </thead>
                                    <tbody id="">
                                        
                                            @if(!empty($data))
                                            
                                            @php
                                            
                                                $i=1;
                                               //dd($data);
                                            @endphp
                                         
                                            @foreach ($data  as $item)
                                            <tr>
                                        
                                           
                                            <td>{{ $i++}}</td>
                                            <td>{{ $item->first_name ?? '' }}</td>
                                            <td>{{ $item->mobile ?? '' }}</td>
                                            <td>{{ $item->name ?? '' }}</td>
                                            <td>{{ $item->book_code ?? '' }}</td>
                                            <td>{{ $item->brand ?? '' }}</td>
                                            <td>{{$item->created_at->format('d-m-Y')}}</td>
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



<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('messages.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('book_category_delete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              <input type=hidden id="delete_id" name=delete_id value="">
              <h5 class="text-white">{{ __('messages.Are you sure you want to delete') }}  ?</h5>
      </div>
      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('messages.Close') }}</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('messages.Delete') }}</button>
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