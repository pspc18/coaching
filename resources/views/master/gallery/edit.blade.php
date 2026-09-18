@php

@endphp
@extends('layout.app')
@section('content')

<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-image"></i> {{ __('master.Edit Gallery') }}</h3>
                    <div class="card-tools">
                    <a href="{{url('gallery_view')}}" class="btn btn-primary  btn-sm" title="View Gallery"><i class="fa fa-eye"></i> {{ __('common.View') }} </a>
                    <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}</a>
                    </div>
                    
                    </div>        
                <form id="form-submit-edit" action="{{ url('gallery_edit') }}/{{$data['id']}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row m-2" id="box2">
        <div class="col-md-2 ">
                          <div class="form-group">
                        <label class="text-danger">{{ __('Type') }}*</label>
                        <select class="select2 form-control @error('type') is-invalid @enderror" id="type" name="type">
                            <option value="">{{ __('messages.Select') }}</option>
                            <option value="gallery" {{ old('type', $data->type ?? '') === 'gallery' ? 'selected' : '' }}> Gallery</option>
                            <option value="slider" {{ old('type', $data->type ?? '') === 'slider' ? 'selected' : '' }}> Slider</option>
                        </select>

                       
                    </div>

                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label style="color:red;">{{ __('master.Image category') }}</label>
                                <input type="text" class="form-control @error('img_category') is-invalid @enderror" id="img_category" Readonly name="img_category" value="{{ $data->img_category ?? '' }}" placeholder="{{ __('master.Image category') }}">
                              
                            </div>
                        </div>
                          
                          <div class="col-md-2">
                            <div class="form-group">
                               <label>{{ __('common.Photo') }}</label>
                               <input type="file" class="form-control " id="photo" name="photo" value="{{$data['photo'] ?? ""}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_error"></p>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <img src="{{ env('IMAGE_SHOW_PATH').'/school_gallery/'.$data->image ?? '' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" style="width:100px; height:100px">
                        </div>
                          <div class="col-md-2 pt-4">
                                           <input type="button" class="addmoreprodtxtbx" id="clonebtn" />
                                <input type="button" id="removerow" class="removeprodtxtbx" />
                         </div>
                         
                         <div class="col-md-12 mt-3 mb-3 text-center">
                            <button type="submit" class="btn btn-primary btn-submit">{{ __('common.Submit') }}</button>
                        </div>
                    </div>
                    <div id="box1">
                        
                    </div>
                 
                       </div>
                </form>
            </div>
</div>
</div>

</section>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $('#photo').change(function(e){
            $('#image_error').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_error').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_error').html("");
            }
        }else{
            $('#image_error').html("Image Size File");
            $(this).val('');
        }
        });
    });
    </script>
    
    <style>
    #image_error{
        font-weight: bold;
    font-size: 14px;
    }
    </style>

<script>
    
$(document).ready(function() {
    $(document).on("click", "#clonebtn", function() {
        //we select the box clone it and insert it after the box
        $('#box2').clone().appendTo('#box1')
        $('#box1').find('#removerow').addClass("buttondel")
        $('.buttondel').css('visibility', 'visible')
    });
    
    $(document).on("click", "#removerow", function() {
        $(this).parents("#box2").remove();
        $('#removerow').focus();
    });
    
    $(".#clonebtn").keyup(function () {
        if (this.value.length == this.maxLength) {
          $(this).next('.inputs').focus();
        }
    });
});
</script>
<style>
 .addmoreprodtxtbx {
  background-color: #FFFFFF;
  background-image: url({{url('https://saleanalysics.rukmanisoftware.com/public/images/list_add.png')}});
  background-repeat: no-repeat;
  border: medium none;
  cursor: pointer;
  height: 16px;
  margin-top:4px;
  width: 16px;
}

.removeprodtxtbx {
  background-color: #FFFFFF;
  background-image: url({{url('https://saleanalysics.rukmanisoftware.com/public/images/delete2.png')}});
  background-repeat: no-repeat;
  border: medium none;
  cursor: pointer;
  height: 15px;
  margin-left: 5px;
   margin-top:4px;
  width: 16px;
  visibility:hidden;
}
</style>


@endsection