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

        <div class="panel panel-default">
            <div class="panel-body">
              <div class="row m-2">  
                  <div class="col-md-12"><h5>Staff Attendance</h5></div>
                  <div class="col-md-6 offset-2 d-flex justify-content-between">
                      <h5>Attendance Date:12/10/21</h5>
                     <h5>Staff Type:Teaching</h5>
                  </div>
                  <table id="example1" class="table table-bordered table-striped border table-responsive dataTable dtr-inline ">
                  <thead>
                  
                             <tr role="row" class="bg-primary">
                             <th>SR.No.</th>
                             <th>Staff Name</th>
                             <th>Unique Id</th>
                             <th>Staff Attendance</th>
                     </thead>

                     <tbody>
                  
                       <tr>
                        <td>1</td>
                        <td>Ramesh Gupta</td>
                        <td>102</td>
                        <td>
                        <select class="form-control" style="width: 20%;">
                          <option>P</option>
                          <option>A</option>
                        </select></td>
                        
                       </tr>

                       <tr>
                        <td>2</td>
                        <td>Ramesh Gupta</td>
                        <td>102</td>
                        <td>
                        <select class="form-control" style="width: 20%;">
                          <option>P</option>
                          <option>A</option>
                        </select></td>
                       </tr>

                       <tr>
                        <td>3</td>
                        <td>Ramesh Gupta</td>
                        <td>102</td>
                        <td>
                        <select class="form-control" style="width: 20%;">
                          <option>P</option>
                          <option>A</option>
                        </select></td>
                       </tr>

                       <tr>
                        <td>4</td>
                        <td>Ramesh Gupta</td>
                        <td>102</td>
                        <td>
                        <select class="form-control" style="width: 20%;">
                          <option>P</option>
                          <option>A</option>
                        </select></td>
                       </tr>

                       <tr>
                        <td>5</td>
                        <td>Ramlal</td>
                        <td>102</td>
                        <td>
                        <select class="form-control" style="width: 20%;">
                          <option>P</option>
                          <option>A</option>
                        </select></td>
                       </tr>
                       
                     </tbody>
                </table>  
                <button type="submit" class="btn btn-sm  btn-primary">Submit</button>   
              </div>
            </div>
        </div>
</div>


@endsection  