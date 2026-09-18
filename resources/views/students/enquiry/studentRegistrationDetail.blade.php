@extends('layout.app')
@section('content')
<div class="content-wrapper">
   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">

          <!-- Left Side (Enquiry Details) -->
          <div class="col-12 col-md-4">
              <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary">
                  <h3 class="card-title mb-0">
                  <i class="fa fa-address-book-o"></i> &nbsp; {{ __('Enquiry Details') }}
                </h3>
              </div>
                <div class="card-body p-4">
                  @if(!empty($data))
                  <ul class="list-group list-group-flush">
                    @php
                        $statusColor = match($data->status) {
                            'Active' => 'badge bg-success',
                            'Partially Closed' => 'badge bg-warning text-dark',
                            'Missed' => 'badge bg-danger',
                            'Closed' => 'badge bg-secondary',
                            default => 'badge bg-light text-dark'
                        };
                    @endphp
                    <li class="list-group-item">
                      <strong>Status:</strong>
                      @php
                          $statusColor = match($data->latest_status) {
                              'Active' => 'badge bg-success',
                              'Partially Closed' => 'badge bg-warning text-dark',
                              'Missed' => 'badge bg-danger',
                              'Closed' => 'badge bg-secondary',
                              default => 'badge bg-light text-dark'
                          };
                      @endphp
                      <span class="{{ $statusColor }}">{{ $data->latest_status ?? '' }}</span>
                    </li>

                    <li class="list-group-item"><strong>Name:</strong> {{ $data['first_name'] ?? ''}}</li>
                    <li class="list-group-item"><strong>Class:</strong> {{ $data['ClassTypes']['name'] ?? '' }}</li>
                    <li class="list-group-item"><strong>Mobile:</strong> {{ $data['mobile'] ?? ''}}</li>
                    <li class="list-group-item"><strong>Email:</strong> {{ $data['email'] ?? ''}}</li>
                    <li class="list-group-item"><strong>Father's Name:</strong> {{ $data['father_name'] ?? ''}}</li>
                    <li class="list-group-item"><strong>Mother's Name:</strong> {{ $data['mother_name'] ?? ''}}</li>
                    <li class="list-group-item"><strong>DOB:</strong> {{ date('d-m-Y', strtotime($data['dob'])) ?? '' }}</li>
                    <li class="list-group-item"><strong>Registration Date:</strong> {{ date('d-m-Y', strtotime($data['registration_date'])) ?? '' }}</li>
                    <li class="list-group-item"><strong>Note:</strong> {{ $data['note'] ?? ''}}</li>
                  </ul>
                  @endif
                </div>
              </div>
          </div>

          <!-- Right Side -->
          <div class="col-12 col-md-8">

            <!-- Add Follow Up -->
            <div class="card card-outline card-orange mb-3">
              <!--<div class="card-header bg-primary">-->
              <!--  <h3 class="card-title mb-0"><i class="fa fa-address-book-o"></i> &nbsp;{{ __('Add Follow Up') }}</h3>-->
              <!--   <a href="{{url('enquiryView')}}" class="btn btn-primary  btn-sm leftbutons" ><i class="fa fa-arrow-left"></i><span class="Display_none_mobile">{{ __('common.View') }}</span></a>-->
              <!--   <a href="{{url('reception_file')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i><span class="Display_none_mobile">{{ __('common.Back') }}</span></a>-->
              <!--</div>-->
              <div class="card-header bg-primary d-flex justify-content-between align-items-center flex-wrap">
                  <h3 class="card-title mb-0 text-white d-flex align-items-center">
                    <i class="fa fa-address-book-o"></i>&nbsp;{{ __('Add Follow Up') }}
                  </h3>
                
                  <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <a href="{{ url('enquiryView') }}" class="btn text-light btn-sm fgfgfg">
                      <i class="fa fa-eye"></i>
                      <span class="d-none d-md-inline">{{ __('common.View') }}</span>
                    </a>
                    <a href="{{ url('reception_file') }}" class="btn text-light btn-sm">
                      <i class="fa fa-arrow-left"></i>
                      <span class="d-none d-md-inline">{{ __('common.Back') }}</span>
                    </a>
                  </div>
                </div>

              <form action="{{ url('enquiryFollowUpAdd/'.$data->id) }}" method="post">
                @csrf
                <div class="row m-2">
                    <div class="col-md-6">
                        <label>{{ __('Follow Up Date') }}</label>
                        <input type="date" class="form-control" name="follow_up_date" value="{{ old('follow_up_date', $data->follow_up_date ? $data->follow_up_date->format('Y-m-d') : date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label style="color:red;">{{ __('Next Follow Up Date') }}*</label>
                        <input type="date" class="form-control" name="next_follow_up_date" value="{{ old('next_follow_up_date', $data->next_follow_up_date ? $data->next_follow_up_date->format('Y-m-d') : '') }}">
                    </div>
                    <div class="col-md-12">
                        <label>{{ __('Response') }}</label>
                        <textarea class="form-control" name="response">{{ old('response', $data->response) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label style="color:red;">{{ __('Status') }}*</label>
                        <select class="form-control" name="status">
                            <option value="">{{ __('Select Status') }}</option>
                            <option value="Active" {{ old('status', $data->status) == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Partially Closed" {{ old('status', $data->status) == 'Partially Closed' ? 'selected' : '' }}>Partially Closed</option>
                            <option value="Missed" {{ old('status', $data->status) == 'Missed' ? 'selected' : '' }}>Missed</option>
                            <option value="Closed" {{ old('status', $data->status) == 'Closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label>{{ __('Note') }}</label>
                        <textarea class="form-control" name="note">{{ old('note', $data->note) }}</textarea>
                    </div>
                </div>
                <div class="row m-2">
                    <div class="col-md-12 text-center pb-2">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
              </form>
            </div>

            <!-- Follow Up List -->
            <div class="card card-outline card-orange">
              <div class="card-header bg-primary">
                <h3 class="card-title mb-0"><i class="fa fa-list"></i> &nbsp; {{ __('Follow Up List') }}</h3>
              </div>
              <div class="row m-2">
                <div class="col-12">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>SR.NO</th>
                        <th>Follow Up Date</th>
                        <th>Next Follow Up</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if(!empty($remark))
                        @php $i=1; @endphp
                        @foreach($remark as $item)
                          @php
                            $lines = explode("\n", $item->remark);
                            $response = $lines[0] ?? '';
                            $status = isset($lines[1]) ? str_replace('Status: ', '', $lines[1]) : '';
                            $nextFollowUp = isset($lines[2]) ? str_replace('Next Follow Up: ', '', $lines[2]) : '';
                          @endphp
                          <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $item->date ? date('d-m-Y', strtotime($item->date)) : '' }}</td>
                            <td>{{ $nextFollowUp ? date('d-m-Y', strtotime($nextFollowUp)) : '' }}</td>
                            <td>
                              <span class="badge 
                                @if($status=='Active') bg-success 
                                @elseif($status=='Partially Closed') bg-warning text-dark 
                                @elseif($status=='Missed') bg-danger 
                                @elseif($status=='Closed') bg-secondary 
                                @else bg-light text-dark @endif">
                                {{ $status }}
                              </span>
                            </td>
                            <td>
                                <a href="javascript:;"  
                                   data-id='{{$item->id}}' 
                                   data-bs-toggle="modal" 
                                   data-bs-target="#FollowUpDeleteModal"  
                                   class="deleteFollowup btn btn-danger btn-xs" 
                                   title="Delete Follow Up">
                                   <i class="fa fa-trash"></i>
                                </a>
                            </td>

                          </tr>
                        @endforeach
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>
      
      <!-- Follow Up Delete Modal -->
<div class="modal" id="FollowUpDeleteModal">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">
      <div class="modal-header">
        <h4 class="modal-title text-white">Delete Confirmation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal">
            <i class="fa fa-times" aria-hidden="true"></i>
        </button>
      </div>

      <form action="{{ url('followupDelete') }}" method="post">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="followup_delete_id" name="delete_id">
          <h5 class="text-white">Are you sure you want to delete this Follow Up?</h5>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

    </section>
</div>

<style>
    .fgfgfg{
        margin-left:60vh;
    }
    
}

</style>

<script>
    $(document).on('click', '.deleteFollowup', function(){
        var delete_id = $(this).data('id');
        $('#followup_delete_id').val(delete_id);
    });
</script>

@endsection
