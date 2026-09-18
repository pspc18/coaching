@php
$getPermission = Helper::getPermission();
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    
<section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
            
            <div class="{{($getPermission->add == 1) ? 'col-md-8 pl-0' : 'col-md-12 pl-0'}}">
                <div class="card card-outline card-orange ml-1">
                 <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-unlock-alt"></i> &nbsp;Locker List </h3>
                <div class="card-tools">
                <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>-->
                </div>
                 </div>                     
                    <div class="row m-2">
                        <div class="col-md-12">
                    	</div>
                        <div class="col-md-12">
                           <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                                <thead>   
                                  <!--  <tr role="row">
                                        <th>{{ __('common.SR.NO') }} </th>
                                        <th>Locker Name</th>
                                        @if($getPermission->edit == 1 || $getPermission->deletes == 1)
                                        <th>{{ __('common.Action') }}</th>
                                        @endif
                                    </tr>-->
                                </thead>
                                <tbody id="">
                                    @if(!empty($data))
                                    @php
                                       $i=1
                                    @endphp
                                      <tr>
                                            <td>
                                    @foreach ($data  as $item)
                                     
                                                <div class="locker profile-pic" id="locker-670">{{ $item['name'] ??'' }} 	
                                                   <div class="edit">
                                                     @if($getPermission->deletes == 1)
                                                <a href="javascript:;" data-id='{{$item['id'] }}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData" title="Delete Library"><i class="fa fa-trash-o"></i></a>
                                                @endif
                                                </div>
                                                </div>
                                            <!-- @if($getPermission->edit == 1)
                                                <a href="{{ url('library/lockerEdit') }}/{{ $item['id'] ?? '' }}" class="btn btn-primary  btn-xs" title="Edit Library" ><i class="fa fa-edit"></i></a> 
                                                @endif-->
                                            

                                               
                                            
                                        
                                       
                                    @endforeach
                                    </td>
                                     </tr>
                                    @endif
                                </tbody>
                              </table>
                    	</div>
                    </div>
                </div>          
            </div>
            
            <div class="col-md-4 pr-0 {{($getPermission->add == 1) ? '' : 'd-none'}}">
                <div class="card card-outline card-orange mr-1">
                 <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-unlock-alt"></i> &nbsp;Locker Add</h3>
                <div class="card-tools">
                <a href="{{url('library_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}</a>
                </div>
                
                </div>                 
                    <form id="quickForm" action="{{ url('library/locker') }}" method="post">
                    @csrf
                    <div class="row m-2">
                        <div class="col-md-12">
                			<label style="color:red;">Locker Name*</label>
                			<input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" placeholder="Locker Name" onfocus="this.setSelectionRange(this.value.length, this.value.length);"  value="L-{{$data->count()+1}}">
                             @error('name')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
                    	</div>
                    </div>
     
                    <div class="row m-2">
                        <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">{{ __('common.submit') }} </button>
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
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
  

var val =$("#name").val();
$("#name").removeAttr('value').attr('value', val).focus();
// I think this is beter for all browsers...


 </script>

<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('library/lockerDelete') }}" method="post">
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
<style>
.profile-pic {
	position: relative;
	display: inline-block;
}

.profile-pic:hover .edit {
	opacity: 1;
}

.edit {
	position: absolute;
	top: 0px;	
	left: 0px;
	display:flex;
	align-items:center;
	justify-content:center;
	opacity: 0;
	transition:0.3s;
	width: 100%;
    height: 100%;
}

.edit a {
    color: white;
    background: red;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size:12px;
}
    .locker
{
  display: inline-block;
  border: 1px solid black;
  width: 55px;
  border-radius: 8px;
  height: 60px;
  text-align: center;
  cursor: pointer;
  margin: 5px;
    margin-bottom: 5px;
  position: relative;
  font-weight: 700;
  background: #d9edf7;
    background-position-x: 0%;
    background-position-y: 0%;
    background-repeat: repeat;
    background-image: none;
    background-size: auto;
  background-image: url({{env('IMAGE_SHOW_PATH').'default/safe-available.png' }});
  background-size: 38px;
  background-position: bottom;
  background-repeat: no-repeat;
  margin-bottom: 20px;
  user-select: none;
}
</style>
@endsection      