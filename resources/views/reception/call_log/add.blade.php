@extends('layout.app')
@section('content')
<div class="content-wrapper">
  <section class="content pt-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12 col-md-12">
          <div class="card card-outline card-orange">
            <div class="card-header bg-primary">
              <h3 class="card-title"><i class="fa fa-address-book-o"></i> &nbsp; {{ __('Add Call Log') }}</h3>
              <div class="card-tools">
                <a href="{{ url('reception_file') }}" class="btn btn-primary btn-sm">
                  <i class="fa fa-arrow-left"></i> {{ __('common.Back') }}
                </a>
              </div>
            </div>

            <div class="card-body">
             
              <form action="{{ url('callLog/add') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="row m-2">

                  <div class="col-md-3">
                    <div class="form-group">
                      <label style="color:red;">{{ __('Call Type') }}*</label>
                      <select class="form-control @error('call_type') is-invalid @enderror" id="call_type" name="call_type">
                        <option value="">Select</option>
                        <option value="Outgoing" {{ old('call_type')=='Outgoing'?'selected':'' }}>Outgoing</option>
                        <option value="Incoming" {{ old('call_type')=='Incoming'?'selected':'' }}>Incoming</option>
                      </select>
                      @error('call_type')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                      @enderror
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label style="color:red;">{{ __('Calling Purpose') }}*</label>
                      <select class="form-control @error('calling_purpose_id') is-invalid @enderror select2"
                              id="calling_purpose_id"
                              name="calling_purpose_id">
                        <option value="">Select</option>
                        @foreach($callingPurposes ?? [] as $purpose)
                          <option value="{{ $purpose->id }}" {{ old('calling_purpose_id') == $purpose->id ? 'selected' : '' }}>
                              {{ $purpose->name }}
                          </option>
                        @endforeach
                      </select>
                      @error('calling_purpose_id')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                      @enderror
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label style="color:red;">{{ __('Name') }}*</label>
                      <input type="text" class="form-control @error('name') is-invalid @enderror"
                             id="name" name="name" placeholder="{{ __('Enter Name') }}" value="{{ old('name') }}">
                      @error('name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                      @enderror
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label style="color:red;">{{ __('Mobile No') }}*</label>
                      <input type="text" class="form-control @error('mobile_no') is-invalid @enderror"
                             id="mobile_no" name="mobile_no" maxlength="10" onkeypress="return isNumber(event)"
                             placeholder="{{ __('Enter Mobile Number') }}" value="{{ old('mobile_no') }}">
                      @error('mobile_no')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                      @enderror
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label style="color:red;">{{ __('Date') }}*</label>
                      <input type="date" class="form-control @error('date') is-invalid @enderror"
                             id="date" name="date" value="{{ old('date') }}">
                      @error('date')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                      @enderror
                    </div>
                  </div>

                  <div class="col-md-2">
                    <div class="form-group">
                      <label style="color:red;">{{ __('Start Time') }}*</label>
                      <input type="time" class="form-control @error('start_time') is-invalid @enderror"
                             id="start_time" name="start_time" value="{{ old('start_time') }}">
                      @error('start_time')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                      @enderror
                    </div>
                  </div>

                  <div class="col-md-2">
                    <div class="form-group">
                      <label style="color:red;">{{ __('End Time') }}*</label>
                      <input type="time" class="form-control @error('end_time') is-invalid @enderror"
                             id="end_time" name="end_time" value="{{ old('end_time') }}">
                      @error('end_time')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                      @enderror
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label>{{ __('Follow Up Date') }}</label>
                      <input type="date" class="form-control @error('follow_up_date') is-invalid @enderror"
                             id="follow_up_date" name="follow_up_date" value="{{ old('follow_up_date') }}">
                      @error('follow_up_date')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                      @enderror
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>{{ __('Note') }}</label>
                      <textarea class="form-control @error('note') is-invalid @enderror"
                                id="note" name="note" placeholder="{{ __('Note') }}" rows="2">{{ old('note') }}</textarea>
                      @error('note')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                      @enderror
                    </div>
                  </div>
                </div>

                <div class="row m-2">
                  <div class="col-md-12 text-center pb-2">
                    <button type="submit" class="btn btn-primary btn-submit">Submit</button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          {{-- Call Log Table --}}
<div class="card mt-4 card-outline card-orange">
  <div class="card-header bg-primary">
    <h3 class="card-title text-white">
      <i class="fa fa-list"></i> &nbsp; {{ __('Call Log List') }}
    </h3>
  </div>
  <div class="card-body">
    <table id="example1" class="table table-bordered table-striped dataTable dtr-inline">
      <thead class="bg-primary text-white">
        <tr>
          <th>{{ __('common.SR.NO') }}</th>
          <th>{{ __('Call Type') }}</th>
          <th>{{ __('Calling Purpose') }}</th>
          <th>{{ __('Name') }}</th>
          <th>{{ __('Mobile No') }}</th>
          <th>{{ __('Date') }}</th>
          <th>{{ __('Start Time') }}</th>
          <th>{{ __('End Time') }}</th>
          <th>{{ __('Follow Up Date') }}</th>
          <th>{{ __('Note') }}</th>
          <th>{{ __('Action') }}</th>
        </tr>
      </thead>
      <tbody>
        @php $i = 1; @endphp
        @forelse($callLogs as $log)
          <tr>
            <td>{{ $i++ }}</td>
            <td>
              @if($log->call_type == 'Outgoing')
                <span class="badge badge-success">{{ $log->call_type }}</span>
              @else
                <span class="badge badge-info">{{ $log->call_type }}</span>
              @endif
            </td>
            <td>{{ $log->callingPurpose->name ?? '-' }}</td>
            <td>{{ $log->name }}</td>
            <td>{{ $log->mobile_no }}</td>
            <td>{{ $log->date ? \Carbon\Carbon::parse($log->date)->format('d-M-Y') : '-' }}</td>
            <td>{{ $log->start_time }}</td>
            <td>{{ $log->end_time }}</td>
            <td>{{ $log->follow_up_date ? \Carbon\Carbon::parse($log->follow_up_date)->format('d-M-Y') : '-' }}</td>
            <td>{{ $log->note }}</td>
            <td>
                 <a href="javascript:;" data-id='{{$log->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData {{ Helper::permissioncheck(28)->delete ? '' : 'd-none' }}">
                                <button class="btn btn-danger btn-xs tooltip1" title1="Delete"><i class="fa fa-trash-o"></i></button>
                          </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="10" class="text-center text-danger">
              {{ __('No call logs found.') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>


        </div>
      </div>
    </div>
  </section>
</div>

<div class="modal" id="Modal_id">
              <div class="modal-dialog">
                <div class="modal-content" style="background: #555b5beb;">

                  <!-- Modal Header -->
                  <div class="modal-header">
                    <h4 class="modal-title text-white">Delete Confirmation</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                  </div>

                  <!-- Modal body -->
                  <form action="{{ url('callLogDelete') }}" method="post">
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
            
            
<script>
    $('.deleteData').click(function() {
        
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
  

</script>
@endsection

