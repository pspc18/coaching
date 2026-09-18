@php
$classType = Helper::classType();
$getSession = Helper::getSession();
@endphp
@extends('layout.app')
@section('content')

<style>
    .fixed_item{
        position:sticky !important;
        right:-8px;
        background-color:white;
        z-index:111;
        box-shadow: -6px 2px 6px #cecece;
    }
    
    .dropdown-menu.show {
        left: -79px !important;
    }
    
    .flex_centered{
        display:flex;
        align-items:center;
        /*justify-content: space-between;*/
        height: 55px;
    }
    
    .flex_centered a{
        margin-left:10px;
    }
    
    .nowrap{
        white-space:nowrap;
        font-size:14px;
    }
    
    .colored_table thead tr{
        background-color:#002c54;
        color:white;
    }
    .colored_table thead tr th{
        padding:10px;
    }
    
    .overflow_scroll{
        height:250px;
        overflow:scroll;
    }
</style>

<div class="content-wrapper">
  <section class="content pt-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12 col-md-12">
          <div class="card card-outline card-orange">
            <div class="card-header bg-primary flex_items_toggel">
              <h3 class="card-title"><i class="fa fa-address-book-o"></i> &nbsp;{{ __('Fees Dues List') }}</h3>
              <div class="card-tools">
                <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i><span class="Display_none_mobile"> {{ __('common.Back') }} </span></a>
              </div>

            </div>
            
            
             <form id="quickForm" action="{{ url('feesRemainderCron') }}" method="post">
              @csrf
              <div class="row m-2">
                    
                    <div class="col-md-2 d-none">
                        <div class="form-group">
                            <label>Session</label>
                            <select class="form-control select2" id="session_id" name="session_id">
                                <option value="">Select</option>
                                @if(!empty($getSession))
                                    @foreach($getSession as $session)
                                        <option value="{{ $session->id ?? '' }}" {{ $session->id == $search['session_id'] ? 'selected' : '' }}>{{ $session->from_year ?? ''  }} - {{ $session->to_year ?? ''  }}</option>
                                    @endforeach
                                @endif
                            </select>                 	    
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="State" class="required">{{ __('student.Admission No.') }}</label>
                            <input type="text" class="form-control" id="admissionNo" name="admissionNo" placeholder="{{ __('student.Admission No.') }}" value="{{ $search['admissionNo'] ?? '' }}">
                        </div>
                        </div>
                        <div class="col-md-2">
                    		<div class="form-group">
                    			<label>Class</label>
                                    <select class="form-control" id="class_type_id" name="class_type_id">
                                        <option value="">Select Class</option>
                                        @if(!empty($classType))
                                            @foreach($classType as $class)
                                                <option value="{{ $class->id ?? '' }}" {{ $class->id == $search['class_type_id'] ? 'selected' : '' }}>{{ $class->name ?? '' }}</option>
                                            @endforeach
                                        @endif
                                    </select>                 	    
                            </div>
                    	</div>
                    	
                    	<!-- <div class="col-md-2">
                    		<div class="form-group">
                    			<label>{{ __('fees.From Date') }}</label>
                                    <input type="date" class="form-control " id="starting" name="starting" value="{{ $_POST['starting'] ?? '' }}">                 	    
                            </div>
                    	</div>
                    	<div class="col-md-2">
                            <div class="form-group ">
                                <label>{{ __('fees.To Date') }}</label>
                                    <input type="date" class="form-control " id="ending" name="ending" value="{{ $_POST['ending'] ?? '' }}">
                			</div> 
                        </div> -->
                        <div class="col-md-2">
					<div class="form-group">
						<label>Status</label>
						<select class="form-control " id="status" name="status">
						    	<option value="">{{ __('common.Select') }}</option>
						    	@if($search['status'] == null)
								<option value="1"  selected>{{ __('Continue') }}</option>
							<option class='text-danger' value="0">{{ __('Discontinue') }}</option>
								<!-- <option value="3">{{ __('Registration Request') }}</option> -->
							
							@else
								<option value="1"  {{$search['status'] == 1 ? 'selected' : '' }}>{{ __('Continue') }}</option>
							<option class='text-danger' value="0" {{$search['status'] == 0 ? 'selected' : '' }}>{{ __('Discontinue') }}</option>
								<!-- <option value="3"  {{$search['status'] == 3 ? 'selected' : '' }}>{{ __('Registration Request') }}</option> -->
							@endif
						
							
				       </select>
					</div>
				</div>
            		<div class="col-md-3">
            			<div class="form-group"> 
            				<label>{{ __('messages.Search By Keywords') }}</label>
            				<input type="text" class="form-control" id="name" name="name" placeholder="{{ __('messages.Ex. Name, Father Name, Mobile, Email, etc.') }}" value="{{$search['name'] ?? ''}}">
            		    </div>
            		</div>                     	
                        <div class="col-md-1 ">
                             <label for="" style="color: white;">Search</label>
                    	    <button type="submit" class="btn btn-primary" >{{ __('messages.Search') }}</button>
                    	</div>             
                </div>
                </form>
            
<form id='remainderForm'action='{{url("whatsappSendFeesRemainder")}}' method='POST'>

@csrf
            <div class="row m-2">
              <div class="col-12" style="overflow-x:scroll;">
                <table id="studentList" class="table table-bordered  table-striped dataTable dtr-inline nowrap">
                 <thead>
                     <th>#</th>
                     <th>{{ __('student.Admission No.') }}</th>
                    <th>{{ __('Student Name') }}</th>
                    <th>{{ __('Mobile') }}</th>
                    <th>{{ __('messages.Fathers Name') }}</th>
                    <th>{{ __('Gender') }}</th>
                    <th>{{ __('Category ') }}</th>
                    <!--<th>Student Type</th>-->
                    <!--<th>Course</th>-->
                    <th>Class</th>
                    <!--<th>Session </th>-->
                    <!--<th>Batch  </th>-->
                    <th>{{ __('Status') }}</th>
                    @php
             $feesGroups = [];
             if(($search['class_type_id'] ?? '') != '')
                        {
                                $feesGroups = DB::table('fees_master')
                                ->leftJoin('fees_group', 'fees_master.fees_group_id', '=', 'fees_group.id') 
                                ->where('fees_master.session_id', Session::get('session_id'))
                                ->whereNull('fees_master.deleted_at')
                                ->groupBy('fees_master.fees_group_id')
                                ->where('fees_master.class_type_id', $search['class_type_id'] ?? '')
                                ->select('fees_master.*', 'fees_group.name as group_name') 
                                ->get();
                        }
                                            @endphp
                                           @if(!empty($feesGroups))
                                           @foreach($feesGroups as $item)
                                              <th>{{$item->group_name ?? ''}}</th>
                                           @endforeach
                                           @endif
                 </thead>
                 <tbody>
                     
                     @if(!empty($data))
                      @php
                      $feesGroupsAmt = [];     
                      @endphp
                     @foreach($data as $key=> $stu)
                     <input type='hidden' name="message[{{$stu['id'] ?? ''}}]" value="{{$stu['message']}}"/>
                     <input type='hidden' name="mobile[{{$stu['id'] ?? ''}}]" value="{{$stu['mobile']}}"/>
                      <tr>
                          <td><input type='checkbox'class='checkbox_id' name='checkbox[]' value="{{$stu['id'] ?? ''}}" />
                         @php
                         $genderName = DB::table('gender')->whereNull('deleted_at')->where('id',$stu['gender_id'])->first();
                  $session = DB::table('sessions')->whereNull('deleted_at')->where('id', $stu['session_id'] ?? '')->first();

                         @endphp
                          {{$key+1}} </td>
                    <td>{{ $stu['admissionNo'] ?? ''  }}</td>
                    <td>{{ $stu['name'] ?? ''  }} </td>
                    <td>{{ $stu['mobile'] ?? ''  }} </td>
                    <td>{{ $stu['father_name'] ?? ''  }}</td>
                    <td>{{ $genderName->name ?? '' }}</td>
                    <td>{{ $stu['category']  }}</td>
                    <!--<td>{{ $stu['student_type'] ?? '' }}</td>-->
                    <!--<td>{{ $stu['course'] ?? '' }}</td>-->
                    <td>{{ $stu['className'] ?? '' }}</td>
                    <!--<td>{{$session->from_year ?? ''}}-{{$session->to_year ?? ''}}</td>-->
                    <!--<td>{{ $stu['batch'] ?? '' }}</td>-->
                    <td>
                    @if($stu['status'] == 1)
                        Continue
                    @else
                       <spam class='text-danger' >Discontinue </spam>
                    @endif
                    </td>
                       
                          
                         @php
                                     if(($search['class_type_id'] ?? '') != '')                                    
                                        {
                                            $feesGroups = DB::table('fees_master')
                                                ->leftJoin('fees_group', 'fees_master.fees_group_id', '=', 'fees_group.id') 
                                                ->where('fees_master.session_id', Session::get('session_id'))
                                                ->where('fees_master.branch_id', Session::get('branch_id'))
                                                ->whereNull('fees_master.deleted_at')
                                                ->groupBy('fees_master.fees_group_id')
                                                ->select('fees_master.*', 'fees_group.name as group_name') 
                                                ->where('fees_master.class_type_id', $search['class_type_id'] ?? '')
                                                ->get();
                                            }
                                            
                                            @endphp
                                           
                                           @if(!empty($feesGroups))
                                           @php
                                           $total=0;
                                           
                                           @endphp
                                           @foreach($feesGroups as $item1)
                                          @php 
                                          $fees_assign = App\Models\fees\FeesAssignDetail::
                                          where('fees_group_id', $item1->fees_group_id)->where('fees_master_id', $item1->id)->where('admission_id',$stu['admission_id'])->sum('fees_group_amount');
                                         
                                          $head = DB::table('fees_detail')->where('session_id', Session::get('session_id'))
                                          ->where('admission_id',$stu['admission_id'])->where('fees_group_id',$item1->fees_group_id)->whereNull('deleted_at')->whereIn('status', [0,1])->sum('total_amount');
                                          
                                        
                                          
                                          if(!empty($head)){
                                          
                                          $total = $fees_assign-$head;
                                          }else{
                                          $total = $fees_assign;
                                          }

                                           if (!isset($feesGroupsAmt[$item1->id])) {
                                                                $feesGroupsAmt[$item1->id] = 0;
                                                            }
                                                            $feesGroupsAmt[$item1->id] += $total;
                                                            @endphp
                                         <td>{{$total ?? 0}} </td>
                                           
                                           
                                           @endforeach
                                           
                                           @endif
                   
                            
                     
                        
                     </tr>
                     @endforeach
                     @endif
                 </tbody>
                 <tfoot>
            <tr>
                 
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <!--<td></td>-->
                <td></td>
                <td></td>
                <!--<td></td>
                <td></td>-->
                @php
                //dd($feesGroupsAmt);
                                     $feesGroups = [];
                                     if(($search['class_type_id'] ?? '') != '')                                    
                                        {
                                            $feesGroups = DB::table('fees_master')
                                                ->leftJoin('fees_group', 'fees_master.fees_group_id', '=', 'fees_group.id') 
                                                ->where('fees_master.session_id', Session::get('session_id'))
                                                ->whereNull('fees_master.deleted_at')
                                                ->groupBy('fees_master.fees_group_id')
                                                ->select('fees_master.*', 'fees_group.name as group_name') 
                                                ->where('fees_master.class_type_id', $search['class_type_id'] ?? '')
                                                ->get();
                                            }
                                            
                                            @endphp
                                           
                                           @if(!empty($feesGroups))
                                           @php
                                           $total=0;
                                           @endphp
                                           @foreach($feesGroups as $item1)
                                          
                                           
                                         <td>{{$feesGroupsAmt[$item1->id]  ?? ''}}</td>
                                           
                                           
                                           @endforeach
                                           
                                           @endif
               
              
            </tr>
            </tfoot>
                </table>
              </div>
            </div>
  </form>
            </div>
            </div>
            </div>
            </div>
            </section>
</div>


<!-- Confirmation Modal -->
<div class="modal fade" id="confirmSendMessageModal" tabindex="-1" role="dialog" aria-labelledby="confirmSendMessageModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        
      <div class="modal-header">
        <h5 class="modal-title" id="confirmSendMessageModalLabel">Send Fee Reminder</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to send a fee reminder to the selected students?
      </div>
      <div class="modal-footer">
         
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmSendMessage">Send</button>
      </div>
      
    
    </div>
  </div>
</div>
<script>
       $( document ).ready(function() {
       $('#studentList').DataTable({
                  "lengthChange": false, "autoWidth": false,"lengthChange": true, 
                "lengthMenu": [10, 20, 50,100,200,300,400,500,1000] ,
                 "buttons": ["copy", "csv", "excel", "pdf", "print"]
                }).buttons().container().appendTo('#studentList_wrapper .col-md-6:eq(0)');
                
                var customElement = $('<div>', {
            id: 'custom-element',
            class: 'custom-element-class'
        });
        customElement.load('{{ url("messangerButtons") }} #custom-buttons', function() {
            $('#studentList_wrapper .col-md-6:eq(1)').append(customElement);
        });
           $('#studentList_wrapper').on('click', '#btn-checkall', function() {
            var status = parseInt($(this).attr('data-status'));
            if(status == 0){
                $(this).attr('data-status',1); 
                $(this).removeAttr('class');
                $(this).attr('class','btn btn-secondary btn-sm');
                $('#check_box_icon').removeAttr('class');
                $('#check_box_icon').attr('class','fa fa-check-square');
                $('.checkbox_id').prop('checked',true);
            }else{
                $(this).attr('data-status',0);
                $(this).removeAttr('class');
                $(this).attr('class','btn btn-outline-secondary btn-sm');
                $('#check_box_icon').removeAttr('class');
                $('#check_box_icon').attr('class','fa fa-square-o');
                $('.checkbox_id').prop('checked',false);
            }
        });     
              $('#studentList_wrapper').on('click', '#btn-whatsapp', function() {
    var selectedStudents = $('.checkbox_id:checked');

    if (selectedStudents.length === 0) {
        alert('Please select at least one student.');
        return;
    }
    $('#confirmSendMessageModal').modal('show');
});

$('#confirmSendMessage').on('click', function() {
   $('#remainderForm').trigger('submit');
});


       });
</script>

            @endsection