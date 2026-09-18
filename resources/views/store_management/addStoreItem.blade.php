@php
  $classType = Helper::classType();
  $getSetting = Helper::getSetting();
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
        <h3 class="card-title"><i class="fa fa-th-large"></i> &nbsp;{{ __('Add Store Item') }} </h3>
        <div class="card-tools">
        <!--<a href="{{url('Fees/add')}}" class="btn btn-primary  btn-sm" title="Add Fees"><i class="fa fa-plus"></i>{{ __('common.Add') }}</a>-->
        <a href="{{url('storeDashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
        </div>
        
        </div>  
        
                  
            <div class='row m-2'>
                
                <div class='col-md-12'>
                    
             <div class="mb-3">
        <button id="addRow" class="btn btn-primary">Add Row</button>
        <button id="editRow" class="btn btn-warning">Edit Row</button>
        <button id="saveRow" class="btn btn-success" disabled>Save Row</button>
        <button id="deleteSelected" class="btn btn-danger">Delete Selected</button>
    </div>
    <table class="table table-bordered">
        <thead class="bg-primary">
            <tr>
                <th scope="col"><input type="checkbox" id="selectAll" class="custom-checkbox"></th>
                <th scope="col">Name</th>
                <th scope="col">Rate</th>
                  <th scope="col">Qty</th>
            </tr>
        </thead>
        <tbody>
            @if(!empty($data))
            @foreach($data as $key => $item)
            <tr data-new="false" data-id="{{ $item->id ?? '' }}">
                <td><input type="checkbox" class="row-checkbox custom-checkbox"></td>
                <td>{{$item->name ?? ''}}</td>
                <td>₹ {{$item->rate ?? ''}}</td>
                 <td>{{$item->qty ?? ''}}</td>
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


<div class="modal fade" id="deleteModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Delete Confirmation</h4>
          <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">
          Are you sure you want to do this action?
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-danger" id="delete_btn">Delete</button>
        </div>
        
      </div>
    </div>
  </div>
        
<script>
    $(document).ready(function() {
        $('#addRow').on('click', function() {
            $('#saveRow').prop('disabled',false);
            const newRow = `<tr data-new="true">
                <td><input type="checkbox" checked class="row-checkbox custom-checkbox"></td>
                <td><input type="text" class="form-control" placeholder="Enter Name"></td>
                <td><input type="text" class="form-control" placeholder="Enter Rate"></td>
                <td><input type="text" class="form-control" placeholder="Enter Qty"></td>
            </tr>`;
            $('table tbody').append(newRow);
        });

        $('#deleteSelected').on('click', function() {
            const checkedRows = $('table tbody').find('input.row-checkbox:checked');
            if (checkedRows.length === 0) {
                toastr.error('Please select at least one row to delete.');
                return;
            }else{
                $('#deleteModal').modal('show');
            }
        });
        
        $('#delete_btn').click(function(){
            const checkedRows = $('table tbody').find('input.row-checkbox:checked');
            checkedRows.each(function() {
                const row = $(this).closest('tr');
                const isNew = row.attr('data-new') === 'true';
                const id = row.attr('data-id');
                
                if(isNew){
                    toastr.success('Row Deleted successfully!');
                    row.remove();
                }else{
                    $.ajax({
                        url: 'deleteStoreItem',
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            id: id,
                        },
                        success: function(response) {
                            toastr.success('Row Deleted successfully!');
                            row.remove();
                        },
                        error: function(xhr) {
                            alert('An error occurred while saving the row.');
                        }
                    });
                }
                
                $('#deleteModal').modal('hide');
                
                $('#selectAll').prop('checked',false);
            }); 
        });

        $('#selectAll').on('click', function() {
            if($(this).is(':checked')){
                $('table tbody').find('input.row-checkbox').prop('checked', true);
                // $('#addRow').prop('disabled',true);
            }else{
                $('table tbody').find('input.row-checkbox').prop('checked', false);
                // $('#addRow').removeAttr('disabled');
            }
        });

        $('#editRow').on('click', function() {
            const checkedRows = $('table tbody').find('input.row-checkbox:checked');
            if (checkedRows.length === 0) {
                toastr.error('Please select at least one row to edit.');
                return;
            }
            
            $('#saveRow').prop('disabled',false);

            checkedRows.each(function() {
                const row = $(this).closest('tr');
                const nameCell = row.children().eq(1);
                const rateCell = row.children().eq(2);
                const qtyCell = row.children().eq(3);

                if (!nameCell.has('input').length) {
                    const nameText = nameCell.text().trim();
                    const rateText = rateCell.text().trim().replace('₹', '').trim();
                    const qtyText = qtyCell.text().trim();
                    nameCell.html(`<input type="text" class="form-control" value="${nameText}">`);
                    rateCell.html(`<input type="text" class="form-control" value="${rateText}">`);
                    qtyCell.html(`<input type="text" class="form-control" value="${qtyText}">`);
                }
            });

            $('#deleteSelected').prop('disabled', true);
        });

        $('#saveRow').on('click', function() {
    const checkedRows = $('table tbody').find('input.row-checkbox:checked');
    if (checkedRows.length === 0) {
        toastr.error('Please select at least one row to save.');
        return;
    }
    checkedRows.each(function() {
        const row = $(this).closest('tr');
        const isNew = row.attr('data-new') === 'true';
        const id = row.attr('data-id');
        const name = row.find('td:eq(1) input').val();
        const rate = row.find('td:eq(2) input').val();
        const qty = row.find('td:eq(3) input').val(); // New qty field

        if(name != "" && rate != "" && qty != ""){
            $.ajax({
                url: 'addStoreItem',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    id: isNew ? null : id,
                    name: name,
                    rate: rate,
                    qty: qty // Send qty data to the server
                },
                success: function(response) {
                    toastr.success('Row saved successfully!');
                    row.removeAttr('data-new');
                    row.attr('data-id', response.id); // Assuming response contains the new id
                    row.find('td:eq(1)').html(name);
                    row.find('td:eq(2)').html('₹ ' + rate);
                    row.find('td:eq(3)').html(qty); // Update the qty column
                    row.find('input.row-checkbox').prop('checked', false);
                },
                error: function(xhr) {
                    alert('An error occurred while saving the row. Please try again later');
                }
            });
        } else {
            toastr.error('All Input Fields Are Required');
        }
    });

    $('#selectAll').prop('checked',false);
    $('#deleteSelected').prop('disabled', false);
});
    });
</script>
@endsection 