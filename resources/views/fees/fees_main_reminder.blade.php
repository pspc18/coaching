<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email with Multiple Data Tables</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter&display=swap');
        body, p, table {
            margin: 0;
            padding: 0;
           font-family: 'Inter', sans-serif;
        }

        .email-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <table>
            <h3>Fees Reminders</h3>
            <thead>
                <tr>
                    <th>Sr No.</th>
                    <th>Reminder Name</th>
                    <th>Student Details</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
            @php
            $i = 1;
            @endphp
            @foreach($data as $item)
                <tr>
                    <td>{{$i++ }}</td>
                    <td>{{$item->name ?? '' }}</td>
                    <td>
                        <table>
                            <thead>
                                <tr>
                                    <td>Student Name</td>
                                    <td>Father's Name</td>
                                    <td>Father's Mobile no.</td>
                                </tr>
                            </thead>
                            @php
                            $stu_data = [];
                            $admission_ids = explode(',', $item->admission_id);
                            foreach($admission_ids as $id){
                                $stu_data = DB::table('admissions')->where('id',$id)->whereNull('deleted_at')->get();
                            }
                            @endphp
                            @foreach($stu_data as $student)
                                <tr>
                                    <td>{{$student->first_name ?? ''}} {{$student->last_name ?? ''}}</td>
                                    <td>{{$student->father_name ?? ''}}</td>
                                    <td>{{$student->father_mobile ?? ''}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                    <td>{{ date('d-m-Y', strtotime($item->due_date)) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
