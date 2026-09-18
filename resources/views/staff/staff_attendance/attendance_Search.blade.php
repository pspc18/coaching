@php
    $getAttendanceStatus= Helper::getMarkableAttendanceStatus();
@endphp                 
                @if($data->count() > 0)
                        @php
                           $i=1
                        @endphp
                        @foreach ($data  as $item)
                        <tr>
                                <input type="hidden" id="teacher_id" name="teacher_id[]" value="{{ $item['id'] ?? '' }}">
                                <input type="hidden" id="first_name" name="first_name[]" value="{{ $item['first_name'] ?? '' }}">
                                <input type="hidden" id="mobile" name="mobile[]" value="{{ $item['mobile'] ?? '' }}">
                                <input type="hidden" id="email" name="email[]" value="{{ $item['email'] ?? '' }}">
                            <td>{{ $i++ }}</td>
                            <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                            <td>{{ $item['father_name'] ?? '' }}</td>
                            <td>{{ $item['mobile'] ?? '' }}</td>
                            <td>
                    			<select class="form-control" id="attendance_status" name="attendance_status[]" >
                               @if(!empty($getAttendanceStatus)) 
                                @foreach($getAttendanceStatus as $attendance_status)
                                    
                                        <option value="{{ $attendance_status->id ?? '' }}">{{ $attendance_status->name ?? '' }}</option>
                                    
                                @endforeach
                            @endif

                                </select>                                    
                            </td>                            
                        </tr>
                   @endforeach
                @else
                                <tr><td colspan="12" class="text-center">No Staff Found !</td></tr>
                @endif
                  
                  
                 
