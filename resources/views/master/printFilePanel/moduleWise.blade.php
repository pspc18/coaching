@extends('layout.app')
@section('content')



<div class="content-wrapper">

    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-image"></i> &nbsp;{{ __('ModuleWise Print Files') }}
                            </h3>
                            <div class="card-tools">
                                @if(Session::get('role_id') == 1)
                               <!-- <a href="{{url('gallery_add')}}" class="btn btn-primary  btn-sm" title="Add Gallary"><i
                                        class="fa fa-plus"></i>{{ __('Add') }}</a>-->
                                @endif
                                <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-sm"
                                    title="Add Gallary"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <form action="{{ url('printFileModuleWiseView') }}/{{ $id }}" method="POST">
                        @csrf
                        <div class="card" style="width: 100%;">
                        <div class="container-fluid filterbox">
                            <div class="main-wrap wrap-inner">
                                <div id="content1">
                                    <div class="main-full" id="main">
                                        @foreach($data as $key => $item1)
                                        <input type="hidden" name="sub_module_id[]" value="{{ $item1->id ?? '' }}">
                                        <div class="col-md-12 mt-3">
                                            <u><span class="gallery_title">{{ $item1->name ?? ''}}</span></u>
                                            <div class="row mt-3">
                                                @php
                                                $details = DB::table('print_file_details')->where('print_file_sub_modules_id',$item1->id)->whereNull('deleted_at')->orderBy('id','ASC')->get();
                                              //  dd($details);
                                                @endphp
                                                @foreach($details as $key2 => $item)
                                                 @php
                                                $oldValue = DB::table('print_file_settings')->where('print_file_sub_modules_id',$item1->id)->where('print_file_details_id',$item->id)->whereNull('deleted_at')->first();
                                               
                                               
                                                @endphp
                                                
                                                <div class="col-md-3">
                                                    @php
                                                    
                                                    $value = Helper::getPrintPreviewSample($item->id);
                                                    @endphp
                                                    <div class="edit_gallery_div">
                                                    <img src='{{$value}}' class="edit_gallery_div" width='100%'height='100%'style='border:1px solid black;padding:5px' data-path="{{$value}}"/>
                                                    </div>
                                                    <div class="checkbox mt-3">
                                                        
                                                        <input type="checkbox" class="selected_check checkbox_{{$key}}"
                                                            data-id="{{ $key }}" name="print_file_id[{{$item1->id}}][]"
                                                            value="{{ $item->id }}"   @if(!empty($oldValue)) {{'checked'}} @endif > {{$item->name ?? ''}}
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <hr>
                                        @endforeach
                                    </div>
                                    
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                    </form>
                </div>
            </div>
        </div>

    </section>

</div>

<!--<div class="modal fade" id="myModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-body">
                <div class="image_gallery_modal">
                    <img src="" id="modalImgValue">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="closeModal" class="btn btn-secondary">{{__('common.Close') }}</button>
            </div>

        </div>
    </div>
</div>-->
<div class="modal fade" id="myModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="image_gallery_modal">
                    <p>dsfdsfs</p>
                    <img src="" id="modalImgValue" style="width: 100%; height: auto;" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="closeModal" class="btn btn-secondary">{{__('common.Close') }}</button>
            </div>
        </div>
    </div>
</div>


<!-- The Modal -->
<div class="modal" id="deleteModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Modal Heading</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{url('gallery_delete')}}" method="Post">
                @csrf
                <!-- Modal body -->
                <div class="modal-body">
                    <input type="hidden" id="delete_id" name="delete_id" class="form-control">
                    Are You Want to Delete Data.
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {
        $('.selected_check').click(function () {
            var id = $(this).data('id');
            $('.checkbox_' + id).prop('checked', false);
            
            if($('.checkbox_' + id + ':checked').length > 0){
                $(this).prop('checked', false);
            } else {
                $(this).prop('checked', true);
            }
        });

    });



    $('.delete_btn').click(function () {
        var id = $(this).data('id');
        $('#delete_id').val(id);
    });
    
    
    
    $(".edit_gallery_div").click(function () {
    const dataValue = $(this).data("path");
    $("#modalImgValue").attr("src", dataValue);
    $("#myModal").modal("show");
    });
    
    $("#closeModal").click(function () {
        $("#myModal").modal("hide");
    });


  /*  $(".edit_gallery_div").click(function () {
        const dataValue = $(this).data("path");
        $("#modalImgValue").attr("src", dataValue);
        $("#myModal").modal("show");
    });

    $("#closeModal").click(function () {
        $("#myModal").modal("hide");
    });*/
    
    

</script>
<style>
    .image_gallery_modal img {
        width: 100%;
        height: 400px;
    }

    .edit_gallery_div {
        position: relative;
        width: 100%;
        height: 200px;
    }

    .Edit_Text {
        width: 80px;
        height: 40px;
    }

    .edit_gallery_div:hover .edit_gallery_overley_div {
        opacity: 1;
    }

    .edit_gallery_overley_div {
        position: absolute;
        top: 0;
        left: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 200px;
        background: #0000008f;
        opacity: 0;
        border-radius: 15px;
        transition: 0.5s;
    }

    .gallery_title {
        text-transform: capitalize;
        font-size: 15px;
        font-weight: 600;
        letter-spacing: 0.60px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        --primary-color: #f05454;
        --secondary-color: #6b6b6b;
        --third-color: #fff;
        --fourth-color: #eee;
        --five-color: #ccc;
        --six-color: #0d0c22;
        --white-light-color: #f3f2f2;
    }

    ol li,
    ul li {
        list-style: none;
    }

    a {
        text-decoration: none;
    }

    img {
        max-width: 100%;
    }

    .container-fluid {
        width: 100%;
        padding: 0px 15px;
        margin: 0 auto;
    }

    .filterbox {
        padding: 30px 108px;
    }

    .filter-tabs {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px 0 35px;
    }

    .navigation a {
        margin-right: 5px;
        padding: 9px 25px;
        text-decoration: none;
        border-radius: 6px;
        color: var(--secondary-color);
        transition: all .5s;
    }

    .navigation a:hover {
        color: var(--third-color);
        background-color: var(--primary-color);
    }

    .navigation a.active {
        color: var(--third-color);
        background-color: var(--primary-color);
    }

    .main-wrap.wrap-inner {
        padding: 0px;
    }

    .main-wrap {
        padding: 32px 0px 40px;
        width: 100%;
        box-sizing: border-box;
    }

    #content {
        position: relative;
        margin: 0 auto;
        padding: 0px;
        font-size: 14px;
    }

    #main.main-full {
        float: none;
        width: none;
        max-width: none;
    }

    ol.content {
        margin: 0 auto;
    }

    .content1 {
        display: grid;
        list-style: none;
        grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
        grid-gap: 36px;
    }

    .multi-shot {
        position: relative;
        transition: 3s all;
    }

    .dribbble-link {
        position: relative;
    }

    .dribbble-img img {
        border-radius: 6px;
    }

    .dribbble-over {
        position: absolute;
        top: 0;
        background: rgba(0, 0, 0, 0.04);
        height: 90%;
        left: 0;
        right: 0;
        bottom: 0;
        color: var(--third-color);
        opacity: 0;
        transition: 0.3s all;
    }

    .gallery_img {
        height: 200px;
        width: 100%;
        border-radius: 15px;
        box-shadow: 0px 0px 8px gray;
    }


    .hover-text {
        padding: 10px;
        display: block;
        position: absolute;
        bottom: 1px;
        font-weight: 500;
        background-image: linear-gradient(178.1deg, rgba(60, 55, 106, .03) 8.5%, rgba(0, 0, 0, .45) 82.4%);
        width: 100%;
        border-radius: 5px;
    }

    .multi-shot:hover .dribbble-over {
        opacity: 1;
    }

    .dribbble-over .icons {
        position: absolute;
        bottom: 8px;
        right: 12px;
    }

    .icons i {
        background: var(--fourth-color);
        padding: 8px 10px;
        border-radius: 10px;
        margin: 0px 5px;
        color: gray;
        font-size: 14px;
        transition: all 0.3s;
    }

    .icons i:hover {
        background: var(--primary-color);
        color: var(--third-color);
    }

    .dribbble-title h3 {
        font-size: 13px;
        color: var(--six-color);
        font-weight: 500;
    }

    .dribbble-title .m-team {
        padding: 0px 4px;
        color: var(--third-color);
        background: var(--five-color);
        border-radius: 2px;
        font-size: 11px;
        letter-spacing: 0.4px;
    }

    .comment {
        float: right;
    }

    .comment i {
        color: var(--five-color);
        padding: 5px;
    }

    .comment i:hover {
        color: var(--primary-color);
    }

    @media (max-width: 1200px) {
        .filterbox {
            padding: 30px 40px;
        }
    }

    @media (max-width: 992px) {
        .filterbox {
            padding: 30px 15px;
        }

        .content {
            grid-gap: 12px;
        }

        .navigation a {
            padding: 9px 12px;
        }
    }

    @media (max-width: 767px) {
        .shot-thumbnail {
            margin-top: 20px;
        }

        .filter-tabs {
            flex-wrap: wrap;
        }

        .filter-sort {
            width: 100%;
        }

        .navigation {
            display: flex;
            overflow-x: scroll;
            margin-top: 15px;
            padding-bottom: 15px;
        }

        .navigation::-webkit-scrollbar {
            width: 10px;
            height: 6px;
        }

        .navigation::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .navigation::-webkit-scrollbar-thumb {
            background: #888;
        }
    }

    @media (max-width: 576px) {
        .px-0 {
            padding: 0px;
        }
    }
</style>


@endsection