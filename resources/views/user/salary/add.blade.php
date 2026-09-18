@php
$incentive_amount = 0;
$roleType = Helper::roleType();

$getMonth = Helper::getMonth();

$staffAtten = Helper::staffAtten($data->id ,$monthId ?? '' );


$RoleId = Session::get('RoleId');
$MonthId = Session::get('MonthId');
$userData = Session::get('userData');
$totel_sal_day = $staffAtten['P']+$staffAtten['d']+$staffAtten['W']+$staffAtten['H'];
$totel_sre = $data['salary']/$staffAtten['TotalDay'];
$totel_amt   = $totel_sre/2;
$totel_half_day = $totel_amt*$staffAtten['HF'];

$totel_amount = $totel_sal_day*$totel_sre;
//dd($totel_sal_day.'--'.$staffAtten['P'].'--'.$staffAtten['d'].'--'.$staffAtten['W'].'--'.$staffAtten['H']);
$expense = DB::table('expenses')->where('user_id',$data->id ?? '')->whereNull('deleted_at')->whereYear('date',date('Y'))->whereMonth('date',$monthId ?? '')->sum('amount');
$expense_name = DB::table('expenses')->where('user_id',$data->id ?? '')->whereNull('deleted_at')->whereYear('date',date('Y'))->whereMonth('date',$monthId ?? '')->pluck('name')->implode(','. PHP_EOL);


@endphp
@extends('layout.app') 
@section('content')

<input type="hidden" id="user_data" value="{{ $userData ?? '' }}">
<div class="content-wrapper">
      <div class="container-fluid">
        <div class="row">    

            <div class="col-md-12">
                <div class="card card-outline card-orange mr-1">
                 <div class="card-header bg-primary">
                    <h3 class="card-title headings_all "><i class="fa fa-money"></i> &nbsp;{{ __('staff.Salary Panel') }}</h3>
                <div class="card-tools">
                <a href="{{url('salary_details')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(6)->view ? '' : 'd-none' }}" ><i class="fa fa-eye"></i> {{ __('messages.View') }} </a>
                <a href="{{url('user_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('messages.Back') }} </a>
               </div>
                </div>                 
    
                    <div class="row m-2">
                        <div class="col-md-2">
                            <form action="{{ url('generate/salary/slip') }}" method="POST"> 
                            @csrf 
                			<label style="color:red;">{{ __('staff.Select Role') }}*</label>
                			<select class="form-control  select2" id="role_id" name="role_id">
                			<option value="">{{ __('messages.Select') }}</option>
                             
                            @if(!empty($roleType))
                                @php
                                    $array = [1,3];
                                @endphp
                                @foreach($roleType as $role)
                                @if (in_array($role->id, $array)) {
                                @else
                                <option value="{{ $role->id ?? ''  }}" {{ ( $role->id == $data['role_id']) ? 'selected' : ''   }}>{{ $role->name ?? ''  }}</option>
                                @endif
                                @endforeach
                            @endif
                            </select> 
                    	</div>
                    	
                        <div class="col-md-2">
                             
                			<label  style="color:red;">{{ __('staff.Salary Month') }}*</label>
                			<select class="form-control  select2" id="monthId" name="monthId">
                			<option value="">{{ __('messages.Select') }}</option>
                             @if(!empty($getMonth)) 
                                  @foreach($getMonth as $item)
                                     <option value="{{ $item->id ?? ''  }}" {{ ( $item->id == $monthId) ? 'selected' : ''   }} {{ ( $item->id == $MonthId) ? 'selected' : ''   }}>{{ $item->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                    	</div>                     	
                    	
                        <div class="col-md-4">
                			<label style="color:red;">{{ __('staff.Select Staff') }}* </label>
                			 
                			<select class="form-control  select2" id="user_id" name="user_id" onchange="this.form.submit()">
                			<option value="">{{ __('messages.Select') }}</option>
                		
                			@if(!empty($dataUsers)) 
                		
                                  @foreach($dataUsers as $type1)
                      
                                     <option value="{{ $type1->id ?? ''  }}" {{ ( $type1['id'] == $data['id']) ? 'selected' : ''   }}>{{ $type1['first_name'] ?? ''  }} {{$type1['last_name']}}</option>
                                  @endforeach
                              @endif
                			
                            </select> 
                            </form>
                    	</div>
                    </div>
                </div>          
            </div>


    
        @if($first_load == 'second_load')      
        
        @php
        
        $oldSalary = DB::table('staff_salary_details')->where('staff_id',$data->id)->where('month_id',$monthId)->whereNull('deleted_at')->first();
        
        
    
        @endphp
        
        @if(!empty($oldSalary))
        
        <div class='col-md-12 text-center'>
            
<p class='text-danger blink_me'> The salary for this month has already been created. If you make changes and save, it will override the previously created salary for this month.</p>
            
        </div>
        
        @endif
      
        <form id="form-submit" action="{{ url('generate/salary') }}" method="post">
                        @csrf 
            <div class="col-md-12" id="form_salary_generate" >
                <div class="row">
                    <div class="col-md-6 pr-0">
                                 
                            <input type="hidden" name="roleId" value="{{ $data->role_id ?? '' }}">
                            <input type="hidden" name="staff_id" value="{{ $data->id ?? '' }}">
                            <input type="hidden" name="month_id" value="{{ $monthId ?? '' }}">
                            <input type="hidden" id="pfPer" value="{{ $data->pfPer ?? '' }}">
                            <input type="hidden" id="tdsPer" value="{{ $data->tdsPer ?? '' }}">
                            <input type="hidden" id="daPer" value="{{ $data->daPer ?? ''  }}">
                            <input type="hidden" id="basicPer" value="{{ $data->basicPer ?? ''  }}">
                            <!--<input type="hidden" name="per_day_amt" value="{{ round($data['salary']/$staffAtten['TotalDay']) ?? '' }}">-->
                            <!--<input type="hidden" name="salary_day" value="{{ $totel_sal_day ?? '' }}">-->
                            <!--<input type="hidden" name="half_day" value="{{$staffAtten['HF'] ?? '' }}">-->
                            <!--<input type="hidden" name="holiday" value="{{ $staffAtten['H'] ?? '' }}">-->
                            <!--<input type="hidden" name="double_shift" value="{{$staffAtten['d'] ?? '' }}">--> 
                            <!--<input type="hidden" name="present" value="{{ $staffAtten['P'] ?? '' }}">-->
                            <!--<input type="hidden" name="absent" value="{{ $staffAtten['A'] ?? '' }}">-->
                            <!--<input type="hidden" name="t_days" value="{{ $staffAtten['TotalDay'] ?? '' }}">-->
                            <input type="hidden" name="total_salary" id="total_salary" value="{{ $data['salary'] ?? '' }}">
                        <div class="card card-outline card-orange mr-1">
                     <div class="card-header bg-primary">
                    <h3 class="card-title headings_all"><i class="fa fa-money"></i> &nbsp;{{ __('staff.Salary Panel') }}</h3>
                    <div class="card-tools">
                  </div>
                    
                    </div>                 
        
                            <div class="row m-2">
                        <div class="col-md-6">
                			<label style="color:red;"> Name*</label>
                			<input class="form-control @error('first_name') is-invalid @enderror" type="text" name="first_name" id="first_name" placeholder="Staff First Name" value="{{$data['first_name'] ?? ''}}">
                                           			
                    	</div>
                        <div class="col-md-6 d-none">
                			<label style="color:red;">Staff Last Name*</label>
                			<input class="form-control @error('first_name') is-invalid @enderror" type="text" name="last_name" id="last_name" placeholder="Staff Last Name" value="{{$data['last_name'] ?? ''}}">
                                             			
                    	</div>
                                        	
              	
                        <div class="col-md-6">
                			<label  style="color:red;">Basic Salary* </label>
                			<input class="form-control @error('basic_amt') is-invalid @enderror" onkeyup="calculateAmount(this.value,'basic_amt');" type="text" name="basic_amt" id="basic_amt" placeholder="Basic Salary" value="{{ $data['salary'] ?? '' }}" readonly>
                                          			
                    	</div>                	
                       <!-- <div class="col-md-6">
                			<label>HRA </label>
                			<input class="form-control" onkeyup="calculateAmount(this.value,'hra');" type="text" name="hra" id="hra" placeholder="HRA" value="">
                      			
                    	</div>  -->              	
                   <!--     <div class="col-md-6">-->
                			<!--<label>DA Amount  </label>-->
                			<!--<input class="form-control" onkeyup="calculateAmount(this.value,'da');" type="text" name="da" id="da" placeholder="DA Amount" value="0" readonly>-->
                   <!-- 	</div>                	-->
                        <div class="col-md-6">
                			<label>Incentive</label>
                			<input class="form-control" onkeyup="calculateAmount(this.value,'incentive');" type="text" name="incentive" id="incentive" placeholder="Incentive" value="{{$expense ?? 0}}"  >
                    	</div>  
                    	 <div class="col-md-6">
                			<label>Incentive Remark</label>
                			<textarea class="form-control" name="incentive_remark" id="incentive_remark" rows='2' placeholder="Incentive Remark"  >{{$expense_name ?? ''}}</textarea>
                    	</div>  
                       <!-- <div class="col-md-6">
                			<label >Allowances</label>
                			<input class="form-control" onkeyup="calculateAmount(this.value,'allowance');" type="text" name="allowance" id="allowance" placeholder="Allowances" value="">
                    	</div>  -->              	
                       <!-- <div class="col-md-6">
                			<label >Advance </label>
                			<input class="form-control" onkeyup="calculateAmount(this.value,'advance');" type="text" name="advance" id="advance" placeholder="Advance" value="">
                    	</div>  -->              	
                   <!--     <div class="col-md-6">-->
                			<!--<label>PF Amount </label>-->
                			<!--<input class="form-control" onkeyup="calculateAmount(this.value,'pf');" type="text" name="pf" id="pf" placeholder="PF Amount" value="0" readonly>-->
                   <!-- 	</div>                	-->
                   <!--     <div class="col-md-6">-->
                			<!--<label>TDS Amount</label>-->
                			<!--<input class="form-control" onkeyup="calculateAmount(this.value,'tds');" type="text" name="tds" id="tds" placeholder="TDS Amount" value="0" readonly>-->
                   <!-- 	</div>                	-->
                      <!--  <div class="col-md-6">
                			<label >ESIC Amount </label>
                			<input class="form-control" onkeyup="calculateAmount(this.value,'esic');" type="text" name="esic" id="esic" placeholder="ESIC Amount" value="">
                   			
                    	</div>  -->              	
                       <!-- <div class="col-md-6">
                			<label>Tax Amount </label>
                			<input class="form-control" onkeyup="calculateAmount(this.value,'tax');" type="text" name="tax" id="tax" placeholder="Tax Amount" value="">
                    	</div>-->                	
                        <div class="col-md-6">
                			<label >Other Deduction </label>
                			<input class="form-control" onkeyup="calculateAmount(this.value,'other_deduction');" type="text" name="other_deduction" id="other_deduction" placeholder="Other Deduction" value="0">
                    	</div>                	
                        <div class="col-md-6">
                			<label>Other Deduction Remark </label>
                			<textarea class="form-control"  name="deduction_remark" rows='2' id="deduction_remark" placeholder="Deduction Remark"></textarea>
                    	</div>                	
                        <div class="col-md-6">
                			<label  style="color:red;">Salary Generate Date*</label>
                			<input class="form-control @error('date') is-invalid @enderror" type="date" name="date" id="date" value="{{ date('Y-m-d') }}">
                                              			
                    	</div>                	
                        <div class="col-md-6">
                			<label style="color:red;">Final Salary*</label>
                			<input class="form-control" type="text" name="total_amount" id="total_amount" placeholder="Final Salary" value="{{round($totel_amount)+$incentive_amount ?? ''}}" >
                    	</div> 
                    	
                </div>
           	  <div class="row m-2">
                    <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary btn-submit">Generate </button>
                    </div>
                </div>
                       
                    </div>          
                </div>
                    <div class="col-md-6">
                    <div class="card card-outline card-orange mr-1">
                     <div class="card-header bg-primary">
                    <h3 class="card-title headings_all"><i class="fa fa-money"></i> &nbsp;{{ __('staff.Attendance Panel') }}</h3>
                    <div class="card-tools">
                   </div>
                    
                    </div> 
                    
                      <div class="row m-2 text-center">
                        <div class="col-md-3">
                			<label> <b>Total Days</b></label><br>
                			<label > 
                			
                			<!--<b style=" color: blue;margin-left: 35px;">{{ $staffAtten['TotalDay'] ?? '0' }}</b>-->
                		
                		    <input type='text' class=' input form-control'value="{{ $staffAtten['TotalDay'] ?? '0' }}" id='totalDays' name='totalDays' readonly />
                			</label>
                		
                    	</div>
                        <div class="col-md-3">
                			<label><b>Holiday</b></label><br>
                			<!--<label><b style="color: red;margin-left: 35px;">{{ $staffAtten['H'] ?? '0' }}</b></label>-->
        			         <input type='hidden'  name='holiday'class='w-50 input' value="0" id='holiday' readonly />             			
        			         <input type='text'  class='form-control input' value="{{$staffAtten['H'] ?? 0 }}"  readonly />             			
                    	</div>                	
                        <div class="col-md-3">
                			<label ><b>Absent</b></label><br>
                			<!--<label ><b style="color: red;margin-left: 35px;">{{ $staffAtten['A'] ?? '0' }}</b></label>-->
                   			  <input type='text' class='form-control input' value="{{ $staffAtten['A'] ?? '0' }}" id='absent' name='absent'/>             		
                    	</div>   
                            
                        <div class="col-md-3">
                			<label ><b> Half-Day</b></label><br>
                			<!--<label ><b style="color: blue;margin-left: 35px;">{{ $staffAtten['HF'] ?? '0' }}</b></label>-->
                  			  <input type='text' class='form-control input' value="{{ $staffAtten['HF'] ?? '0' }}" id='halfDay' name='halfDay' />             		
                    	</div> 
                    	<div class="col-md-3">
                			<label> <b>Double Shift</b></label><br>
                			<!--<label > <b style="color: orange;margin-left: 35px;">{{ $staffAtten['d']/2 ?? '0' }}</b></label>-->
                			
                		 <input type='text' class=' form-control input' value="{{ $staffAtten['d'] ?? '0' }}" id='doubleShift' name='doubleShift' />  
                		                 			
                    	</div>
                    	<div class="col-md-3">
                			<label><b> Working</b></label><br>
                			<!--<label><b style="color: green;margin-left: 35px;">{{ $staffAtten['P']+$staffAtten['d']+$staffAtten['W'] ?? '0' }}</b></label>-->
                			  <input type='text' class=' form-control input' value="0" id='working' readonly/>                  			
                    	</div> 
           
                      <div class="col-md-3">
            <label><b>Late Come</b></label><br>
            <input type='text' class=' form-control input' value="0" id='lateCome' name='lateCome' />
        </div>
          <div class="col-md-3">
            <label><b>Late Come Frequency</b></label><br>
            <input type='text' class=' form-control input' value="3" id='lateComeFrequency' name='lateComeFrequency' />
        </div>
                        <div class="col-md-3">
                			<label ><b style="margin-left: 10px;">Salary Days</b></label><br>
                			<!--<label ><b style=" color: green;margin-left: 35px;">{{$totel_salary_day ?? ''}}</b></label>-->
                			<input type='text'class=' form-control input' value="0" id='salaryDays' name='salaryDays' readonly/>  
                    	</div>                	
                </div>
        
                    </div>  
                    
                   <!--     <div class="card card-outline card-orange mr-1">-->
                   <!--  <div class="card-header bg-primary">-->
                   <!-- <h3 class="card-title headings_all"><i class="fa fa-money"></i> &nbsp;{{ __('messages.LEAVE INFORMATION') }} </h3>-->
                   <!-- <div class="card-tools">-->
                   <!--</div>-->
                    
                   <!-- </div>                 -->
        
                   <!--     <div class="row m-2">-->
                   <!--             <div class="col-md-3">-->
                   <!--     			<label> <b> {{ __('messages.Casual Leave') }}</b></label><br>-->
                   <!--     			<label > <b style=" color: blue;margin-left: 35px;">{{ $data['casual_leave'] ?? '0' }}</b></label>-->
                        			
                        		
                        		                 			
                   <!--         	</div>-->
                   <!--             <div class="col-md-3">-->
                   <!--     			<label><b style="margin-left: 10px;">{{ __('messages.Pay/Earn Leave') }}</b></label><br>-->
                   <!--     			<label><b style="color: red;margin-left: 35px;">0</b></label>-->
                        			                  			
                   <!--         	</div>                	-->
                   <!--             <div class="col-md-3 text-center">-->
                   <!--     			<label> <b style="margin-left: 10px;">{{ __('staff.Medical Leave') }} </b></label><br>-->
                   <!--     			<label > <b style="color: orange;">{{ $data['medical_leave'] ?? '0' }}</b></label>-->
                        			
                        		
                        		                 			
                   <!--         	</div>-->
                   <!--             <div class="col-md-3">-->
                   <!--     			<label><b style="margin-left: 10px;">{{ __('messages.Other Leave') }}</b></label><br>-->
                   <!--     			<label><b style="color: green;margin-left: 35px;">{{ $data['other_leave'] ?? '0' }}</b></label>-->
                        			                  			
                   <!--         	</div>   -->
                   <!--     </div>-->
        
                   <!-- </div>-->
                   
                </div>
                    <!-- <div class="col-md-6 pl-0">
                    <div class="card card-outline card-orange ml-1">
                     <div class="card-header bg-primary">
                    <h3 class="card-title headings_all"><i class="fa fa-money"></i> &nbsp;{{ __('messages.Salary Detail') }}</h3>
                    <div class="card-tools">
                    </div>
                    
                    </div>                 
                       <div class="row m-2">
                            <div class="col-md-12">
                            <table  class="table table-bordered table-striped dataTable dtr-inline  text-nowrap">
                                  <thead>
                                  <tr role="row">
                                      <th>{{ __('messages.Sr.No.') }}</th>
                                      <th>{{ __('messages.Description') }}</th>
                                      <th>{{ __('messages.Amount') }}</th>
                                    </tr>  
                                      
                                      
                                  </thead>
                                  <tbody id="">
                                  
                                
                                        <tr>
                                               <td style="padding-left:3%;">A</td>
                                               <td>{{ __('staff.Basic Salary') }}</td>
                                                <td> {{ $data['salary'] ?? '0' }}</td>
                
                                            </tr>
                                        <tr>
                                               <td style="padding-left:3%;">B</td>
                                               <td>{{ __('staff.Pre Day Salary (A,B)') }} </td>
                                                <td> {{ round($data['salary']/$staffAtten['TotalDay']) ?? '0' }}</td>
                
                                            </tr>
        
                                        <tr>
                                               <td style="padding-left:3%;">D</td>
                                               <td>{{ __('staff.Total Days') }} </td>
                                                <td>{{ $staffAtten['TotalDay'] ?? '0' }}</td>
                
                                            </tr>                                    
                                        <tr>
                                               <td style="padding-left:3%;">C</td>
                                               <td>{{ __('staff.Working Days') }} </td>
                                                <td>{{ $totel_sal_day ?? '0' }}</td>
                
                                            </tr>
        
                                        <tr>
                                               <td style="padding-left:3%;">E</td>
                                               <td>{{ __('staff.Absent') }} </td>
                                                <td>{{ $staffAtten['A'] ?? '0' }}</td>
                
                                            </tr>
                                        <tr>
                                               <td style="padding-left:3%;">F</td>
                                               <td>{{ __('staff.Present') }} </td>
                                                <td>{{ $staffAtten['P']+$staffAtten['d'] ?? '0' }}</td>
                
                                            </tr>
                                    <tr>
                                               <td style="padding-left:3%;">G</td>
                                               <td>Leave </td>
                                                <td>{{ $staffAtten['A'] ?? '0' }}</td>
                
                                            </tr>-->
                                        <!--<tr>
                                               <td style="padding-left:3%;">H</td>
                                               <td> Sunday</td>
                                                <td>{{ $staffAtten['H'] ?? '0' }}</td>
                
                                            </tr>
                                            
                                        <tr>
                                               <td style="padding-left:3%;">I</td>
                                               <td>{{ __('staff.Holiday') }} </td>
                                                <td>{{ $staffAtten['H'] ?? '0' }}</td>
                
                                            </tr>
                                       
                                        <tr>
                                               <td style="padding-left:3%;">S</td>
                                               <td>{{ __('staff.HRA') }} </td>
                                                <td id="hra1"> 0</td>
                
                                            </tr>   
                                        <tr>
                                               <td style="padding-left:3%;">T</td>
                                               <td>{{ __('staff.DA Amount') }} </td>
                                                <td id="da1"> {{ $data['da_amt'] ?? '0' }}</td>
                
                                            </tr>  
                                        <tr>
                                               <td style="padding-left:3%;">P</td>
                                               <td>{{ __('staff.Incentive') }} </td>
                                                <td id="incentive1"> 0</td>
                
                                            </tr>    
        
                                        <tr>
                                               <td style="padding-left:3%;">Q</td>
                                               <td> {{ __('staff.Allowances') }}</td>
                                                <td id="allowance1"> {{ $data['allowance'] ?? '0' }}</td>
                
                                            </tr>   
                                        <tr>
                                               <td style="padding-left:3%;">R</td>
                                               <td>{{ __('staff.Advance') }} </td>
                                                <td id="advance1"> 0</td>
                
                                            </tr> 
                                        <tr>
                                               <td style="padding-left:3%;">M</td>
                                               <td>{{ __('staff.PF Amount') }} </td>
                                                <td id="pf1"> {{ $data['pf'] ?? '0' }}</td>
                
                                            </tr>  
                                        <tr>
                                               <td style="padding-left:3%;">L</td>
                                               <td>{{ __('staff.TDS') }} </td>
                                                <td id="tds1"> {{ $data['tds'] ?? '0' }}</td>
                
                                            </tr>         
                                        <tr>
                                               <td style="padding-left:3%;">N</td>
                                               <td>{{ __('staff.ESIC Amount') }} </td>
                                                <td id="esic1"> {{ $data['esic'] ?? '0' }}</td>
                
                                            </tr>                                    
                                        <tr>
                                               <td style="padding-left:3%;">K</td>
                                               <td>{{ __('staff.Tax Amount') }} </td>
                                                <td id="tax1"> {{ $data['tax'] ?? '0' }}</td>
                
                                            </tr>
                                        <tr>
                                               <td style="padding-left:3%;">O</td>
                                               <td>{{ __('staff.Other Deducation') }} </td>
                                                <td id="other_deduction1"> 0</td>
                
                                            </tr>
        
                                            </tr>
        
                                            </tr>
        
                                            </tr>
        
                                            </tr>
                                        <tr>
                                               <td style="padding-left:3%;">U</td>
                                               <td>{{ __('staff.Total Deducation(K+L+M+N+O+R)') }} </td>
                                                <td id="total_deduction"> 0</td>
                
                                            </tr>
                                            </tr>
                                        <tr>
                                        
                                               <td style="padding-left:3%;">V</td>
                                               <td>{{ __('staff.Final salary (C*l+P+Q+S+T+R-U)') }} </td>
                                                <td id="final_salary"> {{  round($totel_amount+$totel_half_day) ?? '0' }}</td>
                                        </tr>
                                 
                                  </tbody>
                            </table>
                                  
                            <div class="row m-2">
                            <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary">{{ __('staff.Generate') }} </button>
                            </div>
                        </div>
                        	</div>
                        </div>
                       
                    </div>          
                </div> -->
                </div>
            </div>
        </form>
    @endif
    </div>
</div>
</section>
</div>
<style>
@media only screen and (max-width: 600px) {
  .headings_all{
        font-size: 22px;
    }
}

   
</style>
<script>
    
 

$(document).ready(function(){
    var user_data = $('#user_data').val();
    if(user_data != ''){
         $("#user_id").html(user_data);
    }
    
    
    $("#role_id").change(function(){

     var basurl = "{{ url('/') }}";
    var role_id = $(this).val();
    
        $.ajax({
             headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
        type:'post',
        url: basurl+'/find/staff',
        data: {role_id:role_id},
	    success: function(data){
	     if(data != ''){
	         	$("#user_id").html(data);
	         	$("#form_salary_generate").css('display','block');
	     }else{
	         	$("#user_id").html(data);
	            toastr.error('User Not Found !');
	     }
	    }
        }); 
 
}); 
});
</script>       

   <script>
        $(document).ready(function(){
            calculateSalaryDays();
            function calculateSalaryDays() {
                var daPer = parseFloat($('#daPer').val()) || 0;
                var tdsPer = parseFloat($('#tdsPer').val()) || 0;
                var pfPer = parseFloat($('#pfPer').val()) || 0;
                var basicPer = parseFloat($('#basicPer').val()) || 0;
            
                
                var totalDays = parseFloat($('#totalDays').val()) || 0;
                var holiday = parseFloat($('#holiday').val()) || 0;
                var absent = parseFloat($('#absent').val()) || 0;
                var halfDay = parseFloat($('#halfDay').val()) || 0;
                var doubleShift = parseFloat($('#doubleShift').val()) || 0;
                var working = parseFloat($('#working').val()) || 0;
                var lateCome = parseFloat($('#lateCome').val()) || 0;
                var lateComeFrequency = parseFloat($('#lateComeFrequency').val()) || 3;

                var lateComePenalty = Math.floor(lateCome / lateComeFrequency) * 0.5;
                
                var salaryDays = totalDays - holiday - absent - (0.5 * halfDay) + (1 * doubleShift) - lateComePenalty;

                $('#salaryDays').val(salaryDays);
                $('#working').val(salaryDays);
                
                var basic = $('#total_salary').val();
                var incentive = Number($('#incentive').val());
               // var da =  Number($('#da').val());
               // var pf =  Number($('#pf').val());
               // var tds =  Number($('#tds').val());
                var other_deduction =  Number($('#other_deduction').val());
                
                
                var perday = basic/totalDays;
                var final = parseInt(perday*salaryDays)


                var A = parseFloat((final*basicPer)/100);
                var B = parseFloat((final*daPer)/100);
                var C = parseFloat((basic*pfPer)/100);
                var D = parseFloat((final*tdsPer)/100);
                
                $('#basic_amt').val(A);
                $('#da').val(B);
                $('#pf').val(C);
                $('#tds').val(D);
              
             
               // $('#total_amount').val((final+incentive+da) - (other_deduction + pf+ tds));
                $('#total_amount').val(parseInt(((A+B)-(C+D+other_deduction))+incentive));
               
            }

            $('input').on('focusout', function(){
                calculateSalaryDays();
            });
        });
    </script>
<script>
$( document ).ready(function() {
  var firstLoad = "{{$first_load ?? '' }}";
  var secondLoad = "{{$second_load ?? ''}}";
 
 if(firstLoad == 'first_load')
 {
     	$("#role_id").val("");
 }
 else if(firstLoad == 'redirect_load')
 {
     toastr.error('Salary Already Generated !');
     	$("#role_id").val("").change();
    	$("#monthId").val("");
 }
     var role = $("#role_id").val();
 
if(role != '')
{
 
  var basurl = "{{ url('/') }}";
   
    
        $.ajax({
             headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
        type:'post',
        url: basurl+'/find/staff',
        data: {role_id:role},
	    success: function(data){
	        
	     if(data != ''){
	         	$("#user_id").html(data);
	         	$("#user_id").val("{{$data['id'] ?? ''}}");
	         	$("#form_salary_generate").css('display','block');
	     }else{
	         	$("#user_id").html(data);
	            toastr.error('User Not Found !');
	     }
	    }
        }); 
    
}
});
</script>

<style>
    .blink_me {
  animation: blinker 0.3s linear infinite;
}

@keyframes blinker {
  50% {
    opacity: 0.3;
  }
}
</style>
@endsection      