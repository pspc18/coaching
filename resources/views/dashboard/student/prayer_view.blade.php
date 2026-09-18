@extends('layout.app') 
@section('content')

<div class="content-wrapper">

	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="nav-icon fa fa-calendar-plus-o"></i> &nbsp;{{ __('Prayer')  }} </h3>
							<div class="card-tools"> 
                                	<a href="{{url('dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}  </a>
                            </div>
						</div>
						<div class="card-body">
                            @if(!empty($data))
                            @foreach($data as $item)
						    <div class="col-md-12">
						        <div class="card">
						            <div class="card-header">
						                <div class="d-flex justify-content-between">
						                    <p class="mb-0">{{$item->name ?? '' }}</p>
						                </div>
						            </div>
						            
						            <div class="card-body">
						                <div class="text-center">{!! html_entity_decode($item->prayer ?? '') !!}</div>
						            </div>
						        </div>
                            </div>
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