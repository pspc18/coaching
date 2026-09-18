@extends('layout.app') 
@section('content')

<div class="content-wrapper" >

    <section class="content pt-3">
        <div class="container-fluid">
          
        <div class="row">
          <div class="col-12 col-md-12">
            <div class="card card-outline card-orange">
                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-whatsapp"></i> &nbsp;{{__('master.Message Template Dashboard') }}</h3>
                    <div class="card-tools">
                        <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{__('common.Back') }}</a>
                    </div>
            
                </div>               
            </div>
            </div> 
        </div> 
            
        <div class="row">
           <div class="col-md-3">
               <a href="{{url('messageType')}}" class="small-box-footer">
                <div class="small-box bg-primary">
                <div class="inner">
                    <h4>{{__('master.Message Type') }}</h4>

                    <h4>{{\App\Models\Master\MessageType::countMessageType() ?? '0' }}</h4>
                </div>
                <div class="icon">
                    <i class="fa fa-whatsapp"></i>
                </div>
                <div class="text-center small-box-footer">{{ __('common.More info') }} <i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>
       
            <div class="col-md-3">
                <a href="{{url('messageTemplate')}}" class="small-box-footer">
                    <div class="small-box bg-danger">
                        <div class="inner">
                        <h4>{{__('master.Message Template') }} </h4>
                        <h4>{{\App\Models\Master\MessageTemplate::countMessageTemplate() ?? '0' }}</h4>
                        </div>
                <div class="icon">
                    <i class="fa fa-whatsapp"></i>
                </div>
                   <div class="text-center small-box-footer">{{ __('common.More info') }} <i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
                </div>

              
        </div>
            
            
        </div>
    </section>
    
</div>
       

@endsection
  
