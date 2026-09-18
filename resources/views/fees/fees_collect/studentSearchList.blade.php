@php
$i=1
@endphp
@foreach ($data as $item)
    <tr class="quickCollect" style="cursor:pointer; " data-admission_id ="{{ $item['id']  }}" data-ledger_no ="{{ $item['ledger_no']  }}">
    <td>{{ $item->ledger_no ?? 'NA' }}</td>
    <td class="text-center">{{ $item['admissionNo'] ?? '' }}</td>
    <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
    <td>{{ $item['ClassTypes']['name'] ?? '' }}</td>
    <td>{{ $item['father_name'] ?? '' }}</td>
    <!--<td>{{ $item['mother_name'] ?? '' }}</td>-->
    <td>{{ $item['mobile'] ?? '' }}</td>
</tr>                                            
@endforeach
    