@php
$getCountry = Helper::getCountry();
$getState = Helper::getState();
$getCity = Helper::getCity();
$role = Helper::getRole();
$sidebar = Helper::getSiderbar();
@endphp

@extends('layout.app') 
@section('content')
<div class="content-wrapper">
    <!--<div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
                <div class="col-sm-8">
                <h3 class="m-2">Edit User</h3>
                </div>
                <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                <li class="pr-2"><a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-home"></i> Home</a></li>
                <li class="pl-2"><a href="{{url('view_user')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i> Back</a></li>
                </ol>
                </div>
            </div>
        <hr class="bg-primary" style="margin-top:-12px;">
        </div>
    </div>-->

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp; Edit Holiday</h3>
                    <div class="card-tools">
                    <a href="{{url('holiday')}}" class="btn btn-primary  btn-sm" title="Add Holiday "><i class="fa fa-plus"></i> Add </a>
                    <a href="{{url('holiday')}}" class="btn btn-primary  btn-sm" title="View Holiday"><i class="fa fa-eye"></i> View </a>
                    </div>
                    
                    </div>                 
                <form id="quickForm" action="{{ url('holiday') }}/{{($data->id)}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row m-2">
        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="color:red;"> Name*</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ $data->name ??  old('name') }}" placeholder="  Name">
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        </div>
                   

                    <div class="row m-2 pb-2">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary ">Update</button>
                        </div>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>
</section>
</div>  

<script>
//select all checkboxes
    $("#{{ $data->id ?? ''  }}").change(function () {  
        $(".checkbox").prop('checked', $(this).prop("checked")); 
    });

//".checkbox" change 
    $('.checkbox').change(function () {
        if (false == $(this).prop("checked")) { 
            $("#{{ $data->id ?? ''  }}").prop('checked', false);
        }
        if ($('.checkbox:checked').length == $('.checkbox').length) {
            $("#{{ $data->id ?? ''  }}").prop('checked', true);
        }
    });
</script>
 @endsection    