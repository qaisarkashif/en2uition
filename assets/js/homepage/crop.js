function showCropWindow(img_src) {
    $(".imgareaselect-inputs").val('');
    var modal = $("#cropModal");
    modal.find(".modal-body img").remove();
    var img = $("<img />").attr({'src': img_src, 'alt': 'profile img...', 'id': 'cr-img'});
    modal.find(".modal-body").append(img);
    modal.modal('show');
}

function cropImage() {
    if(!$(".imgareaselect-outer:visible").length) {
        $("#cropModal").modal('hide');
        return false;
    }
    $.ajax({
        'url': '/crop-profile-image',
        'type': 'post',
        'data': $("#cropModal .modal-body form").serialize(),
        'dataType': 'json',
        success: function(json) {
            if(json.errors !== undefined) {
                if(json.errors == '') {
                    $(".pro-img img").attr('src', $(".pro-img img").attr('src')+'1');
                    $("#cropModal").modal('hide');
                } else {
                    var errors = json.errors.replace(/\<p\>/gi, '').replace(/\<\/p\>/gi, '\r\n');
                    alert('Errors:\r\n'+errors);
                }
            }
        },
        error: function(xhr, text_status, error_thrown) {
            if (text_status != "abort" && xhr.status !== 0) {
                requestFailed();
            }
        }
    });
}

function removeAvatar() {
    $.ajax({
        'url': '/remove-avatar',
        'type': 'get',
        'dataType': 'json',
        success: function(json) {
            if(json.errors !== undefined) {
                if(json.errors == '') {
                    $(".pro-img img").attr('src', json.no_ava);
                    $(".pro-img .magnific-popup").attr('href', json.no_ava_orig)
                    $("#cropModal").modal('hide');
                } else {
                    alert(json.errors);
                }
            }
        },
        error: function(xhr, text_status, error_thrown) {
            if (text_status != "abort" && xhr.status !== 0) {
                requestFailed();
            }
        }
    });
}

$(function() {
    $("#cropModal").on('hide.bs.modal', function() {
        $(".imgareaselect-outer").remove();
        var box = $(".imgareaselect-selection").parent('div');
        box.remove();
        $(".imgareaselect-inputs").val('');
    }).on('shown.bs.modal', function() {
        var mimg = document.getElementById("cr-img");
        $("#cr-img").imgAreaSelect({
            handles: true,
            imageHeight: mimg.naturalHeight,
            imageWidth: mimg.naturalWidth,
            onSelectEnd: function(img, selection) {
                if (!selection.width || !selection.height) {
                    return;
                }
                $('#x1').val(selection.x1);
                $('#y1').val(selection.y1);
                $('#x2').val(selection.x2);
                $('#y2').val(selection.y2);
                $('#w').val(selection.width);
                $('#h').val(selection.height);
            }
        });
    });
});