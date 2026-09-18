@php
  $classType = Helper::classType();
  $getCountry = Helper::getCountry();
  $getPermission = Helper::getPermission();

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
        <h3 class="card-title"><i class="fa fa-money"></i> &nbsp;{{ __('Hostel Student Report') }}</h3>
        <div class="card-tools">
                <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
        </div>
        
        </div>  
     
         <form id="quickForm" action="{{ url('hostel/student/report') }}" method="post" >
                        @csrf 
                    <div class="row m-2">
                        <div class="col-md-12 p-0">
                        <div class="col-md-4">
    						<div class="form-group">
    							<label>Student's</label>
    							<select class="form-control select2" name="hostel_assign_id" id="hostel_assign_id" onchange="this.form.submit()">
    								<option value="">{{ __('common.Select') }}</option>
    								@if(!empty($all_student))
    								@foreach($all_student as $item)
    								<option value="{{ $item->id ?? ''  }}" {{ ($item->id == $search['hostel_assign_id']) ? 'selected' : '' }}>{{ $item->admissionNo." --". $item->first_name ?? ''  }}</option>
    								@endforeach
    								@endif
    							</select>
    						</div>
    					</div>
					</div>
                    </div>
                </form>        

    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
          <thead>
          <tr role="row">
              <th>{{ __('Invoice No.') }}</th>
                     <th>Admission No</th>
                    <th>Student Name</th>
                    <th>Mobile</th>
                    <th>Paid Amount</th>
                    <th>Total Amount</th>
                    <th>Total Discount</th>
                    @if($getPermission->download == 1)
                     <th>{{ __('common.Action') }}</th>
                    @endif
          </thead>
          <tbody id="fees_list_show">
              
              @if(!empty($data))
                @php
                   $i=1
                @endphp

                @foreach ($data  as $item)
                
             @php
             
             @endphp
                <tr>
                        <td>{{ $item['invoice_no'] ?? '' }}</td>
                         <td>{{$item['admissionNo'] ?? ''}}</td>
                        <td>{{ $item['first_name'] ?? ''  }} {{ $item['last_name'] ?? ''  }}</td>
                        <td>{{ $item['mobile'] ?? '-'  }}</td>
                        <td>{{ $item['paid_amount'] ?? '0'  }}</td>
                        <td>{{ $item['total_amount'] ?? '0'  }}</td>
                        <td>{{ $item['discount'] != 0 ? $item['discount'] : "-"}}</td>
                       
                        @if($getPermission->download == 1)
                        <td>
                            <a href="{{ url('hostel_invoice') }}/{{ $item['invoice_no'] ?? '' }}/{{ $item['admission_id'] ?? '' }}" target="blank" class="btn btn-xs btn-primary"><i class="fa fa-print"></i></a>
                        </td>
                        @endif
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

@endsection 