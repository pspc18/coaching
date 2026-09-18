@extends('layout.app') 
@section('content')


 <div class="content-wrapper">
    <div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
                <div class="col-sm-8">
                <h3 class="m-2">View Homework</h3>
                </div>
                <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                <li class="pr-2"><a href="{{url('#')}}" class="btn btn-primary  btn-xs"><i class="fa fa-home"></i> Home</a></li>
                <li class="pl-2"><a href="{{url('#')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i> Back</a></li>
                </ol>
                </div>
            </div>
        <hr class="bg-primary" style="margin-top:-12px;">
        </div>
    </div>


            <div class="card m-2">
                <div class="card-body">
                <table class="table table-bordered table-striped dataTable dtr-inline">
                  <thead class="bg-primary">
                  <tr role="row">
                      <th>Sr. No.</th>
                      <th>Class</th>
                      <th>Section</th>
                      <th>Subject</th>
                      <th>Homework Date</th>
                      <th>Submission Date</th>
                      <th>Evaluation Date</th>
                      <th>Status</th>
                      <th>Action</th>

                  </thead>

                  <tbody>
                  @if(!empty($data))
                    @php
                       $i=1
                    @endphp
                    @foreach ($data  as $item)                      
                        <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $item['class_type_id'] ?? '' }}</td>
                        <td>{{ $item['section_id'] ?? '' }}</td>
                        <td>{{ $item['Subject']['name'] ?? '' }}</td>
                        <td>{{ $item['homework_date'] ?? '' }}</td>
                        <td>{{ $item['submission_date'] ?? '' }}</td>
                        <td>{{ $item['submission_date'] ?? '' }}</td>
                        <td>Complete</td>
                            <td><i class="fa fa-reorder" data-toggle="modal" data-target="#myModal"></i></td>
                        </tr>
                  @endforeach
                @endif                        
                  </tbody>
                  </table>
              </div>
            </div>

        
</div>


  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
      <div class="modal-content">
        <div class="modal-header">
          <h5 type="button">Homework Details</h5>
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-7">Write an essay on the Happy New Year.</div>
                <div class="col-md-5 bg-light">
                    <h5>Summary</h5 ><hr>
                    <p><i class="fa fa-calendar-plus-o"></i> <b>Homework Date:</b> 12/05/2021</p>
                    <p><i class="fa fa-calendar-plus-o"></i> <b>Submission Date:</b> 12/05/2021</p>
                    <p><i class="fa fa-calendar-plus-o"></i> <b>Evaluation Date:</b> 12/05/2021</p>
                    <p><b>Created By:</b> Shyam Sir</p>
                    <p><b>Evaluated By:</b> Shyam Sir</p>
                    <p><b>Class:</b> Class 1</p>
                    <p><b>Section:</b> A</p>
                    <p><b>Subject:</b> English</p>
                    <p><b>Status:</b> Complete</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary  btn-xs" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div><!-- /.modal-dialog -->
  </div>






















@endsection