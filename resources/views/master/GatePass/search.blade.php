@php
$getSetting=Helper::getSetting();

@endphp
<table class="table table-bordered table-striped dataTable dtr-inline " style="  width: 100%;">
  <thead>
    <tr>
      <th>{{ __('common.SR.NO') }}</th>
      <th>{{ __('master.Student Name') }}</th>
      <th>{{ __('common.Fathers Name') }}</th>
      <th>{{ __('master.Admission No.') }}</th>
      <th>{{ __('common.Fathers Mobile') }}</th>


    </tr>
  </thead>
  <tbody>
    @if(!empty($data))
    @php
    $i=1;

    @endphp
    @foreach ($data as $item)

    <tr style="cursor:pointer; " class="fillData trColor" data-id="{{ $item['id'] ?? '' }}" data-school_name="{{ $getSetting['name'] ?? '' }}" data-class_type_id="{{ $item['class_type_id'] ?? '' }}" data-name="{{ $item['first_name'] ?? '' }}" data-father_mobile="{{ $item['father_mobile'] ?? '' }}" data-dob="{{ $item['dob'] ?? '' }}" data-father_name="{{ $item['father_name'] ?? '' }}" data-address="{{ $item['address'] ?? '' }}">
      <td class="p-1">{{ $i++ }}</td>
      <td class="p-1">{{ $item['first_name'] ?? '' }}</td>
      <td class="p-1">{{ $item['father_name']  ?? ''}}</td>
      <td class="p-1">{{ $item['admissionNo'] ?? '' }}</td>
      <td class="p-1">{{ $item['father_mobile']  ?? ''}}</td>


      <!-- <td>{{ $item['hostel']  }}</td>-->

    </tr>
    @endforeach
    @else
    <tr>
      <td colspan="12" class="text-center">No Students Found !</td>
    </tr>
    @endif
  </tbody>
</table>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
  $(".fillData").on("click", function() {

  $(this).css('backgroundColor', '#6639b5c4');
        $(this).css('color', '#fff');
        $( this ).siblings().css( "background-color", "white" );
        $( this ).siblings().css( "color", "black" );
        
        
    var name = $(this).data('name');
    var father_mobile = $(this).data('father_mobile');
    var father_name = $(this).data('father_name');
    var school_name = $(this).data('school_name');
    var admissionunmber = $(this).data('id');

    $('#stuAdmissionNo').val(admissionunmber);
    $('#father_mobile2').val(father_mobile);

    $('#student_name').val(name);

    $('#father_mobile').val(father_mobile);
    $('#father_name').val(father_name);
    $('#address').val(address);



  });
  

</script>