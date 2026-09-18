@php
  $getSetting = Helper::getSetting();

@endphp
     <div class="col-md-12 card card-outline " style="height: 300px; overflow-y: scroll;">
                <table class="table table-bordered">
                                      <thead class="bg-primary">
                    <tr>
                       <th>{{ __('certificate.Admission No.') }}</th>
                       <th>{{ __('certificate.Student Name') }} </th>
                      <th>{{ __('common.Fathers Name') }}</th>
                      <th>{{ __('common.Fathers Mobile No.') }}</th>
                    </tr>
                  </thead>
                  <tbody  id="trColor">
                            @if(!empty($data))
                                @php
                                   $i=1;
                                @endphp
                                @foreach ($data  as $item)
                                @php
                                $character = DB::table('c_certificates_form')->where('admission_id',$item->id)->whereNull('deleted_at')->count();
                              @endphp
                               <tr style="cursor:pointer; " class="fillData p-0" data-class_name="{{ $item->class_name ?? '' }}" data-id="{{ $item['id'] ?? '' }}" data-old="{{ $character ?? '0'}}" data-admissionNo="{{ $item['admissionNo'] ?? '' }}" 
                                        data-name="{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}" data-father_mobile="{{ $item['father_mobile'] ?? '' }}" data-father_name="{{ $item['father_name'] ?? '' }}">
                                        <td class="p-1" >{{ $item['admissionNo'] ?? '' }}</td>
                                        <td class="p-1" >{{ $item['first_name'] ?? '' }}{{$item['last_name'] ?? '' }}</td>
                                        <td class="p-1" >{{ $item['father_name']  ?? ''}}</td>
                                        <td class="p-1" >{{ $item['father_mobile']  ?? ''}}</td>
                                </tr>
                               @endforeach
                          @else
                                <tr><td colspan="12" class="text-center">No Students Found !</td></tr>
                            @endif
                </tbody>
                </table>
                </div>
<script>
$(".fillData").on("click", function() {
  var old = $(this).data('old');
    var admission_id = $(this).data('id');
    var admissionNo = $(this).data('admissionno');
    var class_name = $(this).data('class_name');
    var student_name = $(this).data('name');
    var father_mobile = $(this).data('father_mobile');
    var father_name = $(this).data('father_name');
    
    $('#class_name').val(class_name);
    $('#admissionNo').val(admissionNo);
    $('#admission_id').val(admission_id);
    $('#student_name').val(student_name);
    $('#father_mobile').val(father_mobile);
    $('#father_name').val(father_name);
    if (old == 0){
         
    }else if(old > 0){
        toastr.error('Already Exists');
        $('#admissionNo,#admission_id,#student_name,#father_mobile,#father_name').val('');
    }
});

$(document).ready(function() {
    $('#trColor tr').click(function() {
        $(this).css('backgroundColor', '#002c54');
        $(this).css('color', '#fff');
        $( this ).siblings().css( "background-color", "#fff" );
        $( this ).siblings().css( "color", "black" );
    });
});
</script>                 