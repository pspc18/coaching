@php
   $bookCategory = Helper::getBookCategory();
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">

    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-book"></i> &nbsp; {{ __('library.Add Library Books') }} </h3>
                            <div class="card-tools"> 
                                    <a href="{{url('library_book_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>{{ __('common.View') }}  </a> 
                                <a href="{{url('library_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
                            </div>
                        </div>
                        <form action="{{ url('book_add_excel') }}" method="post" enctype="multipart/form-data">
                            @csrf                        
                        <div class="row m-2">
                            <div class="col-md-3">
                                <label>{{ __('library.Download Excel Format') }}</label>
                                <button class="btn btn-danger" id="downloadExcel" type="button" data-link="schoolimage/Book Blank Excel Format.xlsx"><i class="fa fa-download"></i> Download Excel</button>
                            </div>

                            <div class="col-md-3">
                                <label>{{ __('library.Upload Excel') }} </label>
                                <input class="form-control" type="file" id="excel" name="excel">
                            </div>
                            <div class="col-md-1">
                                <label class="text-white">{{ __('common.Submit') }}</label>
                                <button type="submit" class="btn btn-primary">{{ __('library.Upload') }}</button>
                            </div>                              
                        </div>
                        </form>
                        
                        <form action="{{ url('library_book_add') }}" id="submit_form" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row m-2">                              
                         <div class="col-md-3">
                                            <label>{{ __('library.Category Select') }}<span class="text-danger">*</span> </label>
                                <select class="form-control invalid" id="category_id" name="category_id" placeholder="{{ __('library.Category Select') }}">
                                <option value="">{{ __('library.Category Select') }}</option>
                                @if(!empty($bookCategory))
                                @foreach ($bookCategory as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                               @endforeach
                               @endif
                                </select>
                                
                                <span class="invalid-feedback" id="category_id_invalid" role="alert">
                                     <strong>{{ __('library.Category field is required') }}</strong>
                                </span>
                        </div>
                                <div class="col-md-3">
                                    <label >{{ __('library.Book Name') }}<span class="text-danger">*</span></label>
                                    <input class="form-control invalid" type="text" id="name" name="name" placeholder="{{ __('library.Book Name') }}" value="{{ old('name') }}">
                                    <span class="invalid-feedback" id="name_invalid" role="alert">
                                            <strong>{{ __('library.Name field is required') }}</strong>
                                    </span>                            
                                </div>
                                <div class="col-md-3">
                                    <label >{{ __('library.Author Name') }}<span class="text-danger">*</span></label>
                                    <input class="form-control invalid" type="text" id="author" name="author" onkeydown="return /[a-zA-Z ]/i.test(event.key)" placeholder="{{ __('library.Author Name') }}" value="{{ old('author') }}">
                                   <span class="invalid-feedback" id="author_invalid" role="alert">
                                            <strong>{{ __('library.Author field is required') }}</strong>
                                    </span>                                  
                                </div>
                                <div class="col-md-3">
                                    <label >{{ __('library.Publisher Name') }}<span class="text-danger">*</span></label>
                                    <input class="form-control invalid" type="text" id="publisher" name="publisher" onkeydown="return /[a-zA-Z ]/i.test(event.key)" placeholder="{{ __('library.Publisher Name') }}" value="{{ old('publisher') }}">
                                  <span class="invalid-feedback" id="publisher_invalid" role="alert">
                                            <strong>{{ __('library.Publisher Name field is required') }}</strong>
                                    </span>
                               
                                </div>
                              <!--</div>-->
                              <!--<div class="row ml-2">-->
                                   <div class="col-md-3">
                                    <label >{{ __('library.Date') }}<span class="text-danger">*</span></label>
                                    <input class="form-control invalid" type="date" id="date" name="date" placeholder="date" value="{{ date('Y-m-d') }}">
                                 <span class="invalid-feedback" id="date_invalid" role="alert">
                                    <strong>{{ __('library.Date field is required') }}</strong>
                                </span>
                                </div>
                                   <div class="col-md-3">
                                    <label >{{ __('library.Edition') }}<span class="text-danger">*</span></label>
                                    <input class="form-control invalid" type="text" id="edition" name="edition" placeholder="{ __('library.Edition') }}" value="{{ old('edition') }}">
                                 
                                 <span class="invalid-feedback" id="edition_invalid" role="alert">
                                    <strong>{{ __('library.Edition field is required') }}</strong>
                                </span> 
                               
                                </div>
                                
                                
                                   <div class="col-md-3">
                                    <label>{{ __('library.Brand') }}<span class="text-danger">*</span></label>
                                    <input class="form-control invalid" type="text" id="brand" name="brand" placeholder="{{ __('library.Brand') }}" value="{{ old('brand') }}">
                                    <span class="invalid-feedback" id="brand_invalid" role="alert">
                                            <strong>{{ __('library.Brand field is required') }}</strong>
                                    </span>
                                </div>
                                
                                   <div class="col-md-3">
                                    <label >{{ __('library.Quantity') }}<span class="text-danger">*</span></label>
                                    <input class="form-control invalid" onkeypress="javascript:return isNumber(event)" type="int" id="quantity" name="quantity" placeholder="{{ __('library.Quantity') }}" value="{{ old('quantity') }}">
                                   <span class="invalid-feedback" id="quantity_invalid" role="alert">
                                    <strong>{{ __('library.Quantity field is required') }}</strong>
                                </span>
                               
                                </div>
                                
                                 <div class="col-md-3">
                                    <label class="">{{ __('library.Book Image') }}</label>
                                    <input class="form-control" type="file" id="image" name="image" placeholder="image" value="{{ old('image') }}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_error"></p>
                                </div>  
                                
                                   <div class="col-md-3">
                                    <label>{{ __('library.Barcode Number') }}</label>
                                    <input class="form-control @error('book_code') is-invalid @enderror" type="text" id="book_code" name="book_code" placeholder="{{ __('library.Barcode Number') }}" value="{{ old('book_code') }}">
                                 @error('book_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror 
                               
                                </div>
                                
                                   <div class="col-md-3">
                                    <label >{{ __('library.MRP') }}.<span class="text-danger">*</span></label>
                                    <input class="form-control invalid" onkeypress="javascript:return isNumber(event)" type="int" id="mrp" name="mrp" placeholder="{{ __('library.MRP') }}."  value="{{ old('mrp') ?? '' }}">
                                    <span class="invalid-feedback" id="mrp_invalid" role="alert">
                                        <strong>{{ __('library.MRP field is required') }}</strong>
                                    </span> 
                               
                                </div>
                                
                                
                                
                                   <div class="col-md-3">
                                    <label>{{ __('library.Almari No.') }}</label>
                                    <input class="form-control @error('almari_no') is-invalid @enderror"  onkeypress="javascript:return isNumber(event)" type="int" id="almari_no" name="almari_no" placeholder="{{ __('library.Almari No.') }}"  value="{{ old('almari_no') ?? '' }}">
                                 @error('almari_no')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror 
                               
                                </div>
                                
                                
                                 <div class="col-md-3">
                                    <label >{{ __('library.With Cover') }}<span class="text-danger">*</span></label><br>
                                    <form action="/action_page.php">
                                    <div class="form-check-inline ">
                                      <label class="form-check-label  cursor">
                                        <input type="radio" class="form-check-input xl" id="cover" name="cover" value="1">{{ __('library.Yes') }}
                                      </label>
                                    </div>
                                    <div class="form-check-inline">
                                      <label class="form-check-label cursor">
                                        <input type="radio" class="form-check-input xl no_checked" id="cover" name="cover" value="0" >{{ __('library.No') }}
                                      </label>
                                    </div>
   
                                    </form>
                               
                                </div>
                                
                              
                              <div class="col-md-12 text-center mt-5">
                                    <button class="btn btn-primary" type="submit" id="is-invalid">{{ __('common.Submit') }}</button>
                                </div>
                                  
                             
                            </div> 
                        </form>
                        
                             
                       
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $('#image').change(function(e){
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
<style>

.cursor{
     cursor: pointer;
}

    [type="checkbox"], [type="radio"] {
height:20px; 
width:20px;
   cursor: pointer;
}
</style>

<script>
    $("#downloadExcel").click(function(){
        var excel_link = $(this).data('link');
        window.location.href = excel_link;
    })
</script>

<script>
$( document ).ready(function() {
    $('.no_checked').click()
});

$('#is-invalid').click(function(e){
    e.preventDefault();
    var all_filled_up = 2000;
    $('.invalid').each(function(){
        var this_value = $(this).val();
        var this_id = $(this).attr('id'); 
        if(this_value == ''){
            $('#' + this_id + '_invalid').css('display','block');
            all_filled_up = all_filled_up + ' && ' + this_value;
        }else{
            $('#' + this_id + '_invalid').css('display','none');
        }
    })
    if(all_filled_up == 2000){
        $('#submit_form').trigger('submit');
    }else{
        $("html, body").animate({ scrollTop: 0 }, "slow");
    }
})
</script>
@endsection      