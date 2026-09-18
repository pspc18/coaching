@php
    $getHostel = Helper::getHostel();
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    
<section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
            
            <!--<div class="col-md-12 pl-0">-->
            <!--    <div class="card card-outline card-orange ml-1">-->
            <!--     <div class="card-header bg-primary">-->
            <!--    <h3 class="card-title"><i class="fa fa-inbox"></i> &nbsp;{{ __('Mess Expenses Heade') }}</h3>-->
            <!--    <div class="card-tools">-->
            <!--           <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i-->
            <!--                            class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a>-->
            <!--    </div>-->
            <!--     </div>    -->
            <!--             <form id="quickForm" action="{{ url('messFoodCategoryAdd') }}" method="post" enctype="multipart/form-data">   -->
            <!--            @csrf-->
            <!--                <div class="row m-2">-->
            <!--                    <div class="col-md-5">-->
            <!--            			<label style="color:red;">{{ __('Mess Category name') }}*</label>-->
            <!--        				<input type="text" name="name" id="name" class="form-control" Placeholder="Head Name">-->
            <!--                	</div>    -->
                               
            <!--                </div>-->
            <!--                <div class="row m-2">-->
            <!--                    <div class="col-md-12 text-center" id="floor_list_show">-->
            <!--                    <button type="submit" class="btn btn-primary ">{{ __('messages.submit') }}</button>-->

            <!--                    </div>-->
            <!--                </div>-->
            <!--                </form>-->
            <!--    </div> -->
                
                
            <!--</div>-->
                
              <div class="col-md-12 pl-0">
                <div class="card card-outline card-orange ml-1">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-building"></i> &nbsp;{{ __('hostel.Mess List') }} </h3>
                    <div class="card-tools">
                    <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}</a>
                    </div>
                     </div>                 
                    <div class="row m-2">
                        <div class="col-md-12">
                    	</div>
                        <div class="col-md-12">
                           <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                <thead>   
                                    <tr role="row">
                                        <th>{{ __('common.SR.NO') }}</th>
                                        <th>{{ __('hostel.Mess Category Name') }}</th>
                                        <!--<th>{{ __('Action') }}</th>-->
                                    </tr>
                                </thead>
                                <tbody id="">
                                    @if(!empty($data))
                                    @php
                                       $i=1;
                                    @endphp
                                        @foreach($data as $item)                         
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $item['name'] ??'' }}</td>
                                    <!--        <td>-->
                                    <!--<a href="{{url('messFoodCategoryEdit',$item->id)}}" class="btn btn-primary  btn-xs ml-3" title="Edit Student Registration"><i class="fa fa-edit"></i></a>-->
                                    <!--<a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData btn btn-danger  btn-xs ml-3" title="Delete Student Registration"><i class="fa fa-trash-o"></i></a>  -->
                                               
                                    <!--        </td>-->
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
        <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('messFoodCategoryDelete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              <input type=hidden id="delete_id" name=delete_id>
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

@endsection      