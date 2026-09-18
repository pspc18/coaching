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
            
            <div class="{{($getPermission->add == 1) ? 'col-md-8 pl-0' : 'col-md-12 pl-0'}}">
                <div class="card card-outline card-orange ml-1">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-slack"></i> &nbsp;{{ __('library.Cabin List') }}</h3>
                    <div class="card-tools">
                    </div>
                     </div>                 
                    <div class="row m-2">
                        <div class="col-md-12">
                    	</div>
                        <div class="col-md-12">
                           <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                                <thead>   
                                    <tr role="row">
                                        <th>{{ __('common.SR.NO') }}</th>
                                        <th>{{ __('library.Library Name') }}</th>
                                        <th>{{ __('library.Cabin Name No.') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="">
                                    @if(!empty($data))
                                    @php
                                       $i=1
                                    @endphp
                                    @foreach ($data  as $item)
    
                                    @php
                                   
                                    $getLibraryCabin = Helper::getLibraryCabin($item['library_id']);
                                    @endphp                                
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $item['Library']['name'] ??'' }}</td>
                                            <td width="68%" >
                                                @if(!empty($getLibraryCabin))
                                                @foreach ($getLibraryCabin  as $type)  
                                               
                                                    <div class=" profile-pic seat seat_not_assigned" id="seat-3" seat-code="S" >S-{{ $type['name'] ?? '' }}
                                                <div class="edit">
                                                     @if($getPermission->deletes == 1)
                                                        <a href="javascript:;" data-id='{{$type['id'] }}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData ml-3" title="Delete Library"><i class="fa fa-trash-o" style="color: red;"></i></a>
                                                @endif
                                                </div>
                                                    </div> 
                                                     
                                                @endforeach
                                                @endif                                        
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
            
            <div class="{{($getPermission->add == 1) ? '' : 'd-none'}} col-md-4 pr-0">
                <div class="card card-outline card-orange mr-1">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-slack"></i> &nbsp;{{ __('library.Add Cabin') }} </h3>
                    <div class="card-tools">
                    <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>-->
                    <a href="{{url('library_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
                    </div>
                    
                    </div>                 
                    <form id="quickForm" action="{{ url('cabin_add') }}" method="post">
                    @csrf
                    <div class="row m-2">
                        <div class="col-md-12">
                			<label style="color:red;">{{ __('library.Select Library') }}*</label>
            				<select class="select2  form-control @error('library_id') is-invalid @enderror" id="library_id" name="library_id">
                             <option value="">{{ __('messages.Select') }}</option>
                             @if(!empty($getLibrary)) 
                                  @foreach($getLibrary as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ( $type['id'] == old('library_id')) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                             @error('library_id')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div>   
                    	
                        <!--<div class="col-md-6">
                			<label style="color:red;">Cabin No. From*</label>
                			<input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" placeholder="Cabin No. From" value="{{ old('name') }}" onkeypress="javascript:return isNumber(event)">
                             @error('name')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
                    	</div>                    	
                        <div class="col-md-6">
                			<label style="color:red;">Cabin No. To*</label>
                			<input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" placeholder="Cabin No. To" value="{{ old('name') }}" onkeypress="javascript:return isNumber(event)">
                             @error('name')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
                    	</div> -->                   	
                        <div class="col-md-12">
                			<label style="color:red;">{{ __('library.Total Cabin No.') }}*</label>
                			<input class="form-control @error('name') is-invalid @enderror" type="number" name="name" id="name" placeholder="{{ __('library.Total Cabin No.') }}" value="{{ old('name') }}">
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
</script>

<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('cabin_delete') }}" method="post">
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
	display: block;
}

.edit {
	padding-top: 7px;	
	padding-right: 7px;
	position: absolute;
	right: 0;
	top: 0;
	display: none;
}

.edit a {
	color: #000;
}
    .seat
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
  background-image: url(https://www.walsisindia.com/library/images/car-seat-available.png);
  background-size: 38px;
  background-position: bottom;
  background-repeat: no-repeat;
  margin-bottom: 20px;
  user-select: none;
}
</style>
@endsection      