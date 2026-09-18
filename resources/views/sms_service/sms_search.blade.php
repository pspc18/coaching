                        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                            <thead class="bg-primary">
                                <tr role="row">
                                    <th>
                                        Sr.No.
                                        @if(!empty($data))
                                        @if($data[0]->role_id == 3)
                                        <input class="ml-3" type="checkbox" id="all_checkbox" checked>
                                        @endif
                                        @endif
                                    </th>
                                    <th>Name</th>
                                    @if(!empty($data))
                                    @if($data[0]->role_id == 3)
                                    <th>Class</th>
                                    @endif
                                    @endif
                                    <th>F Name</th>
                                    <th>Mobile</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
 
                                @if(!empty($data))
                                @php
                                    $i=1
                                @endphp

                                @foreach ($data  as $item)
                                <tr>
                                    <td>
                                    {{ $i++ }}
                                    <input class="ml-3 checkbox" type="checkbox" id="checkbox" name="admission_id[]" value="{{ $item['id'] ?? ''  }}" checked>
                                    </td>
                                    <td>{{ $item['first_name']  }} {{ $item['last_name']  }}</td>
                                    @if($item->role_id == 3)
                                 <td>{{ $item['ClassTypes']['name'] ?? '' }}</td>
                                    
                                    @endif
                                    <td>{{ $item['father_name'] ?? '' }}</td>
                                    <td>{{ $item['mobile'] ?? '' }}</td>
                                    <td>{{ $item['email'] ?? '' }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr><td colspan="12" id="no_data" data-count="{{$data}}" class="text-center">No Students Found !</td></tr>
                                @endif
                             
                            </tbody>
                        </table>
                        
                        
    <script>
        $(document).ready(function(){
            $('#chcekshow').addClass('d-none');
            var searchdata = $('#no_data').data('count');
            if(searchdata == 0){
                $('#chcekshow').addClass('d-none');
            }else{
                $('#chcekshow').removeClass('d-none');
            }
            $('#all_checkbox').click(function(){
                var isChecked = $(this).prop("checked");
                $(".checkbox").prop("checked", isChecked);
                
                var checkedCount = $(".checkbox:checked").length;
                
                if (checkedCount == "0") {
                    $('#chcekshow').addClass('d-none');
                } else {
                    $('#chcekshow').removeClass('d-none');
                }
            });

            
            $('.checkbox').click(function() {
                var checkbox_length = $(".checkbox").length;
                var checkedCount = $(".checkbox:checked").length;
                if(checkbox_length == checkedCount){
                    $("#all_checkbox").prop("checked", true);
                }else{
                    $("#all_checkbox").prop("checked", false);
                }
                if (checkedCount === 0) {
                    $('#chcekshow').addClass('d-none');
                } else {
                    $('#chcekshow').removeClass('d-none');
                }
            });
        })
    </script>                    