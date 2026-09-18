@php
$getSubject = Helper::getSubject();
$getBranch = Helper::getPermisnByBranch();
$classType = Helper::classType();
$periods = Helper::getTimePeriod();
$getAllTeachers = Helper::getAllTeachers();

@endphp



@extends('layout.app')
@section('content')

<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
    <h3 class="mb-3">Create / Edit Time Table</h3>
<div class="row">
<div class="col-sm-12">
    <div class="card card-outline card-orange">

                <div class="card-body">
    <form action="{{ url('teacher_subject_add') }}" method="POST" >
        @csrf
                <div class="table-responsive" style="max-height: 600px; overflow: auto;">
    <table id="scheduleTable" class="table table-bordered table-striped text-center align-middle">
        <thead class="bg-primary text-white">
            <tr>
                <th style="min-width:120px; position: sticky; left:0; background:#002c54 ; z-index:10;">Period</th>
                @foreach($classType as $class)
                    <th style="min-width:160px; position: sticky; top:0; background:#002c54 ; z-index:9;">
                        {{ $class->name }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($periods as $period)
                <tr>
                   
                    <td style="position: sticky; left:0; background:#f8f9fa; z-index:8;">
                        <b>{{ $period->period_name ?? '' }}</b><br>
                        <div class="small">
                                  {{ \Carbon\Carbon::parse($period->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($period->to_time)->format('h:i A') }}
                                            </div>
                    </td>
                     @if($period->period_name == "🍴 Lunch Break") 
        
                                        <td colspan="{{ count($classType)+1 }}" class="text-center fw-bold bg-warning">
                                            🍴 LUNCH BREAK
                                        </td>
                    @else  
                    @foreach($classType as $class)
                        @php
                            $saved = $data
                                ->where('class_type_id', $class->id)
                                ->where('time_period_id', $period->id)
                                ->first();
                        @endphp
                        
                    
                        <td>
                            <div class="d-flex gap-1">
                                {{-- Teacher --}}
                                <select 
                                    name="schedule[{{ $class->id }}][{{ $period->id }}][teacher_id]" 
                                    class="form-select form-select-sm">
                                    <option value="">-- Teacher --</option>
                                    @foreach($getAllTeachers as $teacher)
                                        <option value="{{ $teacher->id }}" 
                                            @if($saved && $saved->teacher_id == $teacher->id) selected @endif>
                                            {{ $teacher->first_name ?? '' }} {{ $teacher->last_name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>

                                {{-- Subject --}}
                                <select 
                                    name="schedule[{{ $class->id }}][{{ $period->id }}][subject_id]" 
                                    class="form-select form-select-sm">
                                    <option value="">-- Subject --</option>
                                    @foreach($getSubject as $subject)
                                         @if($subject->class_type_id == $class->id)
                                        <option value="{{ $subject->id }}" 
                                            @if($saved && $saved->subject_id == $subject->id) selected @endif>
                                            {{ $subject->name }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </td>
                    
                    @endforeach
                @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


         <div class="col-sm-12 text-center mt-2" >  
             <button type="submit" class="btn btn-primary" >Save Time Table</button> 
         </div>
    </form>
    </div>
     </div>
     </div>
            <div class="col-md-12">
    <div class="container-fluid">

        <div class="text-end mb-3 no-print">
            <button class="btn btn-primary" onclick="printDiv()">🖨️ Print</button>
        </div>

        <div id="printBox" class="print-box">
            <h3 class="text-center fw-bold mb-4">
                {{$getBranch->branch_name ?? ''}}
            </h3>

            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:120px;">Period</th>
                            @foreach($classType as $class)
                                <th>{{ $class->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($periods as $period)
                            <tr>
                                @php
                              
                                @endphp
                                <td class="fw-bold">{{ $period->period_name ?? '' }} <br>
                                 <div class="small">
                                  {{ \Carbon\Carbon::parse($period->from_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($period->to_time)->format('h:i A') }}
                                            </div>
                              
                    </td>
                            @if($period->period_name == "🍴 Lunch Break") 
        
                                        <td colspan="{{ count($classType)+1 }}" class="text-center fw-bold bg-warning">
                                            🍴 LUNCH BREAK
                                        </td>
                                @else    
    
                                @foreach($classType as $class)
                                    @php
                                        $record = $data->where('class_type_id', $class->id)
                                                       ->where('time_period_id', $period->id)
                                                       ->first();
                                    @endphp
                                    <td>
                                        @if($record)
                                            <div class="fw-bold text-uppercase">
                                                {{ $record->first_name ?? '' }} {{ $record->last_name ?? '' }}
                                            </div>
                                            <div class="small">
                                                {{ $record->subject_name ?? '' }}
                                            </div>
                                        @else
                                            --
                                        @endif
                                    </td>
                                @endforeach
    @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Print Styling -->
<style>
.print-box {
    border: 2px solid black;
    padding: 15px;
    background: #fff;
}
@media print {
     @page {
    size: A4 landscape !important;
    margin: 10mm;
  }
    body {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .no-print { display: none !important; }
    .print-box {
        border: 2px solid black;
        padding: 20px;
        margin: auto;
        width: 95%;
        page-break-inside: avoid;
    }
    table {
       
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        border: 1px solid #080809 !important;
        padding: 4px;
    }
    h3 {
        font-size: 16px;
        margin-bottom: 15px;
    }
    
}

.table-bordered td, .table-bordered th {
  border: 1px solid #080809;
}
</style>

<!-- JS for print -->
<script>
function printDiv() {
    var printContents = document.getElementById("printBox").innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
  //  location.reload(); // reload back to normal after print
}
</script>

    </div>
</div>
</section>
</div>



@endsection
