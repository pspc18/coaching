                        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                            <thead class="bg-primary">
                                <tr role="row">
                                    <th>{{ __('common.SR.NO') }}</th>
                                    <th>{{ __('hostel.Floor Name/No.') }}</th>
                                    <th>{{ __('common.Action') }}</th>
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
                                    <td>{{ $item['name']  }}</td>
                                    <td>
                                        <a href="{{ url('floor_edit') }}/{{ $item['id'] ?? '' }}" class="btn btn-primary  btn-xs" title="Edit Floor" ><i class="fa fa-edit"></i></a> 
                                        <a href="javascript:;" data-id='{{ $item['id'] ?? '' }}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-primary btn-xs ml-3" title="Delete Floor"><i class="fa fa-trash-o"></i></a>
                                    </td>                                    
                                </tr>
                                @endforeach
                                
                                @else
                                <tr><td colspan="3" class="text-center">{{ __('hostel.No Floor Found') }} !</td></tr>
                                @endif
                            </tbody>
                        </table>

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
                        
                              <form action="{{ url('floor_delete') }}" method="post">
                                      	 @csrf
                              <div class="modal-body">
                                      <input type=hidden id="delete_id" name="delete_id">
                                      <h5 class="text-white">{{ __('common.Are you sure you want to delete') }}  ?</h5>
                              </div>
                              <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
                                            <button type="submit" class="btn btn-primary waves-effect waves-light">{{ __('common.Delete') }}</button>
                                 </div>
                               </form>
                            </div>
                          </div>
                        </div> 
                        