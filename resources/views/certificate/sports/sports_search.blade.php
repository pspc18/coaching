   <div class="col-md-12" style="height: 300px; overflow-y: scroll;">
                <table class="table table-bordered">
                <table class="table table-bordered table-striped dataTable dtr-inline ">
                  <thead>
                    <tr>
                     <th>{{ __('common.SR.NO') }}</th>
                       <th>{{ __('certificate.Student Name') }}</th>
                      <th>{{ __('common.Fathers Name') }}</th>
                      <th>{{ __('common.Mothers Name') }}</th>
                      <th>{{ __('common.Fathers Mobile No.') }}</th>
                      <th>{{ __('common.Date Of  Birth') }}</th>
                      
                    </tr>
                  </thead>
                  <tbody id="trColor">
                            @if(!empty($data))
                                @php
                                   $i=1
                                //dd($data);
                                @endphp
                                @foreach ($data  as $item)
                              @php
                                $character = DB::table('sports_certificates')->where('admission_id',$item->id)->whereNull('deleted_at')->count();
                              //dd($character);
                              @endphp
                                <tr style="cursor:pointer; " class="fillData" data-id="{{ $item['id'] ?? '' }}" data-old="{{$character ?? '0'}}" data-admission_type_id="{{$item['admission_type_id'] ?? ''}}" data-school_name="{{ $getSetting['name'] ?? '' }}" data-class_type_id="{{ $item['class_type_id'] ?? '' }}" data-registration_no="{{ $item['admissionNo'] ?? '' }}" 
                                data-first_name="{{ $item['first_name'] ?? '' }}" data-last_name="{{ $item['last_name'] ?? '' }}" 
                                data-father_mobile="{{ $item['father_mobile'] ?? '' }}" data-dob="{{ $item['dob'] ?? '' }}" data-father_name="{{ $item['father_name'] ?? '' }}" data-address="{{ $item['address'] ?? '' }}" >
                                        <td class="p-1" >{{ $item['admissionNo'] ?? '' }}</td>
                                        
                                        <td class="p-1" >{{ $item['first_name']  }} {{ $item['last_name']  }}</td>
                                        <td class="p-1" >{{ $item['father_name']  }}</td>
                                        <td class="p-1" >{{ $item['mother_name']  }}</td>
                                        <td class="p-1" >{{ $item['father_mobile']  }}</td>
                                        <td class="p-1" >{{date('d-m-Y', strtotime($item['dob'])) ?? '' }}</td>
                                        
                                       <!-- <td>{{ $item['hostel']  }}</td>-->
                                       
                                </tr>
                               @endforeach
                            @endif
                </tbody>
                </table>
                </div>
    <script>
      
$(".fillData").on("click", function() {
    
    var old = $(this).data('old');
    var first_name = $(this).data('first_name');
     var addmission_id = $(this).data('id');
    var last_name = $(this).data('last_name');
    var father_mobile = $(this).data('father_mobile');
    var father_name = $(this).data('father_name');
    var school_name = $(this).data('school_name');
    var registration_no = $(this).data('registration_no');
    var class_type_id = $(this).data('class_type_id');
    var dob = $(this).data('dob');
    var full_name = first_name + last_name;
    
    $('#admission_id').val(addmission_id);
    $('#first_name').val(full_name);
    $('#father_mobile').val(father_mobile);
    $('#father_name').val(father_name);
    $('#school_name').val(school_name);
    $('#class_type_id1').val(class_type_id);
    $('#registration_no').val(registration_no);
    $('#dob1').val(dob);
    if (old === 0) {
   
    } else if (old > 0) {
    toastr.error('Already Exists');
    $('#admission_id,#first_name,#first_name,#father_mobile,#father_name,#school_name,#class_type_id1,#registration_no,#dob1').val('');
    }
    
});


$(document).ready(function() {
    $('#trColor tr').click(function() {
        $(this).css('backgroundColor', '#6639b5c4');
        $(this).css('color', '#fff');
        $( this ).siblings().css( "background-color", "white" );
        $( this ).siblings().css( "color", "black" );
    });
});
</script>                 