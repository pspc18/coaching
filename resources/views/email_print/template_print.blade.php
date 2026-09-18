
@php
$getSetting=Helper::getSetting();
@endphp

@include('print_file.print_header')
<p>{!! html_entity_decode($data, ENT_QUOTES, 'UTF-8') !!}</p>
@include('print_file.print_footer')