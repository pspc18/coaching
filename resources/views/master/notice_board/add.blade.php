@php
   $getRole = Helper::roleType(); // fetch roles for Send To
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
                            <h3 class="card-title">
                                <i class="fa fa-envelope"></i> &nbsp; {{ __('master.Add Notice') }}
                            </h3>
                            <div class="card-tools">
                                <a href="{{ url('notice_board/view') }}" class="btn btn-primary btn-sm" title="Back">
                                    <i class="fa fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>        

                        <form id="form-submit" action="{{ url('notice_board/add') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row m-2">

                                <!-- Left Column: Title & Message -->
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label style="color:red;">{{ __('master.Title') }}*</label>
                                        <input autofocus
                                               class="form-control @error('title') is-invalid @enderror"
                                               id="title" name="title" 
                                               placeholder="{{ __('master.Title') }}" type="text" 
                                               value="{{ old('title') }}" />
                                        @error('title')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label style="color:red;">{{ __('master.Message') }}*</label>
                                        <textarea class="form-control @error('message') is-invalid @enderror"
                                                  id="compose-textarea" name="message"  
                                                  style="height: 300px;">{{ old('message') }}</textarea>
                                        @error('message')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <small class="text-muted">You can use HTML formatting. Consider integrating WYSIWYG editor.</small>
                                    </div>
                                </div>

                                <!-- Right Column: Dates & Roles -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label style="color:red;">{{ __('master.From Date') }}*</label>
                                        <input class="form-control @error('from_date') is-invalid @enderror"
                                               id="from_date" name="from_date" type="date" 
                                               value="{{ old('from_date') }}" />
                                        @error('from_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label style="color:red;">{{ __('master.To Date') }}*</label>
                                        <input class="form-control @error('to_date') is-invalid @enderror"
                                               id="to_date" name="to_date" type="date" 
                                               value="{{ old('to_date') }}" />
                                        @error('to_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label style="color:red;">{{ __('master.Send To') }}*</label>
                                        @if(!empty($getRole)) 
                                            @foreach($getRole as $type)
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" 
                                                               name="role_id[]" 
                                                               value="{{ $type['id'] ?? '' }}"
                                                               {{ (is_array(old('role_id')) && in_array($type['id'], old('role_id'))) ? 'checked' : '' }} />
                                                        <b>{{ $type['name'] ?? '' }}</b>
                                                    </label>
                                                </div>                             
                                            @endforeach
                                        @endif
                                        @error('role_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>  

                                <!-- Submit Button -->
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-submit">
                                        {{ __('common.Submit') }}
                                    </button>
                                    <a href="{{ url('notice_board') }}" class="btn btn-secondary">
                                        {{ __('common.Back') }}
                                    </a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>  
            </div>                      
        </div>
    </section>
</div>
@endsection
