@php
$role_id = Session::get('role_id');
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
							<h3 class="card-title"><i class="fa fa-support"></i> &nbsp;{{ __('master.View Shops') }} </h3>
							<div class="card-tools">
						@if($role_id != 3)
                            <a href="{{url('books_uniform_add')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(9)->add ? '' : 'd-none' }}"><i class="fa fa-plus"></i> {{ __('common.Add') }} </a>
                            <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
                        @else 
                      <a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs "><i class="fa fa-arrow-left"></i> <span class="" >{{ __('Back') }} </span></a>
                      @endif  
                            </div>
						</div>

					<div class="card-body">
						<div class="row">
							@if(!empty($data))
							 @foreach ($data as $item)
								<div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 d-flex">
								    <div class="card shop_card w-100">
								        <div class="card-body">
								            <div class="row align-items-center">
								                <div class="col-4 col-md-4 text-center">
								                    <div class="shadow_box mx-auto">
									                  <img class="shadow_drop img-fluid" 
									                       src="@if($item->category == 'Books')
									                              {{env('IMAGE_SHOW_PATH').'default/Book.png'}}
									                            @else
									                              {{env('IMAGE_SHOW_PATH').'default/Uniform.png'}}
									                            @endif" 
									                       alt="Card image">
									                </div>
								                </div>
								                <div class="col-8 col-md-8 all_p_oof">
								                    <p><b>{{ __('master.Shop Name') }}:</b> {{ $item->shop_name ?? '--' }}</p>
								                    <p><b>{{ __('master.Shopkeeper No') }}:</b> {{ $item->shop_keeper_no ?? '--' }}</p>
								                    <p><b>{{ __('master.Live Location') }}:</b> {{ $item->live_location ?? '--' }}</p>
								                    <p><b>{{ __('common.Address') }}:</b> {{ $item->address ?? '--' }}</p>
								                </div>
								            </div>
								        </div>
								        @if($role_id != 3)
									        <div class="card-footer text-center d-flex justify-content-around flex-wrap gap-2 {{ Helper::permissioncheck(9)->edit ? '' : 'd-none' }}">
												<a href="{{url('books_uniform_edit') }}/{{$item->id}}" class="btn btn-sm btn-primary">
													{{ __('common.Edit') }}
												</a> 
												<a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData {{ Helper::permissioncheck(9)->delete ? '' : 'd-none' }}">
												   <button type="button" class="btn btn-sm btn-danger">{{ __('common.Delete') }}</button>
												</a> 
											</div>
										@endif
								    </div>
								</div>
							 @endforeach 
							@endif
						</div>
					</div>
				</div>
			</div>
        </div>
      </div>
   </section>
</div>

<style>
    .all_p_oof p{
        margin-bottom: 6px;
        font-size: 14px;
        word-wrap: break-word;
    }

    .shop_card{
        margin: 10px 0;
        display: flex;
        flex-direction: column;
    }

    .shadow_drop{
        filter: drop-shadow(4px 4px 2px gray);
        max-height: 80px;
        object-fit: contain;
    }

    .shadow_box{
        border: 1px solid #cbcbcb;
        border-radius: 4px;
        padding: 10px;
        height: 100px;
        width: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
    }

    /* Responsive tweaks */
    @media (max-width: 576px) {
        .all_p_oof p {
            font-size: 12px;
        }
        .shadow_box {
            height: 80px;
            width: 80px;
            padding: 6px;
        }
        .shadow_drop {
            max-height: 60px;
        }
    }
</style>

<script>
    $('.deleteData').click(function() {
		var delete_id = $(this).data('id');
		$('#delete_id').val(delete_id);
	});
</script>

<div class="modal" id="Modal_id">
	<div class="modal-dialog">
		<div class="modal-content" style="background: #555b5beb;">
			<div class="modal-header">
				<h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
			<form action="{{ url('books_uniform_delete') }}" method="post"> 
			    @csrf
				<div class="modal-body">
					<input type="hidden" id="delete_id" name="delete_id">
					<h5 class="text-white">{{ __('common.Are you sure you want to delete') }}?</h5>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
					<button type="submit" class="btn btn-danger">{{ __('common.Delete') }}</button>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection
