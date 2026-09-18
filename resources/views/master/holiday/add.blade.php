



@extends('layout.app')
@section('content')


<div class="content-wrapper">
   

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-child"></i> &nbsp; Holiday</h3>
                    <div class="card-tools">
                    <a href="{{url('holiday/view')}}" class="btn btn-primary  btn-sm" title="View Holiday"><i class="fa fa-eye"></i> View </a>
                    </div>
                    
                    </div>        
                <form id="quickForm" action="{{ url('holiday/add') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row m-2">
        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="color:red;"> Name*</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{old('name')}}" placeholder="  Name">
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        </div>   
                              
                    <div class="row m-2 pb-2">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary ">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
</div>
</div>
</div>
</section>
</div>

<script>
//select all checkboxes
    $("#{{ $data->id ?? ''  }}").change(function () {  
        $(".checkbox").prop('checked', $(this).prop("checked")); 
    });

//".checkbox" change 
    $('.checkbox').change(function () {
        if (false == $(this).prop("checked")) { 
            $("#{{ $data->id ?? ''  }}").prop('checked', false);
        }
        if ($('.checkbox:checked').length == $('.checkbox').length) {
            $("#{{ $data->id ?? ''  }}").prop('checked', true);
        }
    });
</script>

@endsection