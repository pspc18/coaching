
@php
$getUser = Helper::getUser();
@endphp
@extends('student_login.layout.app')
@section('title', 'Time Table')
@section('page_title', 'TIME TABLE')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])
@section('content')
<section class="common-page">
 <div class="common-box m-2">
         <table id="" class="common-table">
                             
                                <thead>
                                    <tr>
                                        <th>Sr No.</th>
                                        <!--<th>Class Name</th>-->
                                        <th>Subject Name</th>
                                        <th>Teacher Name</th>
                                        <th>Time Periods</th>
                                    </tr>
                                </thead>
                              <tbody>
                                @if(!empty($data))
                                @php
                                  $i = 1;
                                @endphp
                                @foreach($data as $item)
                                    <tr>
                                    <td>{{$i++}}</td>
                                    <!--<td>{{$item->className ?? '' }} @if($item->stream != "")[{{$item->stream ?? '' }}] @endif</td>-->
                                    <td>{{$item->subjectName ?? '' }} @if($item->sub_name != ""){{$item->sub_name ?? '' }}@endif</td>
                                    <td style="text-transform: capitalize;">{{$item->first_name ?? '' }} {{$item->last_name ?? '' }}</td>
                                    <td>{{date('h:i A', strtotime($item->from_time)) ?? '' }} {{"To"}} {{date('h:i A', strtotime($item->to_time)) ?? '' }}</td>
                                    </tr>
                                @endforeach
                                @endif       
                              </tbody>
                              </table>
    </div>

  


@endsection 
