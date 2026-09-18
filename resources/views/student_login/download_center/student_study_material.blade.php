
@php
$getUser = Helper::getUser();
@endphp
@extends('student_login.layout.app')
@section('title', 'Study Material')
@section('page_title', 'STUDY  MATERIAL')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])
@section('content')
<section class="common-page">
 <div class="common-box m-2">
       <table class="common-table w-100">
                                <thead>
                                    <tr role="row">
                                        <th>Sr. No.</th>
                                        <th>Content Title</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Action</th>

                                </thead>
                                <tbody>
                      
                                    @if(!empty($data))
                                    @php
                                       $i=1
                                    @endphp
                                    @foreach ($data  as $item)
                                        @if($item->content_type =="Study Material")
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $item['content_title']  }}</td>
                                        <td>{{ $item['content_type']  }}</td>
                                        <td>{{ $item['upload_date']  }}</td>
                                        <td>
                                            <a href="{{ url('download') }}/{{$item['id'] ?? '' }}" class="ml-2"><i class="fa fa-download text-success"></i></a>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
    </div>

  
@endsection 
