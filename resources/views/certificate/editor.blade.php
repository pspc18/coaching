@extends('layout.app')
@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap');

    body {
        font-family: 'Poppins';
    }

    .margin_top_space {
        margin-top: 30px;
    }

    .edtior_card {
        border: 1px solid rgb(173, 173, 173);
        /* border-radius: 10px; */
    }

    .flex_aligns {
        display: flex;
    }

    .tools_sidebar {
        background-color: #002c54;
        width: 10%;
        padding: 20px 10px;
        box-shadow: 4px 0px 8px #0000008f;
        
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .tools_sidebar .tools_list {
        padding-left: 0px;
        margin-bottom: 0px;
        list-style: none;
    }

    .tools_sidebar .tools_list li {
        text-align: center;
        margin-top: 20px;
        border-bottom: 1px solid #b1b1b1;
    }


    .tools_sidebar .tools_list li:first-child {
        margin-top: 0px;
    }

    .theme_default_btn {
        background-color: transparent;
        border: none;
        color: #a5a5a5;
        text-transform: uppercase;
        outline: 0;
        transition: 0.3s;
    }

    .theme_default_btn:hover {
        color: white;
    }

    .theme_default_btn i {
        font-size: 20px;
    }

    .theme_default_btn span {
        font-size: 12px;
        font-weight: 600;
    }

    .theme_default_btn:focus-visible {
        outline: 0;
    }

    .hidden_input_button {
        position: relative;
    }

    .hide_input {
        position: absolute;
        top: 0;
        width: 100%;
        height: 100%;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }

    .tools_top_bar {
        background-color: #002c54;
        padding: 0px 10px;
        color: white;
        z-index: 1;
        position: relative;
        box-shadow: 0px 3px 6px #00000080;
    }

    .tools_bottom_bar {
        background-color: #002c54;
        padding: 10px 10px;
        color: white;
        z-index: 1;
        position: relative;
        box-shadow: 0px -3px 6px #00000080;
    }

    .tools_top_bar .tools_list {
        padding-left: 0px;
        margin-bottom: 0px;
        list-style: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .form-group {
        margin: 15px 0px;
    }

    .primary_color .input-group-text {
        cursor: pointer;
        background-color: #002c54;
        border: 1px solid white;
        color: white;
    }

    .editor_result_wrapper {
        width: 90%;
        padding: 20px;
    }

    .border_of_main_part {
        border: 1px solid #333;
        height: 500px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .certificate-container {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .text_container {
        position: relative;
    }

    .draggable {
        cursor: move;
        position: absolute;
    }
    
     .img_container {
        position: relative;
    }
    
    .selected_container .size_changer_div {
        display: block;
    }

    .size_changer_div {
        display: none;
    }

    .size_changer {
        position: absolute;
        right: -7px;
        bottom: -7px;
        background: #ababab;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        rotate: 85deg;
        cursor: pointer;
    }
    
</style>

<div class="content-wrapper">
    <section class="content pt-1">
        <div class="container-fluid margin_top_space">
        <div class="col-md-12">
            <div class="edtior_card">
                <div class="tools_top_bar">
                    <ul class="tools_list">
                        <li>
                            <div class="form-group">
                                <label>Font Weight</label>
                                <select class="select_box form-control" id="bold_text" name="bold_text">
                                    <option value="400">400</option>
                                    <option value="500">500</option>
                                    <option value="600">600</option>
                                    <option value="800">800</option>
                                </select>
                            </div>
                        </li>
                        <li>
                            <div class="form-group">
                                <label>Font Size</label>
                                <div class="input-group">
                                    <div class="input-group-prepend primary_color" id="decrease_size">
                                        <span class="input-group-text">
                                            <i class="fa fa-minus"></i>
                                        </span>
                                    </div>
                                    <input type="text" class="form-control text-center" value="14" id="font_size"
                                        name="font_size" placeholder="Font Size">
                                    <div class="input-group-append primary_color" id="increase_size">
                                        <span class="input-group-text">
                                            <i class="fa fa-plus"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="form-group">
                                <label>Select Color</label>
                                <input type="color" id="select_color" class="form-control">
                            </div>
                        </li>
                        <li>
                            <div class="form-group">
                                <label>Font Family</label>
                                <select class="select_box form-control" id="font_style" name="font_style">
                                    <option value="Poppins">Poppins</option>
                                    <option value="Open Sans">Open Sans</option>
                                </select>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="flex_aligns">
                    <div class="tools_sidebar">
                        <ul class="tools_list">
                            <li>
                                <button class="theme_default_btn" id="add_text" title="Add Text">
                                    <i class="fa fa-plus"></i> <br>
                                    <span>New Text</span>
                                </button>
                            </li>
                            <li>
                                <button class="theme_default_btn" id="delete_text" title="Delete Text">
                                    <i class="fa fa-trash"></i> <br>
                                    <span>Delete Text</span>
                                </button>
                            </li>
                            <li>
                                <button class="theme_default_btn hidden_input_button" title="Add Background Image">
                                    <input type="file" id="bgUpload" accept="image/*" class="hide_input">
                                    <i class="fa fa-image"></i> <br>
                                    <span> Bg Image</span>
                                </button>
                            </li>
                            <li>
                                <button class="theme_default_btn hidden_input_button" title="Add Image">
                                    <input type="file" id="add_image" accept="image/*" class="hide_input">
                                    <i class="fa fa-image"></i> <br>
                                    <span> New Image</span>
                                </button>
                            </li>
                            <li>
                                <button class="theme_default_btn" id="removeBg" title="Remove Background Image">
                                    <i class="fa fa-trash"></i> <br>
                                    <span>Remove BG</span>
                                </button>
                            </li>
                            <li>
                                <button class="theme_default_btn" id="centerBtn" title="Center Items">
                                    <i class="fa fa-align-center"></i> <br>
                                    <span>Center</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="editor_result_wrapper">
                        <div class="col-md-12 flex_centered border_of_main_part" id="print_preview">
                            <div style="position: absolute;top: 0;left: 0; width: 100%;height: 100%;">
                                <div class="certificate-container"
                                    style="height: 100%;position: relative;top: 0;left: 0; width: 100%;background-size: 100% 100%;background-repeat:no-repeat;"
                                    id="certificate">
                                    <div class="draggable"
                                        style="font-size:14px;font-weight:600;font-family: 'Poppins';position: absolute;">
                                        <div class="text_container">
                                            <span contenteditable="true">[Organization's Name]</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tools_bottom_bar text-center">
                    <button class="btn btn-success" id="save_text" title="Save Code">
                        <i class="fa fa-download"></i>
                        <span>Save Code</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <textarea id="finalcode" rows="5" class="form-control" placeholder="All Code"></textarea>
        </div>
    </div>
    </section>
</div>




<script>
    $(document).ready(function () {
        var deletedElements = [];
        $('#add_text').click(function () {
            var font_size = parseInt($('#font_size').val());
            var font_weight = parseInt($('#bold_text').val());
            var font_style = $('#font_style').val();
            var color = $('#select_color').val();
            var code = '<div class="draggable" style="font-family:' + '' + font_style + '' + '; font-size:' + font_size + 'px; font-weight:' + font_weight + '; position: absolute;"><div class="text_container"><span style="color:' + color + '" contenteditable="true">New Text</span></div></div>';
            $('#certificate').append(code);
        });

        $(document).keydown(function (event) {
            if (event.key === "Delete" || event.keyCode === 46) {
                handleDelete();
            }

            if (event.ctrlKey && event.key === 'c') {
                handleCopy();
            }

            if (event.ctrlKey && event.key === 'v') {
                handlePaste();
            }

            if (event.ctrlKey && event.key === ']') {
                $('#increase_size').click();
            }

            if (event.ctrlKey && event.key === '[') {
                $('#decrease_size').click();
            }

            if (event.ctrlKey && event.key === 'z') {
                if ($('.selected_container').length == 0) {
                    handleUndo();
                }
            }

            if (event.key === "N" || event.key === "n") {
                if ($('.selected_container').length == 0) {
                    $('#add_text').click();
                }
            }

            if (event.key === "B" || event.key === "b") {
                if ($('.selected_container').length == 0) {
                    $('#bgUpload').click();
                }
            }
        });

        function handleUndo() {
            if (deletedElements.length > 0) {
                var lastDeleted = deletedElements.pop();
                $('#certificate').append(lastDeleted);
            }
        }


        function handleDelete() {
            var selectedElement = $('.selected_container');
            if (selectedElement.length > 0) {
                var clone = selectedElement.clone();
                selectedElement.remove();
                deletedElements.push(clone);
            }
        }

        var textToCopy = "";

        function handleCopy() {
            var top = $('.selected_container').css('top');
            var font_size = $('.selected_container').css('font-size');
            var font_weight = $('.selected_container').css('font-weight');
            var color = $('.selected_container').css('color');
            var font = $('.selected_container').css('font-family');
            var html_text = $('.selected_container').find('span').html();

            var font_family = font.replace(/"/g, '');

            var increasetop = parseInt(top.split("px")) + 60;

            textToCopy = '<div class="draggable" style="top:' + increasetop + 'px;font-family:' + '' + font_family + '' + '; font-size:' + font_size + '; font-weight:' + font_weight + '; position: absolute"><div class="text_container"><span style="color:' + color + '" contenteditable="true">' + html_text + '</span></div></div>';
            $('.draggable').removeClass('selected_container');
            $('.draggable').find('span').attr('contenteditable', false);
        }

        function handlePaste() {
            $('.certificate-container').append(textToCopy);
        }

        $('#delete_text').click(function () {
            handleDelete();
        });

        $('#font_size').keyup(function () {
            var font_size = parseInt($('#font_size').val());
            applyFontSize(font_size);
        });

        $('#increase_size').click(function () {
            var fontSize = parseInt($('#font_size').val()) || 14;
            fontSize++;
            $('#font_size').val(fontSize);
            applyFontSize(fontSize);
        });

        $('#decrease_size').click(function () {
            var fontSize = parseInt($('#font_size').val()) || 14;
            fontSize--;
            if (fontSize <= 0) {
                fontSize = 14;
            }
            $('#font_size').val(fontSize);
            applyFontSize(fontSize);
        });

        function applyFontSize(fontSize) {
            $('.selected_container').css('font-size', fontSize + "px");
        }

        $('#bold_text').change(function () {
            var font_weight = parseInt($(this).val());
            $('.selected_container').css('font-weight', font_weight);
        });

        $('#select_color').change(function () {
            var color = $('#select_color').val();
            $('.selected_container').children('.text_container').children('span').css('color', color);
        });

        $('#removeBg').click(function () {
            $('.certificate-container').css('background-image', '');
        });

        $('#font_style').change(function () {
            var font_style = $('#font_style').val();
            var selectedCssLength = $('.selected_container').length;
            if (selectedCssLength == 0) {
                $('.draggable').css('font-family', font_style);
            } else {
                $('.selected_container').css('font-family', font_style);
            }
        });

        $('#certificate').on('mousedown', function (event) {
            if (!$(event.target).closest('.draggable').length) {
                handleOutsideClick();
            }
        });

        function handleOutsideClick() {
            $('.draggable').removeClass('selected_container');
        }

        $('#page_size').change(function () {
            var pageSize = $(this).val();
            if (pageSize == "A4") {
                $('#certificate').css('width', '793px');
                $('#certificate').css('height', '1122px');
            } else if (pageSize == "A3") {
                $('#certificate').css('width', '1122px');
                $('#certificate').css('height', '1587px');
            } else {
                $('#certificate').css('width', '1587px');
                $('#certificate').css('height', '2245px');
            }
        });

        $('#preview').click(function () {
            var printContents = $('#print_preview').html();
            var originalContents = $('body').html();

            $('body').html(printContents);
            window.print();
            $('body').html(originalContents);

            $('#preview').click(arguments.callee);
        });

        $(document).on('mousedown', '.draggable', function () {
            $('.draggable').removeClass('selected_container');
            $(this).addClass('selected_container');
            $('.draggable').find('span').attr('contenteditable', true);

            var font = $(this).css('font-size');
            var fontWeight = $(this).css('font-weight');
            var fontFamily = $(this).css('font-family');
            var color = $(this).css('color');

            var fontSize = parseInt(font.split("px"));
            var font_family = fontFamily.replace(/"/g, '');

            $('#font_size').val(fontSize);
            $('#select_color').val(color);
            $('#bold_text').val(fontWeight);
            $('#font_style').val(font_family);

            lastDragged = this;
            $(this).css('cursor', 'grabbing').css('z-index', '1000');
            const draggable = $(this);
            const container = $('#certificate');
            const containerOffset = container.offset();

            $(document).on('mousemove.draggable', function (e) {
                const x = e.pageX - containerOffset.left - (draggable.width() / 2);
                const y = e.pageY - containerOffset.top - (draggable.height() / 2);

                const containerWidth = container.width();
                const containerHeight = container.height();

                const leftPercent = (Math.min(Math.max(0, x), containerWidth - draggable.width()) / containerWidth) * 100;
                const topPercent = (Math.min(Math.max(0, y), containerHeight - draggable.height()) / containerHeight) * 100;

                draggable.css({
                    left: leftPercent + '%',
                    top: topPercent + '%'
                });
            });

            $(document).on('mouseup.draggable', function () {
                $(document).off('mousemove.draggable mouseup.draggable');
                draggable.css('cursor', 'grab');
            });
        });

        $(document).on('mouseup', function () {
            $('.draggable').css('cursor', 'move').css('z-index', '');
            $(document).off('mousemove.draggable');
        });

        $('#bgUpload').change(function (event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function (e) {
                const base64Image = e.target.result;
                $('#certificate').css('backgroundImage', `url(${base64Image})`);

                // Sending the base64 image to the server
                // $.ajax({
                //     url: '/upload_background_image', // Replace with your server endpoint
                //     method: 'POST',
                //     data: JSON.stringify({ background_image: base64Image }),
                //     contentType: 'application/json',
                //     success: function (response) {
                //         console.log('Image uploaded successfully:', response);
                //     },
                //     error: function (error) {
                //         console.error('Error uploading image:', error);
                //     }
                // });
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        });

        $('#add_image').change(function (event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function (e) {
                const base64Image = e.target.result;
                var code = '<div class="draggable" style="position: absolute;"><div class="img_container"><div class="size_changer_div"><div class="size_changer"><i class="fa fa-expand"></i></div></div><img src="' + base64Image + '" style="width:80px;height:80px" class="img-fluid"></div></div>';
                $('#certificate').append(code);

                // $.ajax({
                //     url: '/upload_background_image', // Replace with your server endpoint
                //     method: 'POST',
                //     data: JSON.stringify({ background_image: base64Image }),
                //     contentType: 'application/json',
                //     success: function (response) {
                //         console.log('Image uploaded successfully:', response);
                //     },
                //     error: function (error) {
                //         console.error('Error uploading image:', error);
                //     }
                // });
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        });

        $('#centerBtn').click(function () {
            if (!lastDragged) return;

            const container = $('#certificate');
            const draggable = $(lastDragged);

            const offsetX = (container.width() - draggable.width()) / 2;
            draggable.css('left', `${offsetX}px`);
        });

        $(window).resize(function () {
            $('#centerBtn').click();
        });

    });

</script>

<script>
    $(document).ready(function () {
        var BASE_URL = "{{ url('/') }}";
        $('#save_text').click(function () {
            var editor_code = $('#print_preview').html();
            
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: BASE_URL + '/certificate_editor',
                data: {
                    editor_code: editor_code,
                    name: "Certificate",
                },
                success: function(data) {
                    toastr.success('Code Save Successfully');
                }
            });
            
        });

        $('#finalcode').change(function () {
            var print = $(this).val();
            $('#print_preview').html("");
            $('#print_preview').html(print);
        });
    });
</script>


<script>
    $(document).ready(function () {
        var isResizing = false;
        var img = "";
        var originalMouseX, originalMouseY;
        var originalWidth, originalHeight;

        $(document).on('mousedown', '.size_changer', function (e) {
            img = $(this).parents('.size_changer_div').siblings('img');
            $(this).perents('.size_changer_div').parents('.')
            isResizing = true;
            originalMouseX = e.pageX;
            originalMouseY = e.pageY;
            originalWidth = img.width();
            originalHeight = img.height();

            e.preventDefault();
        });

        $(document).on('mousemove', function (e) {
            if (isResizing) {
                var mouseX = e.pageX;
                var mouseY = e.pageY;

                var widthDiff = mouseX - originalMouseX;
                var heightDiff = mouseY - originalMouseY;

                var newWidth = originalWidth + widthDiff;
                var newHeight = originalHeight + heightDiff;

                newWidth = Math.max(newWidth, 50);
                newHeight = Math.max(newHeight, 50);

                img.width(newWidth);
                img.height(newHeight);
            }
        });

        $(document).on('mouseup', function () {
            isResizing = false;
        });
    });
</script>


<script>
    $(document).ready(function () {
        $('#page_size').change(function () {
            var pageSize = $(this).val();
            if (pageSize == "A4") {
                $('#certificate').css('width', '793px');
                $('#certificate').css('height', '1122px');
            } else if (pageSize == "A3") {
                $('#certificate').css('width', '1122px');
                $('#certificate').css('height', '1587px');
            } else {
                $('#certificate').css('width', '1587px');
                $('#certificate').css('height', '2245px');
            }
        });
    });
</script>
@endsection