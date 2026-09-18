<style>
table {
  border-collapse: collapse;
  width: 100%;
  text-align:left;
}

tr {
  border-bottom: 1px solid #ddd;
}
.butotm{
    border-bottom:2px solid black;
    
}
</style>
         
             <div class="row m-2">
                  <h2>Student Details</h2>
          <div class="col-12">
           
                <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                  <thead>
                                   @if(!empty($data))
                                            
                                          @foreach($data as $item)
                                          
                                         
                                      <tr>
                                        <th>Student Name</th>
                                        <th>{{ $item['first_name'] ?? ''}}{{ $item['last_name'] ?? ''}}</th>
                                       </tr>
                                      <tr>
                                        <th>Class </th>
                                        <th>{{ $item['class'] ?? '' }}</th>
                                       </tr>
                                     
                                      <tr>
                                        <th>Mobile</th>
                                        <th>{{ $item['mobile'] ?? ''}}</th>
                                       </tr>
                                    
                                      <tr>
                                        <th>Email</th>
                                        <th>{{ $item['email'] ?? ''}}</th>
                                       </tr>
                                         <tr>
                                        <th>dob</th>
                                        <th>{{ $item['dob'] ?? ''}}</th>
                                       </tr>
                                        <tr>
                                        <th>Aadhaar</th>
                                        <th>{{ $item['aadhaar'] ?? ''}}</th>
                                       </tr>
                                      <tr>
                                        <th>Father Name</th>
                                        <th>{{ $item['father_name'] ?? ''}}</th>
                                       </tr>
                                      <tr>
                                        <th>Mother Name</th>
                                        <th>{{ $item['mother_name'] ?? ''}}</th>
                                       </tr>
                                    
                                      <tr>
                                        <th>Registration Date</th>
                                        <th>{{ $item['registration_date'] ?? ''}}</th>
                                       </tr>
                                      <tr>
                                        <th>Pincode</th>
                                        <th>{{ $item['pincode'] ?? ''}}</th>
                                       </tr>
                                      <tr class="butotm">
                                        <th>Address</th>
                                        <th>{{ $item['address'] ?? ''}}</th>
                                       </tr>
                                         
                                    @endforeach
                                  
                                    @endif
                                    </thead>
                                    
                                    </table>
                                                            
                                                               
                                 </div>
                              </div>
                              <br>
                              
            <div class="row m-2">
                <h2>Remark</h2>
          <div class="col-12">
           
                <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                  <thead>
                                   @if(!empty($data))
                                            
                                          @foreach($data as $item)
                                          
                                         
                                      <tr>
                                        <th>Remark</th>
                                        <th>{{ $item['remark'] ?? ''}}</th>
                                       </tr>
                                      <tr class="butotm">
                                        <th>Date </th>
                                        <th>{{ $item['date'] ?? '' }}</th>
                                       </tr>
                                     
                                     
                                          
                                    @endforeach
                                    @endif
                                    </thead>
                                    
                                    </table>
                                                            
                                                               
                                 </div>
                              </div>