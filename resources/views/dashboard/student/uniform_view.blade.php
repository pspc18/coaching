@extends('layout.app') 
@section('content')

<div class="content-wrapper">

	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="nav-icon fa fa-calendar-plus-o"></i> &nbsp;{{ __('School Uniform')  }} </h3>
							<div class="card-tools"> 
							    <!--<a href="{{url('add/exam')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i> Add </a>--> 
                                	<a href="{{url('dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}  </a>
                            </div>
						</div>
						<div class="card-body">
                            <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                             <thead class="bg-primary">
                                 
                                 <tr>
                                     <th>Sr.No.</th>
                                     <th>Image</th>
                                     <th>Description</th>
                                     <th>Action</th>
                                 </tr>
                             </thead>
            
                              <tbody>
                                 
                                  @if(!empty($data))
                                  
                                  @foreach($data as $key => $item)
                                 <tr>
                                      <td width='5%'>{{$key+1 ?? ''}}</td>
                                        <td width='30%'> <img width="100%" src="{{env('IMAGE_SHOW_PATH')}}{{'uniform_image/'}}{{$item->uniform_image ?? '' }} " ></td>
                                        <td width='60%'> {!! html_entity_decode($item->description ?? '') !!}</td>
                                      <td width='5%'>
                                          <button type="button"  data-data="{{env('IMAGE_SHOW_PATH')}}{{'uniform_image/'}}{{$item->uniform_image ?? '' }}"
                                          class="btn btn-primary button1" data-toggle="modal" data-target="#descriptionModal">
                                              <i class="fa fa-eye" aria-hidden="true"></i>

                                                        </button>
                                          
                                        </td></tr>
                                      @endforeach
                                  @endif       
                                    
                              </tbody>
                              </table>
                        </div>
            </div>
        </div>
        </div>
        </div>
    </section>
        
</div>
<!-- Modal -->
<div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">School Uniform</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body  description_modal">
      <img id="modal_id"  width="100%"/>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        
      </div>
    </div>
  </div>
</div>

<script>
    $(".button1").click(function(){
 var value = $(this).data('data');

 $("#modal_id").attr("src",value);

});
</script>
@endsection