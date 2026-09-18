@extends('layout.app')
@section('content')

@php
    use SimpleSoftwareIO\QrCode\Facades\QrCode;
    $classType = Helper::classType();
@endphp
 <div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
        <div class="card card-outline card-orange">
         <div class="card-header bg-primary flex_items_toggel">
          <h3 class="card-title"><i class="fa fa-calendar-check-o"></i> &nbsp;{{ __(' Student Qr Code') }}</h3>
        <div class="card-tools">
            
            <a href="{{url('qrcode_Dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}</a>
        </div> 
        </div>         
      <div class="row m-2">

            <form id="quickForm" action="{{ url('qrcode_student') }}" method="post">
              @csrf
              <div class="row ">

                <div class="col-md-3">
                  <div class="form-group">
                    <label for="State" class="required">Ad. No.</label>
                     <input type="text" class="form-control" id="admissionNo" name="admissionNo" placeholder="Ad. No." value="{{ $search['admissionNo'] ?? '' }}">
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>{{ __('common.Class') }}</label>
                    <select class="form-control select2" id="class_type_id" name="class_type_id">
                      <option value='' >{{ __('common.Select') }}</option>
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
                    <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('common.Ex. Name, Mobile, Email, Aadhaar etc.') }}" value="{{ $search['name'] ?? '' }}">
                  </div>
                </div>
                
              
                
                <div class="col-md-1 ">
                 <div class="Display_none_mobile">
                     <label class="text-white">{{ __('common.Search') }}</label> 
                 </div>
                  <button type="submit" class="btn btn-primary">{{ __('common.Search') }}</button>
                </div>
              
              </div>
            </form>
            
        </div>
        
        <form action="{{ url('staff_attendance_add') }}" method="post">
                @csrf 
                <div class="row m-2"> 
                <div class="col-md-12">
                <div class="row mb-3">
                    <div class="col-md-12">
                         <a id="downloadQrCodes" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#qrCodeModal">
                                                    Download Selected QR Codes
                                                </a>
                    </div>
                    
                </div>
                  <table id="example11" class="table table-bordered table-striped border  dataTable dtr-inline padding_table">
                     
                  <thead>
                  <tr role="row" class="text-center">
                            <th style="width:100px;">Select &nbsp &nbsp<input type="checkbox" id="qrCode" name="qrCode" value="qrCode"></th>
                            <th class="d-none ">{{ __('common.SR.NO') }}</th>
                            <th>{{ __('common.Name') }}</th>
                            <th >{{ __('common.Mobile No.') }}</th>
                            <th >{{ __('Qr Code') }}</th>
                        </tr>
        
                    </thead>
                    <tbody class="student_list_show">
                                @if(!empty($data))
                                    @php $i = 1; @endphp
                                    @foreach ($data as $item)
                                        <tr class="text-center">
                                            <td><input type="checkbox" id="qrCode" name="admission_id[]" value="{{ $item['id'] ?? '' }}"></td>
                                            <td  class="d-none qr-code-data">{{ $i++ }}</td>
                                            <td >{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                                            <td>{{ $item['mobile'] ?? '' }}</td>
                                            <td style="text-align: center; padding: 8px;">
                                                @php
                                                      $qrCode = QrCode::size(75)->generate('student/' . $item['id']);
                                                      $qrCodeData = base64_encode($qrCode);
                                                @endphp
                                                  <span class=" d-none" >{{$qrCodeData}}</span>
                                                  <span class="qr-code-name d-none" >{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</span>
                                                {{ $qrCode }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" style="text-align: center;">No data available</td>
                                    </tr>
                                @endif
                            </tbody>
                       
                </table>
                </div>
                
                 </div>
            </form>                  
    </div>
</div>
</div>
</div>
</section>
        
</div>

<!-- Global Loading Animation (Outside Modal) -->
<div id="globalLoadingIndicator" class="text-center" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 99999;width:100%;height:100%;background-color: rgb(9 16 33 / 80%);color:white;">
  
      
  
    <div class="spinner-border text-primary" role="status" style="position:absolute;top:50%">
        <span class="sr-only">Loading...</span>
    </div>
    <p  style="position:absolute;top:55%;text-align:center;width:100%;">Download will start shortly, please wait...</p>
    
</div>
<div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
           
            <div class="modal-header">
                <div class="container">
                    
                
                <div class="row">
                    <div class="col-md-5">
                        <button type="button" class="btn btn-primary btn-sm" id="btnDownloadqrCode">
                  <i class="fa fa-download"></i> Download QR Codes
                </button>
                    </div>
                    <div class="col-md-7">
                        <h5 class="modal-title" id="qrCodeModalLabel">Student QR Codes</h5>
                    </div>
                </div>
                 
                   </div>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- QR Codes will be inserted here -->
                <div class="container">
                   <div class="row" id="download-qr">
                       
                   </div>
                </div>
            </div>
            
        </div>
    </div>
</div>



<script>
$(document).ready(function () {

  $('#qrCode').change(function () {
    var isChecked = $(this).prop('checked');
    $('input[name="admission_id[]"]').prop('checked', isChecked);
  });

 
  $('input[name="admission_id[]"]').change(function () {
    var allChecked = $('input[name="admission_id[]"]').length === $('input[name="admission_id[]"]:checked').length;
    $('#qrCode').prop('checked', allChecked);
  });

 
  $('#downloadQrCodes').click(function () {
    var selectedIds = [];
    $('input[name="admission_id[]"]:checked').each(function () {
        selectedIds.push($(this).val().trim());
    });

    if (selectedIds.length > 0) {
        $.ajax({
            url: '/student_attendence_qr_download',
            method: 'POST',
            data: {
                ids: selectedIds,
                _token: $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (response) {
                // Insert the returned HTML (QR codes) into the modal body
                $('#download-qr').html(response.html);
                $('#qrCodeModal').modal('show');
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert('An error occurred while processing your request.');
            },
        });
    } else {
        alert('Please select at least one QR Code.');
    }
});


 
});

</script>

 
<script>
    document.getElementById('btnDownloadqrCode').addEventListener('click', function () {
        const modalContent = document.querySelector('#qrCodeModal .modal-body');
        
        // Show global loading indicator
        document.getElementById('globalLoadingIndicator').style.display = 'block';

        // Use html2canvas to capture the content as an image
        html2canvas(modalContent).then(canvas => {
            // Convert the canvas to a data URL
            const imgData = canvas.toDataURL('image/png');

            // Create a PDF using jsPDF
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF();

            const imgWidth = 200; // Width of the PDF
            const pageHeight = 285; // Height of the PDF
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;
            let position = 0;

            // Add image to PDF
            pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight; // Adjust the position
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            // Save the PDF
            pdf.save('QR_Codes.pdf');

            // Hide global loading indicator after the download starts
            document.getElementById('globalLoadingIndicator').style.display = 'none';
        }).catch(function (error) {
            // Handle errors in html2canvas
            console.error('Error generating canvas:', error);
            alert('An error occurred while generating the PDF.');
            document.getElementById('globalLoadingIndicator').style.display = 'none';
        });
    });
</script>
   <script>
    $(document).ready(function () {
        const table = $('#example11').DataTable({
            pageLength: 10, // Default rows per page
        });

        // Update table page length when rows per page selection changes
        $('#rowsPerPage').change(function () {
            const rows = $(this).val();
            table.page.len(rows).draw();
        });
    });
</script>
 
<script src="public/assets/school/js/html2canvas.min.js"></script>
<script src="public/assets/school/js/jspdf.umd.min.js"></script>

@endsection
                    