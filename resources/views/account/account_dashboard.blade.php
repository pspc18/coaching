@extends('layout.app') 
@section('content')
@php
$acount_count = Helper::getCount('accounts','id','count');
$incom_count = Helper::getCount('incomes','id','count');
@endphp    
    <div class="content-wrapper">
    
     <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class=" fa fa-user-circle-o"></i> &nbsp; Account Management</h3>
                    <div class="card-tools">
                        <!--<a href="{{url('add_user')}}" class="btn btn-primary  btn-sm" title="Add User"><i class="fa fa-plus"></i> Add User</a>-->
                    </div>
            
                </div>               
            </div>
            </div>
            
                <div class="col-md-3">
                     <a href="{{url('bank/account/add')}}" class="small-box-footer">
                    <div class="small-box bg-danger">
                        <div class="inner">
        
                
                    <h4>Add Bank A/c</h4>

                    <p>Enter</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user-circle-o"></i>
                </div>
                 <div class="text-center small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>
            
            <div class="col-md-3">
                <a href="{{url('bank/account/index')}}" class="small-box-footer">
                <div class="small-box bg-warning">
                <div class="inner">
                    <h4>Account List </h4>

                    <h4>{{$acount_count ?? '0'}}</h4>
                </div>
                <div class="icon">
                    <i class="fa fa-bank"></i>
                </div>
                <div class="text-center small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>
            
            
           

       
        </div>
    </section
    
</div>
</div>
@endsection
  
