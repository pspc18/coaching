@php
$classType = Helper::classType();
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">    
            <div class="card card-outline card-orange">
             <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-address-book-o"></i> &nbsp;{{ __(' Sales Inventory ') }} </h3>
                <div class="card-tools">
                    <a href="{{url('sale_inventory_view')}}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i> {{ __('common.View') }} </a>
                    <a href="{{url('invantory_dashboard')}}" class="btn btn-primary btn-sm"><i class="fa fa-arrow-left"></i> {{ __('messages.Back') }}</a>
                </div>
            </div>        

            <form id="quickForm" action="{{ url('sales_invantory_add') }}" method="post" enctype="multipart/form-data">
                @csrf
                
                <!-- Top Header Inputs -->
                <div class="row m-2">
                     <div class="col-md-3">
                        <div class="form-group">
                            <label>Invoice/Bill No.</label>
                            <input type="text" class="form-control" id="invoice_no" name="invoice_no" placeholder="Invoice No.">
                        </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}">
                        </div> 
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>GSTIN</label>
                            <input type="text" class="form-control" id="gstin" name="gstin" placeholder="GSTIN">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class='text-danger'>{{ __('common.Class') }}*</label>
                            <select class="form-control select2" id="class_search_id" name="class_search_id" required>
                                <option value="">{{ __('common.Select') }}</option>
                                @if(!empty($classType))
                                @foreach($classType as $type)
                                <option value="{{ $type->id ?? '' }}">{{ $type->name ?? '' }}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Student Selection Section -->
                <div class="row m-2" style="border: 1px solid #ccc; padding: 10px; background: #f9f9f9; border-radius: 5px;">
                    <!-- Left: Student Table -->
                    <div class="col-md-7" style="max-height: 200px; overflow-y: auto;">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>Adm No.</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                </tr>
                            </thead>
                            <tbody id="student_list_body">
                                <tr><td colspan="3" class="text-center text-danger">Please Select Class First</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Right: Selected Student Profile -->
                    <div class="col-md-5">
                        <div class="card mb-0">
                            <div class="card-body py-2">
                                <input id="admission_id" type="hidden" name="admission_id" required>
                                <input id="student_name" type="hidden" name="student_name" required>
                                <input id="mobile" type="hidden" name="mobile" required>

                                <div class="row">
                                    <div class="col-md-4 text-center mt-2">
                                        <img src="{{ url('public/images/dummy_profile.png') }}" alt="Student" style="width: 80px; height: 80px; border-radius: 5px; object-fit: cover; border: 1px solid #ddd;">
                                    </div>
                                    <div class="col-md-8">
                                        <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                                            <tr><th>Name:</th><td id="lbl_name">-</td></tr>
                                            <tr><th>Mobile:</th><td id="lbl_mobile">-</td></tr>
                                            <tr><th>Father:</th><td id="lbl_father">-</td></tr>
                                            <tr><th>Mother:</th><td id="lbl_mother">-</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Items List with Checkboxes (Fees Pay Style) -->
                <div class="m-2">
                    <h5 class="text-primary mt-3">Select Inventory Items :-</h5>
                    <table class="table table-bordered">
                        <thead class="bg-secondary text-white">
                            <tr>
                                <th style="width: 50px;">Select</th>
                                <th>Item Name</th>
                                <th style="width: 150px;">Qty</th>
                                <th style="width: 150px;">Amount</th>
                                <th style="width: 150px;">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($inventoryItems) && count($inventoryItems) > 0)
                                @foreach($inventoryItems as $item)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="item-checkbox" name="inventory_item_id[]" value="{{ $item->id }}" data-id="{{ $item->id }}">
                                    </td>
                                    <td><strong>{{ $item->name ?? $item->item_name ?? 'Item' }}</strong></td>
                                    <td>
                                        <input type="number" class="form-control item-qty" id="qty_{{ $item->id }}" name="qty[{{ $item->id }}]" value="1" min="1" disabled onkeyup="calculateRow({{ $item->id }})" onchange="calculateRow({{ $item->id }})">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control item-amount" id="amount_{{ $item->id }}" name="amount[{{ $item->id }}]" value="{{ $item->mrp ?? $item->amount ?? 0 }}" disabled onkeyup="calculateRow({{ $item->id }})">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control item-total" id="total_amount_{{ $item->id }}" readonly value="0">
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center text-danger">No Inventory Items Found</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Total Summary -->
                <div class="row m-2 justify-content-end">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Total Quantity</label>
                            <input type="text" class="form-control" id="Quantity" readonly name="total_qty" value="0">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="text-success font-weight-bold">Net Amount</label>
                            <input type="text" class="form-control font-weight-bold" id="net_amount" readonly name="net_amount" value="0">
                        </div>
                    </div>
                </div>

                <div class="col-md-12 text-center mt-3 mb-3">
                    <button type="submit" class="btn btn-success btn-lg"><i class="fa fa-shopping-cart"></i> Submit / Collect</button>
                </div>
            </form>

            <!-- Previous Student Sales History Section (Page Ke Niche Table) -->
            <div class="m-2 pt-3" style="border-top: 2px dashed #007bff;">
                <h5 class="text-danger font-weight-bold"><i class="fa fa-history"></i> Previous Inventory Sales History</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="bg-info text-white">
                            <tr>
                                <th>Invoice No</th>
                                <th>Date</th>
                                <th>Total Qty</th>
                                <th>Total Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="student_history_body">
                            <tr>
                                <td colspan="5" class="text-center text-muted">Select a student to view previous transaction history</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            </div>
        </div>
      </div>
   </section>
</div>

<script>
$(document).ready(function() {
    var SITEURL = "{{ url('/') }}";

    // 1. Class change hone par students load karein
    $('#class_search_id').on('change', function() {
        var classId = $(this).val();
        $('#student_list_body').html('<tr><td colspan="3" class="text-center">Loading...</td></tr>');
        resetStudentProfile();

        if(classId) {
            $.ajax({
                headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                url: SITEURL + "/getStudentsForInventory",
                type: 'POST',
                data: { class_id: classId },
                success: function(data) {
                    var html = '';
                    if(data.length > 0) {
                        $.each(data, function(key, student) {
                            var fullName = student.first_name + ' ' + (student.last_name ? student.last_name : '');
                            html += '<tr class="student-row" style="cursor:pointer;" ' +
                                    'data-id="'+student.id+'" ' +
                                    'data-name="'+fullName+'" ' +
                                    'data-mobile="'+(student.mobile || '')+'" ' +
                                    'data-father="'+(student.father_name || '')+'" ' +
                                    'data-mother="'+(student.mother_name || '')+'">' +
                                    '<td>' + (student.admissionNo || 'NA') + '</td>' +
                                    '<td>' + fullName + '</td>' +
                                    '<td>' + student.class_name + '</td>' +
                                '</tr>';
                        });
                    } else {
                        html = '<tr><td colspan="3" class="text-center text-danger">No Students Found</td></tr>';
                    }
                    $('#student_list_body').html(html);
                }
            });
        } else {
            $('#student_list_body').html('<tr><td colspan="3" class="text-center text-danger">Please Select Class</td></tr>');
        }
    });

    // 2. Student select hone par profile fill karein aur Sales History load karein
    $(document).on('click', '.student-row', function() {
        $('.student-row').removeClass('bg-primary text-white');
        $(this).addClass('bg-primary text-white');

        var studentId = $(this).data('id');

        // Form profile set
        $('#admission_id').val(studentId);
        $('#student_name').val($(this).data('name'));
        $('#mobile').val($(this).data('mobile'));

        $('#lbl_name').text($(this).data('name'));
        $('#lbl_mobile').text($(this).data('mobile'));
        $('#lbl_father').text($(this).data('father'));
        $('#lbl_mother').text($(this).data('mother'));

        // Load Sales History (Niche Table ke liye)
        loadStudentSalesHistory(studentId);
    });

    function loadStudentSalesHistory(admissionId) {
        $('#student_history_body').html('<tr><td colspan="5" class="text-center">Loading History...</td></tr>');
        $.ajax({
            headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            url: SITEURL + "/getStudentSaleHistory",
            type: 'POST',
            data: { admission_id: admissionId },
            success: function(data) {
                var html = '';
                if(data.length > 0) {
                    $.each(data, function(key, item) {
                        html += '<tr>' +
                                '<td><span class="badge badge-primary">' + (item.invoice_no || item.id) + '</span></td>' +
                                '<td>' + (item.date || '-') + '</td>' +
                                '<td>' + (item.total_qty || 0) + '</td>' +
                                '<td>₹ ' + (item.total_amount || 0) + '</td>' +
                                '<td><a href="' + SITEURL + '/sale_inventory_print/' + item.id + '" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-print"></i> Print</a></td>' +
                            '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center text-danger">No Previous Sales Records Found</td></tr>';
                }
                $('#student_history_body').html(html);
            }
        });
    }

    function resetStudentProfile() {
        $('#admission_id').val('');
        $('#student_name').val('');
        $('#mobile').val('');
        $('#lbl_name').text('-');
        $('#lbl_mobile').text('-');
        $('#lbl_father').text('-');
        $('#lbl_mother').text('-');
        $('#student_history_body').html('<tr><td colspan="5" class="text-center text-muted">Select a student to view previous transaction history</td></tr>');
    }

    // 3. Checkbox click enable/disable logic
    $(document).on('change', '.item-checkbox', function() {
        var itemId = $(this).data('id');
        if($(this).is(':checked')) {
            $('#qty_' + itemId).prop('disabled', false);
            $('#amount_' + itemId).prop('disabled', false);
            calculateRow(itemId);
        } else {
            $('#qty_' + itemId).prop('disabled', true);
            $('#amount_' + itemId).prop('disabled', true);
            $('#total_amount_' + itemId).val(0);
            calculateGrandTotal();
        }
    });
});

// Single Row Calculation
function calculateRow(itemId) {
    var qty = parseFloat($('#qty_' + itemId).val()) || 0;
    var amount = parseFloat($('#amount_' + itemId).val()) || 0;
    var total = qty * amount;
    $('#total_amount_' + itemId).val(total.toFixed(2));
    calculateGrandTotal();
}

// Grand Total Calculation (Only for Checked items)
function calculateGrandTotal() {
    var grandTotal = 0;
    var grandQty = 0;

    $('.item-checkbox:checked').each(function() {
        var itemId = $(this).data('id');
        var qty = parseFloat($('#qty_' + itemId).val()) || 0;
        var total = parseFloat($('#total_amount_' + itemId).val()) || 0;

        grandQty += qty;
        grandTotal += total;
    });

    $('#Quantity').val(grandQty);
    $('#net_amount').val(grandTotal.toFixed(2));
}
</script>
@endsection