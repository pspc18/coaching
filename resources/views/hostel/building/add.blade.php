@php
  $getHostel = Helper::getHostel();
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    
<section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
            
            <div class="col-md-8 pl-0">
                <div class="card card-outline card-orange ml-1">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-building"></i> &nbsp;{{ __('hostel.Building List') }} </h3>
                    <div class="card-tools">
                    <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>-->
                    </div>
                     </div>                 
                    <div class="row m-2">
                        <div class="col-md-12">
                    	</div>
                        <div class="col-md-12">
                           <table id="example1" class="table table-bordered table-striped dataTable dtr-inline">
                                <thead class="bg-primary">   
                                    <tr role="row">
                                        <th>{{ __('common.SR.NO') }}</th>
                                        <th>{{ __('hostel.Hostel Name') }}</th>
                                        <th>{{ __('hostel.Building Name') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="">
                                    @if(!empty($data))
                                    @php
                                       $i=1
                                    @endphp
                                    @foreach ($data  as $item)
    
                                    @php
                                    $getHostelBuilding = Helper::getHostelBuilding($item['hostel_id']);
                                    @endphp                                
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $item['Hostel']['name'] ??'' }}</td>
                                            <td>
                                                @if(!empty($getHostelBuilding))
                                                @foreach ($getHostelBuilding  as $type)  
                                                    {{ $type['name'] ?? '' }} &nbsp;
                                                    <a href="{{ url('building_edit') }}/{{ $type['id'] ?? '' }}"  title="Edit Building"><i class="fa fa-pencil text-primary"></i></a> &nbsp;
                                                    <a href="javascript:;"  data-id='{{$type->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData"  title="Delete Building"><i class="fa fa-remove text-danger"></i></a>
                                                    <br>
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
            
            <div class="col-md-4 pr-0">
                <div class="card card-outline card-orange mr-1">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-building"></i> &nbsp;{{ __('hostel.Add Building') }}</h3>
                    <div class="card-tools">
                                        <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>

                    </div>
                    
                    </div>                 
                    <form id="quickForm" action="{{ url('building_add') }}" method="post">
                    @csrf
                    <div class="row m-2">
                        <div class="col-md-12">
                			<label style="color:red;">{{ __('hostel.Select Hostel') }}*</label>
            				<select class="select2  form-control @error('hostel_id') is-invalid @enderror" id="hostel_id" name="hostel_id">
                             @if(!empty($getHostel)) 
                                  @foreach($getHostel as $type)
                                     <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                             @error('hostel_id')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div>                    
                        <div class="col-md-12">
                            <table class="_table w-100" id="tableId">
                                <thead>
                                  <tr>
                                    <th width="80%" style="color:red;">{{ __('hostel.Building Name') }}*</th>
                                     <th width="20%"></th>
                                  </tr>
                                </thead>
                                  <tbody id="table_body">
                                      <tr id="appendRow_0">
                                        <td colspan="12">
                			                <input class="form-control @error('name') is-invalid @enderror" type="text" required name="name[]" id="name" placeholder="{{ __('hostel.Building Name') }}" value="{{ old('name') }}">
                                        </td>
                                        <td style="width: 92px;">
                                          <div class="action_container">
                                                <button type="button" class="btn btn-primary btn-xs addmoreprodtxtbx" id="clonebtn"><i class="fa fa-plus"></i></button>
                                                <button type="button" class="btn btn-danger btn-xs removeprodtxtbx" id="removerow"><i class="fa fa-trash"></i></button>
                                          </div>
                                        </td>
                                      </tr>
                                    </tbody>
                                </table>   
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
$(document).ready(function() {
    
    count=0;
        $( ".removeprodtxtbx" ).eq( 0 ).css( "display", "none" );
        $(document).on("click", "#clonebtn", function() {
    count++;
        $('#table_body').append('<tr id="appendRow_'+count+'" ><td colspan="12"><input class="form-control" type="text" required name="name[]" id="name" placeholder="{{ __('hostel.Building Name') }}" value="{{ old('name') }}"></td><td style="width: 92px;"><div class="action_container"><button type="button" class="btn btn-primary btn-xs addmoreprodtxtbx" id="clonebtn"><i class="fa fa-plus"></i></button><button type="button" class="btn btn-danger btn-xs removeprodtxtbx" id="removerow"><i class="fa fa-trash"></i></button></div></td> </tr>');
            

        $( ".removeprodtxtbx" ).eq( count ).css( "display", "block" );
        $( ".addmoreprodtxtbx" ).eq( count ).css( "display", "none" );

        });
    
        $(document).on("click", "#removerow", function() {
            $(this).parents('tr').remove();
            count--;
            window.calculateSum()
        });

});
</script>

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

      <form action="{{ url('building_delete') }}" method="post">
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