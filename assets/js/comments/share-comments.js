function shareComment(link, id, target) {
    var type = $(link).attr('data-type');
    $.ajax({
        url: '/comment/share/'+target,
        type: 'post',
        data: {'id':id, 'type': type},
        dataType: 'json',
        success: function(data) {
            if(data.errors !== undefined) {
                if(data.errors == '') {
                    var count = parseInt($(link).find(".st").text());
                    if(type == "share") {
                        $(link).attr('data-type',  "unshare");
                        count++;
                    } else {
                        $(link).attr('data-type', "share");
                        count--;
                    }
                    $(link).find(".st").text(count);
                    $(link).trigger("mouseleave");
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}