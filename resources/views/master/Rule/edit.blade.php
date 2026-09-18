@php
$roleType = Helper::roleType();
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
                            <h3 class="card-title"><i class="fa fa-image"></i> {{ __('master.Edit Rule') }}</h3>
                            <div class="card-tools">
                                <a href="{{ url('rules_add') }}" class="btn btn-primary btn-sm">
                                    <i class="fa fa-eye"></i> {{ __('View') }}
                                </a>
                                <a href="{{ url('master_dashboard') }}" class="btn btn-primary btn-sm" title="View Gallery">
                                    <i class="fa fa-arrow-left"></i> {{ __('common.Back') }}
                                </a>
                            </div>
                        </div>

                        <form id="form-submit-edit" action="{{ url('rules_edit') }}/{{ $data['id'] }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row m-2">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label style="color: red;">{{ __('common.Name') }}*</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" placeholder="{{ __('common.Name') }}"
                                            value="{{ $data['name'] ?? '' }}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label style="color: red;">{{ __('master.Role Name') }}*</label>
                                        <select class="select2 form-control @error('role_id') is-invalid @enderror" name="role_id" id="role_id">
                                            <option value="">Select</option>
                                            @if(!empty($roleType))
                                                @foreach($roleType as $item)
                                                    <option value="{{ $item->id ?? '' }}" 
                                                        {{ ($item['id'] == ($data['role_id'] ?? old('role_id'))) ? 'selected' : '' }}>
                                                        {{ $item->name ?? '' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('master.Description') }}</label>
                                        <textarea class="form-control" id="description" name="description"
                                            placeholder="{{ __('master.Description') }}">{!! $data['description'] ?? '' !!}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row m-2">
                                <div class="col-md-12 mt-3 mb-3 text-center">
                                    <button type="submit" class="btn btn-primary btn-submit">{{ __('common.Submit') }}</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- ✅ CKEditor Script --}}
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#description'))
        .then(editor => {
            console.log('CKEditor loaded successfully on edit form');
        })
        .catch(error => {
            console.error('CKEditor load error:', error);
        });
</script>

@endsection
