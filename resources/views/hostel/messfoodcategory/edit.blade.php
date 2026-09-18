@php
$all_class = Helper::classType();
$getSection = Helper::getSection();
//dd($data);
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
                            <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('Edit Mess Category') }}
                            </h3>
                            <div class="card-tools">
                                <a href="{{url('messFoodCategoryAdd')}}" class="btn btn-primary  btn-sm"><i
                                        class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a>

                            </div>
                        </div>

                        <form id="quickForm" action="{{ url('messFoodCategoryEdit') }}/{{$data['id']}}" method="post">
                            @csrf
                            <div class="row mb-2 m-2">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="text-danger">{{ __('Mess Foos Category Name') }}*</label>

                                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{$data['name'] ?? ''}}">
                                          
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary ">{{ __('messages.Update')
                                    }}</button><br><br>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </section>
</div>
@endsection