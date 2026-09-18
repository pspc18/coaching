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
            
            @php
              
                $plans = \App\Models\library\LibraryPlan::Select('library_plans.*','library_time_slots.study_time','library_cabins.name as libraryCabin')
                         ->leftjoin('library_time_slots','library_time_slots.id','library_plans.library_time_slot_id')
                         ->leftjoin('library_cabins','library_cabins.id','library_plans.library_cabin_id')
                         ->where('library_plans.invoice_id',$data[0]->id)->where('library_plans.status',0)->get();
                
                $formatter = new NumberFormatter('en_IN', NumberFormatter::SPELLOUT);
                $words = $formatter->format($data[0]->total_amount);
                $words .= ' rupees';
            @endphp
            
            <table class="table table_border table_tr_border padding_item">
                <thead>
                    <tr class="sky_tr">
                        <th>STUDENT DETAILS</th>
                        @if(count($plans) != 0)
                        <th>STUDY TIME (Seat)</th>
                        @endif
                        <th>VALIDITY</th>
                        <th>PLAN</th>
                        <th>AMOUNT (₹)</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                       <td class="capital_letters" style="text-align">
                          <div style="text-align:left;">
                            <p><b>Name: </b>{{ $data[0]->first_name ?? '' }} </p>
                            <p><b>Admission No: </b>{{ $data[0]->admissionNo ?? '' }} </p>
                            <p><b>Mobile No: </b>{{ $data[0]->mobile ?? '' }} </p>
                            <p><b>Invoice No: </b>{{ $data[0]->invoice_no ?? ''}} </p>
                            <p><b>Locker: </b>{{ $data[0]->locker_name ?? 'Nan'}} </p>
                          </div>
                        </td>
                        @if(count($plans) != 0)
                        <td>
                           @foreach ($plans as $time)  
                            {{$time->study_time ?? ''}} ({{ "S- ".$time->libraryCabin }})<br>
                           @endforeach
                        </td>
                        @endif
                        <td>
                            @if(count($plans) != 0)
                               @foreach ($plans as $time)  
                                {{ date('d-M, Y', strtotime($time->renew_date)) }}<br>
                               @endforeach
                               @else
                               {{ date('d-M, Y', strtotime($data[0]->locker_renewal_date)) }}
                            @endif
                        </td>
                        <td>1 Month</td>
                        <td>{{ $data[0]->total_amount ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>        

            <table class="table table_border table_tr_border padding_item">
                <thead>
                    <tr class="sky_tr">
                        <th>SR.NO</th>
                        <th>Date</th>
                        <th>Payment Mode</th>
                        <th>DISCOUNT AMOUNT (₹)</th>
                        <th>PAY AMOUNT (₹)</th>
                    </tr>
                </thead>

                <tbody>
                     
                    @if(!empty($data))
                        @php
                            $sr_no = 2;
                            $total_paid_amount = 0;
                        @endphp
                        @foreach($data as $item)
                            <tr>
                                <td>{{ $sr_no ++ }}</td>
                                <td>{{ date('d-M, Y',strtotime($item['bill_date'])) }}</td>
                                <td>{{ $item->payment_mode_name ?? ''  }}</td>
                                <td>{{ $item->per_discount ?? '' }}</td>
                                <td>{{ $item->per_paid_amount ?? '' }}</td>
                            </tr>
                            
                        @php
                            $total_paid_amount += $item->per_paid_amount;
                        @endphp
                        @endforeach
                        
                    @endif
                    <tr>
                        <td rowspan="3" colspan="3" class="padding_bottom_space">
                            <div class="left_all">
                                <p class="capital_letters"><b>In Words :</b> {{ $words ?? '' }}</p>

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
                                <p>Total (₹)</p>
                                <p>Total Paid (₹)</p>
                                <p class="other_color">Dues (₹)</p>
                            </div>
                        </td>
                        <td class="padding_none">
                            <div class="center_items">
                                <p>{{ $data[0]->total_amount ?? '-' }}</p>
                                <p>{{ number_format($total_paid_amount, 2)}}</p>
                                <p class="other_color">{{ number_format($data[0]->due_amount, 2) }}</p>
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