@php
$getSetting=Helper::getSetting();
$getSession=Helper::getSession();
//dd($getSetting);
@endphp

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<style>
    .title_top {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 0px;
    }

    .end_flex {
        display: flex;
        align-items: end;
    }

    .description_head {
        font-size: 16px;
        font-weight: 400;
        margin-bottom: 0px;
    }

    .title_bottom {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 0px;
    }

    .border_table {
        border: 1px solid black;
        padding: 10px;
    }

    .second_table {
        border-top: 1px solid black;
        border-bottom: 1px solid black;
    }

    .second_table th {
        border: 1px solid black;
        background-color: #CCCCCC;
    }

    .second_table td {
        border: 1px solid black;
    }

    .height_table {
        margin-top: 9%;
    }
    
    .last_tr_main{
        display: flex;
        align-items: end;
        justify-content: space-between;
        padding: 10px;
    }
</style>

<body>
    <div class="container">
        <table width="100%" class="border_table" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="12">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="16%" style="padding:15px">
                                <img src="{{env('IMAGE_SHOW_PATH')}}{{'setting/left_logo/'}}{{ $getSetting['left_logo']}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'"
                                    alt="" width="100%">
                            </td>
                            <td width="71%" class="text-center">
                                <p class="title_top">{{$getSetting['name'] ?? ''}}</p>
                                <p class="description_head"><strong>Email :- </strong>{{$getSetting['gmail'] ?? ''}}</p>
                                <p class="description_head"><strong>Address :- </strong>{{$getSetting['address'] ?? ''}},{{$getSetting['pincode'] ?? ''}}</p>
                                <!--<p class="description_head">Contact No. : {{$getSetting['mobile'] ?? ''}}</p>-->
                                <p class="mb-0"><b><u>Assesment Card :- {{$data[0]->from_year ?? ''}}{{"-"}}{{$data[0]->to_year ?? ''}}</u></b></p>
                            </td>
                            <td width="13%">
                                <!--Reg: No 706/JPP/2007--> 
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Class</td>
                <td>: <b>{{$data[0]->class_name ?? '' }}</b></td>
                <td>Student Name</td>
                <td>: <b>{{$data[0]->name ?? '' }}</b></td>
                <td>Father's Name</td>
                <td>: <b>{{$data[0]->father_name ?? '' }}</b></td>
                <td>Role No.</td>
                <td>: <b>{{$data[0]->admissionNo ?? '' }}</b></td>
                <td rowspan="2" class="text-center">
                    <img src="{{env('IMAGE_SHOW_PATH').'profile/'.($data[0]->image ?? '')}}"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'"  width="100px" alt="">
                </td>
            </tr>
            <tr>
                <td style="padding-left: 20px;">Reg No.</td>
                <td>: <b>{{$data[0]->sr_no ?? ''}}</b></td>
                <td>Date Of Birth</td>
                <td>: <b>{{$data[0]->dob ?? '' }}</b></td>
                <td>Mother's Name</td>
                <td>: <b>{{$data[0]->mother_name ?? '' }}</b></td>
                <td></td>
                <td><b></b></td>
            </tr>

            <tr>
                <td colspan="12">
                    <table width="100%" class="second_table mt-3 text-center" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th rowspan="2">
                                    Subject
                                </th>
                                <th>FA1</th>
                                <th>FA2</th>
                                <th>SA1</th>
                                <th>FA3</th>
                                <th>FA4</th>
                                <th>SA2</th>
                                <th>&nbsp;</th>
                                <th>Total</th>
                            </tr>
                            <tr>
                                <th>Grade</th>
                                <th>Grade</th>
                                <th>Grade</th>
                                <th>Grade</th>
                                <th>Grade</th>
                                <th>Grade</th>
                                <th>&nbsp;</th>
                                <th>Grade</th>
                            </tr>
                            @if(!empty($data)) 
                                @php 
                                    $total_obtained =0;
                                    $total_possible =0; 
                                @endphp 
                            @foreach($data as $key => $item)
                            <tr>
                                <td>{{$item->subject_name ?? '' }}</td>
                                <td>{{$item->student_marks ?? ''}}</td>
                                <td>A+</td>
                                <td>A+</td>
                                <td>A+</td>
                                <td>A+</td>
                                <td>B+</td>
                                <th>&nbsp;</th>
                                <td>A</td>
                            </tr>
                            @endforeach
                            @endif
                            <tr>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                                <th>&nbsp;</th>
                            </tr>
                            <tr>
                                <td>Grand Grade</td>
                                <td>A+</td>
                                <td>A</td>
                                <td>A</td>
                                <td>A+</td>
                                <td>A</td>
                                <td>B+</td>
                                <th>&nbsp;</th>
                                <td>A</td>
                            </tr>
                        </thead>
                    </table>
                </td>
            </tr>

            <tr>
                <td colspan="12">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td colspan="12" class="text-center">
                                <p class="title_bottom">Promoted to Class: Second</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td colspan="12">
                    <table width="100%" cellspacing="0" cellpadding="0" class="height_table">
                        <tr class="last_tr_main">
                            <td colspan="6">
                                <p class="title_bottom">
                                    Note : [1] - Tiwial [AB] - Abent [M] - Medical [JL] - Join [F] Fail
                                </p>
                            </td>
                            <td colspan="3">
                                <p class="title_bottom">Class Teacher's Signature</p>
                            </td>
                            <td colspan="3" class="text-center">
                                <img src="{{env('IMAGE_SHOW_PATH')}}{{'setting/seal_sign/'}}{{ $getSetting['seal_sign']}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/seal.png' }}'" alt="seal" width="100px">
                                <p class="title_bottom">Principal's Signature</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
    </div>
</body>

</html>