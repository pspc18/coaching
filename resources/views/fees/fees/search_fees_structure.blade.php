
                  
                  @if(!empty($data))
                        @php
                           $i=1
                        @endphp
                        @foreach ($data  as $item)
                        <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $item['className']['name']  }}</td>
                                <td>{{ $item['title']  }}</td>
                                <td>{{ $item['fees']  }}</td>
                                <td>
                                    <a  class="pl-5" data-toggle="dropdown" aria-expanded="false">
                                     <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <ul class="dropdown-menu" style="">
                                      <a href="{{url('fees_structure_edit')}}/{{$item->id}}"><li class="dropdown-item text-primary"><i class="fa fa-edit text-primary"></i> Edit</li></a>
                                      <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData"><li class="dropdown-item text-danger"><i class="fa fa-trash-o text-danger"></i> Delete</li></a>
                                    </ul>
                                </td>
                        </tr>
                   @endforeach
                @endif
                 
     <script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>


<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
 </script>
	  
