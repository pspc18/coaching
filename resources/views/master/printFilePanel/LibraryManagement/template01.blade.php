@php
 $setting = Helper::getSetting();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Library Invoice</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans&display=swap');

body {
    font-family: 'Open Sans', sans-serif;
}

table {
    width: 100%;
}

/*.flex_Centered {
    display: flex;
    align-items: center;
    justify-content: space-between;
}*/

p {
    margin-bottom: 0px;
    margin-top: 0px;
}

.description {
    font-size: 14px;
    font-weight: 400;
    text-align: end;
}

.sky_tr {
    background-color: #e0ebff;
}

.padding_item th,
.padding_item td {
    padding: 8px;
    border-bottom: 2px solid #00000082;
}

.title_page {
    font-size: 24px;
    font-weight: 600;
    white-space:nowrap;
}

.table_view {
    margin: 0px 40px;
}

table td {
    text-align: center;
}

.table {
    margin-top: 40px;
}

.table_border {
    border: 2px solid #00000082;
    border-collapse: collapse;
}

.table_border tbody {
    padding-bottom: 40px;
}

.table_tr_border tr th {
    border: 2px solid #00000082;
}

.table_tr_border tr td {
    border: 2px solid #00000082;
}

.left_all {
    text-align: left;
}

.right_items {
    text-align: end;
    padding-top: 15px;
}

.right_items p {
    font-size: 22px;
    font-weight: 500;
    line-height: 32px;
    padding-right: 10px;
}

.center_items {
    text-align: center;
    padding-top: 15px;
}

.text_right{
    text-align:right;
}

.center_items p {
    font-size: 22px;
    font-weight: 500;
    line-height: 32px;
}

.other_color {
    background-color: #ffcaca;
}

.padding_none {
    padding: 0px !important;
}

.top_space {
    margin-top: 30px;
    text-align: center;
    font-size: 20px;
}

@page {
    margin:0;
}

.panel-options{
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top:10px;
}

.panel-options button{
    padding: 8px;
    margin-left: 28px;
    cursor: pointer;
    background: skyblue;
    border: none;
    border-radius: 4px;
    font-size: 18px;
}

.capital_letters{
    text-transform : capitalize;
    /*text-align:left;*/
}

.notes{
    display:flex;
    align-items:first baseline;
}

.margin_left{
    margin-left:10px;
}

.payment_mode{
    margin: 10px 0px;
}
</style>

<body>
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
</html>