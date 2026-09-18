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
                    <h3 class="card-title"><i class="fa fa-indent"></i> &nbsp; Add Sidebar Permission</h3>
                    <div class="card-tools">
                    <a href="{{url('side_permis_view')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-eye"></i> View </a>
                    </div>
                    
                    </div>        
                <form id="quickForm" action="{{ url('side_permis_add') }}" method="post">
                    @csrf

                    <div class="row m-2">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="color:red;">Sidebar Permission*</label>
                                   @if(!empty($sidebar))
                                    @foreach($sidebar as $data)
                                    <div class="custom-control custom-checkbox">
                                    <input name="sidebar_id[]" class="custom-control-input custom-control-input-primary custom-control-input-outline checkbox @error('sidebar_id') is-invalid @enderror" type="checkbox" id="{{ $data->id ?? ''  }}" value="{{ $data->id ?? ''  }}">
                                    <label for="{{ $data->id ?? ''  }}" class="custom-control-label">{{ $data->name ?? ''  }}</label>
                                    </div>
                                    @endforeach
                                    @endif

                                @error('sidebar_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror                       
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <div class="form-group clearfix">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" id="select_all" name="" value="" class="checkbox">
                                    <label for="select_all">Select All</label>
                                </div>
                            </div>                            
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label style="color:red;">Role*</label>
                                <select class="form-control @error('role_id') is-invalid @enderror" name="role_id">
                                    <option value="">Select</option>
                                    @if(!empty($role))
                                    @foreach($role as $data)
                                    <option value="{{ $data->id ?? ''  }}">{{ $data->name ?? ''  }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @error('role_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror                                 
                            </div>
                        </div>                
                    </div>
                    <div class="row m-2 pb-2">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary ">Submit</button>
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