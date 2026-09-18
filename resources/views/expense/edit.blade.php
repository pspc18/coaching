@php
    $getHostel = Helper::getHostel();
    $getPaymentMode = Helper::getPaymentMode();
    $getRole = Helper::getUsers();
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
              <h3 class="card-title"><i class="fa fa-credit-card"></i> &nbsp;{{ __('expense.Add Expense') }}</h3>
              <div class="card-tools">
                <a href="{{url('expenseView')}}" class="btn btn-primary btn-sm">
                  <i class="fa fa-eye"></i> {{ __('common.View') }}
                </a>
              </div>
            </div>
            <form id="quickForm" action="{{ url('expenseAdd') }}" method="post" enctype="multipart/form-data">
              @csrf
              <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-6 col-md-3">
                        <label class="text-danger">{{ __('common.Date') }}*</label>
                        <input type="date" class="form-control" name="date" value="{{ $data[0]['date'] ?? '' }}" required>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="text-danger"><b>{{ __('Staff/User') }}*</b></label>
                        <select class="form-control" name="role" required>
                            <option value="">Select</option>
                            @foreach($getRole as $item)
                                <option value="{{ $item->id }}" {{$item->id == $data[0]["user_id"]  ? "selected" : ""}}>{{ $item->first_name }} {{ $item->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="g-3 mt-3 border rounded p-3 " id="form-rows-container">
                    @foreach($data as $datas)
                  <div class="row expense-entry" id="appendRow_0">
                    <div class="col-6 col-md-2">
                        <label class="text-danger"><b>{{ __('Category') }}*</b></label>
                        <select class="form-control" name="category[]" required>
                            <option value="">Select</option>
                            <option value="2" {{ $datas["category_id"] == 2 ? "selected" : "" }}>Salary</option>
                            <option value="3" {{ $datas["category_id"] == 3 ? "selected" : "" }}>Loan Payments</option>
                            <option value="4" {{ $datas["category_id"] == 4 ? "selected" : "" }}>Mobile Bill &amp; Recharge</option>
                            <option value="6" {{ $datas["category_id"] == 6 ? "selected" : "" }}>School Building Maintenance</option>
                            <option value="7" {{ $datas["category_id"] == 7 ? "selected" : "" }}>Computer &amp; Electronics</option>
                            <option value="8" {{ $datas["category_id"] == 8 ? "selected" : "" }}>Laboratory Expenses</option>
                            <option value="9" {{ $datas["category_id"] == 9 ? "selected" : "" }}>Furniture Expense</option>
                            <option value="10" {{ $datas["category_id"] == 10 ? "selected" : "" }}>Fuel &amp; Gas</option>
                            <option value="11" {{ $datas["category_id"] == 11 ? "selected" : "" }}>Printing &amp; Stationery Items</option>
                            <option value="12" {{ $datas["category_id"] == 12 ? "selected" : "" }}>Donations And Taxes</option>
                            <option value="13" {{ $datas["category_id"] == 13 ? "selected" : "" }}>Electricity Bills</option>
                            <option value="14" {{ $datas["category_id"] == 14 ? "selected" : "" }}>Internet Bills</option>
                            <option value="15" {{ $datas["category_id"] == 15 ? "selected" : "" }}>Water Bills</option>
                            <option value="16" {{ $datas["category_id"] == 16 ? "selected" : "" }}>Staff Welfare Expenses</option>
                            <option value="17" {{ $datas["category_id"] == 17 ? "selected" : "" }}>Rent Expenses</option>
                            <option value="18" {{ $datas["category_id"] == 18 ? "selected" : "" }}>Event Expenses</option>
                            <option value="21" {{ $datas["category_id"] == 21 ? "selected" : "" }}>House Expenses</option>
                            <option value="22" {{ $datas["category_id"] == 22 ? "selected" : "" }}>Maintenance</option>
                            <option value="23" {{ $datas["category_id"] == 23 ? "selected" : "" }}>Insurance</option>
                            <option value="24" {{ $datas["category_id"] == 24 ? "selected" : "" }}>Education &amp; Tuition</option>
                            <option value="25" {{ $datas["category_id"] == 25 ? "selected" : "" }}>Sports Goods</option>
                            <option value="26" {{ $datas["category_id"] == 26 ? "selected" : "" }}>Other Charges</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                      <label class="text-danger">{{ __('Expense name') }}*</label>
                      <input type="text" class="form-control" name="name[]" placeholder="Expense Name" value="{{ $datas->name ?? '' }}" required>
                      <input type="hidden" name="id[]" value="{{ $datas->id ?? '' }}">
                    </div>
                    <div class="col-6 col-md-2">
                      <label class="text-danger">{{ __('expense.Rate') }}*</label>
                      <input type="text" class="form-control" onkeyup="calculateAmount(this);" value="{{ $datas->rate ?? '' }}" name="rate[]" required>
                    </div>
                    <div class="col-6 col-md-2">
                      <label class="text-danger">{{ __('expense.Quantity') }}*</label>
                      <input type="text" class="form-control" onkeyup="calculateAmount(this);" value="{{ $datas->quantity ?? '' }}" name="quantity[]" required>
                    </div>
                    <div class="col-6 col-md-2">
                      <label class="text-danger">{{ __('expense.Total') }}*</label>
                      <input type="text" class="form-control amount" name="amount[]" value="{{ $datas->amount ?? '' }}" readonly required>
                    </div>
                    <div class="col-6 col-md-1 text-center">
                      <button type="button" class="btn btn-primary btn-sm mt-4 add-entry"><i class="fa fa-plus"></i></button>
                      <button type="button" class="btn btn-danger btn-sm mt-4 delete-entry" data-id='{{ $datas["id"] ?? '' }}' data-bs-toggle="modal" data-bs-target="#Modal_id"><i class="fa fa-trash"></i></button>
                    </div>
                  </div>
                  @endforeach
                </div>
                
                <div class="row g-3 mt-4">
                  <div class="col-6 col-md-3">
                    <label class="text-danger"><b>{{ __('expense.Total Amount') }}*</b></label>
                    <input type="text" class="form-control" id="total_amt" name="total_amt" value="{{ $data[0]['total_amt'] ?? '' }}" readonly>
                  </div>
                  <div class="col-6 col-md-3">
                    <label class="text-danger"><b>{{ __('Payment Mode') }}*</b></label>
                    <select class="form-control" name="payment_mode_id" required>
                        @foreach($getPaymentMode as $value)
                            <option value="{{ $value->id }}" {{ $value->id == $data[0]["payment_mode_id"] ? "selected" : "" }}>{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label><b>{{ __('Attachment') }}</b></label>
                    <input type="file" class="form-control" name="attachment" accept="image/png, image/jpg, image/jpeg">
                    <img width='100px' height='100px' class='mt-2 mb-2 doc_img profileImg pointer'src ='{{env("IMAGE_SHOW_PATH").($data[0]["attachment"] == '' ? "default/Icon_images/noImage.png" : "expense/".$data[0]["attachment"] ?? '')}}'  data-img="@if(!empty($data->attachment)) {{ env('IMAGE_SHOW_PATH').'expense/'.$data->attachment }} @endif"/>
                    <p class="text-danger" id="image_error"></p>
                </div>
                <div class="col-6 col-md-3">
                    <label><b>{{ __('expense.Description') }}</b></label>
                    <textarea name="description" class="form-control" placeholder="If have any description write here...">{{ $data[0]['description'] ?? '' }}</textarea>
                </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col text-center">
                        <button type="submit" class="btn btn-primary">{{ __('common.Submit') }}</button>
                    </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- The Modal -->
<div class="modal" id="Modal_id">
	<div class="modal-dialog">
		<div class="modal-content" style="background: #555b5beb;">
			<div class="modal-header">
				<h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
			<form action="{{ url('expenseDelete') }}" method="post"> 
			    @csrf
				<div class="modal-body">
					<input type=hidden id="delete_id" name=delete_id>
					<h5 class="text-white">{{ __('common.Are you sure you want to delete') }}  ?</h5> </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
					<button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('common.Delete') }}</button>
				</div>
			</form>
		</div>
	</div>
</div> 

<script>

$('.delete-entry').click(function() {
	var delete_id = $(this).data('id');
	
	$('#delete_id').val(delete_id);
});

function calculateAmount(element) {
    const row = $(element).closest('.expense-entry');
    const quantity = parseFloat(row.find('input[name="quantity[]"]').val()) || 0;
    const rate = parseFloat(row.find('input[name="rate[]"]').val()) || 0;
    const amount = quantity * rate;

    row.find('input[name="amount[]"]').val(amount.toFixed(2));

    calculateSum();
}

function calculateSum() {
    let sum = 0;
    $('.amount').each(function () {
        const val = parseFloat($(this).val());
        if (!isNaN(val)) {
            sum += val;
        }
    });
    $('#total_amt').val(sum.toFixed(2));
}

$(document).ready(function () {
    let rowIndex = 1;

$(document).on('click', '.add-entry', function () {
    const newRow = $('.expense-entry').first().clone();

    // Clear inputs
    newRow.find('input, select, textarea').val('');

    // Update IDs and events
    newRow.find('[id]').each(function () {
        const id = $(this).attr('id');
        if (id.includes('rate')) {
            const newId = 'rate_' + rowIndex;
            $(this).attr('id', newId);
            $(this).attr('onkeyup', 'calculateAmount(this.value,' + rowIndex + ')');
        }
        if (id.includes('quantity')) {
            const newId = 'quantity_' + rowIndex;
            $(this).attr('id', newId);
            $(this).attr('onkeyup', 'calculateAmount(this.value,' + rowIndex + ')');
        }
        if (id.includes('amount')) {
            const newId = 'amount_' + rowIndex;
            $(this).attr('id', newId);
        }
    });

    // Remove any existing delete button (so it won't duplicate from original)
    newRow.find('.delete-entry').remove();

    // Add a new delete button at the end of the row
    newRow.find('div').last().append(
        '<button type="button" class="btn btn-danger delete-entry mt-4"><i class="fa fa-trash"></i></button>'
    );

    // Set new row ID
    newRow.attr('id', 'appendRow_' + rowIndex);

    // Append to container
    $('#form-rows-container').append(newRow);

    rowIndex++;
});

// Handle delete click

    $(document).on('click', '.delete-entry', function () {
    const row = $(this).closest('.expense-entry');

    // Prevent removal if data-id exists and is not empty
    if ($(this).attr('data-id') && $(this).attr('data-id').trim() !== '') {
        return; // block deletion
    }

    // Remove only if more than one row remains
    if ($('.expense-entry').length > 1) {
        row.remove();
        calculateSum();
    }
});

  });
</script>

<style>

.expense-entry {
    border-bottom: 1px solid #ccc;
    padding-bottom: 15px;
    margin-bottom: 15px;
  }
  .expense-entry:last-child {
    border-bottom: none;
  }
  #image_error {
    font-weight: bold;
    font-size: 14px;
  }
  .action_container>* {
    color: #fff;
    text-decoration: none;
    display: inline-block;
    padding: 4px 7px;
    cursor: pointer;
    transition: 0.3s ease-in-out;
  }
  textarea {
    height: calc(2.25rem) !important;
  }
</style>
@endsection