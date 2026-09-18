      
            <div class="m-2">
              <table class="table table-bordered">
                <thead class="" >
                  <tr style="background: linear-gradient(135deg, #314fa2 0%, #3f62c0 55%, #2f4d9f 100%);
    box-shadow: 0 18px 40px rgba(39, 63, 129, 0.18);color: #fff;">
                    <th>{{__('common.SR.NO') }}</th>
                    <!-- <th class="text-center">{{__('common.Reg.No.') }}</th> -->
                    <th>{{__('common.Name') }} </th>
                    <th>{{__('common.Mobile') }}</th>
                    <th>{{__('common.Email') }}</th>
                    <th>{{__('common.F Name') }}</th>
                    <th>{{__('common.M Name') }}</th>
                    <th>{{__('common.Reg. Date') }}</th>
                    <th>{{__('common.V/C') }}</th>
                  </tr>
                </thead>
                
                <tbody id="trColor">
                  @if($data->count() > 0)
                  @php
                  $i=1
                  @endphp
                  @foreach ($data as $item)

                  <tr style="cursor:pointer;" onclick="showData('{{ $item['id']  }}');" data-status="{{$item->status}}">
                    <td>{{ $i++ }}</td>
                    <!-- <td class="text-center">{{ $item['registration_no'] ?? '' }}</td> -->
                    <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                    <td>{{ $item['mobile']  }}</td>
                    <td>{{ $item['email']  }}</td>
                    <td>{{ $item['father_name']  }}</td>
                    <td>{{ $item['mother_name']  }}</td>
                    <td>{{date('d-m-Y', strtotime($item->registration_date)) }}</td>
                    <td>{{ $item['village_city']  }}</td>

                  </tr>
                  @endforeach

                  @else
                  <tr>
                    <td colspan="12" class="text-center">No Registerd Students Found !</td>
                  </tr>
                  @endif

                </tbody>
              </table>
            </div>
            
    <script>
                
$(document).ready(function() {
    $('#trColor tr').click(function() {
        $(this).css('backgroundColor', '#6639b5c4');
        $(this).css('color', '#fff');
        $( this ).siblings().css( "background-color", "white" );
        $( this ).siblings().css( "color", "black" );
        
        var status = $(this).data('status');
        
        if(status == 1){
            toastr.error('Student Already Exist');
        }
    });
});
</script>