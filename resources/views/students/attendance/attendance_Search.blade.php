@php
    $getAttendanceStatus = Helper::getMarkableAttendanceStatus();
@endphp      

@if(!empty($data) && $data->count() > 0)
    @php $i=1; @endphp
    @foreach ($data as $key => $item)
        @php
            $stu_att = DB::table('student_attendance')
                ->where('session_id', Session::get('session_id'))
                ->where('branch_id', Session::get('branch_id'))
                ->where('admission_id', $item['id'])
                ->whereDate('date', $custom_date)
                ->whereNull('deleted_at')
                
                ->first();
        @endphp
        <tr @if(!empty($stu_att)) style="background-color:#28a745;color: #fff;" @endif>
                <input type="hidden" name="custom_date" value="{{ $custom_date }}">

            <input type="hidden" name="class_type_id" value="{{ $item['class_type_id'] ?? '' }}">
            <input type="hidden" name="admission_id[{{ $item['id'] }}]" value="{{ $item['id'] ?? '' }}">
            <input type="hidden" name="name[{{ $item['id'] }}]" value="{{ $item['first_name'] ?? '' }}">
            <input type="hidden" name="mobile[{{ $item['id'] }}]" value="{{ $item['mobile'] ?? '' }}">
            
            <td>{{ $i++ }}
 <input type="checkbox" class="student-check" name="selected_students[{{ $item['id'] }}]" data-student="{{ $item['id'] }}" value="{{ $item['id'] }}"
            @if(!empty($stu_att)) checked @endif>
            
            
            <td>{{ $item['admissionNo'] ?? '' }}</td>
            <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
            <td>{{ $item['father_name'] ?? '' }}</td> 
            
            
<td>
    <div class="attendance-options">
        @foreach($getAttendanceStatus as $attendance_status)
            <label class="d-inline-flex align-items-center">
                <input class="attendance-radio"
                    type="radio" 
                    value="{{ $attendance_status->id }}"  
                    name="attendance_status[{{ $item['id'] }}]" 
                    @if(!empty($stu_att) && $stu_att->attendance_status_id == $attendance_status->id) 
                        checked 
                    @endif
                    @if(empty($stu_att)) 
                        disabled
                    @endif
                >
                <span class="ms-1">{{ $attendance_status->name ?? '' }}</span>
            </label>
        @endforeach
    </div>
</td>



                                    
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="12" class="text-center">No Students Found !</td>
    </tr>
@endif
<style>
    .attendance-options {
    display: flex;
    gap: 15px;
    align-items: center;
    white-space: nowrap;
}

</style>
<script>
function setForAll(value) {
    if (!value) return;

    // सभी rows में radio buttons चेक करो
    document.querySelectorAll('.attendance-options').forEach(group => {
        let radios = group.querySelectorAll('.attendance-radio');
        radios.forEach(radio => {
            if (radio.value === value) {
                radio.checked = true;
            }
        });
    });
}
// Single student checkbox
$(document).on('change', '.student-check', function () {
    let studentId = $(this).data('student');
    let radios = $('input[name="attendance_status[' + studentId + ']"]');

    if ($(this).is(':checked')) {
        radios.prop('disabled', false);
        if (!radios.is(':checked')) {
            radios.first().prop('checked', true);
        }
    } else {
        radios.prop('disabled', true).prop('checked', false);
    }
});

// Master checkbox
$('#checkAll').on('change', function () {
    let isChecked = $(this).prop('checked');

    $('.student-check').prop('checked', isChecked).trigger('change');
    // 👆 trigger('change') लगाया ताकि ऊपर वाला logic (radios enable/disable) भी चल जाए
});

</script>
