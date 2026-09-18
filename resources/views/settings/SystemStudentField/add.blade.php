@php
    $getCountry = Helper::getCountry();
    $getState = Helper::getState();
    $getCity = Helper::getCity();
    $getaccounts = Helper::getaccount();
@endphp

@extends('layout.app') 
@section('content')
<div class="content-wrapper">
   
   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">

                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-cogs"></i> &nbsp;{{('Custom Field') }} </h3>
                    <div class="card-tools">
                        <a href="{{url('SystemStudentField')}}" class="btn btn-primary  btn-sm" title="View"><i class="fa fa-eye"></i> {{ ('System Student Field ') }} </a>
                        <a href="{{url('settings_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a> 
                    </div>
                </div> 

                    <div class="card-body">
                     <form id="form-submit-field" action="{{ url('AddStudentField') }}" method="post"  enctype="multipart/form-data">   
                     @csrf            
                        <div class="row">
                         
            			 
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="field_label" style="color:red;">Field Label *</label>
                                    <input type="text" id="field_label" name="field_label" class="form-control  invalid"  placeholder="Field Label">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="field_value" >Field value </label>
                                    <input type="text" id="field_value" name="field_value" class="form-control  invalid" placeholder="Auto Generated" readonly>
                                </div>
                            </div>
            
             
                        
                            <!-- Field Type -->
                            <div class="col-md-6 ">
                                <div class="form-group">
                                    <label for="field_type" style="color:red;">Field Type *</label>
                                    <select id="field_type" name="field_type" class="form-control  invalid" >
                                        <option value="text">Text</option>
                                        <option value="email">email</option>
                                        <option value="number">Number</option>
                                        <option value="date">Date</option>
                                        <option value="dropdown">Select (Dropdown)</option>
                                        <option value="checkbox">Checkbox</option>
                                        <option value="radio">Radio</option>
                                    </select>
                                   
                                </div>
                            </div>
            
                        
                       
            
                <div class="col-md-3" id="common_type">
                    <div class="form-group">
                        <label for="default_value">Default Value</label>
                        <input type="text" id="default_value" name="default_value" class="form-control">
                        
                    </div>
                </div>
                <div class="col-md-3"  id="dropdown_type" style="display: none;">
                    <div class="form-group">
						<label  class="control-label">Default Value <span class="required">*</span></label>
							<textarea type="text" rows="2" class="form-control" name="default_value" placeholder="Option Separate By Comma"></textarea>
							<span class="error"></span>
					</div>
			    </div>
                <!-- Grid Column -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="grid_column" style="color:red;">Grid (1-12)</label>
                        <input type="number" id="grid_column" name="grid_column" class="form-control  invalid" value="6" min="1" max="12" >
                       
                    </div>
                </div>
            
                <!-- Sort Order -->
                <div class="col-md-3 ">
                    <div class="form-group">
                        <label for="sort_order" style="color:red;">Order</label>
                        <input type="number" id="sort_order" name="sort_order" class="form-control" value="0" >
                    </div>
                </div>
            
             <div class="col-md-12 text-center">
    			<button type="submit" id="submitButton" class="btn btn-primary  btn-submit">{{ __('common.Submit') }}</button>
    		</div>



            
            </div>
            </form>
        </div>
        
        
           <div class="card-body">
                <table id="example11" class="table table-bordered table-striped dataTable dtr-inline ">
                  <thead class="bg-primary">
                  <tr role="row">
                     
                            <th>  {{ __('Fields Name') }}</th>
                            <th>{{ __('Active') }}</th>
                            <th>{{ __('Required') }}</th>
                            <th>{{ __('Student Login Edit Permission') }}</th>
                            <th>{{ __('Order') }}</th>
                            <th>Action</th>
                           
                         

                     </tr> 
                  </thead>
                  <tbody>
                      
                      @if(!empty($data))
                        @php
                           $i=1
                        @endphp
                        @foreach ($data  as $item)
                        <tr>
                                <td><i class="fa fa-arrow-circle-right" aria-hidden="true"></i> {{ $item['field_label'] ?? '' }}</td>
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
                            
                            <td>
                                <input type="number" class="form-control field-order-input w-50" 
                                       data-id="{{ $item->id }}" 
                                       value="{{ $item['field_order'] ?? '' }}" />
                            </td>
                            
                            <td>
                                <!-- Delete Button -->
                                <button class="btn btn-danger btn-sm deleteBtn" 
                                        data-id="{{ $item->id }}" 
                                        data-label="{{ $item->field_label }}">
                                    <i class="fa fa-trash"></i>
                                </button>
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
</div>
</section>
</div>






<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteFieldModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>Are you sure you want to delete <b id="fieldName"></b> field?</p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmDelete" class="btn btn-danger">Delete</button>
      </div>

    </div>
  </div>
</div>

<script>
let deleteId = null;

// ------------------------------
// 1. Delete Field
// ------------------------------
$(document).on('click', '.deleteBtn', function () {
    deleteId = $(this).data('id');
    let fieldLabel = $(this).data('label');

    $('#fieldName').text(fieldLabel);
    $('#deleteFieldModal').modal('show');
});

$('#confirmDelete').click(function () {
    if (deleteId) {
        $.ajax({
            url: '/student-fields-delete/' + deleteId,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function (res) {
                if (res.success) {
                    $('button[data-id="' + deleteId + '"]').closest('tr').remove();
                    $('#deleteFieldModal').modal('hide');
                    toastr.success("Field deleted successfully!");
                } else {
                    toastr.error(res.message);
                }
            },
            error: function () {
                toastr.error('Something went wrong!');
            }
        });
    }
});

// ------------------------------
// 2. Toggle Active / Required / Edit Permission
// ------------------------------
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
        },
        error: function(){
            toastr.error('Something went wrong!');
        }
    });
});

// ------------------------------
// 3. Update field_order via AJAX
// ------------------------------
$(document).on('change', '.field-order-input', function(){
    let input = $(this);
    let student_field_id = input.data('id');
    let field_order = input.val();

    input.prop('disabled', true);

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
              //  setTimeout(() => location.reload(), 800);
            } else {
                toastr.error(res.message || 'Failed to update order');
            }
        },
        error: function(){
            toastr.error('Something went wrong while updating order!');
        },
        complete: function(){
            input.prop('disabled', false);
        }
    });
});


// ------------------------------
// 5. Dynamic Slug for field_value
// ------------------------------
document.getElementById('field_label')?.addEventListener('input', function() {
    let label = this.value;
    let fieldValue = label
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '_')
        .replace(/^_+|_+$/g, '');
    document.getElementById('field_value').value = fieldValue;
});

// ------------------------------
// 6. Form Submit via AJAX
// ------------------------------
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $("#form-submit-field").on("submit", function (e) {
        e.preventDefault();

        var $form = $(this);
        var btn = $form.find(".btn-submit");
        var formData = new FormData(this);

        $.ajax({
            url: $form.attr("action"),
            type: "POST",
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
            cache: false,
            beforeSend: function () {
                btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> ' + btn.text());
                $(".error, .alert").remove();
                $(".is-invalid").removeClass('is-invalid');
            },
            success: function(response) {
                if (response.status === 'success') {
                    toastr.success(response.message);
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        location.reload();
                    }
                    $form[0].reset();
                    btn.prop("disabled", false).text(btn.data('original-text') || 'Submit');
                } else {
                    toastr.error(response.message);
                    btn.prop("disabled", false).text(btn.data('original-text') || 'Submit');
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (index, value) {
                        var inputField = $form.find("[name='" + index + "']");
                        if (inputField.closest(".form-group").find(".error").length === 0) {
                            inputField.addClass("is-invalid");
                            inputField.closest(".form-group").append("<div class='error invalid-feedback'></div>");
                        }
                        inputField.closest(".form-group").find(".error").text(value);
                    });
                } else {
                    var errorMessage = xhr.responseJSON?.message || "An unexpected error occurred.";
                    toastr.error(errorMessage);
                }
                btn.prop("disabled", false).text(btn.data('original-text') || 'Submit');
            }
        });
    });
});
</script>

@endsection        