@php
  $classType = Helper::classType();
  $getSetting = Helper::getSetting();
@endphp
@extends('layout.app') 
@section('content')

<style>
       .border_none {
            border: none;
        }
        .bg_color_heading {
            background-color: #f1f1f1;
        }
        @media print {
            button {
                display: none;
            }
            body {
                margin: 0;
                padding: 20px;
                font-family: Arial, sans-serif;
            }
            .page {
                width: 100%;
                border: 1px solid #000;
                padding: 20px;
                box-sizing: border-box;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }
            .bg_color_heading td {
                background-color: #f1f1f1;
                font-weight: bold;
            }
            .border_none {
                border: none;
            }
            .no-print {
                display: none;
            }
        }
     
   </style>

<div class="content-wrapper">

   <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">      
    <div class="card card-outline card-orange">
        <div class="card-header bg-primary">
        <h3 class="card-title"><i class="fa fa-th-large"></i> &nbsp;{{ __('View Store Request') }} </h3>
        <div class="card-tools">
        <!--<a href="{{url('Fees/add')}}" class="btn btn-primary  btn-sm" title="Add Fees"><i class="fa fa-plus"></i>{{ __('common.Add') }}</a>-->
        <a href="{{url('storeDashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
        </div>
        
        </div>  
        
            <div class="row m-2">  
                <div class="col-md-12">
                            <table id="example1" class="table table-bordered table-striped  dataTable">
								<thead class="bg-primary">
									<tr role="row">
										<th>{{ __('common.SR.NO') }}</th>
										<th>{{ __('Receipt No.') }}</th>
										<th>{{ __('common.Name') }} </th>
										<th>{{ __('common.Class') }} </th>
										<th>{{ __('common.Mobile') }}</th>
									</tr>
								</thead>
								<tbody > 
								@if(!empty($data)) 
    								@php 
    								    $i=1; 
    								@endphp 
								@foreach ($data as $item)
									<tr>
										<td>{{ $i++ }}</td>
											<td>
											     <a href="#" class="toggle-options btn text-info">#{{$item->receipt_no ?? ''}}
</a>
          <div class="options-buttons mt-2 mb-2"style='display:none;'>
            <a target='_blank' class='btn btn-primary btn-xs' href="{{url('editInvoiceInventory')}}/{{$item->receipt_no ?? ''}}"><i class="fa fa-edit"></i></a>
           <a target='_blank' class='btn btn-info btn-xs' href="{{url('storeReceipt')}}/{{$item->receipt_no ?? ''}}"><i class="fa fa-eye"></i></a>
           <a class='btn btn-danger btn-xs delete_row' data-id="{{$item->receipt_no ?? ''}}" data-toggle="modal" data-target="#revert_modal"><i class="fa fa-trash"></i></a>
									                
          </div>
											  
											    </td>
								        <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
										<td>{{ $item['class_name'] ?? ''}}</td>
										<td>{{ $item['mobile'] ?? ''}}</td>
									
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
</div>
</section>
</div>
        
        <div class="modal fade" id="revert_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
      
        <!-- Modal Header --> 
        <div class="modal-header">
          <h5 class='text-danger'>If you delete this receipt,all related transactions will also be deleted. </h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <form method='post' action='{{url("deleteReceiptInventory")}}' method="post">
      	    @csrf
            <div class="modal-body">
              
                <input type="hidden" id="delete_receipt" name="delete_receipt">
                <h5 >
               Do you still wish to continue?</h5>
            </div>
        
            <div class="modal-footer">
                <button type="button" id="hide_modal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
      </div>
    </div>
  </div>
  
   <script>
      $( ".delete_row" ).on( "click", function() {
var delete_receipt = $(this).data('id');
$('#delete_receipt').val(delete_receipt);
} );
  </script>
<script>
  $(document).ready(function(){
    $('.toggle-options').click(function(e){
      e.preventDefault(); 
      var $options = $(this).next('.options-buttons');
      $('.options-buttons').not($options).slideUp();
      $options.slideToggle();
    });
    $(document).click(function(event) { 
      var $target = $(event.target);
      if(!$target.closest('.toggle-options').length && !$target.closest('.options-buttons').length) {
        $('.options-buttons').slideUp();
      }        
    });
  });
</script>

@endsection 