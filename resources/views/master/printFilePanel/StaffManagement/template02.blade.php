@php
$getSetting=Helper::getSetting();
@endphp

<center style="border: 2px solid black;">
    @include('print_file.print_header')
<table style="width:100%; ">
    <div class="img_background_fixed">
                <div class="img_absolute">
                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmanisoft_logo.png' }}'" alt="" class="backhround_img">
                </div>
        <h2 style="color:red;"><u>Teacher Experience Certificate</u></h2>
   

        <p style="padding:10px;"><I><b> Teacher Experience Certificate </b></I></p>
  
        <p style="text-align: center;padding:10px;"><I> <b>This is to certify that &nbsp;&nbsp; <q style="font-size: larger;"> {{$data['first_name'] ?? ''}} {{$data['last_name'] ?? ''}} </q>&nbsp;&nbsp; S/O </b></p></I>
  
       <p style="text-align: center;padding:10px;"> <I><b> has worked in  &nbsp; {{$getSetting['name'] ?? ''}}&nbsp; as</p></I></td>
   
       <p style="text-align: center;padding:10px;"><I><b> He /&nbsp; She&nbsp; has&nbsp; shown&nbsp; the&nbsp; best&nbsp; possible&nbsp; results&nbsp; with&nbsp; good &nbsp;academic &nbsp;&nbsp;&nbsp;&nbsp;</b></p></I>
   
       <p style="text-align: center;padding:10px;">  <I> <b>performance. &nbsp;&nbsp;&nbsp;He /&nbsp;&nbsp;&nbsp; She &nbsp;has&nbsp; been &nbsp;very &nbsp;innovative &nbsp; his&nbsp; / her &nbsp;proffession &nbsp;and &nbsp;his &nbsp;/ her &nbsp; </b></p></I>
  
        <p style="text-align: center;padding:10px;"> <I> <b>performence&nbsp; in&nbsp; the&nbsp; School&nbsp;has&nbsp; been &nbsp;pleasent.&nbsp;</p></I></td>

      
       <p style="text-align: center;padding:10px;">  <I> <b>We &nbsp;wish&nbsp; to&nbsp;&nbsp; his&nbsp; / her&nbsp; bright &nbsp;future&nbsp; & &nbsp;&nbsp;good &nbsp;&nbsp;luck &nbsp;in &nbsp;is &nbsp;/ her &nbsp;career.&nbsp; </b></p></I>
    
      
        <I><p style="text-align: center;padding:10px;"> <b>Name:&nbsp;&nbsp;&nbsp;  {{$data['first_name'] ?? ''}} {{$data['last_name'] ?? ''}} &nbsp;Join from {{date('d-M-Y', strtotime($data['joining_date'])) ?? '' }}</b></p></I>
  
      
       <p style="text-align: center;padding:10px;"><I><b> Designation:&nbsp; Accountant</b></p></I>
       
    <br><br><br>
  </div>
   
@include('print_file.print_footer')
    
</center>
<style>
     .img_background_fixed{
          position: relative;
        }
        
        .img_absolute{
            position: absolute;
            top: -53px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            right: 0;
        }
        
        .backhround_img{
            opacity: 0.3;
            width:59%;
        }
        body{
            max-width: 740px;
            margin: 25px auto;
            
        }
</style>
<script type="text/javascript">
//window.print();
</script>