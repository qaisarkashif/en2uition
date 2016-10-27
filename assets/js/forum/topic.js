var sliders = new Array();

function addNewTopic(color, shape) {
    var form = $("form[name=topic]");
    var topic_data = {
        'title': $(form).find('[name="topic_title"]').val(),
        'text': $(form).find('.reply').val(),
        'color': color,
        'shape': shape
    };
    $.ajax({
        url: '/add-new-topic',
        type: 'post',
        data: topic_data,
        dataType: 'json',
        success: function(data) {
            if (data.errors !== undefined) {
                if (data.errors == '') {
                    window.location = data.url;
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function commentTopic(topic_id, reply_to) {
    var topic_data = {
        'topic_id': topic_id,
        'text': $(".wysiwyg-container:visible .reply").val()
    };
    if(reply_to !== undefined) {
        topic_data.reply_to = reply_to;
    }
    $.ajax({
        url: '/forum/topic/add-comment',
        type: 'post',
        data: topic_data,
        dataType: 'json',
        success: function(data) {
            if (data.errors !== undefined) {
                if (data.errors == '' && data.comment !== undefined) {
                    if(reply_to !== undefined) {
                        replyAdded(topic_data, data.comment);
                    } else {
                        mainCommentCreated(topic_data, data.comment);
                    }
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function deletePost(box, cid, undelete) {
    $.ajax({
        url: '/forum/topic/delete-comment',
        type: 'post',
        data: { 
            'id' : cid, 
            'undelete' : undelete ? 'yes' : 'no'
        },
        dataType: 'json',
        success: function(data) {
            if (data.errors !== undefined) {
                if (data.errors == '') {
                    if(undelete) {
                        var cmt = data.comment;
                        box.find('.cmt-short-txt .txt').html(cmt.short_text);
                        box.find('.cmt-full-txt .txt').html(cmt.full_text);
                        if($.trim(cmt.short_text) == '') {
                            box.find('.cmt-short-txt').remove();
                            box.find('.cmt-full-txt').removeClass('hide');
                        } else {
                            box.find('.cmt-full-txt').addClass('hide');
                            box.find('.cmt-short-txt').removeClass('hide');
                            box.find('.btn-expand').before('<a class="btn-more">more</a>').before('<a class="btn-less hide">less</a>');
                        }
                        box.find(".btn-undelete").remove();
                        box.find('.btn-reply, .btn-thread').removeClass('hide');
                        box.find('.cmt-btns').append('<a class="btn-delete" data-id="'+cid+'">delete</a>');
                        var cub = box.prev();
                        if(cub.length && cub.hasClass('comment-user-box')) {
                            cub.find('.like-box .like').attr('onclick', "vote(this, 'topic_comment', 'like', " + cid + ");");
                            cub.find('.like-box .dislike').attr('onclick', "vote(this, 'topic_comment', 'dislike', " + cid + ");");
                        }
                    } else {
                        if(data.topic_removed !== undefined && data.topic_removed == 'yes') {
                            window.location = $("#topic-title").attr('href');
                        } else if(data.full_del !== undefined && data.full_del) {
                            $(".forum-topic-user #cmt-"+cid+", .forum-reply-user #reply-"+cid).remove();
                        } else {
                            box.find('.cmt-short-txt .txt, .cmt-full-txt .txt').html('<span class="deleted-comment-text">' + (data.del_text !== undefined ? data.del_text : 'deleted') + '</span>');
                            box.find('.btn-delete, .btn-less, .btn-more').remove();
                            box.find('.btn-reply, .btn-thread').addClass('hide');
                            box.find('.btn-collapse').after('<a class="btn-undelete" data-id="' + cid + '">undelete</a>');
                            var cub = box.prev();
                            if(cub.length && cub.hasClass('comment-user-box')) {
                                cub.find('.like-box .dislike, .like-box .like').attr('onclick', 'return false;');
                            }
                        }
                    }
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function mainCommentCreated(topic_data, comment) {
    var cmt_box = $("#new-topic-forum .forum-topic-user"),
        reply_box = $("#new-topic-forum .forum-reply-user"),
        cmt_user_box = $(".comm-reply.thread-2.thread-4 .comment-user-box").clone(),
        cmt_text_area = $("#cmt-txt-area-html .cmt-txt-area").clone();
    cmt_text_area.attr({'data-topic' : topic_data.topic_id, 'data-cid' : comment.id});
    if($.trim(comment.short_text) == '') {
        cmt_text_area.find('.btn-more, .btn-less, .cmt-short-txt').remove();
        cmt_text_area.find('.cmt-full-txt').removeClass('hide');
    } else {
        cmt_text_area.find('.cmt-short-txt .txt').html(comment.short_text);
    }
    cmt_text_area.find('.btn-delete').attr('data-id', comment.id);
    cmt_text_area.find('.cmt-full-txt .txt').html(topic_data.text);
    cmt_text_area.find('.btn-thread').attr({'data-type' : 'share', 'data-sid' : comment.id});
    var reply_table = '<table cellspacing="0" cellpadding="0" border="0" width="100%" style="display: table;" id="topic-slider-' + comment.id + '" class="topic-slider-bb" data-id="' + comment.id + '">';
        reply_table += '<tbody><tr><td valign="top" class="topic"><div class="slider">';
    for (var s = 1; s <= 7; s++) {
        reply_table += '<div class="slide"><div></div></div>';
    }
    reply_table += '</div></td></tr></tbody></table>';
    var lbox = cmt_user_box.find(".like-box");
    lbox.attr('data-id', comment.id);
    lbox.find(".total-dislike").attr('title', 'Voted -1:');
    lbox.find(".total-like").attr('title', 'Voted +1:');
    lbox.find(".like").attr('onclick', "vote(this, 'topic_comment', 'like', " + comment.id + ");");
    lbox.find(".dislike").attr('onclick', "vote(this, 'topic_comment', 'dislike', " + comment.id + ");");
    cmt_user_box.find(".comment-time").html(comment.date + '<br/>' + comment.time);
    var cmt = $("<div/>")
        .attr({'id' : 'cmt-' + comment.id, 'class' : 'user-topics'})
        .append(cmt_user_box)
        .append(cmt_text_area);
    var reply = $("<div/>")
        .attr({'id' : 'reply-' + comment.id, 'class' : 'topic-user-reply'})
        .append(reply_table);
    cmt_box.append(cmt);
    reply_box.append(reply);
    initReplySlider(reply.find('.slider'), comment.id);
    $("#forum-topic-user").addClass('hide');
    $("#new-topic-forum").removeClass('hide');
    adjustBoxHeight(comment.id);
    Tipped.create('.cmt-tooltip',{ containment: 'viewport', position: 'bottom', inline: true });
}

function replyAdded(topic_data, comment) {
    var rt = topic_data.reply_to,
        slide = $(".slide[data-id='"+rt+"']");
    if(slide.length) {
        slide.find("span").text(parseInt(slide.find("span").text()) + 1);
        rt = slide.closest("[id^='topic-slider-']").attr('data-id');
    } else {
        var html  = '<div class="slide" style="float: left; list-style: none; position: relative; margin-right: 4px; width: 70px;" data-id="'+comment.id+'"><div>';
        html += '<a onclick="showReplies(this, '+comment.id+', '+topic_data.topic_id+'); return false;"><img alt="" src="'+$(".comm-reply.thread-2.thread-4 .user-img").attr('src')+'"><span>1</span></a></div></div>';
        $("#topic-slider-"+rt+" .slide:not([data-id])").remove();
        $("#topic-slider-"+rt+" .slider").append(html);
    }
    $("#topic-slider-"+rt).css('display', 'table');
    $("#reply-"+rt).find('.comm-reply.thread-2.thread-4, .thread-replies').remove();
    adjustBoxHeight(rt);
    sliders[rt].reloadSlider();
}

function showReplies(slide, id, main_id) {
    $.ajax({
        url: '/forum/topic/show-comments',
        type: 'get',
        data: {
            'id' : id
        },
        dataType: 'json',
        success: function(data) {
            if (data.replies !== undefined && data.length > 0) {
                var table = $("<table/>").attr({'cellspacing': '0', 'cellpadding': '0', 'border': '0', 'width': "100%", 'class': "thread-replies"}).css("display", "table"),
                    tbody = $("<tbody/>");
                var rt;
                $.each(data.replies, function(cid, cmt) {
                    var box = $("<div/>").attr("class", "comm-reply"),
                        cmt_box = $("#forum-topic-user .comment-user-box").clone(),
                        cmt_area = $("#cmt-txt-area-html .cmt-txt-area").clone();
                    if(rt === undefined) { rt = cmt.reply_to; }
                    cmt_area.attr({'data-topic' : main_id, 'data-cid' : rt});
                    if($.trim(cmt.short_text) == '') {
                        cmt_area.find('.btn-more, .btn-less, .cmt-short-txt').remove();
                        cmt_area.find('.cmt-full-txt').removeClass('hide');
                    }
                    if(cmt.author_id == $("#my-id").val()) {
                        cmt_area.find('.btn-delete').attr('data-id', cid);
                    } else {
                        cmt_area.find('.btn-delete').remove();
                    }
                    if(cmt.deleted == '1') {
                        cmt_area.find('.cmt-short-txt .txt, .cmt-full-txt .txt').html('<span class="deleted-comment-text">' + (data.del_text !== undefined ? data.del_text : 'deleted') + '</span>');
                        cmt_area.find('.btn-delete').remove();
                        if(cmt.author_id == $("#my-id").val()) {
                            cmt_area.find(".btn-collapse").after('<a class="btn-undelete" data-id="'+cid+'">undelete</a>')
                        }
                        cmt_area.find('.btn-reply, .btn-thread').addClass('hide');
                    } else {
                        cmt_area.find('.cmt-short-txt .txt').html(cmt.short_text);
                        cmt_area.find('.cmt-full-txt .txt').html(cmt.full_text);
                        cmt_area.find('.btn-thread').attr({'data-type' : (cmt.my_share ? 'unshare' : 'share'), 'data-sid' : cid});
                        cmt_area.find('.btn-thread .st').text(cmt.shares_count ? cmt.shares_count : 0);
                    }
                    cmt_area.find(".btn-expand").remove();
                    cmt_area.find(".btn-collapse").removeClass('hide');
                    cmt_box.find(".user-img").attr('src', cmt.ava);
                    cmt_box.find(".cmt-user-info").html(cmt.author+'<br/><span class="comment-time">'+cmt.date+"<br/>"+cmt.time+'</span>');
                    var lbox = cmt_box.find(".like-box");
                    lbox.attr('data-id', cid);
                    lbox.find(".total-dislike").attr('title', cmt.dislike_tooltip_title).text(cmt.dislike_count ? '-'+cmt.dislike_count  : '-0');
                    lbox.find(".total-like").attr('title', cmt.like_tooltip_title).text(cmt.like_count ? '+'+cmt.like_count : '+0');
                    var like_li = lbox.find(".like"),
                        dislike_li = lbox.find(".dislike");
                    like_li.attr('onclick', cmt.deleted == '1' ? "return false;" : "vote(this, 'topic_comment', 'like', " + cid + ");");
                    dislike_li.attr('onclick', cmt.deleted == '1' ? "return false;" : "vote(this, 'topic_comment', 'dislike', " + cid + ");");
                    if(cmt.my_vote == 'like') {
                        dislike_li.find('a').removeClass('r');
                        like_li.find('a').addClass('g');
                    } else if(cmt.my_vote == 'dislike') {
                        like_li.find('a').removeClass('g');
                        dislike_li.find('a').addClass('r');
                    }
                    box
                        .append(cmt_box)
                        .append(cmt_area);
                    var tr = $('<tr class="thread-posts" data-cid="'+cid+'"><td></td></tr>').append(box);
                    tbody.append(tr);
                });
                tbody.find(".btn-reply:not(:last)").remove();
                tbody.find(".btn-reply, .btn-expand, .btn-collapse, .btn-more, .btn-less").addClass('rpl');
                table.append(tbody);
                var box = $(slide).closest('.topic-user-reply'),
                    $box = box.find('[id^="topic-slider-"]');
                $box.before(table).hide();
                $('#cmt-'+rt).find('.btn-more').click();
                adjustBoxHeight($box.attr('data-id'));
                $("#reply-"+rt).find(".btn-more").click();
                Tipped.create('.cmt-tooltip',{ containment: 'viewport', position: 'bottom', inline: true });
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function adjustBoxHeight(tid) {
    $("#reply-" + tid + ", #cmt-" + tid).css('height', 'auto');
    if ($("#reply-" + tid).height() <= $("#cmt-" + tid).height()) {
        $("#reply-" + tid).css('height', ($("#cmt-" + tid).height() + 2) + 'px');
    } else {
        $("#cmt-" + tid).css('height', ($("#reply-" + tid).height() + 2) + 'px');
    }
}

function initReplySlider(slider, id) {
    var sl = $(slider).bxSlider({
        slideWidth: 70,
        minSlides: 6,
        maxSlides: 6,
        slideMargin: 4,
        moveSlides: 1,
        infiniteLoop: false,
        pager: false,
        speed: 1
    });
    if(id === undefined && $(slider).closest("[id^='topic-slider-']").length !== 0) {
        id = $(slider).closest("[id^='topic-slider-']").attr('data-id');
    }
    if(id !== undefined) {
        sliders[id] = sl;
    }
    $("#topic-slider-"+id+" .slider").mousewheel(function(event, delta, deltaX, deltaY) {
        event.stopPropagation();
        event.preventDefault();
        if (delta > 0) {
            sl.goToPrevSlide();
        }
        if (deltaY < 0) {
            sl.goToNextSlide();
        }
    });
}

function toggleElems(box, lessmore, is_right_thread) {
    var selectors1 = new Array(),
        selectors2 = new Array();
    if(box.find(".cmt-short-txt").length) {
        selectors1.push('.cmt-short-txt');
        selectors1.push('.btn-more');
        selectors2.push('.cmt-full-txt');
        selectors2.push('.btn-less');
    }
    if(!is_right_thread) {
        selectors1.push(".btn-expand");
        selectors2.push(".btn-collapse");
    }
    if (lessmore == 'more') {
        box.find(selectors1.join(',')).addClass('hide');
        box.find(selectors2.join(',')).removeClass('hide');
    } else {
        box.find(selectors2.join(',')).addClass('hide');
        box.find(selectors1.join(',')).removeClass('hide');
    }
    adjustBoxHeight(box.attr('data-cid'));
}

$(function() {
    $(document).on("mouseenter", ".btn-thread", function() {
        $(this).find('.cs').html($(this).attr('data-type'));
        $(this).find('.st').css("opacity", "0");
    }).on("mouseleave", ".btn-thread", function() {
        $(this).find('.st').css("opacity", "1");
        $(this).find('.cs').html("shares");
    }).on('click', ".btn-thread", function(e) {
        e.preventDefault();
        shareComment(this, $(this).attr('data-sid'), 'topic_comment');
    }).on('submit', 'form', function(event) {
        event.preventDefault();
        event.stopPropagation();
    }).on('click', '.cmt-btns a', function(event) {
        event.preventDefault();
        var box = $(this).closest(".cmt-txt-area"),
            cid = box.attr('data-cid'),
            tid = box.attr('data-topic'),
            rbox = $("#reply-"+cid),
            tslider = $("#topic-slider-"+cid);
        if ($(this).hasClass('btn-more') || $(this).hasClass('btn-less')) {
            toggleElems(box, $(this).hasClass('btn-more') ? 'more' : 'less', $(this).hasClass('rpl'));
        } else if ($(this).hasClass('btn-expand') || $(this).hasClass("btn-collapse")) {
            var t = $(this).hasClass('rpl') ? "reply" : "cmt",
                t2 = $(this).hasClass('btn-expand') ? "more" : "less";
            if($(this).hasClass('rpl')) {
                if($(this).hasClass('btn-collapse')) {
                    rbox.find(".comm-reply.thread-2.thread-4:visible .comm-btn .cancel").click();
                    rbox.find(".thread-replies").remove();
                    tslider.css('display', 'table');
                    $("#cmt-"+cid).find('.btn-less').click();
                }/* else if($(this).hasClass('btn-expand')) {
                    rbox.find(".thread-posts").each(function() {
                        toggleElems($(this).find(".cmt-txt-area"), t2);
                    });
                    $("#cmt-"+cid).find('.btn-more').click();
                }*/
            } else {
                $(".user-topics").each(function() {
                    toggleElems($(this).find(".cmt-txt-area"), t2, false);
                });
            }
        } else if ($(this).hasClass('btn-reply')) {
            $(".wysiwyg-container .wysiwyg-editor").text('');
            $(".wysiwyg-container .reply").val('');
            if(box.closest('.user-topics').is(":last-child") && !$(this).hasClass('rpl')) {
                $("[id^='reply-'] .comm-reply.thread-2.thread-4:visible").find(".comm-btn .cancel").click();
                $("#forum-topic-user table:first").css('border-top', 'none');
                $("#forum-topic-user .publish-new-topic").attr('onclick', 'commentTopic('+tid+');');
                $("#forum-topic-user").removeClass('hide');
            } else {
                $("#forum-topic-user").addClass('hide');
                $("[id^='reply-'] .comm-reply.thread-2.thread-4:visible").find(".comm-btn .cancel").click();
                if(!$(".comm-reply.thread-2.thread-4:visible").length) {
                    if(!$(this).hasClass('rpl')) {
                        rbox.find(".btn-collapse").click();
                    }
                    var html = $(".comm-reply.thread-2.thread-4").clone();
                    html.find(".wysiwyg-container").after('<textarea name="topic" class="reply"></textarea>');
                    html.find(".wysiwyg-container").remove();
                    tslider.css('display', 'none').before(html);
                    rbox.css('height', 'auto');
                    var comm_reply = $(".comm-reply.thread-2.thread-4:visible");
                    var ptd =  comm_reply.find(".cmt-user-info").prev('td');
                    comm_reply.find(".cmt-user-info").insertBefore(ptd);
                    comm_reply.find(".comment-user-box").css('float', 'right');
                    var lbox = comm_reply.find(".like-box");
                    lbox.find(".total-dislike").attr('title', 'Voted -1:');
                    lbox.find(".total-like").attr('title', 'Voted +1:');    
                    var rep_on = $(this).hasClass('rpl') ? rbox.find(".thread-posts:first").attr('data-cid') : cid;
                    comm_reply.find(".publish-new-topic").attr('onclick', 'commentTopic('+tid+','+rep_on+');');
                    $.fn.wysiwyg_ini($(".reply:visible"));
                    comm_reply.find(".comm-btn .cancel").click(function(e) {
                        e.preventDefault();
                        comm_reply.remove();
                        if(!rbox.find(".thread-replies").length) {
                            tslider.css('display', 'table');
                        } else {
                            rbox.find(".thread-replies").removeClass('topic-slider-bb');
                        }
                        adjustBoxHeight(cid);
                    });
                    if($(this).hasClass('rpl')) {
                        rbox.find(".thread-replies:last").addClass('topic-slider-bb');
                    }
                }
            }
            Tipped.create('.cmt-tooltip',{ containment: 'viewport', position: 'bottom', inline: true });
            setTimeout(function(){ $(".wysiwyg-editor:visible").focus(); }, 0);
        } else if($(this).hasClass('btn-delete') || $(this).hasClass('btn-undelete')) {
            deletePost(box, $(this).attr('data-id'), $(this).hasClass('btn-undelete'));
        }
        adjustBoxHeight(cid);
    }).on('click', '.show-all-voters', function(e) {
        e.preventDefault();
        var type = $(this).attr('data-type'),
            cid = $(this).attr('data-cid');
        showAllVoters('topic_comment', type, cid);
    });

    $(".thread-4 .wysiwyg-editor").focus();
    
    $('.topic .slider').each(function(i, slider) { 
        initReplySlider(slider);
    });
    
    $(".user-topics").each(function() {
        adjustBoxHeight($(this).find(".cmt-txt-area").attr('data-cid'));
    });
});