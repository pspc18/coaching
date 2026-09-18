@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    <div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
                <div class="col-sm-8">
                	<h3 class="card-title"><i class="fa fa-cloud-download"></i> &nbsp; Other Downloadst</h3>
                </div>
                <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                <li class="pr-2"><a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-home"></i> Home</a></li>
                <li class="pl-2"><a href="{{url('download_center')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i> Back</a></li>
                </ol>
                </div>
            </div>
        <hr class="bg-primary" style="margin-top:-12px;">
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                <thead class="bg-primary">
                                    <tr role="row">
                                        <th>Sr. No.</th>
                                        <th>Content Title</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Action</th>

                                </thead>
                                <tbody>
                      
                                    @if(!empty($data))
                                    @php
                                       $i=1
                                    @endphp
                                    @foreach ($data  as $item)
                                    @if(i==2)
                                    {
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $item['content_title']  }}</td>
                                        <td>{{ $item['content_type']  }}</td>
                                        <td>{{date('d-m-Y', strtotime($item['upload_date'])) ?? '' }}</td>
                                        <td>
                                            <a href="{{ url('download') }}/{{$item['id'] ?? '' }}" class="ml-2" title="Download"><i class="fa fa-download text-success"></i></a>
                                            <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData ml-4" title="Delete"><i class="fa fa-trash-o text-danger"></i></a>
                                        </td>
                                    </tr>
                                    }
                                    @endif
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
        <h4 class="modal-title text-white">Delete Confirmation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('upload_delete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              
            
            
              <input type=hidden id="delete_id" name=delete_id>
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
  
@endsection