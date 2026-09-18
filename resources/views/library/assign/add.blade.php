@extends('layout.app')
@section('content')
@php
$classType = Helper::classType();
$getLibrary = Helper::getLibrary();
$getgenders = Helper::getgender();

@endphp
<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-book"></i> &nbsp;{{ __('library.Assign Books') }} </h3>
                            <div class="card-tools">
                                <!--<a href="{{url('book_assign_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>{{ __('messages.View') }}  </a>-->
                                <a href="{{url('library_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
                            </div>
                        </div>
                        <form action="{{ url('book_assign') }}" method="post">
                            @csrf
                            <div class="row m-2">
                                <div class="col-md-6">
                                    <label class="text-danger">{{ __('common.Search By Keywords') }}*</label>
                                    <input class="form-control @error('name') is-invalid @enderror" autofocus type="text"
                                        id="name" name="name" placeholder="{{ __('common.Search By Keywords') }}"
                                        value="{{$search ?? ''}}">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-md-1 text-center">
                                    <label class="text-white">{{ __('common.Search') }}</label>
                                    <button class="btn btn-primary" type="submit">{{ __('common.Search') }}</button>
                                </div>

                            </div>
                        </form>
                        @if(!empty($data))
                         <div class="row m-2">
                    <div class="col-md-12" style="overflow-x:auto;">
                          <table id="" class=" table table-bordered table-striped dataTable dtr-inline text-center ">


                                <thead>
                                <tr role="row">
                                            <!--<th>Category</th>-->
                                            <th>{{ __('library.Book Name') }}</th>
                                            <th>{{ __('library.Authour Name') }}</th>
                                            <th>{{ __('library.Publidher Name') }}</th>
                                            <th>{{ __('library.Edition') }} </th>
                                            <th>{{ __('library.Quantity') }} </th>
                                            <th>{{ __('MRP') }} </th>
                                            <th>{{ __('library.Scan QR Code') }} </th>
                                            <th>{{ __('library.Image') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="">
                                        <tr>      
                                      
                                            <!--<td>{{ $data['category_id'] ?? '' }}</td>-->
                                      
                                            <td>{{ $data['name'] ?? '-' }}</td>
                                            <td>{{ $data['author'] ?? '-' }}</td>
                                            <td>{{ $data['publisher'] ?? '-' }}</td>
                                            <td>{{ $data['edition'] ?? '-' }}</td>
                                            <td>{{ $data['quantity'] ?? '-' }}</td>
                                            <td>{{ $data['mrp'] ?? '-' }}</td>
                                           
                                             @php
                                            $generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
                                            @endphp
                                            <td class="text-center"> <img src="data:image/png;base64,{{ base64_encode($generatorPNG->getBarcode($data['book_code'], $generatorPNG::TYPE_CODE_128)) }}"><br><span class="text-center">{{$data['book_code'] ?? ''}}</span>
                                            </td>
                                            <td>
                                                @if($data->image)
                                                <img src="{{ env('IMAGE_SHOW_PATH').'Book_img/'.$data['image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/no_image.png' }}'" class="img-fluid" style="width:50px; height:50px;">
                                                @else
                                                <img src="{{asset('schoolimage/library/book_image.png')}}"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/no_image.png' }}'"class="img-fluid" style="max-width:80px" alt="avatar.png">
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <!--<td colspan="4" class="text-center"><button type="button"   class="btn btn-success w-50" id="assignBook"><i class="fa fa-plus"></i> Assign Book</button></td>-->
                                            <td colspan="4" class="text-center"><button type="button"   class="btn btn-success w-50" id="student_library"><i class="fa fa-plus"></i> {{ __('library.Assign Books') }}</button></td>
                                            <!--<td colspan="5" class="text-center"><button type="button" class="btn btn-warning w-50 active" id="returnBook"><i class="fa fa-arrow-left"></i> Return Book</button></td>-->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                        <!-- assign Book--->
                        <div class="row m-2 d-none" id="assign_student">
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary w-50" id="school_student"><i class="fa fa-plus"></i> {{ __('library.School Students') }}</button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary w-50" id="student_library"><i class="fa fa-plus"></i> {{ __('library.Library Students') }}</button>
                            </div>
                            <div class="col-md-4">
                                @if(!empty($data['quantity']))
                                <button type="button" class="btn btn-primary w-50" id="student_new"><i class="fa fa-plus" {{ ( $data['quantity']==0 ) ? 'disabled' : '' }}></i> New Students</button>
                                @endif
                            </div>

                        </div>
                        <form action="{{ url('assign_book') }}" method="post">
                            @csrf
                            <input type="hidden" id="library_book_id" name="library_book_id"
                                value="{{ $data['id'] ?? '' }}">
                            <div class="row m-2" id="school_student_serach" style="display: none;">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>{{ __('common.Class') }}</label>
                                        <select class="form-control select2" id="class_type_id" name="class_type_id">
                                            <option value="">{{ __('common.Select') }}</option>
                                            @if(!empty($classType))
                                            @foreach($classType as $type)
                                            <option value="{{ $type->id ?? ''  }}">{{ $type->name ?? '' }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="">{{ __('library.Search Students') }}</label>
                                    <input class="form-control" type="text" id="student_name" name="name"
                                        placeholder="Search students by keywords" value="">
                                </div>
                                <div class="col-md-2">
                                    <label class="">{{ __('library.Student Qr Code') }}</label>
                                    <input class="form-control" type="text" id="stu_qr_code" name="stu_qr_code"
                                        placeholder="Search student by Qr code of fill Qr code" value="">
                                </div>
                                <div class="col-md-1 text-center">
                                    <label class="text-white">{{ __('common.Search') }}</label>
                                    <button class="btn btn-primary" onclick="SearchValue()"
                                        type="button">{{ __('common.Search') }}</button>
                                </div>
                                <div class="col-md-12" style="overflow-x:auto;">
                                    <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                                        <thead>
                                            <tr role="row">
                                                <th>{{ __('common.SR.NO') }}</th>
                                                <th>{{ __('common.Name') }}</th>
                                                <th>{{ __('common.Class') }}</th>
                                                <th>{{ __('common.F Name') }}</th>
                                                <th>{{ __('common.Mobile') }}</th>
                                                <th>{{ __('common.Email') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="student_list"> </tbody>
                                    </table>
                                </div>
                                @if(!empty($data['quantity']))
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-success">{{ __('common.Submit') }}</button>
                                </div>
                                @endif
                            </div>
                        </form>
                        <form action="{{ url('assign_book') }}" id="block_enter" method="post">
                            @csrf
                            <input type="hidden" id="library_book_id" name="library_book_id"
                                value="{{ $data['id'] ?? '' }}">
                            <div class="row m-2" id="library_student" style="display: none;">
                                <div class="col-md-4">
                                    <label class="text-danger">{{ __('library.Search Students') }}*</label>
                                    <input class="form-control" type="text" id="library_name" name="name" placeholder="Search students by keywords" value="">
                                </div>
                                <div class="col-md-2">
                                    <label class="">{{ __('library.Student Qr Code') }}</label>
                                    <input class="form-control" type="text" id="li_stu_qr_code" name="li_stu_qr_code"
                                        placeholder="Search student by Qr code of fill Qr code" value="">
                                </div>                                
                                <div class="col-md-1 text-center">
                                    <label class="text-white">{{ __('common.Search') }}</label>
                                    <button class="btn btn-primary" onclick="SearchValuelibrary()"
                                        type="button">{{ __('common.Search') }}</button>
                                </div>
                                <div class="col-md-12">
                                    <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                        <thead>
                                            <tr role="row">
                                                <th>{{ __('common.SR.NO') }}</th>
                                                <th>{{ __('common.Name') }}</th>
                                                <th>{{ __('common.F Name') }}</th>
                                                <th>{{ __('common.Mobile') }}</th>
                                                <th>{{ __('common.Email') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="library_student_list"></tbody>
                                    </table>
                                </div>
                                @if(!empty($data['quantity']))
                                <div class="col-md-12 text-center">
                                    <button type="submit" id="submit_btn_assign" class="btn btn-success" style="display:none">{{ __('common.Submit') }}</button>
                                </div>
                                @endif
                            </div>
                        </form>
                        <form action="{{ url('assign_book') }}" method="post">
                            @csrf
                            <input type="hidden" id="library_book_id" name="library_book_id"
                                value="{{ $data['id'] ?? '' }}">
                            <div class="row m-2" id="new_students" style="display: none;">
                                <div class="col-md-3 ">
                                    <label style="color:red;">First Name*</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                        placeholder="First Name" id="first_name" name="first_name"
                                        value="{{old('first_name')}}" required>
                                    @error('first_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-3 ">
                                    <label style="color:red;">Last Name*</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" placeholder="Last Name" id="last_name" name="last_name" value="{{old('last_name')}}" required>
                                    @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-3 ">
                                    <label style="color:red;">Mobile No.*</label>
                                    <input type="text" class="form-control @error('mobile') is-invalid @enderror"
                                        id="mobile" name="mobile" placeholder="Mobile No." value="{{old('mobile')}}"
                                        maxlength="10" onkeypress="javascript:return isNumber(event)" required>
                                    @error('mobile')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-3 email">
                                    <label style="color:red;">Email*</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        placeholder="Email" id="email" name="email" value="{{old('email')}}" required>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-md-3 father_name">
                                    <label>Father Name</label>
                                    <input type="text" class="form-control" placeholder="Father Name" id="father_name"
                                        name="father_name" value="{{old('father_name')}}">
                                </div>

                                <div class="col-md-3 gender_id">
                                    <label>Gender</label>
                                    <select class="form-control" id="gender_id" name="gender_id">
                                        <option value="">Select</option>
                                        @if(!empty($getgenders))
                                        @foreach($getgenders as $value)
                                        <option value="{{ $value->id}}" {{ ($value->id == old('gender_id')) ? 'selected'
                                            : '' }}>{{ $value->name ?? '' }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3 address">
                                    <label> Address</label>
                                    <textarea type="text" class="form-control" id="address" name="address" placeholder="Address">{{old('address')}}</textarea>
                                </div>
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </div>
                        </form>
                        
                         <!-- end assign Book--->
                         
                          <!-- return_student Book--->
                        <div class="row m-2 " id="return_student">
                            @php
                            if(!empty($data['id'])){
                           
                            $return_book = DB::table('assign_books')
                            
                            ->leftjoin('library_assign', 'library_assign.id', '=', 'assign_books.library_assign_id')
                            ->leftjoin('admissions', 'admissions.id', '=', 'library_assign.admission_id')
                            ->leftjoin('library_books', 'library_books.id', '=', 'assign_books.library_book_id')
                            ->select('assign_books.*','library_books.mrp as book_mrp','admissions.first_name','admissions.mobile')
                            ->where('assign_books.library_book_id',$data['id'])
                            ->where('assign_books.status',0)->get();
                            
                       }
                            @endphp
                             <div class="col-md-12" style="overflow-x:auto;">
                             <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                                    <thead>
                                        <tr role="row">
                                            <th>Id</th>
                                            <th>Student Name</th>
                                             <th>Student Mobile</th>
                                            <th>Book Name</th>
                                            
                                            <th>Brand </th>
                                           
                                        </tr>
                                    </thead>
                                    <tbody id="">
                                        
                                            @if(!empty($return_book))
                                            
                                            @php
                                            
                                                $i=1;
                                            
                                            @endphp
                                         
                                            @foreach ($return_book  as $item)
                                            <tr id="remove_{{$item->id }}" class =" " style="cursor:pointer; " onclick="returnInvoice('{{ $item->id }}','{{$item->book_mrp }}','{{ $item->first_name ?? '' }}');">
                                            
                                            <td>{{ $i++}}</td>
                                            <td>{{ $item->first_name ?? '' }} </td>
                                            <td>{{ $item->mobile ?? '' }} </td>
                                            <td>{{ $data['name'] ?? '' }}</td>
                                            <td>{{ $data['brand'] ?? '' }}</td>
                                           </tr>
                                            @endforeach
                                           
                                            @endif
                                        
                                        
                                    </tbody>
                                </table>
                        </div>
                         <!-- end return_student Book--->
                         
                        <div class="row m-2 d-none" id="returnInvoice" >
                                <div class="col-md-3 ">
                                    <label >Book MRP</label>
                                    <input type="hidden"   id="assign_book_id" name="assign_book_id"  readonly>
                                    <input type="text" class="form-control "  id="book_mrp" name="book_mrp"  readonly>
                                </div>
                                <div class="col-md-3 ">
                                    <label>Student Name</label>
                                    <input type="text" class="form-control" placeholder="Student Name" id="students_name" name="students_name" readonly>
                                </div>
                                <div class="col-md-3 ">
                                    <label>Invoice Amount</label>
                                    <input type="text" class="form-control" placeholder="Invoice Amount" id="amount" name="amount" >
                                </div>
                               
                               
                               
                                <div class="col-md-3 mt-4">
                                    
                                    <button  class="btn btn-success" onclick="SubmitValue()">Submit</button>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<input type="hidden" id="book_stock" name="book_stock" value="{{ $data['quantity'] ?? '' }}">
<script type="text/javascript">
    var count = 0;
    function book_count() {
        count++;
        var book_stock = $('#book_stock').val();
        if (count > book_stock) {
            alert('book stock not available');
            return false;
        }

    }
    
        function SubmitValue() {
           var student_name = $('#student_name').val();
           var amount = $('#amount').val();
           var assign_book_id = $('#assign_book_id').val();
           
           $.ajax({
                headers: { 'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content') },
                type: 'post',
                url: '/return_invoice_book',
                data: { assign_book_id: assign_book_id, amount: amount, student_name: student_name},
                // dataType: 'json',
                success: function (data) {
               
                $("#remove_"+assign_book_id).addClass('d-none');
                 $('#student_name').val('');
                 $('#amount').val('');
                  $('#assign_book_id').val('');
                toastr.success('Return Book');
                $("#returnInvoice").addClass('d-none');  
                }
            });
        
    }    

    function SearchValue() {
        var name = $('#student_name').val();
        var stu_qr_code = $('#stu_qr_code').val();
        var class_type_id = $('#class_type_id').val();
        $.ajax({
            headers: { 'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content') },
            type: 'post',
            url: '/search_student_assign_book',
            data: { name: name, class_type_id: class_type_id,stu_qr_code: stu_qr_code },
            // dataType: 'json',
            success: function (data) {

                $('#student_list').html('');
                $('#student_list').html(data);
            }
        });
    }
    function SearchValuelibrary() {

        var name = $('#library_name').val();
        var li_stu_qr_code = $('#li_stu_qr_code').val();
        $.ajax({
            headers: { 'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content') },
            type: 'post',
            url: '/searchStudentlibrary',
            data: { name: name, li_stu_qr_code: li_stu_qr_code },
            // dataType: 'json',
            success: function (data) {
                $('#library_student_list').html('');
                $('#library_student_list').html(data);
                // var checked_id = 'chcek_0';
                var checkbox = $('input[data-check_id="check_0"]');
                checkbox.prop('checked', true);
                $('#submit_btn_assign').show();
            }
        });
    }
    $("#assignBook").click(function () {
        $("#searchStudent,#assign_student").removeClass('d-none');
        $("#return_student").addClass('d-none');
        $("#returnInvoice").addClass('d-none');
        $("#assignBook").addClass('active');
        $("#returnBook").removeClass('active');
       
    });
    $("#returnBook").click(function () {
        $("#return_student").removeClass('d-none');
         $("#assign_student").addClass('d-none');
         $("#returnBook").addClass('active');
        $("#assignBook").removeClass('active');
         $("#new_students").hide();
        $("#library_student").hide();
        $("#school_student_serach").hide();
        $("#return_student").show();
    });
    $("#school_student").click(function () {
        $("#school_student_serach").show();
        $("#library_student").hide();
        $("#new_students").hide();
        $("#school_student").addClass('active');
        $(".Submit").removeClass('d-none');
    });

    $("#student_library").click(function () {
        $("#library_student").show();
        $("#school_student_serach").hide();
        $("#new_students").hide();
        $("#return_student").hide();
        $("#student_library").addClass('active');
        $(".Submit").removeClass('d-none');
        $("#returnInvoice").addClass('d-none');
    });
    $("#student_new").click(function () {
        $("#new_students").show();
        $("#library_student").hide();
        $("#school_student_serach").hide();
        $("#student_new").addClass('active');
        $(".Submit").removeClass('d-none');
    });
   
   
   function returnInvoice(assign_book_id,book_mrp,students_name) {
      $("#returnInvoice").removeClass('d-none');
      $('#assign_book_id').val(assign_book_id);
      $('#book_mrp').val(book_mrp);
      $('#students_name').val(students_name);     
    }
    
    
        
</script>

<script>
  const form = document.getElementById('block_enter');
  form.addEventListener('keypress', function(e) {
    if (e.keyCode === 13) {
      e.preventDefault();
    }
  });
</script>
<style>
    .active{
     color: #fff !important;
background-color: #6639b5 !important;
border-color: #6639b5 !important;
    }
</style>
@endsection