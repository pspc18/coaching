$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    $("#form-submit").on("submit", function (e) {
        e.preventDefault();
        var $form = $(this);
        var btn = $form.find(".btn-submit");
        var formData = new FormData(this);

        $.ajax({
            url: $form.attr("action"),
            type: "POST",
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
            cache: false,
            beforeSend: function () {
                btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> ' + btn.text());
                $(".error, .alert").remove(); // Clear old messages
                    $(".is-invalid").removeClass('is-invalid');
            },
            success: function (response) {
                toastr.success(response.message);
                $form[0].reset();
                btn.prop("disabled", false).text(btn.text());
                location.reload();
            },
            error: function (xhr) {
               // console.log("Error Triggered:", xhr);

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (index, value) {
                        var inputField = $form.find("[name='" + index + "']");
                        if (inputField.closest(".form-group").find(".error").length === 0) {
                            inputField.addClass("is-invalid");
                            inputField.closest(".form-group").append("<div class='error invalid-feedback'></div>");
                        }
                        inputField.closest(".form-group").find(".error").text(value);
                    });
                } else {
                    var errorMessage = xhr.responseJSON?.message || "An unexpected error occurred.";
                    toastr.error(errorMessage);
                }

                $(".alert").hide().fadeIn();
                btn.prop("disabled", false).text(btn.text());
            },
        });
    });
    
    
    $("#form-submit-edit").on("submit", function (e) {
        e.preventDefault();
        var $form = $(this);
        var btn = $form.find(".btn-submit");
        var formData = new FormData(this);

        $.ajax({
            url: $form.attr("action"),
            type: "POST",
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
            cache: false,
            beforeSend: function () {
                btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> ' + btn.text());
                $(".error, .alert").remove(); // Clear old messages
                    $(".is-invalid").removeClass('is-invalid');
            },
            success: function (response) {
                toastr.success(response.message);
               if (response.redirect) {
                window.location.href = response.redirect;
            } else {
                location.reload(); // Refresh page if no redirect URL is provided
            }
                
                $form[0].reset();
                btn.prop("disabled", false).text(btn.text());
            },
            error: function (xhr) {
               // console.log("Error Triggered:", xhr);

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (index, value) {
                        var inputField = $form.find("[name='" + index + "']");
                        if (inputField.closest(".form-group").find(".error").length === 0) {
                            inputField.addClass("is-invalid");
                            inputField.closest(".form-group").append("<div class='error invalid-feedback'></div>");
                        }
                        inputField.closest(".form-group").find(".error").text(value);
                    });
                } else {
                    var errorMessage = xhr.responseJSON?.message || "An unexpected error occurred.";
                    toastr.error(errorMessage);
                }

                $(".alert").hide().fadeIn();
                btn.prop("disabled", false).text(btn.text());
            },
        });
    });
    $("#form-submit-print").on("submit", function (e) {
        e.preventDefault();
        var $form = $(this);
        var btn = $form.find(".btn-submit");
        var formData = new FormData(this);
 
        $.ajax({
            url: $form.attr("action"),
            type: "POST",
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
            cache: false,
            beforeSend: function () {
                btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> ' + btn.text());
                    $(".is-invalid").removeClass('is-invalid');
            },
            success: function (response) {
                toastr.success(response.message);
                 window.open(response.print_url, "_blank"); // Open print page
                $form[0].reset();
                btn.prop("disabled", false).text(btn.text());
                
            },
            error: function (xhr) {
               // console.log("Error Triggered:", xhr);

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function (index, value) {
                        var inputField = $form.find("[name='" + index + "']");
                        if (inputField.closest(".form-group").find(".error").length === 0) {
                            inputField.addClass("is-invalid");
                            inputField.closest(".form-group").append("<div class='error invalid-feedback'></div>");
                        }
                        inputField.closest(".form-group").find(".error").text(value);
                    });
                } else {
                    var errorMessage = xhr.responseJSON?.message || "An unexpected error occurred.";
                    toastr.error(errorMessage);
                }

                btn.prop("disabled", false).text(btn.text());
            },
        });
    }); 
    
 //    < !-- Add Subject  Start -->

     
$(document).ready(function () {
    $("#form-submit-AddSubject").off("submit").on("submit", function (e) {
        e.preventDefault();
        var $form = $(this);
        var btn = $form.find(".btn-submit");
        var formData = new FormData(this);

        $.ajax({
            url: $form.attr("action"),
            type: "POST",
            data: formData,
            dataType: "json",
            contentType: false,
            processData: false,
            cache: false,
            beforeSend: function () {
                btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> ' + btn.text());
                $(".is-invalid").removeClass('is-invalid');
            },
            success: function (response) {
                toastr.success(response.message);
                window.open(response.print_url, "_self"); // Opens in the same tab
                $form[0].reset();
                btn.prop("disabled", false).text(btn.text());
            },
            error: function (xhr) {
                if (xhr.status === 422) { // Handle validation errors
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function (field, messages) {
                        var inputField = $("[name='" + field + "']");
                        inputField.addClass("is-invalid"); // Bootstrap invalid style
                        if (inputField.closest(".form-group").find(".error").length === 0) {
                            inputField.closest(".form-group").append("<div class='error invalid-feedback'></div>");
                        }
                        inputField.closest(".form-group").find(".error").text(messages[0]);
                    });

                    var errorMessage = xhr.responseJSON.message || "Validation failed.";
                    toastr.error(errorMessage); // Show toastr error
                } else {
                    // Handle other errors
                    var errorMessage = xhr.responseJSON?.message || "An unexpected error occurred.";
                    toastr.error(errorMessage);
                }

                btn.prop("disabled", false).text(btn.text());
            }
        });
    });
});
    
  //    < !-- Add Subject  End -->  
  
  //    < !--Promote Add  start -->  
    
    
$("#form-submit-promote").on("submit", function (e) {
    e.preventDefault();
    var $form = $(this);
    var btn = $form.find(".btn-submit");

    var formData = new FormData(this);

    // Manually append roll_no fields to ensure correct array structure
    $(".roll_no").each(function () {
        let key = $(this).attr("name"); // Example: roll_no[25]
        let value = $(this).val();
        formData.append(key, value);
    });

    $.ajax({
        url: $form.attr("action"),
        type: "POST",
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        cache: false,
        beforeSend: function () {
            btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> ' + btn.text());
            $(".error, .alert").remove();
            $(".is-invalid").removeClass('is-invalid');
        },
        success: function (response) {
            toastr.success(response.message);
            $form[0].reset();
            btn.prop("disabled", false).text(btn.text());
        },
        error: function (xhr) {
            console.log("Error Triggered:", xhr);
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function (index, value) {
                    var inputField = $form.find("[name='" + index + "']");
                    if (inputField.closest(".form-group").find(".error").length === 0) {
                        inputField.addClass("is-invalid");
                        inputField.closest(".form-group").append("<div class='error invalid-feedback'></div>");
                    }
                    inputField.closest(".form-group").find(".error").text(value);
                });
            } else {
                var errorMessage = xhr.responseJSON?.message || "An unexpected error occurred.";
                toastr.error(errorMessage);
            }
            $(".alert").hide().fadeIn();
            btn.prop("disabled", false).text(btn.text());
        },
    });
});
 
 
   //    < !--Promote Add  End -->  


 let originalBtnText = "";

$("#Send_whatsapp_reciept").on("submit", function (e) {
    e.preventDefault();
    var $form = $(this);
    var btn = $form.find(".btn-submit");
    var formData = new FormData(this);

    $.ajax({
        url: $form.attr("action"),
        type: "POST",
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        cache: false,
        beforeSend: function () {
            originalBtnText = btn.text();
            btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> ' + originalBtnText);
            $(".error, .alert").remove();
            $(".is-invalid").removeClass("is-invalid");
        },
        success: function (response) {
            toastr.success(response.message);
            $form[0].reset();
            btn.prop("disabled", false).text(originalBtnText);
            $('#whatsapp_modal').modal('hide'); // Optional instead of full reload
            // location.reload(); // If reload is still needed
        },
        error: function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function (index, value) {
                    var inputField = $form.find("[name='" + index + "']");
                    if (inputField.closest(".form-group").find(".error").length === 0) {
                        inputField.addClass("is-invalid");
                        inputField.closest(".form-group").append("<div class='error invalid-feedback'></div>");
                    }
                    inputField.closest(".form-group").find(".error").text(value);
                });
            } else {
                var errorMessage = xhr.responseJSON?.message || "An unexpected error occurred.";
                toastr.error(errorMessage);
            }
            $(".alert").hide().fadeIn();
            btn.prop("disabled", false).text(originalBtnText);
        },
    });
});



});