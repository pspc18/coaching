@php
  $classType = Helper::ClassType();
  $getsubject = Helper::getSubject();
@endphp
@extends('layout.app')
@section('content')

<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
                     <div class="card-header bg-primary flex_items_toggel">
                    <h3 class="card-title"><i class="nav-icon fas fa fa-tag"></i> &nbsp;Edit :: {{$data->name ?? ''}} </h3>
                    <div class="card-tools">
                    <a href="{{url('view/exam')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> <span class="Display_none_mobile">{{ __('messages.Back') }}</span>  </a>
                    </div>
                    
                    </div>        
                    <form id="quickForm" action="{{ url('assign/exam') }}/{{$data->id ?? ''}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row m-2">

                           <div class="col-md-3 col-6">
                			<div class="form-group">
                				<label style="color:red;">{{ __('messages.Class') }}*</label>
                				<select class="form-control @error('class_type_id') is-invalid @enderror" id="class_type_id" name="class_type_id" value="{{old('class_type_id')}}">
                                <option value="" >{{ __('messages.Select') }}</option>
                                 @if(!empty($classType)) 
                                      @foreach($classType as $type)
                                         <option value="{{ $type->id ?? ''  }}" {{ ($type->id == old('class_type_id')) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                      @endforeach
                                  @endif
                                </select>
                                 @error('class_type_id')
                					<span class="invalid-feedback" role="alert">
                						<strong>{{ $message }}</strong>
                					</span>
                				@enderror
                		    </div>
                		    
                		</div>
                           <div class="col-md-3 col-6">
        		                <div class="form-group">
        		                    <label style="color:red;">Exam Date*</label>
                                    <input type="date" class="form-control @error('exam_date') is-invalid @enderror" name="exam_date" value="{{ old('exam_date') }}">
                                    @error('exam_date')
                					<span class="invalid-feedback" role="alert">
                						<strong>{{ $message }}</strong>
                					</span>
                				@enderror
                                </div>
                           </div>
                           <div class="col-md-6 col-12">
                                <label class="Display_none_mobile">&nbsp;</label>
                                <div class="d-flex flex-wrap" style="gap:10px; margin-top: 5px;">
                                    <button type="submit" name="submit_action" value="single" class="btn btn-primary">
                                        {{ __('examination.Assign') }}
                                    </button>
                                </div>
        		                <input type="hidden" name="id" value="{{$data->id ?? ''}}" />
                           </div>
    		            </div>
                    </form>

                    <div class="row m-2 mt-0">
                        <div class="col-12 col-lg-6">
                            <div class="border rounded p-3 bg-light">
                                <form id="applyDateAllForm" action="{{ url('assign/exam') }}/{{$data->id ?? ''}}" method="post">
                                    @csrf
                                    <input type="hidden" name="submit_action" value="global">
                                    <input type="hidden" name="id" value="{{$data->id ?? ''}}" />
                                    <div class="row">
                                        <div class="col-md-8 col-12">
                                            <div class="form-group mb-2">
                                                <label style="color:red;">Apply Date To All*</label>
                                                <input type="date" class="form-control @error('exam_date') is-invalid @enderror" name="exam_date" value="{{ old('exam_date') }}">
                                                @error('exam_date')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12 d-flex align-items-end">
                                            <button type="button" class="btn btn-warning btn-block mb-2" data-toggle="modal" data-target="#applyAllConfirmModal">
                                                Apply Date To All
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <small class="text-muted d-block">
                                    Fill the date once, then apply it to all assigned classes for this exam.
                                </small>
                            </div>
                        </div>
                    </div>

                <div class="row m-3 pb-2">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <td>Sr No.</td>
                                <td>Class</td>
                                <td>Exam Date</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($AssignExam))
                            @php
                            $i = 1;
                            @endphp
                            @foreach($AssignExam as $item)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{ $item->class_name}}</td>
                                <td>
                                    <form action="{{ url('assign/exam/'.$data->id) }}" method="post" class="d-flex align-items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="submit_action" value="single">
                                        <input type="hidden" name="assign_id" value="{{ $item->id ?? '' }}">
                                        <input type="hidden" name="class_type_id" value="{{ $item->class_type_id ?? '' }}">
                                        <input type="date" name="exam_date" class="form-control form-control-sm" value="{{ old('exam_date', !empty($item->exam_date) ? \Carbon\Carbon::parse($item->exam_date)->format('Y-m-d') : '') }}">
                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                    </form>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger deleteAssign" data-assign_id="{{$item->id ?? ''}}" data-toggle="modal" data-target="#deleteModal">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <div class="modal fade" id="deleteModal">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                              <h4 class="modal-title">Delete Conformation</h4>
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <form action="{{ url('assign/delete/exam') }}" method="post">
                                @csrf
                            <div class="modal-body">
                            <input type="hidden" id="exam_id" name="exam_id" value="{{$data->id ?? ''}}">
                            <input type="hidden" id="assign_id" name="assign_id">
                             Are You Sure ?
                            </div>
                            
                            <div class="modal-footer">
                              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Sumbit</button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="applyAllConfirmModal">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                              <h4 class="modal-title">Confirm Apply To All</h4>
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                This will overwrite the exam date for all assigned classes. Do you want to continue?
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                              <button type="submit" form="applyDateAllForm" class="btn btn-primary">Yes, Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div>
          </div>
        </div>
</section>
</div>


<script>
    $(document).ready(function(){
        $('.deleteAssign').click(function(){
           var assign_id = $(this).data('assign_id');
           $('#assign_id').val(assign_id);
        });
    })
</script>


@endsection
