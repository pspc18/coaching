 

		<table style="border-bottom: 2px solid;">
			<tbody >
					<tr>
      <td  rowspan="4">
          <img  rowspan="4" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/common_images/rukmani_logo.png' }}'" style="width: 130px;"></td>
  
      <td   style=" font-size:28px;text-align:center;width:100%;"><span class="style71"><strong>{{$getSetting['name'] ?? ''}}</strong></span></td>
   
      <td rowspan="4"> 
      <img  src="{{ env('IMAGE_SHOW_PATH').'/setting/right_logo/'.$getSetting['right_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/common_images/rukmani_logo.png' }}'" style="width: 130px;"></td>
     
    
     
   </tr>
	<tr style="text-align:center;">
       
 
   
      <td   style="font-size:14px;text-align:center;"><p style="margin-bottom:-0.5%;"><b >{{$getSetting['address'] ?? ''}}</b></p></td>

      </tr>
      <tr>
     
   
      <td  style="font-size:14px; text-align:center;"><p style="margin-bottom:-0.5%;"><b >Phone:-</b> {{$getSetting['mobile'] ?? ''}} &nbsp;<b>Email :</b> {{$getSetting['gmail'] ?? ''}}  </p></td>
    </tr>
   	<tr style="text-align:center;">
       

   
      <td   style="font-size:14px;text-align:center;"><b>www.rukmanisoftware.com</b></td>

      </tr>

    
  </tbody></table> 