@php
    $classType = Helper::ClassType();
@endphp

@extends('layout.app') 
@section('content')

<input type="hidden" id="session_id" value="{{ Session::get('role_id') ?? '' }}">

<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <!-- Card -->
                    <div class="card card-outline card-orange">
                        
                        <!-- Card Header -->
                        <div class="card-header bg-primary">
                            <h3 class="card-title">
                                <i class="fa fa-calendar-check-o"></i> &nbsp;
                                {{ __('Exam Result Update') }}
                            </h3>
                            <div class="card-tools">
                                <a href="{{ url('examination_dashboard') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-arrow-left"></i>
                                    <span class="Display_none_mobile">{{ __('messages.Back') }}</span>
                                </a>
                            </div>
                        </div>
                        <!-- End Card Header -->

                        <!-- Search Form -->
                        <form id="quickForm" action="{{ url('exam_result_update') }}" method="post">
                            @csrf
                            <div class="row m-2">

                                <!-- Class Type -->
                                <div class="col-md-2 col-4">
                                    <div class="form-group">
                                        <label class="text-danger">{{ __('messages.Class') }}*</label>
                                        <select class="select2 form-control @error('class_type_id') is-invalid @enderror" 
                                                id="class_type_id" name="class_type_id">
                                            <option value="">{{ __('messages.Select') }}</option>
                                            @if(!empty($classType))
                                                @foreach($classType as $type)
                                                    <option value="{{ $type->id ?? '' }}"
                                                        {{ ($type->id == $search['class_type_id']) ? 'selected' : '' }}>
                                                        {{ $type->name ?? '' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('class_type_id')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Exam Name -->
                                <div class="col-md-2 col-4">
                                    <div class="form-group">
                                        <label class="text-danger">{{ __('messages.Exam Name') }}*</label>
                                        <select class="select2 form-control exam_id_" id="exam_id" name="exam_id" required>
                                            <option value="">{{ __('messages.Select') }}</option>
                                            @if(!empty($exam))
                                                @foreach($exam as $type)
                                                    <option value="{{ $type->exam_id }}"
                                                        {{ ($type->exam_id == $search['exam_id'] ? 'selected' : '' ) }}>
                                                        {{ $type->exam_name ?? '' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                            @error('exam_id')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </select>
                                    </div>
                                </div>

                                <!-- Search Button -->
                                <div class="col-md-1 col-12 text-center">
                                    <label class="Display_none_mobile text-white">Search</label>
                                    <button type="submit" class="btn btn-primary" onclick="SearchValue1()">
                                        {{ __('messages.Search') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                        <!-- End Search Form -->

                        <!-- Student Table -->
                        <div class="col-md-12">
                            <form action="{{ url('exam_result_update_save') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="table-responsive">                        
                                    <table class="bg-white table table-bordered table-striped dataTable dtr-inline">
                                        <thead>
                                            <tr role="row">
                                                <th>{{ __('Admission No') }}</th>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Roll No.') }}</th>
                                                <th>{{ __('Attendence ') }}</th>
                                                <th>{{ __('Permote to') }}</th>
                                                <th>{{ __('Rank') }}</th>
                                                <th>{{ __('Remark') }}</th> 
                                            </tr>
                                        </thead>
                                        <tbody class="student_list_show">
                                            @if($data->count() > 0)
                                                @php $i = 1; @endphp
                                                @foreach ($data as $key => $item)
                                                    @php
                                                        $exam_data = DB::table('exam_result_updates')
                                                            ->where('admission_id', $item->id)
                                                            ->where('exam_id', $search['exam_id'])
                                                            ->first();
                                                    @endphp
                                                    <tr class="active_color">
                                                        <input type="hidden" name="admission_id[]" value="{{ $item->id ?? '' }}">
                                                        <input type="hidden" name="exam_ids" value="{{ $search['exam_id'] ?? '' }}">
                                                        <input type="hidden" name="class_type_id" value="{{ $search['class_type_id'] ?? '' }}">
    
                                                        <td>{{ $item['admissionNo'] ?? '' }}</td>
                                                        <td>{{ $item['first_name'] ?? '' }}</td>
                                                        <td><input type="text" name="roll_no[]" value="{{ $exam_data->roll_no ?? '' }}" style="width:150px;" placeholder="Roll No"></td>
                                                        <td><input type="text" name="attendence[]" value="{{ $exam_data->attendence ?? '' }}" style="width:150px;" placeholder="Attendence"></td>
                                                        <td><input type="text" name="permote_to[]" value="{{ $exam_data->permote_to ?? '' }}" style="width:150px;" placeholder="Permote to"></td>
                                                        <td><input type="text" name="rank[]" value="{{ $exam_data->rank ?? '' }}" style="width:150px;" placeholder="Rank"></td>
                                                        <td><input type="text" name="remark[]" value="{{ $exam_data->remark ?? '' }}" style="width:250px;" placeholder="Remark"></td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="12" class="text-center">No Students Found !</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Submit Button -->
                                <div class="col-md-12 text-center p-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                        <!-- End Student Table -->

                    </div>
                    <!-- End Card -->

                </div>
            </div>
        </div>
    </section>
</div>

<style>
    /* Mobile view improvements */
@media (max-width: 768px) {
  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  table {
    font-size: 13px;
    white-space: nowrap;
  }
  th, td {
    text-align: center;
    vertical-align: middle;
  }
  input.form-control-sm {
    min-width: 120px; /* so input fields don’t shrink too much */
  }
}

</style>

<!-- Scripts -->
<script src="{{ URL::asset('public/assets/school/js/jquery.min.js') }}"></script>

<script>
$(document).ready(function(){
    // Check/uncheck view
    $("#view1").click(function(){
        if ($(this).is(':checked')) {
            $(".viewcheck").prop('checked', true);
        } else {
            $(".viewcheck").prop('checked', false);
        }
    });

    // Load exams on class change
    $('#class_type_id').on('change', function(){
        var baseurl = "{{ url('/') }}";
        var class_type_id = $(this).val();

        $.ajax({
            headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            url: baseurl + '/examData/' + class_type_id,
            success: function(data){
                $("#exam_id").html(data);
            }
        });
    });
});
</script>

@endsection
