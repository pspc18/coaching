@extends('layout.app') 
@section('content')
    
<div class="content-wrapper">

	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">        
        <div class="card card-outline card-orange">
			<div class="card-header bg-primary flex_items_toggel">
				<h3 class="card-title"><i class="fa fa-clipboard"></i> &nbsp;{{ __('staff.Teacher Documents') }}</h3>
				<div class="card-tools"> <a href="{{url('staff_file')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> <span class="Display_none_mobile">{{ __('common.Back') }}  </span></a> </div>
			</div>            
          
                 <section class="content">
      <div class="container-fluid">
           <form id="quickForm" action="{{ url('teacher/image') }}" method="post" enctype="multipart/form-data">
        @csrf  
        <div class="row">
          <div class="col-12">
                <div class="card-body">
                <table id="example1" class="table table-bordered table-striped  dataTable dtr-inline padding_table">
                  <thead>
                  <tr role="row">
                    <th>{{ __('common.SR.NO') }}</th>
                    <th>{{ __('common.Name') }}</th>
                    <th>{{ __('common.Photo') }}</th>
                    <th>{{ __('staff.Experience Letter') }}</th>
                    <th>{{ __('staff.Id Proof') }}</th>
                    <th>{{ __('staff.Qualification Proof') }}</th>
                    </tr>
                  </thead>
                  <tbody>
                      
                      @if(!empty($data))
                        @php
                           $i=1
                        @endphp
                        
                        @foreach ($data  as $key=>$item)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                            <td>
                                @if($item['photo'] != null)
                                <a href="{{ env('IMAGE_SHOW_PATH').'profile/'.$item['photo']}}" target="blank">
                                <img src="{{ env('IMAGE_SHOW_PATH').'profile/'.$item['photo']}}" width="100px" height="100px" >
                                </a>
                                @else
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d1/Image_not_available.png/640px-Image_not_available.png" width="100px" height="100px" >
                                @endif
                            </td>
                            <td>
                                @if($item['experience_letter_img'] != null)
                                <a href="{{ env('IMAGE_SHOW_PATH').'teacher/teacher_'.$item['UniqueId'].'/experience_letter/'.$item['experience_letter_img'] }}" target="blank">
                                <img src="{{ env('IMAGE_SHOW_PATH').'teacher/teacher_'.$item['UniqueId'].'/experience_letter/'.$item['experience_letter_img'] }}" width="100px" height="100px" >
                                </a>
                                @else
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d1/Image_not_available.png/640px-Image_not_available.png" width="100px" height="100px" >
                                @endif
                            </td>
                            <td>
                                @if($item['Id_proof_img'] != null)
                                <a href="{{ env('IMAGE_SHOW_PATH').'teacher/teacher_'.$item['UniqueId'].'/id_proof/'.$item['Id_proof_img']}}" target="blank">
                                <img src="{{ env('IMAGE_SHOW_PATH').'teacher/teacher_'.$item['UniqueId'].'/id_proof/'.$item['Id_proof_img']}}" width="100px" height="100px" >
                                </a>
                                @else
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d1/Image_not_available.png/640px-Image_not_available.png" width="100px" height="100px" >
                                @endif
                            </td>
                            <td>
                                @if($item['qualification_proof_img'] != null)
                                <a href="{{ env('IMAGE_SHOW_PATH').'teacher/teacher_'.$item['UniqueId'].'/qualification_proof/'.$item['qualification_proof_img']}}" target="blank">
                                <img src="{{ env('IMAGE_SHOW_PATH').'teacher/teacher_'.$item['UniqueId'].'/qualification_proof/'.$item['qualification_proof_img']}}" width="100px" height="100px" >
                                </a>
                                @else
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d1/Image_not_available.png/640px-Image_not_available.png" width="100px" height="100px" >
                                @endif
                            </td>
                        </tr>
                      @endforeach
                @endif
            </tbody>
                  </table>
                  
              </div>
        </div>
        
      </div>
      
    </section>

</div>

</div>
</div>
</div>
</div>
</section>
</div>
<style>
     .padding_table thead tr{
    background: #002c54;
    color:white;
    }
        
    .padding_table th, .padding_table td{
         padding:5px;
         font-size:14px;
    }
</style>
@endsection