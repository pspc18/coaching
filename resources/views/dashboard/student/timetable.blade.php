@extends('layout.app') 
@section('content')

<div class="content-wrapper">

	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="nav-icon fa fa-calendar-plus-o"></i> &nbsp;{{ __('My Class Timetable')  }} </h3>
						<div class="card-tools"> 
                                	<!--<a href="{{url('time/table/dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}  </a>-->
                                	  @if(Session::get('role_id') == 3)
                                <a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs "><i class="fa fa-arrow-left"></i> <span class="" >{{ __('Back') }} </span></a>
                                 @endif
                            </div>
						</div>
						<div class="card-body">
                            <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                             
                                <thead class="bg-primary">
                                    <tr>
                                        <th>Sr No.</th>
                                        <!--<th>Class Name</th>-->
                                        <th>Subject Name</th>
                                        <th>Teacher Name</th>
                                        <th>Time Periods</th>
                                    </tr>
                                </thead>
                              <tbody>
                                @if(!empty($data))
                                @php
                                  $i = 1;
                                @endphp
                                @foreach($data as $item)
                                    <tr>
                                    <td>{{$i++}}</td>
                                    <!--<td>{{$item->className ?? '' }} @if($item->stream != "")[{{$item->stream ?? '' }}] @endif</td>-->
                                    <td>{{$item->subjectName ?? '' }} @if($item->sub_name != ""){{$item->sub_name ?? '' }}@endif</td>
                                    <td style="text-transform: capitalize;">{{$item->first_name ?? '' }} {{$item->last_name ?? '' }}</td>
                                    <td>{{date('h:i A', strtotime($item->from_time)) ?? '' }} {{"To"}} {{date('h:i A', strtotime($item->to_time)) ?? '' }}</td>
                                    </tr>
                                @endforeach
                                @endif       
                              </tbody>
                              </table>
                        </div>
            </div>
        </div>
        </div>
        </div>
    </section>
        
</div>

@endsection