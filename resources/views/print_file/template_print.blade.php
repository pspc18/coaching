@include('print_file.print_header')

<p>{{$emailData['data'] ?? ''}}</p>

@include('print_file.print_footer')