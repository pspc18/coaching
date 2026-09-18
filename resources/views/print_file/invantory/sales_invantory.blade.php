@php
 $setting = Helper::getSetting();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/invoice.css') }}">  
    <title>Invoice</title>
</head>



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
            <table class="table">
                <thead>
                    <tr class="flex_Centered">
                        <th>
                            <p class="title_page">PAYMENT RECEIPT</p>
                        </th>
                        <th>
                            <p class="title_page text_right">{{ $setting->name ?? '' }}</p>
                            <p class="description">{{ $setting->address }}-{{ $setting->pincode ?? '' }}</p>
                            <p class="description">Phone : {{ $setting->mobile ?? '' }}| Email : {{ $setting->gmail ?? '' }}</p>
                        </th>
                    </tr>
                </thead>
            </table>
            
           
            
            <table class="table table_border table_tr_border padding_item">
                <thead>
                    <tr class="sky_tr">
                        <th>STUDENT DETAILS</th>
                        
                      
                        
                        <th>Date</th>
                       
                    </tr>
                </thead>

                <tbody>
                    <tr>
                       <td class="capital_letters" style="text-align">
                          <div style="text-align:left;">
                            <p><b>Name: </b><spam>{{ $data->first_name ?? '' }} {{ $data->list_name ?? '' }}</spam></p>
                            <p><b>Admission No: </b>{{ $data->admissionNo ?? '' }} </p>
                            <p><b>Mobile No: </b>{{ $data->mobile ?? '' }} </p>
                            <p><b>Invoice No: </b>{{ $data->invoice_no ?? ''}} </p>
                           
                          </div>
                        </td>
                       
                        
                      
                        <td>
                           {{ $data->date ?? ''}}
                        </td>
                        
                    </tr>
                </tbody>
            </table>        

            <table class="table table_border table_tr_border padding_item">
                <thead class="bg-primary">
                    <tr class="sky_tr">
                        <th>SR.NO</th>
                        <th>Inventory Item Name</th>
                        <th>Quantity </th>
                        <th>PAY AMOUNT (₹)</th>
                    </tr>
                </thead>

                <tbody>
                     
                    @if(!empty($dataDetail))
                        @php
                            $sr_no = 1;
                            $total_paid_amount = 0;
                        @endphp
                        @foreach($dataDetail as $item)
                            <tr>
                                @php
                              
                                @endphp
                                <td>{{ $sr_no ++ }}</td>
                                <td>{{ $item['inventory_item_name'] ?? '' }}</td>
                                <td>{{ $item->qty ?? ''  }}</td>
                                <td>{{ $item->amount ?? '' }}</td>
                            </tr>
                            
                        @php
                            $total_paid_amount += $item->amount;
                            
                        @endphp
                        @endforeach
                        
                    @endif
                    @php
                    $formatter = new NumberFormatter('en_IN', NumberFormatter::SPELLOUT);
                $words = $formatter->format($total_paid_amount);
                $words .= ' rupees';
                    @endphp
                    <tr>
                        <td rowspan="3" colspan="2" class="padding_bottom_space">
                            <div class="left_all">
                                <p class="capital_letters"><b>In Words :</b> {{$words ?? ''}}</p>

                                <div class="notes">
                                    <p class="note">
                                       <b>Note:</b> 
                                    </p>
                                    <div>
                                        <p class="margin_left">1. Fee, Charges, Funds, once paid are not refundable.</p>
                                        <p class="margin_left">2. Cheque subject to encashment.</p>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="padding_none">
                            <div class="right_items">
                                
                                <p>Total Quantity  </p>
                                <p>Total Amount (₹)</p>
                                
                            </div>
                        </td>
                        <td class="padding_none">
                            <div class="center_items">
                                <p>{{ $data->total_qty ?? '-' }}</p>
                                <p>{{ number_format($total_paid_amount, 2)}}</p>
                                
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>


            <p class="description top_space">* This is computer generated receipt.</p>
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
            const doc = new jsPDF({
                orientation: 'p',
                unit: 'pt',
                format: [canvas.width, canvas.height],
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