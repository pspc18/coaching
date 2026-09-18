@php
  $classType = Helper::classType();
  $getsubject = Helper::getSubject();
  $date = date('Y-m-d');
  
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
                        @if(Session::get('') == 3)
                            <h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp; {{ __('Send Message') }}</h3>
                        @else						    
							<h3 class="card-title"><i class="nav-icon fas fa fa-leanpub "></i> &nbsp;{{ __('Send Message') }}</h3>
						@endif
						
						</div>
						
						<div class="container-flulid mt-2 pl-2">
						<div class='row'>
						    
						     
            <div class="col-md-3 col-6">
                <a href="{{ url('send_message')}}" class="small-box-footer">
                <div class="small-box bg-danger">
                    <div class="inner">
                    <h4 class="mobile_text_title">{{ __('Send Messages') }}</h4>
                    <p class="mobile_text_title">
                           &nbsp;
                    
                        </p>
                    </div>
                <div class="icon">
                    <i class="fa fa-leanpub"></i>
                </div>
                    <div class="text-center small-box-footer">{{ __('common.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>
           
            
                  <div class="col-md-3 col-6">
                <a href="{{ url('happy_birthday')}}" class="small-box-footer">
                <div class="small-box bg-warning">
                    <div class="inner">
                    <h4 class="mobile_text_title">{{ __('Send Birthday Wishes') }}</h4>
                    <p class="mobile_text_title">
                  
                 {{(App\Models\Admission::countTodaysBirthday()) + (App\Models\User::countTodaysBirthday())}}
                    </p>
                    </div>
                <div class="icon">
                    <i class="fa fa-leanpub"></i>
                </div>
                    <div class="text-center small-box-footer">{{ __('common.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                </div></a>
            </div>
            
            
                            <div class="col-md-3 col-6">
                                <a href="{{ url('message_queue')}}" class="small-box-footer">
                                <div class="small-box bg-primary">
                                    <div class="inner">
                                    <h4 class="mobile_text_title">{{ __('Message Histry') }}</h4>
                                    <p class="mobile_text_title">
                                           &nbsp;
                                    
                                        </p>
                                    </div>
                                <div class="icon">
                                    <i class="fa fa-leanpub"></i>
                                </div>
                                    <div class="text-center small-box-footer">{{ __('common.More info') }}<i class="fa fa-arrow-circle-right"></i></div>
                                </div></a>
                            </div>
            
       
            
						</div>
						</div>
			
					</div>
				</div>
			</div>
		</div>
	</section>
</div>









@endsection