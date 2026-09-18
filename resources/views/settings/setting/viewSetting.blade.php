

@extends('layout.app') 
@section('content')

<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">

                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-cogs"></i> &nbsp;{{ __('setting.View Setting') }} </h3>
                    <div class="card-tools">
                      <!--@if(Session::get('role_id') == 1)
                        <a href="{{url('addSetting')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> {{ __('common.Add') }} </a>
                      @endif-->
                         <a href="{{url('settings_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a> 
                      
                       

                    </div>
                </div> 
                <div class="card-body">
                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                  <thead class="bg-primary">
                  <tr role="row">
                      <th>{{ __('common.SR.NO') }}</th>
                            <th>{{ __('setting.Logo') }}</th>
                            <th>{{ __('common.E-Mail') }} </th>
                            <th>{{ __('common.Name') }}</th>
                            <th>{{ __('common.Address') }}</th>
                            <th>{{ __('common.Mobile') }}</th>
                            <th>{{ __('setting.Pin Code') }}</th>
                            <th>{{ __('setting.Seal & Sign.') }}</th>
                            
                            <th class="{{ Helper::permissioncheck(17)->delete ? '' : 'd-none' }}">{{ __('common.Action') }}</th>
                           
                             
                      
                  </thead>
                  <tbody>
                      
                      @if(!empty($data))
                        @php
                           $i=1
                        @endphp
                        @foreach ($data  as $item)
                        <tr>
                                <td>{{ $i++ }}</td>
                               
                                <td><img src="{{ env('IMAGE_SHOW_PATH').'setting/left_logo/'.$item['left_logo'] }}" width="120px" height="50px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/no_image.png' }}'"></td>
                                <td>{{ $item['gmail'] ?? '' }}</td>
                                <td>{{ $item['name'] ?? '' }}</td>
                                <td>{{ $item['address'] ?? '' }}</td>
                                <td>{{ $item['mobile'] ?? '' }}</td>
                                <td>{{ $item['pincode'] ?? '' }}</td>
                                <td><img src="{{ env('IMAGE_SHOW_PATH').'setting/seal_sign/'.$item['seal_sign'] }}" width="120px" height="50px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/no_image.png' }}'"></td>
                               
                                    <td>
                                       
                                        <a href="{{url('editSetting',$item->id)}}" class="btn btn-primary  btn-xs tooltip1 {{ Helper::permissioncheck(17)->edit ? '' : 'd-none' }}" title1="Edit"><i class="fa fa-edit"></i></a>
                                       
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
    </section>
    

    
</div>
        
        
        <script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
        
        
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
        <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('deleteSetting') }}" method="post">
              	 @csrf
      <div class="modal-body">
              
            
            
              <input type=hidden id="delete_id" name=delete_id>
              <h5 class="text-white">{{ __('common.Are you sure you want to delete') }}  ?</h5>
           
      </div>

      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-dismiss="modal">{{ __('common.Close') }}</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('common.Delete') }}</button>
         </div>
       </form>

    </div>
  </div>
</div>

@endsection 