
                  
                  @if(!empty($data))
                        @php
                           $i=1
                        @endphp
                        @foreach ($data  as $item)
                        <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $item['registration_no']  }}</td>
                                <td>{{ $item['first_name']  }} {{ $item['last_name']  }}</td>
                                <td>{{ $item['ClassTypes']['name']  }} ({{ $item['Section']['name']  }})</td>
                                <td>{{ $item['father_name']  }}</td>
                                <td>{{ $item['mother_name']  }}</td>
                                <td>{{ $item['mobile']  }}</td>
                                <td>{{ $item['dob']  }}</td>
                                <td>{{ $item['registration_date']  }}</td>
                               
                                <td>
                                    <a href="{{url('students/edit',$item->id)}}" class="btn btn-primary  btn-xs" title="Edit Student Registration"><i class="fa fa-edit"></i></a>
                                    <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData btn btn-danger  btn-xs ml-3" title="Delete Student Registration"><i class="fa fa-trash-o"></i></a>                                      
                                </td>
                        </tr>
                   @endforeach
                @endif

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
      <form action="{{ url('registration_delete') }}" method="post">
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