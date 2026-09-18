<div class="card-body p-0">
                <table class="table table-bordered">
                  <thead class="bg-primary">
                    <tr>
                       <th>Sr.No.</th>
                       <th>Student Name </th>
                      <th>Father's Name</th>
                      <th>Mother's Name</th>
                      <th>Father's Mobile No.</th>
                      <th>Date Of Birth</th>
                      
                    </tr>
                  </thead>
                  <tbody>
                            @if(!empty($data))
                                @php
                                   $i=1
                                @endphp
                                @foreach ($data  as $item)
                                <tr style="cursor:pointer; " onclick="showData('{{ $item['id']  }}');">
                                        <td class="p-1" >{{ $i++ }}</td>
                                        <td class="p-1" >{{ $item['name']  }}</td>
                                        <td class="p-1" >{{ $item['father_name']  }}</td>
                                        <td class="p-1" >{{ $item['mother_name']  }}</td>
                                        <td class="p-1" >{{ $item['father_mobile']  }}</td>
                                        <td class="p-1" >{{ $item['dob']  }}</td>
                                        
                                       <!-- <td>{{ $item['hostel']  }}</td>-->
                                       
                                </tr>
                               @endforeach
                            @endif
                </tbody>
                </table>
              </div>