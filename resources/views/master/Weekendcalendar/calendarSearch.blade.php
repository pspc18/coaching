      
            <div class="m-2">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>{{__('common.SR.NO') }}</th>
                    <th class="text-center">{{__('Month Type') }}</th>
                  </tr>
                </thead>
                
                <tbody id="trColor">
                  @if($data->count() > 0)
                  @php
                  $i=1
                  @endphp
                  @foreach ($data as $item)

                  <tr style="cursor:pointer; " onclick="showData('{{ $item['id']  }}');" data-status="{{$item->status}}">
                    <td>{{ $i++ }}</td>
                    <td class="text-center">{{ $item['month_type'] ?? '' }}</td>

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