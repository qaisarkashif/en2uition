var nextrows_request = false,
    pag_page = 1;

function loadNextRows() {
    nextrows_request = true;
    $(".nei-img").addClass('hide');
    $(".wait-shape").removeClass('hide');
    $.ajax({
        url  : window.location.pathname,
        type : 'post',
        data: {
            'pag_page' : pag_page,
            'topic' : $(".by-date[name=topic]").val()
        },
        dataType: 'json',
        success: function(data) {
            nextrows_request = data.stop_request !== undefined ? data.stop_request : false;
            if (data.rows !== undefined) {
                $.each(data.rows, function(key, rows) {
                    for(var i in rows) {
                        var row = rows[i];
                        $("<tr/>")
                            .append('<td width="3%" class="nopad" align="center"><img src="' + row.ava + '" alt="usr-img" width="34"/></td>')
                            .append('<td width = "20%" class="user-name"><a href="/neighborhood/username/'+row.encoded_username+'">'+ row.username + '</a></td>')
                            .append('<td width = "12%">' + row.created_datetime + '</td>')
                            .append('<td width = "26%"><a href="/forum/topic-'+row.id+'/'+data.color2+'/'+data.shape2+'"">“' + row.title + '”</a></td>')
                            .append('<td width = "7%" align="center" class="nopad">' + row.users_count + '</td>')
                            .append('<td width = "7%" align="center" class="nopad">' + row.posts_count + '</td>')
                            .append('<td width = "7%" align="center" class="nopad">' + row.shares_count + '</td>')
                            .append('<td width = "18%"><a href="/neighborhood/date/' + row.last_post1 + '/'+data.color2+'/'+data.shape2+'">' + row.last_post2 + '</a></td>')
                            .appendTo("."+key);
                    }
                    $("."+key).closest(".nbr-data").slimScroll().mouseover();
                });
            }
            pag_page++;
        },
        error: function (xhr, text_status, error_thrown) {
            nextrows_request = false;
            if (text_status != "abort" && xhr.status !== 0) {
                requestFailed();
            }
        },
        complete: function() {
            $(".wait-shape").addClass('hide');
            $(".nei-img").removeClass('hide');
        }
    });
}

$(function() {
    $(".nbr-data").scroll(function() {
        var h = $(this).find("#table tbody").height(),
            h1 = $(this).height(),
            scr_top = $(this).scrollTop();
        if((h1 + scr_top) > h && nextrows_request === false) {
            loadNextRows();
        }
    });

    $("select.post-date").change(function() {
        var date = $(this).val();
        if ($.trim(date) != '') {
            var color = $("select.post-date").attr('data-color'),
                shape = $("select.post-date").attr('data-shape');
            window.location = "/neighborhood/date/" + date + "/" + color + "/" + shape;
        }
    });

    $(".header-forum-search input:text.by-date").keypress(function(e) {
        var code = e.which || e.keyCode;
        if (code == 13) {
            $(".header-forum-search").removeAttr('onsubmit').submit();
        }
    });
});