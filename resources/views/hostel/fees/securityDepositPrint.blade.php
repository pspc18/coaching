@php
 $setting = Helper::getSetting();
 $getSetting = Helper::getSetting();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/invoice.css') }}">  
    <title>Invoice</title>
</head>
<style>
    .img_background_fixed{
          position: relative;
        }
        
        .img_absolute{
            position: absolute;
            /*top: 116px;*/
            top: 1px;
            bottom: -57px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            right: 0;
        }
        
        .backhround_img{
            opacity: 0.3;
            width:38%;
        }
</style>
<body>
    <div class="panel-options">
        <a href="{{ URL::previous() }}">
            <button class="btn btn-sm btn-danger"><i class="fa-fa-arrow-left" aria-hidden="true"></i>
                Back
            </button>
        </a>
        <button class="btn btn-sm btn-danger" id="downloadBtnPDF"><i class="fa fa-pdf" aria-hidden="true"></i>
            Download PDF
        </button>
        <button class="btn btn-sm btn-danger" id="downloadBtnImage"><i class="fa fa-image"
        aria-hidden="true"></i> Download Image</button>
        <button class="btn btn-sm btn-danger" id="printFile"><i class="fa-fa-print"
        aria-hidden="true"></i> Print</button>
    </div>

    <div class="downloadLeaflet" id="downloadLeaflet">
        <div class="table_view">
            <div class="img_background_fixed">
                <div class="img_absolute">
                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" alt="" class="backhround_img">
                </div>
            <table class="table">
                <thead>
                    <tr class="flex_Centered">
                        <th>
                            <p class="title_page">PAYMENT RECEIPT </p>
                        </th>
                        <th>
                            <p class="title_page text_right">
                            {{ $setting->name ?? '' }}
                            
                            </p>
                            <p class="description">
                                <!--{{ $setting->address }}-{{ $setting->pincode ?? '' }}-->
                               Samarth Residency Plot no 10 and 11,<br> bajrang nagar,koradi road, 30, <br> New Mankapur, Nagpur, <br>Maharashtra 440030
                                </p>
                            <p class="description">Phone : {{ $setting->mobile ?? '' }}| Email : {{ $setting->gmail ?? '' }}</p>
                        </th>
                    </tr>
                </thead>
            </table>
            
           
            
            <table class="table table_border table_tr_border padding_item">
                <thead>
                    <tr class="sky_tr">
                        <th width="35%">STUDENT DETAILS</th>
                        <!--<th width="25%">QR Code</th>-->
                    </tr>
                </thead>
                    @php
                        $hostel_details = DB::table('hostel_assign')
                            ->select('hostel_assign.*', 'hostel_building.name as building_name','hostel_floor.name as floor_name','hostel_bed.name as bed_name','hostel_room.name as room_name')
                            ->leftJoin('hostel_building', 'hostel_assign.building_id','hostel_building.id')
                            ->leftJoin('hostel_floor', 'hostel_assign.floor_id','hostel_floor.id')
                            ->leftJoin('hostel_room', 'hostel_assign.room_id','hostel_room.id')
                            ->leftJoin('hostel_bed', 'hostel_assign.bed_id','hostel_bed.id')
                            ->where('hostel_assign.id', $data[0]->hostel_assign_id)
                            ->first();
                         
                    @endphp
                <tbody>
                    <tr>
                       <td class="capital_letters" style="text-align">
                          <div style="text-align:left;">
                            <p><b>Name: </b>{{ $data[0]->first_name ?? '' }} </p>
                            <p><b>Mobile No: </b>{{ $data[0]->mobile ?? '' }} </p>
                            <p><b>Building Name: </b>{{ $hostel_details->building_name ?? ''}} </p>
                            <p><b>Floor Name : </b>{{ $hostel_details->floor_name ?? ''}} </p>
                            <p><b>Room Name: </b>{{ $hostel_details->room_name ?? ''}} </p>
                            <p><b>Bed Name: </b>{{ $hostel_details->bed_name ?? ''}} </p>
                          </div>
                        </td>
                        
                  <!--      <td><img src="{{env('IMAGE_SHOW_PATH')}}{{'setting/qr/'}}{{ $getSetting['qr']}}" alt="seal"-->
                  <!--width="58%"></td>-->
                    </tr>
                </tbody>
            </table>        

            <table class="table table_border table_tr_border padding_item">
                <thead>
                    <tr class="sky_tr">
                        <!--<th>SR.NO</th>-->
                        <th>Sr No.</th>
                        <th>Date</th>
                        <th>Payment Mode</th>
                        <th>SECURITY DEPOSIT AMOUNT (₹)</th>
                    </tr>
                </thead>

                <tbody>
                     
                    @if(!empty($data))
                        @php
                            $sr_no = 1;
                            $total_paid_amount = 0;
                        @endphp
                        @foreach($data as $item)
                            @php
                                $paymode = DB::table('payment_modes')->whereNull('deleted_at')->where('id', $item->payment_mode_id)->first();
                            @endphp
                            <tr>
                                <td>{{ $sr_no ++ }}</td>
                                <td>{{ date('d-M, Y',strtotime($item['date'] ?? '')) }}</td>
                                <td>{{ $paymode->name ?? ''  }}</td>
                                <td>{{ $item->security_deposit ?? '' }}</td>
                            </tr>
                            
                       
                        @endforeach
                        
                    @endif
                    <tr>
                        <td rowspan="3" colspan="3" class="padding_bottom_space">
                            <div class="left_all" style="margin-top: 0%;">

                                <div class="notes">
                                    <p class="note">
                                       <b>Note : </b> 
                                    </p>
                                    <div>
                                        <p class="margin_left">1. Fee, Charges, Funds, once paid are not refundable.</p>
                                        <p class="margin_left">2. Cheque subject to encashment.</p>
                                    </div>
                                </div>
                            </div>
                           
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="padding_none">
                            <div class=" text-center">
                                <p>Total Paid (₹): {{ $data[0]->security_deposit ?? '-' }}</p>
                            </div>
                        </td>
                        
                    </tr>
                </tbody>
                
            </table>
                    <table style="margin-top:40px;">
                        <tr>
                            <td style="text-align:center;">
                                <p class="description top_space" style="display: inline;">* This is computer generated receipt Signatory.</p>
           <!-- <img src="{{env('IMAGE_SHOW_PATH')}}{{'setting/seal_sign/'}}{{ $getSetting['seal_sign']}}" alt="seal"
                  width="100px">-->
                            </td>
                        </tr>
                    </table>
            
             
            
        </div>
    </div>
    </div>
</body>

<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
<script src="{{URL::asset('public/assets/school/js/jquery-ui.min.js')}}"></script>
<script src="{{URL::asset('public/assets/school/js/html2canvas.js')}}"></script>
<script src="{{URL::asset('public/assets/school/js/jspdf.js')}}"></script>

<script>
function downloadPDF() {
        const {
            jsPDF
        } = window.jspdf;
        const leafletElement = document.getElementById('downloadLeaflet');

        html2canvas(leafletElement, {
            useCORS: true,
            scrollX: 0,
            scrollY: 0,
            dpi: window.devicePixelRatio * 1000, // Set higher DPI value for better image quality
            scale: 5
        }).then((canvas) => {
            const imgData = canvas.toDataURL('image/jpeg', 2.0); // Use JPEG format with highest quality
            const a4Width = 841.89; // A4 width in points (landscape)
        const a4Height = 595.28; // A4 height in points (landscape)

        const doc = new jsPDF({
            orientation: 'landscape',
            unit: 'pt',
            format: [a4Width, a4Height],
            compress: true,
        });
            const pdfWidth = doc.internal.pageSize.getWidth();
            const pdfHeight = doc.internal.pageSize.getHeight();

            const elementWidth = canvas.width;
            const elementHeight = canvas.height;

            const scaleRatio = Math.min(pdfWidth / elementWidth, pdfHeight / elementHeight);
            const scaledWidth = elementWidth * scaleRatio;
            const scaledHeight = elementHeight * scaleRatio;
            const xOffset = (pdfWidth - scaledWidth) / 2;
            const yOffset = (pdfHeight - scaledHeight) / 2;

            doc.addImage(imgData, 'JPEG', xOffset, yOffset, scaledWidth, scaledHeight, '', 'FAST');
            doc.save('Invoice.pdf');
        });
    }

    function downloadImageLeaflet() {
        const leafletElement = document.getElementById('downloadLeaflet');
        html2canvas(leafletElement, {
            useCORS: true,
            scrollX: 0,
            scrollY: 0,
            dpi: window.devicePixelRatio * 1000, // Set higher DPI value for better image quality
            scale: 5
        }).then((canvas) => {
            const imgData = canvas.toDataURL('image/jpeg', 2.0); // Use JPEG format with highest quality
            // Create a temporary link element
            const link = document.createElement('a');
            link.href = imgData;
            link.download = 'Invoice.png';

            // Trigger the download
            link.click();
        });
    }


    const downloadBtnPDF = document.getElementById('downloadBtnPDF');
    const downloadBtnImage = document.getElementById('downloadBtnImage');
    downloadBtnPDF.addEventListener('click', downloadPDF);
    downloadBtnImage.addEventListener('click', downloadImageLeaflet);
</script>

<script>
$(document).ready(function() {
    $("#printFile").click(function() {
        printContent();
    });
});

function printContent() {
    var styles = '';

    $(document).ready(function() {
        $('style, link[rel="stylesheet"]').each(function() {
            styles += $(this).prop('outerHTML');
        });
        var content = $("#downloadLeaflet").html();
        var printWindow = window.open('', '_blank');
        styles += `
            <style>
                 @page { size: A4 landscape; margin: 0; }
            body { margin: 0; }
            #downloadLeaflet {
                width: 100%;
                max-width: 100%;
                height: auto;
                overflow: hidden; /* Prevent overflow */
               
            }
            </style>
        `;
        printWindow.document.write('<html><head><title>Invoice</title>' + styles + '</head><body>');
        printWindow.document.write(content);
        printWindow.document.write('</body></html>');
            
        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 500);
    });
}

</script>

</html>