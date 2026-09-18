<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/adminlte.min.css') }}">    
<style>
    table{
       font-size: 14px; 
    }
</style>
</head>
<body>
    @php
        $getSetting=Helper::getSetting();
    @endphp
   @include('print_file.print_header')

    <table class="table table-bordered" style="font-size: 14px;">
         <tr role="row">
                      <th>{{ __('messages.Sr.No.') }}</th>
                      <th>{{ __('messages.Reg.No.') }}</th>
                       <th>{{ __('messages.Name') }}</th>
                        <th>{{ __('messages.Mobile No.') }}</th>
                       <th>{{ __('messages.Class') }}</th>
                      <th>{{ __('messages.F Name') }}</th>
                      <th>{{ __('messages.Reg.\Date') }}</th>

                      
                      </tr>
        @if(!empty($data))
                        @php
                       // dd($data);
                           $i=1
                        @endphp
                        @foreach ($data  as $item)
                        <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $item['registration_no'] ?? ''  }}</td>
                                <td>{{ $item['first_name'] ?? ''  }} {{ $item['last_name'] ?? ''  }}</td>
                                <td>{{ $item['mobile'] ?? ''  }}</td>
                                <td>{{ $item['class_name'] ?? '' }}
                            @if (!empty($item['section_name']))
                                ({{ $item['section_name'] }})
                            @endif</td>
                                <td>{{ $item['father_name'] ?? ''  }}</td>
                                <td>{{date('d-m-Y', strtotime($item['registration_date'])) ?? '' }}</td>
                             
                        </tr>
                   @endforeach
                @endif
    </table>
  
</body>
</html>