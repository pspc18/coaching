@extends('layout.app') 
@section('content')
<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-edit"></i> &nbsp; Edit Sports</h3>
							<div class="card-tools"> <a href="{{url('sports_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> Back</a> </div>
						</div>
						<form id="quickForm" action="{{ url('sports_edit') }}/{{($data->id)}}" method="post" enctype="multipart/form-data"> 
						    @csrf						
						
					
                           <!--      <label for="text" style="color: red;">&nbsp;&nbsp; Game* &nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp; </LABEL>&nbsp; &nbsp;&nbsp; &nbsp;
                              <input type="checkbox" id="cricket" name="cricket" value="cricket">
                              <label for="cricket">&nbsp; Cricket</label>&nbsp; &nbsp;&nbsp; &nbsp;
                              <input type="checkbox" id="hoki" name="hoki" value="hoki">
                              <label for="hoki">&nbsp; Hoki</label>&nbsp; &nbsp;&nbsp; &nbsp;
                              <input type="checkbox" id="badminton" name="badminton" value="badminton">
                              <label for="badminton">&nbsp; Badminton</label>&nbsp; &nbsp;&nbsp; &nbsp;
                              <input type="checkbox" id="tennis" name="tennis" value="tennis">
                              <label for="tennis">&nbsp; Tennis</label>&nbsp; &nbsp;&nbsp; &nbsp;
                              <input type="checkbox" id="swimming" name="swimming" value="swimming">
                              <label for="swimming">&nbsp; Swimming</label>&nbsp; &nbsp;&nbsp; &nbsp;
                              -->


								
								<div class="col-md-4">
									<div class="form-group">
										<label style="color: red;">For Class*</label>
										<select class="form-control  @error('for_class') is-invalid @enderror" id="for_class" name="for_class">
										    <option value="">Select</option>
                                             @if(!empty($classType)) 
                                                  @foreach($classType as $type)
                                                     <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                                                  @endforeach
                                              @endif
										</select>
                                        @error('for_class')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror										
								    </div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label style="color: red;">Photo*</label>
										<input class="form-control  @error('photo') is-invalid @enderror" type="file" id="photo" name="photo" placeholder="Sports Photo"> 
                                        @error('photo')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
								</div>
                        <div class="col-md-1">
                            @if(!empty($data['photo']))
                            <img src="{{ env('IMAGE_SHOW_PATH').'Sports/'.$data['photo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}'" width="60px" height="60px">
                            @else
                            <h1><i class="fa fa-users mt-4" aria-hidden="true"></i></h1>
                            @endif
                        </div>								
																
																<div class="col-md-12 text-center">
									<button type="submit" class="btn btn-primary">update</button>
								</div>
								</div>
					    </form>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection