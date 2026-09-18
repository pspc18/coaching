
@php
$getUser = Helper::getUser();
@endphp
@extends('student_login.layout.app')
@section('title', 'Prayers')
@section('page_title', 'PRAYERS')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])
@section('content')
<section class="common-page">
 <div class="common-box m-2">
         @if(!empty($data))
                            @foreach($data as $item)
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
                            @endforeach
          @endif     
    </div>
</section>
@endsection 
