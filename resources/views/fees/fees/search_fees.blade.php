              <div class="m-2">
                <table class="table table-bordered small_td" id="trColor">
                  <thead>
                    <tr>
                       <th>Sr.No.</th>
                       <th class="text-center">Admission No. </th>
                       <th>Name</th>
                       <th>Class </th>
                      <th>Father Name</th>
                      <th>Mother Name</th>
                      <th>Mobile</th>
                    </tr>
                  </thead>
                  <tbody>
                            @if(!empty($data))
                                @php
                                   $i=1
                                @endphp
                                @foreach ($data  as $item)
                                <tr style="cursor:pointer; " onclick="showData('{{ $item['id']  }}')" >
                                    <td>{{ $i++ }}</td>
                                    <td class="text-center">{{ $item['admissionNo']  }}</td>
                                    <td>{{ $item['first_name']  }} {{ $item['last_name']  }}</td>
                                    <td>{{ $item['ClassTypes']['name']  }} ({{ $item['Section']['name']  }})</td>
                                    <td>{{ $item['father_name']  }}</td>
                                    <td>{{ $item['mother_name']  }}</td>
                                    <td>{{ $item['mobile']  }}</td>
                                 
                                </tr>
                               @endforeach
                            @endif
                </tbody>
                </table>
            </div>


<style>
.selected {
    color: #6639b5;
    background-color: #8966c670;
}
</style>
<script>
$("#trColor tbody tr").click(function(){
   $(this).addClass('selected').siblings().removeClass('selected');    
});
</script>