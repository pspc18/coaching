@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    <div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
                <div class="col-sm-8">
                <h3 class="m-2">Other Download</h3>
                </div>
                <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                <li class="pr-2"><a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-home"></i> Home</a></li>
                <li class="pl-2"><a href="{{url('student_download_center')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i> Back</a></li>
                </ol>
                </div>
            </div>
        <hr class="bg-primary" style="margin-top:-12px;">
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
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
                                    @if($item->content_type =="Other Downloads")
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
                    </div>
                </div>
            </div>
        </div>
    </section>
    
</div>
        
        

@endsection