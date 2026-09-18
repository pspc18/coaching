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
    					<h3 class="card-title"><i class="fa fa-shirtsinbulk"></i> &nbsp; {{ __('View Inventory ') }}</h3>
    					    <div class="card-tools">
    					        @if(Session::get('role_id') == 1)
    					        <a href="{{url('invantory_add')}}" class="btn btn-primary"  ><i class="fa fa-plus"></i> {{ __('common.Add') }} </a> 
    					        <a href="{{url('invantory_dashboard')}}" class="btn btn-primary"  ><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a> 
    			                @endif
        			           <!-- @if(Session::get('role_id') !== 3)
        			            <a href="{{url('')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i> Back</a> 
        			            @endif-->
    			            </div>
    				    </div>  
    				    <form id="quickForm" action="{{ url('view/invantory_item') }}" method="post" >
                            @csrf 
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                    <thead class="bg-primary">
                                        <tr role="row">
                                            <th>{{ __('invantory.S.NO.') }}</th>
                                            <th>{{ __('Invoice No') }}</th>
                                            <th>{{ __('Party Name') }}</th>
                                            <th>{{ __('Mobile') }}</th>
                                            <th>{{ __('invantory.Date') }}</th>
                                            <th>{{ __('Qty') }}</th>
                                            <th>{{ __('Total Amount') }}</th>
                                            <th>{{ __('invantory.Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($data))
                                            @php
                                                $i=1
                                            @endphp
                                            @foreach ($data  as $item)
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $item['invoice_no'] ?? ''  }} </td>
                                                    <td>{{ $item['party_name']  }}</td>
                                                    <td>{{ $item['mobile']  }}</td>
                                                    
                                                    <td>{{date('d-m-Y', strtotime($item['date'])) ?? '' }}</td>
                                                    <td>{{ $item['total_qty']  }}</td>
                                                    <td>{{ $item['total_amount']  }}</td>
                                                    <td><a href="{{ url('invantory_edit') }}/{{ $item['id'] ?? '' }}" class="btn btn-primary  btn-xs tooltip1" title1='Edit' ><i class="fa fa-edit"></i></a>
                                                        <a href="javascript:;" data-id='{{$item['id'] }}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger btn-xs ml-3 tooltip1" title1='Delete'><i class="fa fa-trash-o"></i></a></td>
                                                </tr>    
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </form>
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
        <h4 class="modal-title text-white">{{__('common.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('invantory_delete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              
            
            
              <input type=hidden id="delete_id" name=delete_id>
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




@endsection 