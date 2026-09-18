@php
$getstudents = Helper::getstudents();
$classType = Helper::classType();
$getPaymentMode = Helper::getPaymentMode();
//dd($data);
@endphp
@extends('layout.app')
@section('content')
<div class="content-wrapper">
    
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card card-outline card-orange mb-0">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-money"></i> &nbsp;{{ __('fees.Collect Student Fees') }}</h3>
                            <div class="card-tools">
                                <a href="{{url('fees/index')}}" class="btn btn-primary  btn-sm" title="View Fees"><i class="fa fa-eye"></i>{{ __('common.View') }} </a>
                                <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
                            </div>

                        </div>
                        <div class"card-body">
                            <form id="quickForm" method="post" action="{{ url('fees/ledger/collect') }}">
                                @csrf
                                <div class="row m-2">
                                 <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ __('common.Search By Keywords') }}</label>
                                            <input type="text" class="form-control" value="{{$serach['name'] ?? ''}}" id="name" name="name" placeholder="{{ __('common.Search By Keywords') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ __('common.Class') }}</label>
                                            <select class="form-control " id="class_type_id" name="class_type_id">
                                                <option value="">{{ __('common.Select') }}</option>
                                                @if(!empty($classType))
                                                @foreach($classType as $type)
                                                    @if(Session::get('role_id') !== 2)
                                                        <option value="{{ $type->id ?? ''  }}" {{ ( $type->id == $serach['class_type_id'] ?? '' ) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                                    @else
                                                        <option value="{{ $type->id ?? ''  }}" {{ ( $type->id == $serach['class_type_id'] ?? '' ) ? 'selected' : '' }} {{ ($type->id !== Session::get('class_type_id')) ? 'hidden' : '' }}>{{ $type->name ?? ''  }}</option>
                                                    @endif
                                                
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                	
                                	     <div class="col-md-2">
                                        <div class="form-group">
                                            <label>{{ __('Sr.No') }}</label>
                                            <select class="form-control " id="sr_no" name="sr_no">
                                            
                                            </select>
                                        </div>
                                    </div>
                                  
                                    <div class="col-md-1 ">
                                        <div class="form-group">
                                            <label class="text-white">{{ __('common.Search') }}</label>
                                            <button type="submit" class="btn btn-primary">{{ __('common.Search') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            @if(!empty($data))
                            <div class="row m-2" style="max-height: 225px;overflow-y: scroll;">
                                <table class="table table-bordered small_td" id="trColor">
                                    <thead>
                                        <tr>
                                            <th>{{ __('common.SR.NO') }}</th>
                                            <th class="text-center">{{ __('fees.Admission No.') }} </th>
                                            <th>{{ __('common.Name') }}</th>
                                            <th>{{ __('common.Class') }} </th>
                                            <th>{{ __('common.Fathers Name') }}</th>
                                            <th>{{ __('common.Mothers Name') }}</th>
                                            <th>{{ __('common.Mobile') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $i=1
                                        @endphp
                                        @foreach ($data as $item)
                                            <tr class="quickCollect" style="cursor:pointer; " onclick="showData('{{ $item['id']  }}')" >
                                            <td>{{ $i++ }}</td>
                                            <td class="text-center">{{ $item['admissionNo'] ?? '' }}</td>
                                            <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                                            <td>{{ $item['ClassTypes']['name'] ?? '' }}</td>
                                            <td>{{ $item['father_name'] ?? '' }}</td>
                                            <td>{{ $item['mother_name'] ?? '' }}</td>
                                            <td>{{ $item['mobile'] ?? '' }}</td>
                                        </tr>                                            
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="student_fees_detail"></div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    $('#trColor tr').click(function() {
        $(this).css('backgroundColor', '#6639b5c4');
        $(this).css('color', '#fff');
        $( this ).siblings().css( "background-color", "white" );
        $( this ).siblings().css( "color", "black" );
    });
});

    function showData(admission_id) {
         var basurl = "{{ url('/') }}";
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: basurl+'/student_fees_onclick',
            data: {
                admission_id: admission_id
            },
            // dataType: 'json',
            success: function(data) {
                if (data == 0) {
                    
                    alert('Please Assign the Fees for this Student !');
                    //window.location.href = "{{ url('feesMasterAdd') }}";
                    var url = "{{ url('admissionEdit') }}/" + admission_id;
                    var width = 1000; 
                    var height = 500; 
                    var leftPosition = (window.screen.width - width) / 2; 
                    var topPosition = (window.screen.height - height) / 2; 
                    var features = 'width=' + width + ',height=' + height + ',left=' + leftPosition + ',top=' + topPosition; 
                    // var newWindow = window.open(url, '_blank', features); 
                    // if (newWindow) {
                    //     newWindow.focus(); 
                    // } else {
                    //     alert('Your browser blocked opening the new window. Please check your browser settings.'); 
                    // }
                    
                } else {
                    $('.student_fees_detail').html(data);
                }
            }
        });
    };

    function SearchValue() {
         var basurl = "{{ url('/') }}";
        var class_type_id = $('#class_type_id :selected').val();
        var name = $('#name').val();
        if (class_type_id > 0 || name != '') {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: basurl+'/SearchValueStd',
                data: {
                    class_type_id: class_type_id,
                    name: name
                },
                //dataType: 'json',
                success: function(data) {
                    $('.student_list_show').html(data);
                }
            });
        } else {
            alert('Please put a value in minimum one column !');
        }

    };
    
        
       
   
</script>

<script>
 $(document).ready(function(){
    var allStudents = @json($srno);
    
    var old_class_value = parseInt("{{$serach['class_type_id'] ?? ''}}");
    
    if(old_class_value > 0)
    {
            allStudents.forEach(function(item, index) {
            if(parseInt(item.class_type_id) == old_class_value) {
                       
              $('#sr_no').append('<option value="' + item.ledger_no + '">' + item.ledger_no + ' / '+ item.father_name + '</option>');
            
                        }
        });
        
         var old_srno_value = "{{$serach['sr_no'] ?? ''}}";
         $('#sr_no').val(old_srno_value)
    }

    $("#class_type_id").on("change", function(){
        var class_type_id = $(this).val();
        
        // Clear previous options before appending new ones
        $('#sr_no').empty();
        
        allStudents.forEach(function(item, index) {
            if(parseInt(item.class_type_id) == parseInt(class_type_id)) {
                       
              $('#sr_no').append('<option value="' + item.ledger_no + '">' + item.ledger_no + ' / '+ item.father_name + '</option>');
            
                        }
        });
    });
});
</script>
@endsection