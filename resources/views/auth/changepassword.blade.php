@extends('layout.app') 
@section('content')
 
<div class="content-wrapper">
    <!--<div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
                <div class="col-sm-8">
                <h3 class="m-2">Change Password</h3>
                </div>
                <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                <li class="pr-2"><a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-home"></i> Home</a></li>
                </ol>
                </div>
            </div>
        <hr class="bg-primary" style="margin-top:-12px;">
        </div>
    </div>-->

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-4"></div>
          <div class="col-12 col-md-4">
            <div class="card card-outline card-orange">

                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-key"></i> &nbsp; Change Password</h3>
                    <div class="card-tools">
                        <!--<a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add User</a>-->
                    </div>
                </div> 
                <div class="card-body">
                  <form id="quickForm" action="{{ url('change_password') }}" method="post">
                         @csrf
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Old Password:</label>
                                    <input type="password" class="form-control @error('old_password') is-invalid @enderror " name="old_password" id="old_password" value="" autocomplete="off" placeholder="Old Password">
                                    @error('old_password')
            		                <span class="invalid-feedback" role="alert">
            			            <strong>{{ $message }}</strong>
            		                </span>
            			            @enderror
            			     </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>New Password:</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror " name="password" id="password" value="{{ old('password') }}" placeholder="New Password">
                                    @error('password')
            		                <span class="invalid-feedback" role="alert">
            			            <strong>{{ $message }}</strong>
            		                </span>
            			            @enderror
            			     </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Confirm Password:</label>
                                    <input type="password" class="form-control @error('confirm_password') is-invalid @enderror " name="confirm_password" id="confirm_password" value="{{ old('confirm_password') }}" placeholder="Confirm Password">
                                    @error('confirm_password')
            		                <span class="invalid-feedback" role="alert">
            			            <strong>{{ $message }}</strong>
            		                </span>
            			            @enderror
            			     </div>
                        </div>
                    <div class="row text-center">
                      <div class="col-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                      </div>
                    </div>
                  </form>
                 </div>
    </div>
</div>
        <div class="col-12 col-md-4"></div>
    </div>
</div>
</section>
</div>
@endsection