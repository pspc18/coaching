@php
$getSetting=Helper::getSetting();
@endphp

@include('print_file.print_header')

<p>{!! $name !!}</p>
@include('print_file.print_footer')