@php
$getSetting=Helper::getSetting();
@endphp

@include('print_file.print_header')

<p>{!! $branch_name !!}</p>
@include('print_file.print_footer')