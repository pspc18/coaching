@php
$role = Helper::roleType();
$getMonth = Helper::getMonth();
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
							<h3 class="card-title"><i class="fa fa-shirtsinbulk"></i> &nbsp;{{ __('Staff Salary Details') }}</h3>
							<div class="card-tools"> 
							    <a href="{{url('generate/salary/slip')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(6)->add ? '' : 'd-none' }}"><i class="fa fa-plus"></i> {{ __('messages.Add') }} </a>
							    <a href="{{url('user_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('messages.Back') }} </a>
							</div>
						</div>					    

                    <form id="quickForm" action="{{ url('salary_details') }}" method="post" >
                        @csrf 
                        <div class="row m-2">
                        <div class="col-md-2">
                			<label>{{ __('messages.Select Role') }}</label>
                			<select class="select2 form-control" id="role_id" name="role_id">
                			<option value="">{{ __('messages.Select') }}</option>
                             @if(!empty($role)) 
                                  @foreach($role as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ( $type['id'] == $search['role_id']) ? 'selected' : ''   }} {{ ( $type['id'] == 3 ?? '' ) ? 'hidden' : '' }}{{ ( $type['id'] == 1 ?? '' ) ? 'hidden' : '' }}>{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select> 
                    	</div>
                    	
                    
        
                		<div class="col-md-4">
                			<div class="form-group">
                				<label>{{ __('messages.Search By Keywords') }}</label>
                				<input type="text" class="form-control" id="name" name="name" placeholder="Ex. Staff Name, Mobile, Aadhaar etc." value="{{ $search['name'] ?? '' }}">
                		    </div>
                		</div> 
                        <div class="col-md-1 ">
                            <div class="Display_none_mobile">
                                <label for="" class="text-white">{{ __('messages.Select') }}</label> 
                            </div>
                    	    <button type="submit" class="btn btn-primary">{{ __('messages.Search') }}</button>
                    	</div>
            			
            </div>
        </form>
			   <form id="form-submit" action="{{ url('assignSalaryDetail') }}" method="post" >
                        @csrf 		
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
		    <!--<input type='radio'  id='partial' name='text_type' checked/> <label for='partial'>Partial Text</label> &nbsp;&nbsp;&nbsp;-->
		    <!--<input type='radio' id='full' name='text_type'/> <label for='full'>Full Text</label>-->
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline text-nowrap">
          <thead class="bg-primary">
          <tr role="row">
     
 
              <th>{{ __('messages.Sr.No.') }}</th>
                    <th>{{ __('messages.Name') }} </th>
                    <th>{{ __('messages.Mobile') }} </th>
                    <th>{{ __('Salary') }} </th>
                    <th>{{ __('Basic') }} <input type='hidden' id='basic_main' value='100' /></th>
                    <th>{{ __('DA') }} <input type='hidden' id='da_main' value='0' /></th>
                    <th>{{ __('TDS') }} <input type='hidden' id='tds_main' value='0'/>%</th>
                    <th>{{ __('PF') }} <input type='hidden' id='pf_main' value='0' />%</th>
                    <!--<th>{{ __('messages.Action') }} </th>-->
          </thead>
          <tbody>
              @if(!empty($data))
                @php
                   $i=1;
                   $total_salary = 0;
                @endphp
                @foreach ($data  as $item)
                
                @php
                $salary = DB::table('staff_salarys')->where('staff_id',$item->id)->whereNull('deleted_at')->first();
                $total_salary += $salary->total_amount ?? 0;
                @endphp
                <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                        <td>{{ $item['mobile'] ?? '' }}</td>
                        <td>
                            <input type='hidden' name='staff_id[]' value="{{$item['id']}}" />
                            <input type='hidden' name='first_name[]' value="{{$item['first_name'] ?? ''}}" />
                            <input type='hidden' name='last_name[]' value="{{$item['last_name'] ?? ''}}" />
                            <input type='number' name='salary[]' value='{{$salary->total_amount ?? 0}}' id="salary-{{$item['id']}}" min='0'  placeholder='Type Salary'/>
                            </td>
                            
                            @php
                            $da = (($salary->da ?? 0)*($salary->total_amount ?? 0)/100);
                            $pf =($salary->pf ?? 0);
                            $tds = ($salary->tds ?? 0);
                            $basic = (($salary->basic_amt ?? 0)*($salary->total_amount ?? 0)/100);
                            
                            @endphp
                        <td><input type='number' name='basic[]' value='{{$basic}}' id="basic-{{$item['id']}}"  min='0' onfocus="adjustBasic({{$item['id']}})" /></td>
                        <td><input type='number' name='da[]' value='{{$da}}' id="da-{{$item['id']}}"  min='0'  onfocus="adjustBasic({{$item['id']}})"/></td>
                        <td><input type='number' name='tds[]' value='{{$tds}}' min='0' max='40' /></td>
                        <td><input type='number' name='pf[]' value='{{$pf}}' min='0'  max='12'/></td>
				<!--		<td> -->
				<!--<a  target="blank" class="btn btn-xs btn-danger btn-xs ml-3" title="delete"><i class="fa fa-trash"></i></a>  -->
    <!--                    </td>-->
              </tr>
              @endforeach
            @endif
            </tbody>
            
            <tfoot>
                <tr class='bg-secondary '>
                    <td  colspan='2'></td>
                    <td  class='p-2 pl-0 text-center'>Total Salary</td>
                    <td  class='p-2 pl-0'>Rs.{{$total_salary}}</td>
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td ></td>
                </tr>
            </tfoot>
        </table>
        </div>
          <div class="col-md-12 text-center">	
          <button class='btn btn-primary btn-submit'>Save</button>
          </div>
        </div>
        
        </form>
    </div>

	</div>
		</div>
	</div>
	</section>
</div>



<!--<script>
    document.addEventListener('DOMContentLoaded', function() {
        let oldMainValues = {
            basic_main: document.getElementById('basic_main').value,
            da_main: document.getElementById('da_main').value,
            tds_main: document.getElementById('tds_main').value,
            pf_main: document.getElementById('pf_main').value
        };

        function validateTotalPercentages() {
            const basicPercentage = parseFloat(document.getElementById('basic_main').value) || 0;
            const daPercentage = parseFloat(document.getElementById('da_main').value) || 0;
            const tdsPercentage = parseFloat(document.getElementById('tds_main').value) || 0;
            const pfPercentage = parseFloat(document.getElementById('pf_main').value) || 0;

            const totalPercentage = basicPercentage + daPercentage + tdsPercentage + pfPercentage;

            if (totalPercentage > 100) {
                alert('The total percentage (Basic + DA + TDS + PF) cannot exceed 100%. Reverting the last change.');
                return false;
            }

            return true;
        }

        function revertMainInput(id) {
            document.getElementById(id).value = oldMainValues[id];
        }

        function storeMainValues() {
            oldMainValues.basic_main = document.getElementById('basic_main').value;
            oldMainValues.da_main = document.getElementById('da_main').value;
            oldMainValues.tds_main = document.getElementById('tds_main').value;
            oldMainValues.pf_main = document.getElementById('pf_main').value;
        }

        function calculateSalaryForAllRows() {
            if (!validateTotalPercentages()) return;

            const salaryInputs = document.querySelectorAll("input[name='salary[]']");
            const basicPercentage = parseFloat(document.getElementById('basic_main').value) || 0;
            const daPercentage = parseFloat(document.getElementById('da_main').value) || 0;
            const tdsPercentage = parseFloat(document.getElementById('tds_main').value) || 0;
            const pfPercentage = parseFloat(document.getElementById('pf_main').value) || 0;

            salaryInputs.forEach((salaryInput, index) => {
                const salaryValue = parseFloat(salaryInput.value) || 0;
                const basicInput = document.querySelectorAll("input[name='basic[]']")[index];
                const daInput = document.querySelectorAll("input[name='da[]']")[index];
                const tdsInput = document.querySelectorAll("input[name='tds[]']")[index];
                const pfInput = document.querySelectorAll("input[name='pf[]']")[index];

                const basicValue = (salaryValue * basicPercentage) / 100;
                const daValue = (salaryValue * daPercentage) / 100;
                const tdsValue = (salaryValue * tdsPercentage) / 100;
                const pfValue = (salaryValue * pfPercentage) / 100;

                basicInput.value = basicValue.toFixed(2);
                daInput.value = daValue.toFixed(2);
                tdsInput.value = tdsValue.toFixed(2);
                pfInput.value = pfValue.toFixed(2);
            });
        }

        const mainPercentageInputs = document.querySelectorAll('#basic_main, #da_main, #tds_main, #pf_main');
        mainPercentageInputs.forEach(input => {
            input.addEventListener('input', function() {
                if (!validateTotalPercentages()) {
                    revertMainInput(this.id); 
                } else {
                    storeMainValues(); 
                    calculateSalaryForAllRows();
                }
            });
        });
 calculateSalaryForAllRows();
        const salaryInputs = document.querySelectorAll("input[name='salary[]']");
        salaryInputs.forEach(salaryInput => {
            salaryInput.addEventListener('input', function() {
                const basicPercentage = parseFloat(document.getElementById('basic_main').value) || 0;
                const daPercentage = parseFloat(document.getElementById('da_main').value) || 0;
                const tdsPercentage = parseFloat(document.getElementById('tds_main').value) || 0;
                const pfPercentage = parseFloat(document.getElementById('pf_main').value) || 0;

                const salaryValue = parseFloat(this.value) || 0;
                const index = [...salaryInputs].indexOf(this);

                const basicInput = document.querySelectorAll("input[name='basic[]']")[index];
                const daInput = document.querySelectorAll("input[name='da[]']")[index];
                const tdsInput = document.querySelectorAll("input[name='tds[]']")[index];
                const pfInput = document.querySelectorAll("input[name='pf[]']")[index];

                const basicValue = (salaryValue * basicPercentage) / 100;
                const daValue = (salaryValue * daPercentage) / 100;
                const tdsValue = (salaryValue * tdsPercentage) / 100;
                const pfValue = (salaryValue * pfPercentage) / 100;

                basicInput.value = basicValue.toFixed(2);
                daInput.value = daValue.toFixed(2);
                tdsInput.value = tdsValue.toFixed(2);
                pfInput.value = pfValue.toFixed(2);
            });
        });
    });
</script>-->
<script>
    function adjustDA(rowId) {
        let salary = parseFloat(document.getElementById('salary-' + rowId).value) || 0;
        let basic = parseFloat(document.getElementById('basic-' + rowId).value) || 0;
        
       
        let da = salary - basic;
        da = da < 0 ? 0 : da; 

        document.getElementById('da-' + rowId).value = da;
    }

    function adjustBasic(rowId) {
        let salary = parseFloat(document.getElementById('salary-' + rowId).value) || 0;
        let da = parseFloat(document.getElementById('da-' + rowId).value) || 0;

       
        if (da > salary) {
            da = salary;
            document.getElementById('da-' + rowId).value = da;
        }

       
        let basic = salary - da;
        basic = basic < 0 ? 0 : basic; 
       
        document.getElementById('basic-' + rowId).value = basic;
    }
</script>
<style>
input:read-only {
  background-color: #f2f2f2;
  
}
input {
 width:100px;
  
}
</style>
@endsection

