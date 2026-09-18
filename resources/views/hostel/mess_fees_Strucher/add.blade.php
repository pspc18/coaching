@php
    $getHostel = Helper::getHostel();
    $classType = Helper::classType();
    $getMessFeesStrucher = Helper::getMessFeesStrucher();

@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">

	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">

    <div class="col-md-12" id="get_content">  
   
   <div class="card card-primary">
              <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-inbox"></i> &nbsp; {{__('hostel.Mess Fees Strucher Add') }}</h3>
                <div class="card-tools">
                       <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{__('common.Back') }}</a>
                </div>
                 </div>
			   <div class="card-body">
                <form Action="{{url('messFeesStrucher')}}" method="POST" enctype="multipart/form-data">
			       @csrf
                <br>
               	<div class="row table-responsive">
                 <table id="example1" class="table table-bordered table-striped">
                <thead class="my_background_color">
                <tr>
				  <th> {{__('common.SR.NO') }}</th>
				  <th> {{__('hostel.Class Name') }}</th>
				  <th> {{__('hostel.Veg Food') }}</th>
				  <th> {{__('hostel.Non Veg Food') }}</th>
			   </tr>
                </thead>
                <tbody>
				    @if(!empty($classType))
                    @php
                    $i=1
                    @endphp
                    @foreach ($classType as $item)
                        @php
                            $editdata = DB::table('mess_fees_strucher')->where('class_name',$item->name)->whereNull('deleted_at')->get()->first();
                         
                        @endphp
            <tr>
           <td>{{ $i++ }}</td>
           <td>{{$item['name'] ?? ''}}</td>
		    <td><input type="text" name="vag_fees[]" class="form-control food_class_0" value="{{$editdata->vag_fees ?? ''}}" maxlength="12" onkeypress="javascript:return isNumber(event)"></td>
		    <td>
		        <input type="text" name="nonvag_fees[]" class="form-control food_class_0" value="{{$editdata->nonvag_fees ?? ''}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
		        <input type="hidden" name="class_name[]" class="form-control food_class_0" value="{{$item['name'] ?? ''}}" >
		        
		        </td>
   
        </tr>
        
        
      @endforeach
      @endif
       
       	<tr>
       	    
       	<td colspan="6">
       	    <center>
       	        <input type="submit" name="submit" value="Save" class="btn btn-primary mt-4">
       	        </center>
       	    </td>
       	</tr>
       	</tbody>
                </table>
                </div>
                </form>
		
          </div>
	      </div>
	      </div>
	      
	      


@endsection   