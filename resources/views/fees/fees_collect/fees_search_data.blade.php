

                                @if(!empty($data))
                                @php
                                    $i=1
                                @endphp

                                @foreach ($data  as $item)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $item['Student']['first_name']  }} {{ $item['Student']['last_name']  }}</td>
                                    <td>{{ $item['Student']['father_name'] ?? '' }}</td>
                                    <td>{{ $item['ClassTypes']['name'] ?? '' }}</td>
                                    <td>{{ $item['pay_amt'] ?? '' }}</td>
                                    <td>{{ $item['PaymentMode']['name'] ?? '' }}</td>
                                    <td>
                                        <a href="{{url('print_payement',$item->id)}}" target="blank" class="btn btn-success  btn-xs" title="Fees Print"><i class="fa fa-print"></i></a>
                                        <a data-id='{{$item->id ?? ''}}' data-first_name='{{$item['Student']->first_name ?? ''}} {{$item['Student']->last_name ?? ''}}' data-class_type_id='{{$item['ClassTypes']->name ?? ''}}' data-toggle="modal" data-target="#myModal" class="btn btn-primary  btn-xs ml-3 feesDetail" title="View Fees"><i class="fa fa-bars"></i></a>
                                    </td>                                
                                </tr>
                                @endforeach
                                @endif




<script>
    $('.feesDetail').click(function() {
	var first_name = $(this).data('first_name');
	var class_type_id = $(this).data('class_type_id');

	$('#first_name').html(first_name);
	$('#class_type_id1').html(class_type_id);
    });

</script>