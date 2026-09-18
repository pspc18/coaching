                    
                                @if(!empty($data))
                                @php
                                    $i=1
                                  
                                @endphp
                                @foreach ($data  as $key => $item)
                                <tr>
                                    <td>{{ $i++ }}
                                    <input class="ml-3 assignChecked_check" data-check_id="check_{{$key}}" type="checkbox" id=" library_assign_id" name="library_assign_id[]" value="{{ $item['id'] ?? ''  }}" onclick="return book_count();">
                                    </td>
                                    <td>{{ $item['first_name']  }} {{ $item['last_name']  }}</td>
                                    <td>{{ $item['father_name'] ?? '' }}</td>
                                    <td>{{ $item['mobile'] ?? '' }}</td>
                                    <td>{{ $item['email'] ?? '' }}</td>
                                </tr>
                                @endforeach
                                @endif
