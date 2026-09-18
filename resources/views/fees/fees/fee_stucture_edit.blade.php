@php
    $getstudents = Helper::getstudents();
    $all_class = Helper::getclass();
    $classType = Helper::classType();
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    <div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
                <div class="col-sm-8">
                <h3 class="m-2">Edit Fees Structure</h3>
                </div>
                <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                <li class="pr-2"><a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-home"></i> Home</a></li>
                <li class="pl-2"><a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i> Back</a></li>
                </ol>
                </div>
            </div>
        <hr class="bg-primary" style="margin-top:-12px;">
        </div>
    </div>
    

    
    <div class="card m-2">
        <form id="quickForm" action="{{ url('fees_structure_edit') }}/{{$data['id']}}" method="post" enctype="multipart/form-data">
        @csrf
    
        <div class="card m-2">
            <div class="row m-2">
                    <div class="col-md-4">
                		<div class="form-group">
                			<label style="color:red;">Class*</label>
                			<select class="form-control" id="class_type_id" name="class_type_id" >
                			<option value="">Select</option>
                             @if(!empty($classType)) 
                                  @foreach($classType as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ( $type->id == $data['class_type_id'] ? 'selected' : '' ) }}>{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                	    </div>
                	</div>
            </div>
            <div class="row m-2">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                          <th>Title</th>
                          <th>Fees</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr class="tr_clone">
                            <td>
                                <div class="form-group">
                                    <input name="title"  id="title" placeholder="Title" class="form-control" maxlength="100" type="text" value="{{$data['title'] ?? ''}}">
                        	   </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input name="fees" id="fees" placeholder="Fees" class="form-control cal amount" maxlength="100" type="text" value="{{$data['fees'] ?? ''}}">
                        	   </div>
                            </td>

                        </tr>
                        
                     </tbody>
                </table>
                
    	    </div> 
       </div>   
            <div class="row m-2">
                <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit </button>
                </div>
            </div>
        </form>
    </div>
    
</div>


@endsection      