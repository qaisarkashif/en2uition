var my_name,
    my_uid,
    my_ava;

function cutComment(cid, text, target, isSub) {
    if (text.length > 200 && target !== 'photo') {
        var str = text.substr(0, 200),
            str2 = text.substr(200, text.length - 200),
            cmt_html = str + '<span id="more-text' + cid + '" style="display:none;">' + str2 + '</span>' +
            '<a id="main-more-' + cid + '" class="more" ' + (isSub ? '' : 'onClick="$(\'#more-text' + cid + '\').toggle();"') + '>more</a>';
        return cmt_html;
    } else {
        return text;
    }
}

function krsort(inputArr) {
    var indexes = [];
    indexes = $.map(inputArr, function(val, idx) {
        return parseInt(idx);
    });
    indexes.sort(function (a, b) { return a - b; });
    indexes.reverse();
    var outputArray = [];
    for(var x in indexes) {
        outputArray.push(inputArr[indexes[x]]);
    }
    return outputArray;
}

function newCommentBox(target, tid, rto) {
    var $box = $("#new-comment-html").clone(),
        $img = $box.find(".comment-img"),
        $cmt = $box.find(".comment-detail"),
        $txt = $box.find(".comment-text"),
        $field = $txt.find("[name=comment]"),
        link = '/visitor/uid-' + autor_uid;
    $img.find('a').attr('href', link);
    $img.find('img').attr('src', autor_ava);
    $cmt.find('a').attr('href', link);
    $cmt.find("span").html(autor_name);
    $field.attr({
        'data-target'    : target,
        'data-target-id' : tid,
        'data-parent-id' : rto
    });
    if(rto > 0) {
        $("<a/>")
            .addClass("cancel-cmt")
            .text("cancel")
            .appendTo($txt);
    }
    return $box.html();
}

function commentBox(cid, target, cmt) {
    var $box = $("#comment-html").clone(),
        $img = $box.find(".comment-img"),
        $cmt = $box.find(".comment-detail"),
        $txt = $box.find(".comment-text"),
        $like = $box.find(".like-box"),
        $share = $box.find('.comment-share'),
        $cmt_post = $box.find(".comment-post"),
        link = '/visitor/uid-' + cmt.user_id,
        my_vote = cmt.my_vote !== undefined ? cmt.my_vote : '',
        my_share = cmt.my_share !== undefined ? cmt.my_share : '',
        like_count = cmt.like_cnt ? cmt.like_cnt : 0,
        dislike_count = cmt.dislike_cnt ? cmt.dislike_cnt : 0,
        who_voted_like = new Array("voted +1:"),
        who_voted_dislike = new Array("voted -1:"),
        shares_count = cmt.shares_count ? cmt.shares_count : 0,
        $del = $box.find(".delete-cmt-box");
    $img.find('a').attr('href', link);
    $img.find('img').attr('src', cmt.ava);
    $cmt.find('a').attr('href', link);
    $cmt.find("span").html(cmt.username);
    $cmt.find("dttm").html(cmt.date);

    var $flw_lnk = '';
    if($(".comments.photo-page").length == 0) {
        if(target == 'photo_comment' && !cmt.reply_to) {
            $cmt.after($("<div/>").text('Comment on a photo:'));
            var $a = $("<a/>").attr('href', '/photo/page/preset/'+cmt.album_id+'/'+cmt.target_id+'/'+cmt.owner).text('go to photo');
            $flw_lnk = $("<div/>").addClass("follow-photo").append($a);
        } else if(target == 'topic_comment') {
            $cmt.after($("<div/>").text('Comment on topic: '+cmt.topic_title));
        }
    }

    if(target === 'photo') {
        var text_html = '';
        if($.trim(cmt.album_title) !== '') {
            text_html += '<p>'+$.trim(cmt.album_title)+'</p>';
        }
        if($.trim(cmt.photo_title) !== '') {
            text_html += '<p>'+$.trim(cmt.photo_title)+'</p>';
        }
        text_html += '<div class="clearfix" style="margin-bottom: 5px;"></div><div class="shared-foto">'+
               '<a class="magnific-popup" href="'+cmt.original+'"><img alt="shared foto" src="'+cmt.medium+'"/></a></div>';
    } else {
        var text_html = cutComment(cid, cmt.comment, target, Boolean(cmt.reply_to));
    }

    $txt.html(text_html);
    $like.find(".total-dislike").text("-" + dislike_count);
    $like.find(".total-like").text("+" + like_count);
    $like.find(".dislike").attr('onclick', "vote(this, '" + target + "', 'dislike', " + cid + "); return false;");
    $like.find(".like").attr('onclick', "vote(this, '" + target + "', 'like', " + cid + "); return false;");
    if (my_vote == 'dislike') {
        $like.find(".dislike a").addClass('r');
    } else if (my_vote == 'like') {
        $like.find(".like a").addClass('g');
    }
    if (cmt.who_voted_like) {
        who_voted_like = who_voted_like.concat(cmt.who_voted_like.split('|'));
    }
    if (cmt.who_voted_dislike) {
        who_voted_dislike = who_voted_dislike.concat(cmt.who_voted_dislike.split('|'));
    }
    if (who_voted_like.length > 10) {
        who_voted_like.push('<a onclick="showAllVoters(\'' + target + '\', \'like\', ' + cid + '\)" style="color: blue; text-decoration: underline;">show all voters</a>');
    }
    if (who_voted_dislike.length > 10) {
        who_voted_dislike.push('<a onclick="showAllVoters(\'' + target + '\', \'dislike\', ' + cid + '\)" style="color: blue; text-decoration: underline;">show all voters</a>');
    }
    if(!cmt.reply_to) {
        $like.attr('data-id', cid);
        $box.attr({
            'data-id': (target == 'topic_comment' || target == 'photo' ? cmt.shared_id : cid),
            'data-target': target });
    } else {
        var $pusher = $('<div class="subcomm-likebox-pusher">');
        var $bb = $box.find(".comment-info");
        $bb.find(".clear").remove();
        $("<div/>")
            .addClass("like-box")
            .attr('data-id', cid)
            .html($like.html())
            .insertAfter($(".comment-img", $bb));
        $pusher.prependTo($bb);
        $like.remove();
        $box.find(".comment-footer").remove();
    }
    if(cmt.user_id == autor_uid && $(".homepage-comments").length == 0 && target !== 'topic_comment') {
        var a = $("<a/>").text('delete').attr('onclick', "deleteComment(this, '" + target + "'," + cid + "); return false;");
        $del.append(a);
    } else {
        $del.remove();
    }
    $like = $box.find(".like-box");
    Tipped.create($like.find('.total-dislike'), who_voted_dislike.join('<br/>'), {containment: 'viewport', position: 'bottom'});
    Tipped.create($like.find('.total-like'), who_voted_like.join('<br/>'), {containment: 'viewport', position: 'bottom'});

    if($flw_lnk)
        $flw_lnk.insertBefore($like);

    if($(".comments.photo-page").length == 0) {
        $share.find('a').attr({
            'data-type' : my_share ? 'unshare' : 'share',
            'onclick'   : "shareComment(this, " + cid + ", '" + target + "'); return false;"
        });
        $share.find(".st").text(shares_count);
        $share.find(".cs").text('shares');
    } else {
        $share.remove();
    }

    if(target === 'photo' || target === 'topic_comment') {
        var href = '';
        if(target === 'photo')
            href = '/photo/page/preset/'+cmt.album_id+'/'+cmt.id+'/'+cmt.owner;
        else
            href = '/forum/topic-'+cmt.topic_id+'/'+cmt.color+'/'+cmt.shapename;
        var $a = $("<a/>").attr('href', href).text('follow the link to comment');
        $cmt_post.empty().append($a).addClass('photo-post');
    }

    $box.removeClass('hide').removeAttr('id');
    return $box;
}

function displayComments($main_box, data, form_at_top) {
    var $w = $main_box.find(".wait"),
        comments = krsort(data.comments);
    autor_uid = data.autor_uid;
    autor_name = data.autor_name;
    autor_ava = data.autor_ava;

    if(form_at_top === undefined && $("div.new-comment").length == 0) {
        var $ncbox = $("<div/>")
                .addClass('new-comment')
                .append(newCommentBox(data.target, data.id, -1));
        $w.before($ncbox);
    }

    for(var x in comments) {
        var cmt = comments[x],
            target = cmt.target ? cmt.target : data.target,
            cid = cmt.id,
            target_id = data.id !== undefined ? data.id : cmt.target_id,
            $comment = commentBox(cid, target, cmt);
        if(target == 'profile_comment' || target == 'photo_comment') {
            var $ncbox = $("<li/>").addClass('new-comment hidden').append(newCommentBox(target, target_id, cid)),
                $replies = $("<ul/>").append($ncbox),
                replies = cmt.replies;
            for(var j in replies) {
                var rcmt = replies[j],
                    rcid = rcmt.id;
                    $reply = commentBox(rcid, target, rcmt);
                $("<li/>")
                    .addClass("sub-comm-detail")
                    .append($reply.find("div:first-child .comment-info"))
                    .appendTo($replies);
            }
            $("<div/>")
                .addClass('sub-comm hidden')
                .append($replies)
                .appendTo($comment);
        }
        $comment.append($("<div/>").addClass("clear"));
        $w.before($comment);
    }
    postInit();
    $(".comments-box textarea[name$='comment']").elastic();
    setTimeout(function() {
        $("textarea[name$='comment']:visible").focus();
    }, 0);
}

function postInit() {
    $(".comments-box .comment > div:not(.sub-comm)").each(function(i) {
        $(this).removeClass('even odd');
        $(this).addClass((i + 1) % 2 == 0 ? 'even' : 'odd');
        $(this).closest(".comment").find(".sub-comm-detail").each(function(i) {
            $(this).removeClass('even odd');
            $(this).addClass((i + 1) % 2 == 0 ? 'even' : 'odd');
        });
    });

    $(".comments-box .comment").each(function() {
        var tc = $(this).find(".sub-comm-detail").length,
            cm = tc > 1 ? 'comments' : 'comment';
        var bb = $(this).find(".comment-post");
        bb.find(".toggle-replies .tc").text(tc);
        bb.find(".toggle-replies .cm").text(cm);
    });
}

function toggleReplies(link, state) {
    var replies = $(link).closest(".comment").find('.sub-comm');
    if (state !== undefined) {
        replies.toggleClass('hidden', state);
    } else {
        replies.toggleClass('hidden');
    }

    replies.find('.sub-comm-detail').each(function(idx, element) {
        pushDownSubCommentLikesbox($(element));
    });

    replies.find('textarea').val('');
    replies.find('.new-comment').addClass('hidden');
    replies.closest('.comment').find('span.ts').text(replies.hasClass('hidden') ? 'view' : 'hide');
}

function addReply(link) {
    $(".sub-comm:not(.hidden)").each(function() {
        $(this).closest(".comment").find('.toggle-replies:first').click();
    });
    toggleReplies(link, false);
    var nc = $(link).closest(".comment").find(".new-comment");
    nc.removeClass('hidden');
    nc.find('textarea').val('').focus();
}

function addComment(input, comm) {
    $.ajax({
        url:'/comment/add/' + comm.target,
        type: 'post',
        data: comm,
        dataType: 'json',
        success: function(data) {
            if(data.errors !== undefined) {
                if(data.errors == '') {
                    input.val('');
                    if(data.comment_info !== undefined) {
                        var inf = data.comment_info;
                        var comm_data = {
                            'id'            : inf.id,
                            'target'        : comm.target,
                            'comment'       : comm.comment,
                            'user_id'       : inf.uid,
                            'ava'           : inf.ava,
                            'username'      : inf.username,
                            'date'          : inf.date,
                            'dislike_count' : 0,
                            'like_count'    : 0,
                            'my_vote'       : '',
                            'shares_count'  : 0,
                            'my_share'      : '',
                            'reply_to'      : comm.reply_to == -1 ? '' : comm.reply_to,
                            'my_id'         : inf.uid
                        };
                        if(comm.reply_to > 0) {
                            var $reply = commentBox(inf.id, comm.target, comm_data),
                                $nc = $("<li/>")
                                    .addClass("sub-comm-detail")
                                    .append($reply.find("div:first-child .comment-info"));
                                input.closest('ul').append($nc);
                            pushDownSubCommentLikesbox($nc);
                        } else {
                            var $ncbox = $("<li/>").addClass('new-comment hidden').append(newCommentBox(comm.target, comm.target_id, inf.id)),
                                $replies = $("<ul/>").append($ncbox),
                                $comment = commentBox(inf.id, comm.target, comm_data);
                            $("<div/>")
                                .addClass('sub-comm hidden')
                                .append($replies)
                                .appendTo($comment);
                            var $nc = $comment.append($("<div/>").addClass("clear"));
                            input.closest('.new-comment').after($nc);
                        }
                        postInit();
                        $(input).next().html("");
                        $(input).css('height', '22px');
                     }

/* remove if not needed (12/10)
                    if(comm.reply_to > 0) {
                        input.closest('.new-comment').addClass('hidden');
                    }
*/
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) {
            if (text_status != "abort" && xhr.status !== 0) {
                requestFailed();
            }
        }
    });
}

function deleteComment(link, target, target_id) {
    $.ajax({
        'url': '/comment/delete/' + target,
        'type': 'post',
        'data': {'target_id': target_id},
        'dataType': 'json',
        success: function(data) {
            if (data.errors !== undefined) {
                if (data.errors == '') {
                    $(link).closest('div.comment').remove();
                } else {
                    alert(data.errors);
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

function pushDownSubCommentLikesbox($subComment) {
    // hide the like box to take measurements
    var $likeBox = $subComment.find('.like-box');
    var $likeBoxPusher = $subComment.find('.subcomm-likebox-pusher');
    var $commentHeader = $subComment.find('.comment-detail');
    var $commentText = $subComment.find('.comment-text');

    $likeBox.hide();
    var cmtHeaderHeight = parseInt($commentHeader.css('height'));
    var cmtTextHeight = parseInt($commentText.css('height'));
    var likeboxHeight = parseInt($likeBox.css('height'));
    var height = Math.max(cmtHeaderHeight + cmtTextHeight - likeboxHeight, 50);
    $likeBoxPusher.css('height', height + 'px');
    $likeBox.show();

    // now recalc text with likebox positioned and showing
    cmtTextHeight = parseInt($commentText.css('height'));
    height = Math.max(cmtHeaderHeight + cmtTextHeight - likeboxHeight, 50);
    $likeBoxPusher.css('height', height + 'px');
 }

$(function() {
   $(document).on("keypress", ".comm-area textarea[name=comment]", function(e) {
       var code = e.keyCode || e.which;
       if(code == 13) {
           e.preventDefault();
           var comm = {
               'target'     : $(this).attr('data-target'),
               'target_id'  : $(this).attr('data-target-id'),
               'reply_to'   : $(this).attr('data-parent-id'),
               'comment'    : $.trim($(this).val())
           };
           if(comm.comment) {
               addComment($(this), comm);
           }
       }
   });

    $(".comments-box").on("click", ".sub-comm a.more", function(e) {
        e.preventDefault();
        $(e.currentTarget).prev("span").toggle();
        var $subCommDetail = $(e.currentTarget).closest('.sub-comm-detail');
        pushDownSubCommentLikesbox($subCommDetail);
    });

   $(document).on('mouseenter', '.comment-share a', function() {
       $(this).find(".cs").text($(this).attr('data-type'));
       $(this).find(".st").css('visibility', 'hidden');
   }).on('mouseleave', '.comment-share a', function() {
       $(this).find(".cs").text("shares");
       $(this).find(".st").css('visibility', 'visible');
   }).on('click', '.cancel-cmt', function(e) {
      e.preventDefault();
      $(this).closest(".comm-area").find("textarea").val('');
      $(this).closest(".comment").find(".toggle-replies:first").click();
   }).on('mouseenter', '.toggle-replies.go-comment', function() {
      $(this).find('.tc').css('visibility', 'hidden');
      $(this).find('.cm').text('comment');
   }).on('mouseleave', '.toggle-replies.go-comment', function() {
      $(this).find('.tc').css('visibility', 'visible');
      var c = parseInt($(this).find('.tc').text());
      $(this).find('.cm').text('comment' + (c > 1 ? 's' : ''));
   });
});
