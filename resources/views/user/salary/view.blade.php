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
                    	
                        <div class="col-md-2">
                			<label>{{ __('messages.Select Month') }}</label>
                			<select class="select2 form-control" id="month_id" name="month_id">
                			<option value="">{{ __('messages.Select') }}</option>
                             @if(!empty($getMonth)) 
                                  @foreach($getMonth as $item)
                                     <option value="{{ $item->id ?? ''  }}" {{ ( $item['id'] == $search['month_id']) ? 'selected' : ''   }}>{{ $item->name ?? ''  }}</option>
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
                             <label for="" class="text-white">{{ __('messages.Select') }}</label>
                    	    <button type="submit" class="btn btn-primary">{{ __('messages.Search') }}</button>
                    	</div>
            			
            </div>
        </form>
					
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
		    <input type='radio'  id='partial' name='text_type' checked/> <label for='partial'>Partial Text</label> &nbsp;&nbsp;&nbsp;
		    <input type='radio' id='full' name='text_type'/> <label for='full'>Full Text</label>
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline text-nowrap">
          <thead class="bg-primary">
          <tr role="row">
     
 
              <th>{{ __('messages.Sr.No.') }}</th>
                    <th>{{ __('messages.Name') }} </th>
                    <th>{{ __('messages.Mobile') }} </th>
                    <!--<th>{{ __('messages.E-Mail') }} </th>-->
                    <th>{{ __('Salary') }} </th>
                    <th>{{ __('Basic') }} </th>
                    <th>{{ __('DA') }} </th>
                    <th>{{ __('Incentive') }} </th>
                    <th>{{ __('TDS') }} </th>
                    <th>{{ __('PF') }} </th>
                    <th>{{ __('Gross') }} </th>
                    <th>{{ __('Attendance') }} </th>
                    <th>{{ __('messages.Month') }} </th>
                    <th>{{ __('messages.Date') }} </th>
                    <th>{{ __('messages.Action') }} </th>
                    
          </thead>
          <tbody>
              
              @if(!empty($data))
                @php
                   $i=1
                @endphp
                @foreach ($data  as $item)

                <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $item['name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                        <td>{{ $item['User']['mobile'] ?? '' }}</td>
                        <!--<td>{{ $item['User']['email'] ?? '' }}</td>-->
                        <td>{{ number_format($item['salary'] ,2) ?? '' }}</td>
                        <td>{{ number_format($item['basic_amt'] ,2) ?? '' }}</td>
                        <td>{{ number_format($item['da'] ,2) ?? '' }}</td>
                        <td>{{ number_format($item['incentive'] ,2) ?? '' }}</td>
                        <td>{{ number_format($item['tds'] ,2) ?? '' }}</td>
                        <td>{{ number_format($item['pf'] ,2) ?? '' }}</td>
                        <td>{{ number_format($item['gross_salary'] ,2) ?? '' }}</td>
                        <td><p class='show_para hide_para'>
                            <strong>Total Days: </strong>{{ $item['total_days'] ?? 0 }}<b class='dot_dot'>...</b><br>
                            <strong>Present: </strong>{{ $item['present'] ?? 0 }}<br>
                            <strong>Absent: </strong>{{ $item['absent'] ?? 0 }}<br>
                            <strong>Holiday: </strong>{{ $item['holiday'] ?? 0 }}<br>
                            <strong>HalfDay: </strong>{{ $item['half_day'] ?? 0 }}<br>
                            <strong>DoubleShift: </strong>{{ $item['double_shift'] ?? 0 }}
                        
                        
                        
                        </p></td>
                        <td>{{ $item['Month']['name'] ?? '' }}</td>
                        <td>{{date('d-m-Y', strtotime($item['date'])) ?? '' }}</td>
                       
                        
						<td> 
						    <a href="{{ url('download/salary/slip') }}/{{ $item->staff_id }}/{{ $item->month_id }}" target="blank" class="btn btn-xs btn-primary btn-xs tooltip1 " title1="Download"><i class="fa fa-download"></i></a>  
						    <a href="{{ url('salary_print') }}/{{ $item->staff_id }}/{{ $item->month_id }}" target="blank" class="btn btn-xs btn-success btn-xs ml-3 tooltip1 {{ Helper::permissioncheck(6)->print ? '' : 'd-none' }}" title1="Print"><i class="fa fa-print"></i></a>  
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
	</div>
	</section>
</div>


<script>
    $(document).ready(function(){
    $("input[name='text_type']").on('change', function() {
        if ($("#partial").is(":checked")) {
           $('.show_para').addClass('hide_para');
            $('.dot_dot').show();
        } else if ($("#full").is(":checked")) {
           $('.show_para').removeClass('hide_para');
           $('.dot_dot').hide();
        }
    });
});
</script>
<style>
.hide_para{
    max-height: 28px;overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
 
}
</style>
@endsection

