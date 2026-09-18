<!DOCTYPE html>
<html lang="en">
@php
    $url = request()->url();
    $parsedUrl = parse_url($url);

    if (isset($parsedUrl['host'])) {
        $hostParts = explode('.', $parsedUrl['host']);

        if (count($hostParts) > 2) {
            $subdomain = $hostParts[0];
           
        }
    }
@endphp
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Registration</title>
  <style>
  body{
  display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        padding:30px;
}
.main{
    margin-top:30px;
    width:80%;
    border-radius:20px;
    /*background:#f2f2f2cf;*/
}
 .bg_image{
        background-image: url('{{ env('IMAGE_SHOW_PATH').'default/Icon_images/rm347-porpla-01.jpg' }}');
        background-repeat: no-repeat;
        background-size: 100% 100%;
    }
     .main_logo {
      position: fixed;
      top: 10px;
      right: 40px;
      filter: drop-shadow(6px 5px 3px #7d7d7d);
    }
    .heading {
  font-weight: 600;
  font-size: 18px;
  font-family: Georgia;
  letter-spacing: 3px;
  margin-bottom: 10px;
  text-transform: capitalize;
  text-shadow: 5px 5px 4px gray;
 text-align:center;
 margin-bottom:30px;
}
    .heading2 {
  font-weight: 600;
  font-size: 15px;
  font-family: Georgia;
  text-transform: capitalize;
  text-align: left;
  margin-top: 30px;
}  

.notice_text{
    margin-top: 1rem;
    display: flex;
    align-items: first baseline;
}
.notice_text b{
    white-space:nowrap;
    margin-right:10px;
}

</style>
</head>

<body class="bg_image">
<img src="https://www.rukmanisoftware.com/public/assets/img/header-logo.png" alt="Company Logo" class="main_logo" width="100px">
     
    <div class="main" style='overflow:auto;height:100%'>
           <h2 class='heading'>Set Environment Variables For New User</h2>
        <form action="{{ url('newRegistration') }}" method="post">
            @csrf
			
			<h2 class='heading2'>Env Details: - </h2>
                		<div class="row ">
                 
                		    <div class="col-md-12">
                					<label style="color:red;" for="softwareTokenNo">Software Token No*</label>
                					<input type="text" class="form-control " id="softwareTokenNo" name="softwareTokenNo" placeholder="Software Token No" value="{{old('softwareTokenNo')}}" required>
                			</div>
                			<div class="col-md-12">
                					<label style="color:red;" for="dbDatabase" class="required">Database Name*</label>
                					<input type="text" class="form-control " id="branch_name" name="dbDatabase" placeholder="Database Name" value="{{old('dbDatabase')}}" required>
                			</div>
							 <div class="col-md-12">
                				
                					<label style="color:red;" for="dbUsername">Db User*</label>
                					<input type="text" class="form-control " id="dbUsername" name="dbUsername" placeholder="Database User" value="{{old('dbUsername')}}" required>
                				
                			</div>
							 <div class="col-md-12">
                				
                					<label style="color:red;" for="dbPassword">Db Password*</label>
                					<input type="text" class="form-control " id="dbPassword" name="dbPassword" placeholder="Database Password" value="{{old('dbPassword')}}" required>
                				
                			</div>
							 <div class="col-md-12">
                				
                					<label style="color:red;" for="imageShowPath">Image Show Path*</label>
                					<input type="text" class="form-control " id="imageShowPath" name="imageShowPath" placeholder="Image Show Path" value="https://{{$subdomain}}.rusoft.in/schoolimage/" required>
                				
                			</div>
							 <div class="col-md-12">
                				
                					<label style="color:red;" for="imageUploadPath">Image Upload Path*</label>
                					<input type="text" class="form-control " id="imageUploadPath" name="imageUploadPath" placeholder="Image Upload Path" value="/home/rusoft/public_html/{{$subdomain}}/schoolimage/" required>
                				
                			</div>
                		
                			</div>
                			
                			
                		  
                
                		
                		
                    
        <center><button type="submit" class="refresh-button">Submit</button></center>
        </form>
        <br><br>
    </div>

<style>

.local-link::before {
  display: inline-block;
  margin-left: 12px;
}
/* Reset default browser styles */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

/* Set basic styles */
/*body {*/
/*  font-family: Arial, sans-serif;*/
/*  background-color: white;*/
/*  justify-content: center;*/
/*  align-items: center;*/
/*  min-height: 100vh;*/
/*}*/

.refresh-button {
  display: inline-block;
  padding: 12px 24px;
  background-color: #6639b5;
  color: #fff;
  border: 1px solid transparent;
  border-color: #6639b5;
  border-radius: 4px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  text-decoration:none;
}

.refresh-button:hover {
    color: #ff5722;
    background-color: #6639b500;
    border-color: #ff5722;
}

.connectionimg{max-width: 400px;}
.d-none{display:none;}
.pointer{cursor:pointer;}
section{
    position: absolute;
    top: 0;
    width: 100%;
    display: flex;
    height: 100%;
    
    text-align: center;
    justify-content: center;
}

</style>
<link rel="stylesheet" href="{{ asset('public/assets/school/css/arise-theme.css') }}">
<link rel="stylesheet" href="{{ asset('public/assets/school/css/icheck-bootstrap.min.css') }}">
<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>

</body>
</html>
