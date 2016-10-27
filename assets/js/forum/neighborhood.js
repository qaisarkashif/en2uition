var nextrows_request = false;

function loadNextRows($this) {
    nextrows_request = true;
    
    var $box = $this.closest(".nbr"),
        $spin = $box.find(".wait-shape"),
        $shimg = $box.find(".nei-img"),
        color = $(".neighborhood-list").attr('data-color'),
        shape = $box.attr('id'),
        topic_title = $.trim($(".header-forum-search [name=topic]").val()),
        post_data = {
            'pag_page' : $this.attr('data-page'),
            'shape_id' : $this.attr('data-shapeid')
        };
    if(topic_title != '') {
        post_data.topic = topic_title;
    }
    $.each(['country', 'state', 'city'], function(i, id) {
        var vs = $("#"+id+"-filter option:selected").val();
        if(vs != '') {
            post_data[id] = vs;
        } 
    });
    
    $shimg.addClass('hide');
    $spin.removeClass('hide');
    
    $.ajax({
        url  : '/neighborhood/' + color,
        type : 'post',
        data : post_data,
        dataType: 'json',
        success: function(data) {
            if(data.stop_request !== undefined && data.stop_request) {
                $this.addClass('stop-request');
            }
            if (data.participants !== undefined) {
                $.each(data.participants, function(i, row) {
                    var $tr = $("<tr/>")
                        .append('<td style="width:34px; height:25px;" class="nopad text-center"><img src="' + row.ava + '" alt="usr-img" width="34"/></td>')
                        .append('<td style="width:188px;" class="user-name"><a href="/neighborhood/username/'+row.encoded_username+'">'+ row.username + '</a></td>')
                        .append('<td style="width:115px;">' + row.created_datetime + '</td>')
                        .append('<td style="width:247px;"><a href="/forum/topic-'+row.id+'/'+color+'/'+shape+'"">“' + row.title + '”</a></td>')
                        .append('<td style="width:67px;" class="nopad text-center">' + row.users_count + '</td>')
                        .append('<td style="width:67px;" class="nopad text-center">' + row.posts_count + '</td>')
                        .append('<td style="width:67px;" class="nopad text-center">' + row.shares_count + '</td>')
                        .append('<td style="width:175px;"><a href="/neighborhood/date/' + row.last_post1 + '/'+color+'/'+shape+'">' + row.last_post2 + '</a></td>');
                    if($.inArray(row.id, data.unviewed_ids) !== -1) {
                        $tr.addClass('unviewed');
                    }
                    $tr.appendTo($this.find('tbody'));
                });
                $this.slimScroll().mouseover();
                $this.attr('data-page', parseInt(post_data.pag_page) + 1);
                if($this.find('tbody tr').length == 0) {
                    $this.collapse('hide');
                }
            }
        },
        error: function (xhr, text_status, error_thrown) {
            if (text_status != "abort" && xhr.status !== 0) {
                requestFailed();
            }
        },
        complete: function() {
            nextrows_request = false;
            $spin.addClass('hide');
            $shimg.removeClass('hide');
        }
    });
}

$(function() {
    $(".neighborhood-list > div").mouseover(function() {
        if (!$(".nh-shape").hasClass("disabled"))
            $(".nh-shape img").attr("src", "/assets/img/shapes/shape/" + $(this).attr("id") + ".png");
    });
    
    if($(".pre-shape").length) {
        $('html, body').animate({
            scrollTop: $(".pre-shape:first").offset().top - 40 
        }, 500);
        $(".pre-shape:first").trigger('mouseover');
    } else if($(".collapse.in").length) {
        $('html, body').animate({
            scrollTop: $(".collapse.in:first").offset().top - 75
        }, 500);
    }
    
    $(".btn-search").click(function(e) {
        e.preventDefault();
        var color = $(".neighborhood-list").attr('data-color'),
            action_url = '/neighborhood/' + color;
        $(".header-forum-search")
            .removeAttr('onsubmit')
            .attr('action', action_url)
            .submit();
    });
    
    $(document).on('mouseover', '.tpd-tooltip', function() {
        $(this).remove();
    });
    
    $(".nbr-data").on('show.bs.collapse', function (e) {
        if(nextrows_request === false) {
            var $this = $(e.currentTarget);
            $this.find('tbody').empty();
            $this.attr('data-page', 0);
            $this.removeClass('stop-request');
            loadNextRows($this);
        } else {
            e.preventDefault();
            e.stopPropagation();
        }
    }).on('shown.bs.collapse', function (e) {
        var $this = $(e.currentTarget);
        $this.slimScroll().mouseover();
    }).on('hide.bs.collapse', function (e) {
        var $this = $(e.currentTarget);
        $this.scrollTop(0);
        $this.next(".slimScrollBar").css('top', '0px');
    }).scroll(function(e) {
        var h = $(this).find("#table tbody").height(),
            h1 = $(this).height(),
            scr_top = $(this).scrollTop();
        if((h1 + scr_top) > h && nextrows_request === false) {
            var $this = $(e.currentTarget);
            if(!$this.hasClass('stop-request')) {
                loadNextRows($this);
            }
        }
    });
});