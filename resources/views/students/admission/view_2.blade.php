@php
$classType = Helper::classType();
$getState = Helper::getState();
$getcitie = Helper::getCity();

$getCountry = Helper::getCountry();

@endphp
@extends('layout.app')
@section('content')

<div class="content-wrapper">

  <section class="content pt-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12 col-md-12">
          <div class="card card-outline card-orange">
            <div class="card-header bg-primary flex_items_toggel">
              <h3 class="card-title"><i class="fa fa-address-book-o"></i> &nbsp;{{ __('student.View Students Admission') }}</h3>
              <div class="card-tools">
                <a href="{{url('admissionAdd')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(3)->add ? '' : 'd-none' }} "><i class="fa fa-plus"></i><span class="Display_none_mobile">{{ __('common.Add') }} </span></a>
                <a href="{{url('studentsDashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i><span class="Display_none_mobile">{{ __('common.Back') }} </span></a>
              </div>

            </div>
            
            <form id="quickForm" action="{{ url('bulkIdPrint') }}" target='_blank'method="post">
              @csrf
              <div class="row m-2">

                <div class="col-md-2">
                  <div class="form-group">
                    <label for="State" class="required">{{ __('student.Admission No.') }}</label>
                     <input type="text" class="form-control" id="admissionNo" name="admissionNo" placeholder="{{ __('student.Admission No.') }}" value="{{ $search['admissionNo'] ?? '' }}">
                  </div>
                </div>
                <div class="col-md-2 d-none">
                  <div class="form-group">
                    <label for="State" class="required">{{ __('common.State') }}</label>
                    <select class="form-control select2" id="state_id" name="state_id">
                      <option value="" >{{ __('common.Select') }}</option>
                      @if(!empty($getState))
                      @foreach($getState as $state)
                      <option value="{{ $state->id ?? ''}}" {{ ($state->id == $search['state_id']) ? 'selected' : '' }}>{{ $state->name ?? ''}}</option>
                      @endforeach
                      @endif

                    </select>
                  </div>
                </div>
                <div class="col-md-2 d-none">
                  <div class="form-group">
                    <label for="City">{{ __('common.City') }}</label>
                    <select class="form-control select2" name="city_id" id="city_id">
                      <option value="">{{ __('common.Select') }}</option>
                      @if(!empty($getcitie))
                      @foreach($getcitie as $city)
                     <option value="{{ $city->id ?? ''}}" {{ ($city->id == $search['city_id']) ? 'selected' : '' }}>{{ $city->name ?? ''}} {{ $city->id ?? ''}}</option>

                      @endforeach
                      @endif
                    </select>
                  </div>
                </div>
                
                <div class="col-md-2">
                  <div class="form-group">
                    <label>{{ __('common.Class') }}</label>
                    <select class="select2 form-control" id="class_type_id1" name="class_type_id">
                      <option value="" selected>{{ __('common.Select') }}</option>
                      @if(!empty($classType))
                      @foreach($classType as $type)
                      <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['class_type_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>{{ __('common.Search By Keywords') }}</label>
                    <input type="text" class="form-control" id="name_search" name="name" placeholder="{{ __('Ex. Student Name,Father Name, Mobile') }}" value="{{ $search['name'] ?? '' }}">
                  </div>
                </div>
                <div class="col-md-2 ">
                <label class="text-white">{{ __('common.Generate') }}</label>
                 <button type="submit" class="btn btn-primary" id="generate_ids">{{ __('Generate Bulk Ids') }}</button>
               </div>

              </div>
            </form>

                <div class="row m-2">
              <div class="col-12" style="overflow-x:scroll;">
                <table id="studentList" class="table table-bordered table-striped dataTable dtr-inline ">
                  <thead>
                    <tr role="row">
                      <th>{{ __('common.SR.NO') }}</th>
                      <th class="text-center">Image</th>
                      <th class="text-center">{{ __('student.Ad. No') }}</th>
                      <th>{{ __('common.Name') }}</th>
                      <th>{{ __('common.F Name') }}</th>
                      <th>{{ __('common.M Name') }}</th>
                      <th>{{ __('common.Class') }}</th>
                      <th>{{ __('common.Mobile') }}</th>
                      <!--<th>{{ __('student.Ad. Type') }}</th>-->
                      <th>{{ __('student.Ad. Date') }}</th>
                      <th>{{ __('common.Action') }}</th>
                    </tr>
                  </thead>
                  <tbody id="product_list_show">

              
                  </tbody>
                </table>
              </div>
            </div>

            </div>
            </div>
            </div>
            </div>
            </section>
    </div>

            <!-- The Modal -->
            <div class="modal" id="Modal_id">
              <div class="modal-dialog">
                <div class="modal-content" style="background: #555b5beb;">

                  <!-- Modal Header -->
                  <div class="modal-header">
                    <h4 class="modal-title text-white">Delete Confirmation</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                  </div>

                  <!-- Modal body -->
                  <form action="{{ url('admissionDelete') }}" method="post">
                    @csrf
                    <div class="modal-body">



                      <input type="hidden" id="delete_id" name="delete_id">
                      <h5 class="text-white">Are you sure you want to delete ?</h5>

                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
                      <button type="submit" class="btn btn-danger waves-effect waves-light">Delete</button>
                    </div>
                  </form>

                </div>
              </div>
            </div>


        <div class="modal fade" id="myModal">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-body">
                        <form id="quickForm" action="{{ url('admissionView') }}" method="post">
                      @csrf
                      <div class="row m-2">
        
                        <div class="col-md-2">
                          <div class="form-group">
                            <label for="State" class="required">Admission No.</label>
                             <input type="text" class="form-control" id="admissionNo" name="admissionNo" placeholder="Admission No." value="{{ $search['admissionNo'] ?? '' }}">
                          </div>
                        </div>
                        <div class="col-md-2 col-6">
                          <div class="form-group">
                            <label for="State" class="required">{{ __('messages.State') }}</label>
                            <select class="form-control select2" id="state_id" name="state_id">
                              <option value="">{{ __('messages.Select') }}</option>
                              @if(!empty($getState))
                              @foreach($getState as $state)
                              <option value="{{ $state->id ?? ''}}" {{ ($state->id == $search['state_id']) ? 'selected' : '' }}>{{ $state->name ?? ''}}</option>
                              @endforeach
                              @endif
        
                            </select>
                          </div>
                        </div>
                        <div class="col-md-2 col-6">
                          <div class="form-group">
                            <label for="City">{{ __('messages.City') }}</label>
                            <select class="form-control select2" name="city_id" id="city_id">
                              <option value="">{{ __('messages.Select') }}</option>
                              @if(!empty($getCity))
                              @foreach($getCity as $cities)
                              <option value="{{ $cities->id ?? ''}}" {{ ($cities->id == $search['city_id']) ? 'selected' : '' }}>{{ $cities->name ?? ''  }}</option>
                              @endforeach
                              @endif
                            </select>
                          </div>
                        </div>
                        @if(Session::get('role_id') == 1)
                        <div class="col-md-2 col-6">
                          <div class="form-group">
                            <label>{{ __('messages.Class') }}</label>
                            <select class="form-control select2" id="class_type_id" name="class_type_id">
                              <option value="">{{ __('messages.Select') }}</option>
                              @if(!empty($classType))
                              @foreach($classType as $type)
                              <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['class_type_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                              @endforeach
                              @endif
                            </select>
                          </div>
                        </div>
                       
                        @endif
                        <div class="col-md-3">
                          <div class="form-group">
                            <label>{{ __('messages.Search By Keywords') }}</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('messages.Ex. Name, Mobile, Email, Aadhaar etc.') }}" value="{{ $search['name'] ?? '' }}">
                          </div>
                        </div>
                        <div class="col-md-1 text-center">
                          <button type="submit" class="btn btn-primary">{{ __('messages.Search') }}</button>
                        </div>
        
                      </div>
                    </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
   
        
<div id="profileImgModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">

    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <img id="profileImg" src="" width="100%" height="100%">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>        
        
            <script>

              $('#generate_ids').hide();
              $('#product_list_show').on('click', '.deleteData', function() {
                var delete_id = $(this).data('id');

                $('#delete_id').val(delete_id);
              });
              
                $('#product_list_show').on('click', '.profileImg', function() {
                    var profileImgUrl = $(this).data('img');
                    if(profileImgUrl != ''){
                        $('#profileImgModal').modal('show');
                        $('#profileImg').attr('src',profileImgUrl);
                    }
                });
                
                
                
                $( document ).ready(function() {
    $("#studentList").DataTable({
                  "lengthChange": false, "autoWidth": false,"lengthChange": true, // Default number of rows per page
                "lengthMenu": [10, 20, 50,100] ,
                 "buttons": ["copy", "csv", "excel", "pdf", "print"]
                }).buttons().container().appendTo('#studentList_wrapper .col-md-6:eq(0)');
});
             
              
            </script>
            <style>
                .profileImg {
                    width:50px;
                    height:50px;
                    border-radius:50%;
                }
              .card-header .nav-pills .nav-link {
                color: #db5b06;
              }
              
           
    .new-data {
        display: none; /* Initially hide the newly appended data */
    }

            </style>
            
            
            <script>
            $( document ).ready(function() {
                
                
                
                
                   $('#name_search').on('keyup', function() {
                        $('#class_type_id1').val('')
                         $('#admissionNo').val('')
                          
                          $('#class_type_id1').trigger('change');
                       var searchString = $('#name_search').val().toLowerCase();
                       $('table tr td').each(function() {
                           
                           var cellText = $(this).text().toLowerCase();
    if (cellText.indexOf(searchString) !== -1) {
        $(this).closest('tr').show().siblings().hide();
        return false; 
    }
});

if(searchString == '')
{
    $('#product_list_show').html('');
}
 });
                   $('#admissionNo').on('keyup', function() {
                       $('#class_type_id1').val('')
                         $('#class_type_id1').trigger('change'); 
                   
                    
            });
             $('#class_type_id1').change(function(event) {
                  $('#studentList').DataTable().destroy();
                 var count =1;
                 var allStudents = @json($data);
                   var container = $('#product_list_show');
                   container.html('');
                   var class_type_id = $('#class_type_id1').val();
                   var admissionNo = $('#admissionNo').val();
                   
                  

                 allStudents.forEach(function(item,index) {
                     
                
            
               if(class_type_id != '' )
                     {

                if(parseInt(item.class_type_id) != parseInt(class_type_id))
                {
                 
return;
                }
               
                $('#generate_ids').show();
                     }
                     else{
                      $('#generate_ids').hide();
                     }
                     
                
                     if(admissionNo != '' )
                     {
                       
                           if(parseInt(admissionNo) != parseInt(item.admissionNo)  )
                     {
                          return;
                     }
                         
                     }
           
                if(item.image){
                    var image = "{{ env('IMAGE_SHOW_PATH').'profile/' }}" + item.image;
                    var dataimg = image;

                }else{
                    var image = "{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}";
                    var dataimg = '';
                }
             
                   var newData = $('<tr class="new-data">' +
    '<td>' + (count++) + '</td>' +
    '<td><img class="profileImg pointer" src="' + image + '" data-img="' + dataimg + '"></td>' +
    '<td>' + (item.admissionNo ?? '') + '</td>' +
    '<td>' + (item.first_name ?? '') + ' ' + (item.last_name ?? '') + '</td>' +
    '<td>' + (item.father_name ?? '')  + '</td>' +
    '<td>' + (item.mother_name ?? '')  + '</td>' +
    '<td>' + (item.class_name ?? '')  + '</td>' +
    '<td>' + (item.mobile ?? '')  + '</td>' +
    '<td>' + (item.admission_date ?? '')  + '</td>' +
    '<td>' +
        '<a class="btn btn-primary btn-xs" data-toggle="dropdown" title="Show Option"><i class="fa fa-bars"></i></a>' +
        '<ul class="dropdown-menu" style="">' +
            '<a href="{{ url('/') }}/admissionStudentPrint/'+item.id+'" target="blank">' +
                '<li class="dropdown-item text-success" title="Admission Form"><i class="fa fa-print text-success"></i> Ad. Print </li>' +
            '</a>' +
            '<a href="{{ url('/') }}/admissionStudentIdPrint/'+item.id+'" target="blank">' +
                '<li class="dropdown-item text-success" title="Admission Id"><i class="fa fa-credit-card text-success"></i> Id Print</li>' +
            '</a>' +
            '<a href="{{ url('/') }}/admissionEdit/'+item.id+'" target="_blank">' +
                '<li class="dropdown-item text-primary" title="Admission Edit" ><i class="fa fa-edit text-primary"></i> Edit</li>' +
            '</a>' +
            '<a href="javascript:;" data-id="'+item.id+'" data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData">' +
                '<li class="dropdown-item text-danger" title="Admission Delete"><i class="fa fa-trash-o text-danger"></i> Delete</li>' +
            '</a>' +
        '</ul>' +
        '<a href="{{ url('/') }}/studentDetail/'+item.id+'" class="btn btn-success btn-xs ml-3" target="_blank" title="Admission View"><i class="fa fa-eye"></i></a>' +
    '</td>' +
'</tr>');
      container.append(newData);
        var randomNumber = Math.floor(Math.random() * 700) + 1;
                   newData.fadeIn(randomNumber);
            });
            
            
           
            $('#studentList').DataTable({
                  "lengthChange": false, "autoWidth": false,"lengthChange": true, // Default number of rows per page
                "lengthMenu": [10, 20, 50,100] ,
                 "buttons": ["copy", "csv", "excel", "pdf", "print"]
                }).buttons().container().appendTo('#studentList_wrapper .col-md-6:eq(0)');

 });
 });


            </script>
            @endsection