

@extends('layout.app') 
@section('content')

<div class="content-wrapper">
   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">

                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-cogs"></i> &nbsp;{{('System Student Field') }} </h3>
                    <div class="card-tools">
                        <a href="{{url('AddStudentField')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(17)->add ? '' : 'd-none' }}" title="Add User"><i class="fa fa-plus"></i> {{ ('Custom Field Add') }} </a>
                        <a href="{{url('settings_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a> 
                    </div>
                </div> 
                <div class="card-body">
                <table id="example11" class="table table-bordered table-striped dataTable dtr-inline ">
                  <thead class="bg-primary">
                  <tr role="row">
                            <th>{{ __('Fields Name') }}</th>
                            <th>{{ __('Active') }}</th>
                            <th>{{ __('Required') }}</th>
                            <th>{{ __('Student Login Edit Permission') }}</th>
                            <th>{{ __('Order') }}</th>
                     </tr> 
                  </thead>
                  <tbody>
                      @if(!empty($data))
                        @foreach ($data as $item)
                        <tr>
                            <td><i class="fa fa-arrow-circle-right"></i> {{ $item['field_label'] ?? '' }}</td>

                            {{-- Active --}}
                            <td>  
                                <label class="switch1">
                                    <input type="checkbox" class="toggle-status" 
                                           data-id="{{ $item->id }}"  
                                           data-inputname="status"
                                           data-label="{{ $item->field_label }} Status"
                                           {{ $item->status == 0 ? 'checked' : '' }}>
                                    <span class="slider1">
                                        <span class="on">✔ </span>
                                        <span class="off">✖ </span>
                                    </span>
                                </label>
                           </td>

                           {{-- Required --}}
                           <td>  
                                <label class="switch1">
                                    <input type="checkbox" class="toggle-status" 
                                           data-id="{{ $item->id }}"  
                                           data-inputname="required"
                                           data-label="{{ $item->field_label }} Required"
                                           {{ $item->required == 0 ? 'checked' : '' }}>
                                    <span class="slider1">
                                        <span class="on">✔ </span>
                                        <span class="off">✖ </span>
                                    </span>
                                </label>
                            </td>

                            {{-- Student Edit Permission --}}
                            <td>  
                                <label class="switch1">
                                    <input type="checkbox" class="toggle-status" 
                                           data-id="{{ $item->id }}"  
                                           data-inputname="stu_edit_perm"
                                           data-label="{{ $item->field_label }} Permission"
                                           {{ $item->stu_edit_perm == 0 ? 'checked' : '' }}>
                                    <span class="slider1">
                                        <span class="on">✔ </span>
                                        <span class="off">✖ </span>
                                    </span>
                                </label>
                            </td>

                            {{-- Order Input --}}
                            <td>
                                <input type="number" class="form-control field-order-input w-50" 
                                       data-id="{{ $item->id }}" 
                                       value="{{ $item['field_order'] ?? '' }}" />
                            </td>
                        </tr>
                      @endforeach
                @endif
            </tbody>
                  </table>
              </div>
            </div>
        </div>
      </div>
    </section>
</div>

<script>
 $(document).on('change', '.toggle-status', function(){
    let checkbox = $(this);
    let student_field_id = checkbox.data('id');
    let inputName = checkbox.data('inputname'); 
    let fieldLabel = checkbox.data('label');
    let status = checkbox.is(':checked') ? '0' : '1';

    $.ajax({
        url: "{{ url('SystemStudentFieldStatusUpdate') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            student_field_id,
            inputName,
            status
        },
        success: function(res){
            if(res.success){
                if(status === '0'){
                    toastr.success(fieldLabel + ' Activated Successfully!');
                } else {
                    toastr.warning(fieldLabel + ' Deactivated Successfully!');
                }
            } else {
                checkbox.prop('checked', !checkbox.prop('checked'));
                toastr.error('Something went wrong!');
            }
        }
    });
});


 $(document).on('change', '.field-order-input', function(){
    let input = $(this);
    let student_field_id = input.data('id');
    let field_order = input.val();

    $.ajax({
        url: "{{ url('SystemStudentFieldOrderUpdate') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            student_field_id,
            field_order
        },
        success: function(res){
            if(res.success){
                toastr.success('Field order updated successfully!');
               // setTimeout(() => location.reload(), 800);
            } else {
                toastr.error(res.message || 'Failed to update order');
            }
        }
    });
});
</script>
@endsection
