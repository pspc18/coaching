@php
  $classType = Helper::classType();
  $getSection = Helper::getSection();
  $getCountry = Helper::getCountry();

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
        <h3 class="card-title"><i class="fa fa-bar-chart-o"></i> &nbsp;{{ __('hostel.Student Fees Ledger') }}</h3>
        <div class="card-tools">
        <!--<a href="{{url('hostel/collect/fees')}}" class="btn btn-primary  btn-sm" title="Add Fees"><i class="fa fa-plus"></i> Add</a>-->
        <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('common.Back')}} </a>
        </div>
        
        </div>  
        
                    <form id="quickForm" action="{{url('hostel/fees/ledger/view')}}" method="post" >
                        @csrf 
                    <div class="row m-2">

                        <div class="col-md-2">
                    		<div class="form-group">
                    			<label>{{ __('hostel.From Date')}}</label>
                                    <input type="date" class="form-control " id="starting" name="starting" value="{{$serach['starting'] ?? ''}}">                 	    
                            </div>
                    	</div>
                    	<div class="col-md-2">
                            <div class="form-group ">
                                <label>{{ __('hostel.To Date')}}</label>
                                    <input type="date" class="form-control " id="ending" name="ending" value="{{$serach['ending'] ?? ''}}">
                			</div> 
                        </div>

            		<div class="col-md-4">
            			<div class="form-group"> 
            				<label>{{ __('common.Search By Keywords')}}</label>
            				<input type="text" class="form-control" id="name" name="name" placeholder="{{ __('common.Ex. Name, Mobile, Email, Aadhaar etc.')}}" value="{{$serach['name'] ?? ''}}">
            		    </div>
            		</div>                     	
                        <div class="col-md-1 ">
                             <label for="" style="color: white;">. &nbsp;</label>
                    	    <button type="submit" class="btn btn-primary" >{{ __('common.Search')}}</button>
                    	</div>
                    			
                    </div>
                </form>        

        
    	<div class="row m-2">
		    <div class="col-md-12">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline text-nowrap">
          <thead>
          <tr role="row">
              <th>{{ __('common.SR.NO')}}</th>
                    <th>{{ __('hostel.Student Name')}}</th>
                    <th>{{ __('common.Fathers Name')}}</th>
                    <th>{{ __('common.Mobile')}}</th>
                    <!--<th>{{ __('messages.E-Mail')}}</th>-->
                    <th>{{ __('hostel.Fee Per Month')}}</th>
                    <th>{{ __('hostel.Total Paid Fees')}}</th>
                    <th>{{ __('hostel.Total Discount')}}</th>
                     <th>{{ __('common.Action')}}</th>
          </thead>
          <tbody>
              
              @if(!empty($data))
                @php
                   $i=1;
                @endphp

                @foreach ($data  as $item)
               
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $item['first_name'] ?? ''  }}{{ $item['last_name'] ?? ''  }}</td>
                    <td>{{ $item['father_name'] ?? ''  }}</td>
                    <td>{{ $item['mobile']  }}</td>
                    <!--<td>{{ $item['email']  }}</td>-->
                    <td>{{ number_format($item['hostel_fees'] ,2) ?? '' }}</td>
                    <td>{{ number_format($item['collect_amount'] ,2) ?? '' }}</td>
                    <td>{{ number_format($item['discount'] ,2) ?? '' }}</td>
                   
                    <td>
                        <a href="{{url('ledger_fees_print',$item->id)}}" class="btn btn-primary  btn-xs" title="View Fees Ledger"><i class="fa fa-bar-chart-o"></i></a>
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
        

@endsection 