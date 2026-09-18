@extends('layout.app') 
@section('content')

<div class="content-wrapper">
           
        <div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
            <div class="col-sm-6">
                <h1 class="m-2"> Date Wise Ledger Detalis</h1>
                
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                <li class="breadcrumb-item active"><a href="{{url('account_dashboard')}}">Account</a></li>
                </ol>
                
            </div>
            </div>
            <hr class="bg-danger m-1">
        </div>
        </div>
         
        
                         
            <div class="row m-4">
                <div class="col-md-5 bg-success">
                    <div class="row">
                     <div class="col-md-4">
              <div class="form-group">
                         <label>From Date:</label>
                       <input type="date" class="form-control" id="date" name="date" value="">
                    </div>
            </div> 
            <div class="col-md-4">
              <div class="form-group">
                         <label> To Date:</label>
                       <input type="date" class="form-control" id="date" name="date" value="">
                    </div>
            </div> 
            <div class="col-md-4">
              <div class="form-group">
				<label> Month Wise:</label>
				<select class="form-control">
               <option value="0">January</option>
                <option value="1">february</option>
                <option value="18">March</option>
                <option value="20">April</option>
                <option value="34">May</option>
                <option value="35">June</option>
                <option value="36">July</option>
                <option value="37">Auguest</option>
                <option value="38">September</option>
                <option value="39">October</option>
                <option value="40">November</option>
                 <option value="40">December</option>
                </select>
		    </div>
            </div> 
            
                </div> 
                </div> 
                <div class="col-md-1">
                    </div>
                <div class="col-md-6 bg-success">
                   <div class="row">
                     <div class="col-md-4">
              <div class="form-group">
                         <label>Income Total </label>
                       <input type="text" class="form-control" id="income_total" name="income_total" value="0123456">
                    </div>
            </div> 
            <div class="col-md-4">
              <div class="form-group">
                         <label>Expence Total</label>
                       <input type="text" class="form-control" id="expence_total" name="expence_total" value="785424">
                    </div>
            </div> 
            <div class="col-md-4">
              <div class="form-group">
				<label>Grand Total</label>
			        <input type="text" class="form-control" id="grand_total" name="grand_total" value="4544456">
		    </div>
            </div> 
            
                </div>  
                </div>
               
            </div>  
            <div class="row m-1">
                
                <div class="col-md-5">
            <div class="card">
            <div class="card-body">
                <h3 class="text-danger text-center"> Income Detalis:-</h3>
               <div class="row">
                   <div class="col-md-7">
                       </div>
                   <div class="col-md-5">
                   <div>
                    <input class="form-control" type="text" placeholder="Search" aria-label="Search">
                    </div></div>
                   </div>
                        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline">
                  <thead>
                  <tr role="row" class="bg-info">
                      <th>Serial No.</th>
                      <th>Customer Name</th>
                      <th>Amount Type</th>
                      <th>Income Form</th>
                      <th>Totle Amount</th>
                      <th>Detalis</th>
                  </thead>
                  <tbody>
                      <tr>
                        <td>1</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-block btn-sm btn btn-success">Detalis</button>
                        </td> 
                          </tr>
                          
                           <tr>
                        <td>2</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-block btn-sm btn btn-success">Detalis</button>
                        </td> 
                          </tr>
                          
                           <tr>
                        <td>3</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-block btn-sm btn btn-success">Detalis</button>
                        </td> 
                          </tr>
                          
                           <tr>
                        <td>4</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-block btn-sm btn btn-success">Detalis</button>
                        </td> 
                          </tr>
                          
                           <tr>
                        <td>5</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-block btn-sm btn btn-success">Detalis</button>
                        </td> 
                          </tr>
                          
                           <tr>
                        <td>6</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-block btn-sm btn btn-success">Detalis</button>
                        </td> 
                          </tr>
                      </table>
                        </div>
                </div>
                </div>
                <div class="col-md-1">
                    </div>
                <div class="col-md-6">
            <div class="card">
            <div class="card-body">
                <h3 class="text-danger text-center"> Expence Detalis:-</h3>
               <div class="row">
                   <div class="col-md-6">
                       </div>
                   <div class="col-md-1">
                       </div>
                       <div>
                    <input class="form-control" type="text" placeholder="Search" aria-label="Search">
                    </div>
                       <table class="table table-bordered table-striped dataTable dtr-inline">
                  <thead>
                  <tr role="row" class="bg-info">
                      <th>Serial No.</th>
                      <th>Customer Name</th>
                      <th>Amount Type</th>
                      <th>Income Form</th>
                      <th>Totle Amount</th>
                      <th>Detalis</th>
                  </thead>
                  <tbody>
                      <tr>
                        <td>1</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-block btn-sm btn btn-success">Detalis</button>
                        </td> 
                          </tr>
                          
                           <tr>
                        <td>2</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-block btn-sm btn btn-success">Detalis</button>
                        </td> 
                          </tr>
                          
                           <tr>
                        <td>3</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-block btn-sm btn btn-success">Detalis</button>
                        </td> 
                          </tr>
                          
                           <tr>
                        <td>4</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-block btn-sm btn btn-success">Detalis</button>
                        </td> 
                          </tr>
                          
                           <tr>
                        <td>5</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-block btn-sm btn btn-success">Detalis</button>
                        </td> 
                          </tr>
                          
                           <tr>
                        <td>6</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-block btn-sm btn btn-success">Detalis</button>
                        </td> 
                          </tr>
                      </table>
                      </table>
                        </div>
                </div>
                </div>
            </div>  
            </div>    
                
</div>

    @endsection 