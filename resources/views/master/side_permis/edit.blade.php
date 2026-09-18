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

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp; Edit Sidebar Permission</h3>
                    <div class="card-tools">
                    <!--<a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add </a>-->
                    <a href="{{url('side_permis_view')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-arrow-left"></i> Back </a>
                    </div>
                    
                    </div>                 
                <form id="quickForm" action="{{ url('side_permis_edit') }}/{{($data->id)}}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row m-2">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="color:red;">Sidebar Permission</label>
                                @php
                                    $sidebarId =explode(",",$data['sidebar_id']);
                                @endphp                                
                                    @if(!empty($sidebar))
                                    @foreach($sidebar as $data)
                                    <div class="custom-control custom-checkbox">
                                    <input name="sidebar_id[]" class="custom-control-input custom-control-input-primary custom-control-input-outline checkbox" type="checkbox" id="{{ $data->id ?? ''  }}" value="{{ $data->id ?? ''  }}" 
                                    {{ in_array($data->id, $sidebarId)  ? 'checked' : '' }}>
                                    <label for="{{ $data->id ?? ''  }}" class="custom-control-label">{{ $data->name ?? ''  }}</label>
                                    </div>
                                    @endforeach
                                    @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div class="form-group clearfix">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="select_all" name="" class="checkbox" value="{{ $data->id ?? ''  }}" {{ in_array($data->id, $sidebarId)  ? 'checked' : '' }}>
                                    <label for="select_all">Select All</label>
                                </div>
                            </div>                            
                        </div>                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="color:red;">Role*</label>
                                <select class="form-control" name="role_id">
                                 @if(!empty($role)) 
                                      @foreach($role as $value)
                                     
                                         <option value="{{ $value->id ?? ''  }}" {{ ( $value->id == $data['role_id'] ??  old('role_id')) ? 'selected' : '' }}>{{ $value->name ?? ''  }}</option>
                                      @endforeach
                                @endif
                                </select>
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
    $("#select_all").change(function () {  
        $(".checkbox").prop('checked', $(this).prop("checked")); 
    });
</script>
 @endsection    