
@php
$getUser = Helper::getUser();
@endphp
@extends('student_login.layout.app')
@section('title', 'Download Center')
@section('page_title', 'DOWNLOAD CENTER')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])
@section('content')
<section class="common-page">
 <div class="common-box m-2">
        <table class="common-table w-100">
                          <thead class="bg-primary">
                          <tr role="row">
                              <th>{{ __('master.Sr.No.') }}</th>
                              <th>{{ __('master.Content Title') }}</th>
                              <th>{{ __('master.Class') }}</th>
                              <th>{{ __('master.Content Type') }}</th>
                              <th>{{ __('master.Date') }}</th>
                              <th>{{ __('Description') }}</th>
                              
                              <th>{{ __('master.Action') }}</th>
                             
                              
                              
                          </thead>
                          <tbody id="">
                          
                          @if(!empty($dataview))
                                @php
                                   $i=1;
                                @endphp
                                @foreach ($dataview  as $item)
                                <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $item['content_title'] ?? '' }}</td>
                                        <td>{{ $item['class_name'] ?? 'All' }}</td>
                                        <td>{{ $item['content_type'] ?? '' }}</td>
                                        <td>{{date('d-m-Y', strtotime($item['upload_date'])) ?? '' }}</td>
                                        
                                          <td>{{ $item['description'] ?? '' }}</td>
                                        
                                        <td>
                                           
                                            

                                            @if(($item['video_link'] ?? '') != '')
                                            <a class='text-primary tooltip1' target='_blank'href="{{$item['video_link'] ?? '' }}" title1="Play Video"><i class="fa fa-play-circle-o text-white"></i></a>
                                            @endif

                                            @if(($item['content_file'] ?? '') != '')
                                           &nbsp; <a href="{{ url('download/'.$item['id']) }}"
   class="ml-2 tooltip1 "
   title1="Download">
    <i class="fa fa-download text-success"></i>
</a>
                                            @endif

                                        </td>
                                        
                                    </tr>
                           @endforeach
                        @endif
                          </tbody>
                          </table>
    </div>

  
@endsection 
