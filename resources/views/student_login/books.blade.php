@php
$getUser = Helper::getUser();
@endphp

@extends('student_login.layout.app')

@section('title', 'Books')
@section('page_title', 'BOOKS')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])

@section('content')

<section class="common-page">
    <div class="common-box m-2">

        <div class="row">
            @if(!empty($data))
                @foreach ($data as $item)

                <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 d-flex">
                    <div class="card shop_card w-100">

                        <div class="card-body">
                            <div class="row align-items-center">

                                <!-- Image -->
                                <div class="col-4 text-center">
                                    <div class="shadow_box mx-auto">
                                        <img class="shadow_drop img-fluid"
                                             src="@if($item->category == 'Books')
                                                    {{ env('IMAGE_SHOW_PATH').'default/Book.png' }}
                                                  @else
                                                    {{ env('IMAGE_SHOW_PATH').'default/Uniform.png' }}
                                                  @endif"
                                             alt="Item Image">
                                    </div>
                                </div>

                                <!-- Text Details -->
                                <div class="col-8 all_p_oof">
                                    <p><b>{{ __('master.Shop Name') }}:</b> {{ $item->shop_name ?? '--' }}</p>
                                    <p><b>{{ __('master.Shopkeeper No') }}:</b> {{ $item->shop_keeper_no ?? '--' }}</p>
                                    <p><b>{{ __('master.Live Location') }}:</b> {{ $item->live_location ?? '--' }}</p>
                                    <p><b>{{ __('common.Address') }}:</b> {{ $item->address ?? '--' }}</p>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                @endforeach
            @endif
        </div>

    </div>
</section>

<style>
    .all_p_oof p {
        margin-bottom: 6px;
        font-size: 14px;
        word-wrap: break-word;
    }

    .shop_card {
        margin: 10px 0;
        display: flex;
        flex-direction: column;
    }

    .shadow_drop {
        filter: drop-shadow(4px 4px 2px gray);
        max-height: 80px;
        object-fit: contain;
    }

    .shadow_box {
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

@endsection
