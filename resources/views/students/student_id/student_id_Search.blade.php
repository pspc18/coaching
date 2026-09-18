
                  
                  @if(!empty($data))
                        @php
                           $i=1
                        @endphp
                        @foreach ($data  as $item)
                        <tr>
                        
                                <td>{{ $i++ }}</td>
                                <td>{{ $item['first_name']  }} {{ $item['last_name']  }}</td>
                                <td>{{ $item['dob']  }}</td>
                                <td>{{ $item['admissionNo']  }}</td>
                        
                               
                                <!--<td><a href="{{url('student_id_print',$item['id'])}}" target="blank" title="Print" class="text-success"><i class="fa fa-print"></i> Id Print</a></td>-->
                        </tr>
                   @endforeach
                @endif
                 