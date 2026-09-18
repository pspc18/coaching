@php
  $classType = Helper::classType();
  $getgenders = Helper::getgender();
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    <div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
                <div class="col-sm-8">
                <h3 class="m-2">Assign Fees to Students</h3>
                </div>
                <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                <li class="pr-2"><a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-home"></i> Home</a></li>
                <li class="pl-2"><a href="{{url('feesMasterAdd')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i> Back</a></li>
                </ol>
                </div>
            </div>
        <hr class="bg-primary" style="margin-top:-12px;">
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <form id="quickForm" action="#" method="post" >
                    @csrf 
                    <div class="row m-2">
                        <div class="col-md-12"><h5>Select Criteria</h5><hr></div>
                        <div class="col-md-3">
                    		<div class="form-group">
                    			<label>Class</label>
                    			<select class="form-control" id="class_type_id" name="class_type_id" >
                    			<option>Select</option>
                                 @if(!empty($classType)) 
                                      @foreach($classType as $type)
                                         <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                                      @endforeach
                                  @endif
                                </select>
                    	    </div>
                    	</div>
                    	<div class="col-md-3">
                    		<div class="form-group">
                    			<label>Section</label>
                    				<select class="form-control section_id" id="" name="section_id" >
                    			   <option value="">Select</option>
                                 @if(!empty($section)) 
                                      @foreach($section as $section)
                                         <option value="{{ $section->id ?? ''  }}" >{{ $section->name ?? ''  }}</option>
                                      @endforeach
                                  @endif
                                </select>
                    	    </div>
                    	</div>

                        <div class="col-md-1 ">
                             <label for="">Search</label>
                    	    <button type="button" class="btn btn-info" onclick="SearchValue()">Search</button>
                    	</div>
                    </div>
                </form>


            <form id="quickForm" action="{{ url('fees_master/assign') }}/{{$dataview['fees_group_id'] ?? '' }}" method="post" id="assign_form">
                @csrf
                <div class="row m-2">
                    <div class="col-md-4 text-center bg-light"><h5>{{ $dataview['feesGroup']['name'] ?? '' }}</h5></div>
                    <div class="col-md-8">&nbsp;</div>
                    <div class="col-md-4"> 
                        <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                            
                                @php
                                $fees_type = Helper::feestypeGet($dataview['class_type_id'],$dataview['fees_group_id'] ?? '' );
                                @endphp

                            <tbody class="">
                                <tr>
                                    <td>
                                        @if(!empty($fees_type))
                                        @foreach ($fees_type  as $type)
                                        <div class="row">
                                            <div class="col-md-6">
                                                <i class="fa fa-money"></i> &nbsp; {{ $type['feesType']['name'] ?? '' }}
                                            </div>
                                            <div class="col-md-6">
                                                &#8377; {{ $type['amount'] ?? '' }}
                                            </div>
                                        </div>
                                        <br>
                                        @endforeach
                                        @endif
                                    </td>
                                </tr>
                               
                            </tbody>
                        </table>                        
                    </div>
                    
                    <div class="col-md-8">
                        <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                            <thead>
                                <tr role="row">
                                  <th><input type="checkbox" id="select_all"  class="checkbox"> &nbsp; All</th>
                                  <th>Adm.No.</th>
                                   <th>Name</th>
                                   <th>Class</th>
                                  <th>F Name</th>
                                  <th>Gender</th>
                                  <th>Mobile</th>
                                </tr>
                            </thead>
                            <tbody class="product_list_show">


                            </tbody>
                        </table>                        
                    </div>
                    <div class="col-md-8">&nbsp;</div>
                    <div class="col-md-4 text-center"><button type="submit" class="btn btn-info">Submit</button></div>
                    
                </div>
            </form>
            </div>
        </div>
    </section>
</div>





<script>
        function SearchValue() {
            var basurl = "{{ url('/') }}";
            var class_type_id = $('#class_type_id :selected').val();
            var section_id = $('.section_id :selected').val();
            if(section_id > 0 || class_type_id > 0 ){
            $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                type:'post',
                url: basurl+'/assign_search_data',
                data: {class_type_id:class_type_id,section_id:section_id},
                 //dataType: 'json',
                success: function (data) {
                   
                  
                    $('.product_list_show').html(data);
                   
                }
              });
            }else{
                    alert('Please put a value in minimum one column !');
                }               
        };

</script>

<script>

//select all checkboxes
    $("#select_all").change(function () {  
        $(".checkbox").prop('checked', $(this).prop("checked")); 
    });

//".checkbox" change 
    $('.checkbox').change(function () {
        if (false == $(this).prop("checked")) { 
            $("#select_all").prop('checked', false);
        }
        if ($('.checkbox:checked').length == $('.checkbox').length) {
            $("#select_all").prop('checked', true);
        }
    });


</script>
@endsection    
