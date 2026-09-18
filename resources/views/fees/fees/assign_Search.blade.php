
                                @if(!empty($data))
                                @php
                                    $i=1
                                @endphp
                                @foreach ($data  as $item)
                                <tr>
                                                      
                                    <input type="hidden" id="admissionNo" name="admissionNo[]" value="{{ $item['admissionNo'] ?? ''  }}">
                                    <input type="hidden" id="student_id" name="student_id[]" value="{{ $item['id'] ?? ''  }}">
                
                                    <td><input type="checkbox" id="student_session_id" name="student_session_id[]" value="{{ $item['id'] ?? ''  }}" class="checkbox"></td>
                                    <td>{{ $item['admissionNo'] ?? ''  }}</td>
                                    <td>{{ $item['name']  }}</td>
                                    <td>{{ $item['ClassTypes']['name'] ?? ''}} ({{ $item['Section']['name'] ?? ''}})</td>
                                    <td>{{ $item['father_name'] ?? '' }}</td>
                                    <td>{{ $item['Gender']['name'] ?? '' }}</td>
                                    <td>{{ $item['mobile'] ?? '' }}</td>
                                </tr>
                                @endforeach
                                @endif
                 