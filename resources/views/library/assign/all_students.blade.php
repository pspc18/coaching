                    
                                @if(!empty($data))
                                @php
                                    $i=1;
                       
                                @endphp
                                @foreach ($data  as $item)
                           
                                <tr>
                                    <td>{{ $i++ }}
                                    <input class="ml-3" type="checkbox" id="admission_id" name="admission_id[]" value="{{ $item['id'] ?? ''  }}" onclick="return book_count();">
                                    </td>
                                    <td>{{ $item['first_name']  }} {{ $item['last_name']  }}</td>
                                    {{ $item['ClassTypes']['name'] ?? '' }}</td>
                                    <td>{{ $item['father_name'] ?? '' }}</td>
                                    <td>{{ $item['mobile'] ?? '' }}</td>
                                    <td>{{ $item['email'] ?? '' }}</td>
                                </tr>
                                @endforeach
                                @endif
