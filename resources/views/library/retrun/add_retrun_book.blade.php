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
                            <h3 class="card-title"><i class="fa fa-book"></i> &nbsp; {{ __('library.Add Retrun Book') }} </h3>
                            <div class="card-tools"> 
                                    <a href="{{url('retrun_book_invoice')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>{{ __('common.View') }}  </a> 
                                <a href="{{url('library_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
                            </div>
                        </div>
                        <form action="{{ url('add_retrun_book') }}" method="post" enctype="multipart/form-data">
                            @csrf                        
                        <div class="row m-2">
                            <div class="col-md-3">
                                    <label class="text-danger">{{ __('library.Student Name') }}</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" id="student_name" name="student_name" placeholder="{{ __('library.Student Name') }}" value="{{ old('name') }}">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror                               
                                </div>
                                
                            <div class="col-md-3">
                                    <label class="text-danger">{{ __('library.Book Name') }}</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" id="book_name" name="book_name" placeholder="{{ __('library.Book Name') }}" value="{{ old('name') }}">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror                               
                                </div>
                                
                            <div class="col-md-3">
                                    <label class="text-danger"> {{ __('library.Book Invoice') }}</label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="text" id="book_invoice" name="book_invoice" placeholder="{{ __('library.Book Invoice') }}" value="{{ old('name') }}">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror                               
                                </div>
                                
                            <div class="col-md-3">
                                    <label class="text-danger"> Retrun Date </label>
                                    <input class="form-control @error('name') is-invalid @enderror" type="date" id="retrun_date" name="retrun_date" placeholder="retrun_date" value="{{ old('name') }}">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror                               
                                </div>
                        </div>
                 
                        
                               
                                </div>
                                
                              
                              <div class="col-md-12 text-center mt-5">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                                  
                             
                            </div> 
                        </form>
                        
                             
                       
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

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
@endsection      