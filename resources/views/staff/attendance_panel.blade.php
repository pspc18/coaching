@extends('layout.app') 
@section('content')

<div class="content-wrapper">

<div class="panel panel-primary">
            <div class="container-fluid panel-heading">
                <div class="row">
                    <div class="col-sm-6">
                        <h1 class="m-2">Attendance Management</h1>
                
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                          <li class="breadcrumb-item"><a href="#">Home</a></li>
                           <li class="breadcrumb-item active"><a href="students">Staff</a></li>
                        </ol>
                
                    </div>
                </div>
                 <hr class="bg-primary">
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">
               <div class="row m-2">
                   
                   <div class="col-md-3 bg-white py-3">
                      <div class=" col-12 title"><h5>Fill Attendance</h5></div>
                      <div class="form-group">
				         <label class="font-bold">Staff Type:</label>
                         <select class="form-control select2bs4" style="width: 100%;">
                          <option>Teaching</option>
                          <option>Non-Teaching</option>
                        </select>
		              </div>
                      <div class="form-group">
				         <label class="font-bold">Date:</label>
                         <input type="text" class="form-control" name="date" id="date">
                      </div>
                      <div class="buttons text-center">
                      <a href="{{url('attendance_panel')}}" class="btn btn-sm btn-primary">Fill Attendance</a>
                      <a href="{{url('view_sallary')}}" class="btn btn-sm btn-primary">View Sallary</a>
                      </div>
                   </div>

                   <div class="col-md-8 bg-white py-3 ml-4">
                   <div class=" col-12 title"><h5>Current Month Attendance List</h5></div>
                   <div class="col-sm-12">
                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline">
                    <thead>
                             <tr role="row" class="bg-primary">
                             <th>S.No.</th>
                             <th>Staff Name</th>
                             <th>Type</th>
                             <th>Designation</th>
                             <th>Month</th>
                             <th>Present</th>
                             <th>Absent</th>
                             <th>Leave</th>
                             <th>View</th>
                    </thead>
               </table>
                   </div>
               </div>
            </div>
        </div>
</div>

@endsection    