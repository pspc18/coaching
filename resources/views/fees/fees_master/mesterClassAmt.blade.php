<div class="row m-2">
   <div class=" col-md-12 title">
      <h5 class="text-danger">{{__('student.Fess Details') }} :-</h5>
   </div>
@php
//dd($feesAssignDetail);
@endphp
   @if(!empty($data))
   @php
   $i=1;
  
   @endphp
            <div class="col-md-2">
                <lable>{{__('student.Fees Group') }}</lable>
            </div>
            <div class="col-md-2">
                <lable>{{__('common.Amount') }}</lable>
            </div>
            <div class="col-md-2">
                <lable>{{__('student.Dis. in Amt.') }}</lable>
            </div>
           
            <div class="col-md-4">
                <lable>&nbsp;</lable>
            </div>
            
            <div class="col-md-2">
                <!--<lable>{{__('student.Dis. in %') }}</lable>-->
            </div>
                 <input type="hidden" class="form-control" name="checked" id="checked" >
                 <input type="hidden" class="form-control" name="unchecked" id="unchecked" >

        @foreach ($data  as $item)
            @php
        

   
          $data22 = DB::table('fees_assign_details')->where('fees_master_id',$item->id)->where('admission_id',$admission_id)->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->whereNull('deleted_at')->first();
         //  dd($data22);     
          if(!empty($data22))
          {
          
          
               $fees_master_id = $data22->fees_master_id;
          }
          else
          {
               $fees_master_id = 0;
          }
            
          @endphp
           <div class="col-md-2">
              <input type="input" class="form-control" name="" id="" value="{{$item['feesGroup']->name ?? '' }}" placeholder="{{__('student.Fees Group') }}" readonly>
     
              <input type="hidden" class="form-control" name="fees_master_id[]" id="" value="{{$item->id ?? '' }}" readonly>
              <input type="hidden" name="fees_group_id[]" value="{{$item['feesGroup']->id ?? '' }}">
              <input type="hidden" name="fees_assign_ids[]" value="{{$data22->id ?? '' }}">
           </div>
           <div class="col-md-2">
                @php
               
        $amount = 0;
         if(!empty($data22))
          {
           $amount = $data22->fees_group_amount ?? 0;
          }
       
       else{
         $amount = $item->amount ?? 0;
        }
        
        @endphp
              <input type="tel" class="form-control" name="fees_group_amount[]" id="amount_{{ $item->id ?? '' }}" value="{{ $amount ?? 0 }}" placeholder="{{__('common.Amount') }}" {{$item->editable == 0 ? 'readonly' : ''}}>
              <!--<input type="tel" class="form-control" name="fees_group_amount[]" id="amount_{{ $item->id ?? '' }}" value="{{ $data22->fees_group_amount ?? 0 }}" placeholder="{{__('common.Amount') }}" {{$item->editable == 0 ? 'readonly' : ''}}>-->
           </div>
           <div class="col-md-2">
              <input type="text" class="form-control discount" name="discount[]" id="discount_{{ $item->id ?? '' }}" placeholder="{{__('student.Dis. in Amt.') }}" onkeypress="javascript:return isNumber(event)" value="{{$data22->discount ?? '' }}" >
              <input type="hidden" class="form-control" name="fees_breakdown[]" id="transport_{{ $item->id ?? '' }}" value='{{$data22->fees_breakdown ?? '' }}' >
           </div>

           <div class="col-md-3">
                <div class="">
                    <input type="checkbox" name="fees_assign[]" class="feesAssignCheckbox pointer"  id="assign_{{ $item->id ?? '' }}" data-id="{{ $item->id ?? '' }}" value="{{$item->id ?? ''}}"  {{ $fees_master_id == $item->id ? 'checked' : '' }}>
                    <lable class="pointer" for="assign_{{ $item->id ?? '' }}">{{__('student.Assign') }}</lable>
                    @if($item->editable == 1)
                       <!--<button type='button'class="btn btn-xs bg-info pointer open_model"  data-id="{{$item->id ?? ''}}"data-toggle="modal" data-target="#myModal" >{{__('Add Breakdown') }}</button>-->
                    @endif
                </div> 
           </div>
            <div class="col-md-2">
           </div>
        @endforeach
        
        <div class="col-md-12">
            <div class="row">
    			<div class="col-md-2">
                    <div class="form-group">
    					 <label for="">{{__('student.Total Amount') }}</label>
    					<input type="text" class="form-control" id="total_amount" readonly="" tabindex="1" placeholder="{{__('student.Total Amount') }}" name="total_amount" required="" value="{{ $feesAssign->total_amount ?? '' }}">
    				</div>
    			</div> 
    		
    			<div class="col-md-2">
                    <div class="form-group">
                        <label for="">{{__('student.Total Discount') }}</label>
    					<input type="tel" class="form-control" tabindex="1" id="net_discount" placeholder="{{__('student.Total Discount') }}" name="net_discount" readonly value="{{ $feesAssign->total_discount ?? '' }}">
    				</div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="">{{__('student.Net Amount') }}</label>
    					<input type="text" class="form-control" id="pay_amt" tabindex="1" placeholder="{{__('student.Net Amount') }}" name="pay_amt" required="" readonly value="{{ $feesAssign->net_amount ?? '' }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="">{{__('student.Discount Remark') }}</label>
    					<input type="text" class="form-control" id="discount_remark" tabindex="1" placeholder="Net Amount" name="discount_remark">
                    </div>
                </div>
			</div>
		</div>
   @endif
</div>


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Enter Month and Amount</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="month">Month:</label>
          <select class="form-control" id="month">
            <option value="January">January</option>
            <option value="February">February</option>
            <option value="March">March</option>
            <option value="April">April</option>
            <option value="May">May</option>
            <option value="June">June</option>
            <option value="July">July</option>
            <option value="August">August</option>
            <option value="September">September</option>
            <option value="October">October</option>
            <option value="November">November</option>
            <option value="December">December</option>
          </select>
        </div>
        <div class="form-group">
          <label for="amount">Amount:</label>
          <input type="text" class="form-control" id="amount">
        </div>
        <button type="button" class="btn btn-primary" id="addBtn">Add</button>

        <!-- Table to display entered data -->
        <table class="table mt-3">
          <thead>
            <tr>
              <th>Sr. No</th>
              <th>Month</th>
              <th>Amount</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="dataTableBody">
            <!-- Table rows will be added dynamically here -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id='save_changes'onclick="saveData()">Save changes</button>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function(){
    assignCheck()
  
    $('.feesAssignCheckbox').click(function(){
        assignCheck()
    })
    $('.discount,#great_discount').keyup(function(){
        assignCheck()
    })
    
    function assignCheck(){
        var checked ='' ;
        var unchecked ='' ;
         $('#checked').val('');
           $('#unchecked').val('');
        var thisamt = thisdisct = thisid = netamt = totalamt = totaldisct = payamt = greatdisct = finaldisct = 0;
        $('.feesAssignCheckbox').each(function(){
            
            if($(this).is(':checked')){

                 thisid = $(this).data('id');
                thisamt = parseFloat($('#amount_' + thisid).val());
                thisdisct = parseFloat($('#discount_' + thisid).val());
                greatdisct = parseFloat($('#great_discount').val());
                
                if(isNaN(thisdisct)){thisdisct = 0;}
                if(isNaN(greatdisct)){greatdisct = 0;}
                totalamt = totalamt + thisamt;
                totaldisct = totaldisct + thisdisct;
                
                  checked = checked+','+$(this).data('id');
                $('#checked').val(checked.substring(1));
            }
            
            else
            {
              
                
                //toastr.warning(greatdisct);
                //toastr.info(thisdisct); 
                unchecked = unchecked+','+$(this).data('id');
                $('#unchecked').val(unchecked.substring(1));
                 
            }
        })  
                
        payamt = totalamt - totaldisct;
        $('#total_amount').val(totalamt);
        $('#net_discount').val(totaldisct);
        $('#pay_amt').val(payamt);

        if(greatdisct > 0){

            finaldisct = totaldisct + greatdisct;
            payamt = totalamt - finaldisct;
            $('#net_discount').val(finaldisct);
            $('#pay_amt').val(payamt);
        }
        
        if(totalamt > 0){
            if(totaldisct >= totalamt || finaldisct >= totalamt){
                toastr.error('Total discount should be lessthan Net amount');
            }
        }
    }
    
})
</script>
<script>
  var rowNum = 0; 
      var array =[];
  $(document).ready(function() {


  $('.open_model').click(function(){
      rowNum = 0;
       $('#dataTableBody').html('');
        var id = $(this).attr('data-id');
        $('#save_changes').attr('data-id',id);
        $('#save_changes').attr('data-total_amount_id', id);
      var jsonString = $('#transport_'+id).val();
     
      var dataArray = JSON.parse(jsonString);

      dataArray.forEach(function(item) {
        rowNum++;

      var newRow = "<tr>" +
                    "<td>" + rowNum + "</td>" +
                    "<td>" + item.month + "</td>" +
                    "<td>" + item.amount + "</td>" +
                    "<td><button class='btn btn-xs bg-danger' onclick='deleteRow(this)'>Delete</button></td>" +
                  "</tr>";

      $('#dataTableBody').append(newRow);
      });
   
       
    });

    $('#addBtn').click(function() {
      var month = $('#month').val();
      var amount = $('#amount').val();
        
        rowNum++;
        var newRow = "<tr>" +
                "<td>" + rowNum + "</td>" +
                "<td>" + month + "</td>" +
                "<td>" + amount + "</td>" +
                "<td><button class='btn btn-xs bg-danger' onclick='deleteRow(this)'>Delete</button></td>" +
              "</tr>";

        $('#dataTableBody').append(newRow);
        $('#amount').val('');
    });
  });

  function deleteRow(button) {
    var row = $(button).closest("tr");
    row.remove();
  }




  function saveData() {
      
    var id = $('#save_changes').attr('data-id');
    var total_transport_amount_id = $('#save_changes').attr('data-total_amount_id');
    array = [];
    var count = 0;
        var tableRows = $("#dataTableBody tr");
        var total_transport_amount = 0;
        tableRows.each(function(index, row) {
          var rowData = $(row).find("td");
          var month = $(rowData[1]).text();
          var amount = $(rowData[2]).text();
          total_transport_amount = total_transport_amount + parseFloat(amount);
          array[count] = { 'month': month,'amount' : amount };
        count++;
        
        $('#transport_'+ id).val(JSON.stringify(array))
          //console.log("Row " + (index + 1) + ": Month: " + month + ", Amount: " + amount);
        });
        
        $('#amount_' + total_transport_amount_id).val(total_transport_amount);
        
        toastr.info('Transport Breakdown Added Successfully')
        $('.close').trigger('click');
      }
</script>
