                <table class="table table-bordered">
                  <thead class=bg-primary>
                    <tr>
                       <th>{{__('common.SR.NO') }}</th>
                       <th class="text-center">{{__('hostel.Ad. No.') }}</th>
                       <th>{{__('common.Name') }} </th>
                      <th>{{__('common.F Name') }}</th>
                      <th>{{__('common.M Name') }}</th>
                       <th>{{__('common.Mobile') }}</th>
                       <th>{{__('common.Email') }}</th>                      
                    </tr>
                  </thead>
                  <tbody id="trColor">
                            @if($data->count() > 0)
                                @php
                                
                                   $i=1
                                @endphp
                                @foreach ($data  as $item)
                              
                                <tr style="cursor:pointer;" class="fillData" 
                                data-admission_no="{{ $item['admissionNo'] ?? '' }}" 
                                data-admission_id="{{ $item['id'] ?? '' }}" 
                                data-hostel="{{ $item['hostel'] ?? '' }}" 
                                data-first_name="{{ $item['first_name'] ?? '' }}" 
                                data-last_name="{{ $item['last_name'] ?? '' }}" 
                                data-mobile="{{ $item['mobile'] ?? '' }}" 
                                data-email="{{ $item['email'] ?? '' }}" 
                                data-gender_id="{{ $item['gender_id'] ?? '' }}"
                                data-father_name="{{ $item['father_name'] ?? '' }}" 
                                data-mother_mobile="{{ $item['mothers_mobile'] ?? '' }}" 
                                data-father_mobile="{{ $item['father_mobile'] ?? '' }}" 
                                data-mother_name="{{ $item['mother_name'] ?? '' }}" 
                                data-sig_img="{{ $item['Signature_img'] ?? '' }}" 
                                data-dob="{{ $item['dob'] ?? '' }}" 
                                data-college="{{ $item['college'] ?? '' }}" 
                                data-course="{{ $item['course'] ?? '' }}" 
                                data-guardian_whatsapp="{{ $item['guardian_whatsapp'] ?? '' }}" 
                                 data-address="{{ $item['address'] ?? '' }}" 
                                 data-duration_stay="{{ $item['duration_stay'] ?? '' }}" 
                                 data-student_image="{{ $item['image'] ?? '' }}" 
                                 data-father_image="{{ $item['father_img'] ?? '' }}" 
                                 data-mother_image="{{ $item['mother_img'] ?? '' }}" 
                                 data-sig1_img="{{ $item['Signature1_img'] ?? '' }}" 
                                 data-sig2_img="{{ $item['Signature2_img'] ?? '' }}" 
                                 data-sig4_img="{{ $item['Signature4_img'] ?? '' }}" 
                                 data-Occupancy="{{ $item['Occupancy'] ?? '' }}" 
                                data-guardian_photo="{{ $item['guardian_photo'] ?? '' }}" 
                                 data-guardian_name="{{ $item['guardian_name'] ?? '' }}" 
                                 data-guardian_address="{{ $item['guardian_address'] ?? '' }}" 
                                 data-guardian_tel="{{ $item['guardian_tel'] ?? '' }}" 
                                 data-guardian_mobile="{{ $item['guardian_mobile'] ?? '' }}" 
                                 data-student_id_proof="{{ $item['student_id_proof'] ?? '' }}" 
                                 data-college_id="{{ $item['college_id'] ?? '' }}" 
                                 data-police_verification="{{ $item['police_verification'] ?? '' }}" 
                                 data-covid_certificate="{{ $item['covid_certificate'] ?? '' }}" 
                                 data-other_document="{{ $item['other_document'] ?? '' }}" 
                                 data-father_adhar="{{ $item['father_adhar'] ?? '' }}" 
                                 data-hostel_room="{{ $item['hostel_room'] ?? '' }}" 
                                 
                                 
                                 >
                                        <td>{{ $i++ }}</td>
                                        <td class="text-center" >{{ $item['admissionNo'] ?? '' }}</td>
                                        <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                                        <td>{{ $item['father_name']  }}</td>
                                        <td>{{ $item['mother_name']  }}</td>
                                        <td>{{ $item['mobile']  }}</td>
                                        <td>{{ $item['email']  }}</td>                                        
                                </tr>
                               @endforeach
                               
                            @else
                                <tr><td colspan="12" class="text-center">No Students Found !</td></tr>
                            @endif
                                
                </tbody>
                </table>

                  
<script>
$(".fillData").on("click", function() {
    var admission_no = $(this).data('admission_no');
  // alert(admission_no);
    var admission_id = $(this).data('admission_id');
    var hostel = $(this).data('hostel');
    var first_name = $(this).data('first_name');
    var last_name = $(this).data('last_name');
    var mobile = $(this).data('mobile');
    var email = $(this).data('email');
    var base =" {{ env('IMAGE_SHOW_PATH')}}";
// alert($(this).data('sig1_img'));
    var gender_id = $(this).data('gender_id');
    var address = $(this).data('address');
    if(hostel == 0){
        $('#admission_no').val(admission_no); 
    $('#admission_id').val(admission_id);
    $('#first_name').val(first_name);
    $('#last_name').val(last_name);
    $('#mobile').val(mobile);
    $('#email').val(email);
    $('#dob').val($(this).data('dob'));
    $('#college').val($(this).data('college'));
    $('#Course').val($(this).data('course'));
    $('#father_name').val($(this).data('father_name'));
    $('#guardian_whatsapp').val($(this).data('guardian_whatsapp'));
    $('#gender_id').val(gender_id).change();
    $('#address').val(address); 
    $('#father_mobile').val($(this).data('father_mobile'));  
    $('#mother_name').val($(this).data('mother_name'));  
    $('#mothers_mobile').val($(this).data('mother_mobile'));  
    $('#guardian_name').val($(this).data('guardian_name'));  
    $('#guardian_mobile').val($(this).data('guardian_mobile'));
    $('#guardian_address').val($(this).data('guardian_address'));
    $('#guardian_tel').val($(this).data('guardian_tel'));  
    $('#duration_Of_stay').val($(this).data('duration_stay'));
    $('#hostel_room').val($(this).data('hostel_room')).change();   
    $('#student_img_link').attr('src',base+'profile/'+$(this).data('student_image'));   
    $('#Signature_img_link').attr('src',base+'Signature_img/'+$(this).data('sig_img')); 
    $('#father_photo_link').attr('src',base+'father_photo/'+$(this).data('father_image')); 
    $('#mother_photo_link').attr('src',base+'mother_photo/'+$(this).data('mother_image')); 
    $('#Signature1_img_link').attr('src',base+'Signature1_img/'+$(this).data('sig1_img')); 
    $('#Signature2_img_link').attr('src',base+'Signature2_img/'+$(this).data('sig2_img')); 
    $('#Signature4_img_link').attr('src',base+'Signature4_img/'+$(this).data('sig4_img')); 
    $('#guardian_photo_link').attr('src',base+'guardian_photo/'+$(this).data('guardian_photo')); 
      $('#Occupancy').val($(this).data('Occupancy')).change();
    $('#student_id_proof_link').attr('src',base+'student_id_proof/'+$(this).data('student_id_proof')); 
    $('#college_id_link').attr('src',base+'college_id/'+$(this).data('college_id')); 
    $('#police_verification_link').attr('src',base+'police_verification/'+$(this).data('police_verification')); 
    $('#covid_certificate_link').attr('src',base+'covid_certificate/'+$(this).data('covid_certificate')); 
    $('#other_document_link').attr('src',base+'other_document/'+$(this).data('other_document')); 
    $('#father_adhar_link').attr('src',base+'father_adhar/'+$(this).data('father_adhar')); 
    }else{
        toastr.error('Already assigned student !');
    
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