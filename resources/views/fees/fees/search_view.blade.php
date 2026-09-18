            <div class="col-md-12" style="height: 110px; overflow-y: scroll;">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                     
                       <th>Sr.No.</th>
                       <th class="text-center">Addmission.No.</th>
                       <th>Name </th>
                       <th>Mobile</th>
                       <th>Email</th>
                       <th>F. Name</th>
                      <th>M. Name</th>
                      <th>V/C</th>
                    </tr>
                  </thead>
                  <tbody>
                            @if($data->count() > 0)
                                @php
                                   $i=1;
                                @endphp
                                @foreach ($data  as $item)
                              
                                <tr style="cursor:pointer; " onclick="showData('{{ $item['id']  }}');">
                                       <td><input type="hidden" id="email" name="email[]" value="{{ $item['email'] ?? ''  }}">
                                    <!--    <input type="hidden" id="admission_id" name="admission_id[]" value="{{ $item['id'] ?? ''  }}">-->{{ $i++ }}
                                        <input class="ml-3" type="checkbox" id="checkbox" name="checkbox[]" value="{{ $item['id'] ?? ''  }}" checked>
                                        </td>
                                        <td class="text-center" >{{ $i++ }}</td>
                                        <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                                        <td>{{ $item['mobile']  }}</td>
                                        <td>{{ $item['email']  }}</td>
                                        <td>{{ $item['father_name']  }}</td>
                                        <td>{{ $item['mother_name']  }}</td>
                                        <td>{{ $item['village_city']  }}</td>
                                </tr>
                               @endforeach
                               
                            @else
                                <tr><td colspan="12" class="text-center">No Registerd Students Found !</td></tr>
                            @endif
                                
                </tbody>
                </table>
            </div>
                  
                  
                 