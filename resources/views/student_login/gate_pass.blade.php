
@php
$getUser = Helper::getUser();
@endphp
@extends('student_login.layout.app')
@section('title', 'Gate Pass')
@section('page_title', 'GATE PASS')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])
@section('content')
<section class="common-page">
 <div class="common-box m-2">
         <table class="common-table w-100">
                                    <thead class="bg-primary">
                                        <tr role="row">
                                            <th>{{ __('messages.Sr.No.') }}</th>
                                            <th>{{ __('Student Name') }}</th>
                                            <th>{{ __('Father Name') }}</th>
                                            <th>{{ __('Father Mobile') }}</th>
                                            <th>{{ __('Reciver  Name') }}</th>
                                            <th>{{ __('Reciver Mobile') }}</th>
                                            <th>{{ __('Relation') }}</th>
                                            <th>{{ __('Date') }}</th>

                                            <!--<th>{{ __('messages.Action') }}</th>-->
                                    </thead>
                                    <tbody>

                                        @if(!empty($data))
                                        @php
                                        $i=1
                                        @endphp
                                        @foreach ($data as $item)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $item['student_name'] ?? '' }}</td>
                                            <td>{{ $item['father_name'] ?? '' }}</td>
                                            <td>{{ $item['father_mobile'] ?? '' }}</td>
                                            <td>{{ $item['reciver_name'] ?? '' }}</td>
                                            <td>{{ $item['reciver_mobile'] ?? '' }}</td>
                                            <td>{{ $item['relation'] ?? '' }}</td>
                                            <td>{{date('d-m-Y', strtotime($item['iessu_date'] ?? ''))}} {{date('h:i A', strtotime($item['iessu_date'] ?? ''))}}</td>

                                            <!--<td>
                                                <a href="{{url('gate_pass_print') }}/{{$item->admissionNo}}" class="btn btn-success  btn-xs ml-3" title="Gate Pass Print" target="_blank"><i class="fa fa-print"></i></a>
                                                <a href="{{url('gate_pass_edit') }}/{{$item->id}}" class="btn btn-primary  btn-xs ml-3" title="Edit Complaint"><i class="fa fa-edit"></i></a>
                                                <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3" title="Delete Book"><i class="fa fa-trash-o"></i></a>
                                            </td>-->
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
    </div>

  
@endsection 
