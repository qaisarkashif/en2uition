$(function() {

    $("#btn-save-album").unbind('click').click(function(event) {
        var form = $("#frm-new-album");
        form.find('p.verrors, div.alert').remove();
        var album_id = form.find('#album-id').val();
        $("#btn-save-album").prop('disabled', true).parent("div").css("cursor", "wait");
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            dataType: 'json',
            data: form.serialize(),
            success: function(data) {
                if (data.errors !== undefined) {
                    if (data.errors == '') {
                        form.find('legend').after('<div class="alert alert-success" role="alert">Album successfully ' + (album_id == -1 ? 'created' : 'updated') + '.</div>');
                        if(album_id == -1) {
                            form.find('#album-title').val('');
                        }
                        $('#progress div').removeClass('progress-bar progress-bar-success').css('width', '0%');
                        $("#files").empty();
                    } else if (data.errors == 'general') {
                        form.find('legend').after('<div class="alert alert-danger" role="alert">Failed to ' + (album_id == -1 ? 'create' : 'update') + ' an album.</div>');
                    } else {
                        form.find('#album-title').after(data.errors);
                    }
                    $('html, body').animate({
                        scrollTop: form.offset().top
                    }, 100);
                }
            },
            error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } },
            complete: function() {
                $("#btn-save-album").prop('disabled', false).parent("div").css("cursor", "default");
            }
        });
        event.stopImmediatePropagation();
    });

    $('#fileupload').fileupload({
        dataType: 'json',
        url: '/photo/upload_to_album',
        done: function(e, data) {
            $.each(data.result.files, function(index, file) {
                var res_html = '';
                if (file.error !== undefined) {
                    res_html = '<td>&nbsp;</td><td>' + file.origin_name + '</td><td><span class="error-uploaded">Failed! ' + file.error + '</span></td>';
                } else {
                    res_html = '<td><a href="javascript: void(0);" class="thumbnail"><img src="' + file.thumbnailUrl + '" alt=""/></a></td>';
                    res_html += '<td>' + file.origin_name + '</td><td><span class="file-uploaded">Uploaded</span></td>';
                }
                $('<tr/>').html(res_html).appendTo($("#files"));
            });
        },
        start: function() {
            $("#btn-save-album").prop('disabled', true);
            $('#progress div').addClass('progress-bar progress-bar-success');
        },
        stop: function() {
            $("#btn-save-album").prop('disabled', false);
        },
        progressall: function(e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css({'width' : progress + '%'});
        }
    });
});