@php
$getSetting=Helper::getSetting();
//dd(Session::all());
@endphp
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="author" content="atulktp" href="https://github.com/atulktp">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">

    <title>admit Card</title>



  </head>
  <body>
    
<section>
	<div class="container" style="border: 3px solid black;">
		<div class="admit-card">
			<div class="BoxA border- padding"> 
				<div class="row">
					<div class="col-sm-2 txt-center">
						<img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" width="150px" height="150px" />
					</div>
					<div class="col-sm-9 text-center">
						<h2><b>{{$getSetting->name ?? ''}}</b></h2>
						<p>Pre D.El.Ed. Examination, 2022</p>
					</div>

				</div>
			</div>
			<div class="BoxD border- padding mar-bot">
				<div class="row">
					<div class="col-sm-10">
						<table class="table table-bordered">
						  <tbody>
						
							<tr>
							  <td><b>Student Name: </b>{{$data->name ?? ''}}</td>
							  <td><b>Sex: </b>Male</td>
							</tr>
							<tr>
							  <td><b>Father/Husband Name: </b>{{$data->father_name ?? ''}}</td>
							  <td><b>DOB: </b>{{ date('d-m-Y', strtotime($data->dob)) }}</td>
							</tr>
							<tr>
							  <td colspan="2" style="    height: 120px;"><b>Address: </b>{{$data->address ?? ''}}</td>
							</tr>
						  </tbody>
						</table>
					</div>
					<div class="col-sm-2 txt-center">
						<table class="table table-bordered">
						  <tbody>
							<tr>
							  <th scope="row txt-center">Office Copy
                                  <img class="user-img" src="default-user.jpg" width="120px" height="150px"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
                                </th>
							</tr>
							<tr>
							  <th scope="row txt-center">{{$data->first_name ?? ''}}{{$data->last_name ?? ''}}</th>
							</tr>
						  </tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="BoxF border- padding mar-bot txt-center">
				<div class="row">
					<div class="col-sm-12">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Sr. No.</th>
									<th>Subject</th>
									<th>Exam Date</th>
								</tr>
							</thead>
						  <tbody>
							<tr>
							  <td>1</td>
							  <td>English</td>
							  <td>1 Nov 2021</td>
							</tr>
							<tr>
							  <td>2</td>
							  <td>Hindi</td>
							  <td>2 Nov 2021</td>
							</tr>
							<tr>
							  <td>3</td>
							  <td>Geography</td>
							  <td>6 Nov 2021</td>
							</tr>
                            <tr>
                                <td>4</td>
                                <td>Political</td>
                                <td>8 Nov 2021</td>
                              </tr>
						  </tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="border-cut padding">✂-------------------------------------------------------------------Cut from here------------------------------------------------------------✂</div>
            <br>
            <div class="BoxD border- padding mar-bot">
			<div class="row">
					<div class="col-sm-10">
						<table class="table table-bordered">
						  <tbody>
						
							<tr>
							  <td><b>Student Name: </b>{{$data->first_name ?? ''}}{{$data->last_name ?? ''}}</td>
							  <td><b>Sex: </b>Male</td>
							</tr>
							<tr>
							  <td><b>Father/Husband Name: </b>{{$data->father_name ?? ''}}</td>
							  <td><b>DOB: </b>{{ date('d-m-Y', strtotime($data->dob)) }}</td>
							</tr>
							<tr>
							  <td colspan="2" style="    height: 120px;"><b>Address: </b>{{$data->address ?? ''}}</td>
							</tr>
						  </tbody>
						</table>
					</div>
					<div class="col-sm-2 txt-center">
						<table class="table table-bordered">
						  <tbody>
							<tr>
							  <th scope="row txt-center">Student Copy
                                  <img class="user-img" src="default-user.jpg" width="120px" height="150px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
                                </th>
							</tr>
							<tr>
							  <th scope="row txt-center">{{$data->first_name ?? ''}}{{$data->last_name ?? ''}}</th>
							</tr>
						  </tbody>
						</table>
					</div>
				</div>
			</div>

			
		</div>
	</div>
	
</section>
    

   
</body>

</html>