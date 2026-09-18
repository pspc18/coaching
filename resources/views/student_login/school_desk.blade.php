@php
$getUser = Helper::getUser();
@endphp
@extends('student_login.layout.app')

@section('title', 'School Desk')
@section('page_title', 'SCHOOL DESK')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])

@section('content')
<section class="download-page">

  <div class="download-box">
     
                        	<div id="log">
                                {{$data->description ?? ''}}
                            </div>
                        	<div  id="divMain"></div>          
          
  </div>

</section>

<script>
    var support = (function() {
        if (!window.DOMParser) return false;
        var parser = new DOMParser();
        try {
            parser.parseFromString('x', 'text/html');
        } catch (err) {
            return false;
        }
        return true;
    })();

    var textToHTML = function(str) {

        // check for DOMParser support
        if (support) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(str, 'text/html');
            return doc.body.innerHTML;
        }

        // Otherwise, create div and append HTML
        var dom = document.createElement('div');
        dom.innerHTML = str;
        return dom;

    };

    var myValue9 = document.getElementById("log").innerText;

    document.getElementById("divMain").innerHTML = textToHTML(myValue9);

    document.getElementById("log").innerText="";
</script>
@endsection