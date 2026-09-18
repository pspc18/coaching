
@php
$getUser = Helper::getUser();
@endphp
@extends('student_login.layout.app')
@section('title', 'Subject')
@section('page_title', 'SUBJEST')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])
@section('content')
<section class="common-page">
 <div class="common-box m-2">
         <table  class="common-table w-100">
                                    <thead>
                                        <tr role="row">
                                            <th>{{ __('messages.Sr.No.') }}</th>
                                            <th>{{ __('messages.Subject') }}</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @if(!empty($data)) 
                                        @php $i=1 
                                        @endphp 
                                        @foreach ($data as $item)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $item['name'] ?? ''}}</td>
                                            <td>@if($item->other_subject == 0) Main @else Other @endif</td>
                                        </tr>
                                        @endforeach @endif
                                    </tbody>
                                </table>
    </div>

  
@endsection 
