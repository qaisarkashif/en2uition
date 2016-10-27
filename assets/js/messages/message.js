function deleteMessages(id) {
    $.ajax({
        url: '/message/delete',
        type: 'get',
        data: {'id':id},
        dataType: 'json',
        success: function(data) {
            if(data.errors !== undefined) {
                if(data.errors == "") {
                    if($("#msg-list").length) {
                        $("#msg-"+id).remove();
                        updateCounters();
                    } else {
                        window.location = '/messages';
                    }
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function markAsUnread(id, link) {
    $.ajax({
        url: '/message/mark_unread',
        type: 'get',
        data: {'id':id},
        dataType: 'json',
        success: function(data) {
            if(data.errors !== undefined) {
                if(data.errors == "") {
                    $("#msg-"+id).addClass('unread');
                    var c = parseInt($("#messages .badge").text());
                    c++;
                    $("#messages .badge").text(c).removeClass('hide');
                    $(link).remove();
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function updateBlackList(action, uid) {
    $.ajax({
        url: '/users/' + (action == 'add' ? 'block' : 'unblock'),
        type: 'get',
        data: {'uid':uid},
        dataType: 'json',
        success: function(data) {
            if(data.errors !== undefined) {
                if(data.errors == "") {
                    if(action == 'add') {
                        $(".msg-block[data-uid='"+uid+"']").addClass('hide');
                        $(".msg-unblock[data-uid='"+uid+"']").removeClass('hide');
                    } else {
                        $(".msg-unblock[data-uid='"+uid+"']").addClass('hide');
                        $(".msg-block[data-uid='"+uid+"']").removeClass('hide');
                    }
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function addReply(to_msg, to_user) {
    $.ajax({
        url: '/message/send',
        type: 'post',
        data: {
            'reply_to' : to_msg,
            'to_user': to_user,
            'msg_text' : $("#reply").val()
        },
        dataType: 'json',
        success: function(data) {
            if(data.errors !== undefined) {
                if(data.errors == "") {
                    var msg = data.message;
                    $("#reply").val('');
                    var msg_text = "";
                    if (msg.txt.length > 250) {
                        var str = msg.txt.substr(0, 250),
                            str2 = msg.txt.substr(250, msg.txt.length - 250),
                            msg_text = str + '<span id="more-text' + msg.mid + '" style="display:none;">' + str2 + '</span>' +
                            '<a id="main-more-' + msg.mid + '" class="more" onClick="$(\'#more-text' + msg.mid + '\').toggle();">more</a>';
                    } else {
                        msg_text = msg.txt;
                    }
    
                    var tr_html = '<td class="msg-user-info" valign="top"><a href="/visitor/uid-'+msg.from_user+'"><img class="profile-img" src="'+msg.ava+'" alt=""/></a>';
                    tr_html += '<span class="user-info"><a href="/visitor/uid-'+msg.from_user+'"><span>' + msg.username + '</span></a><br>' + msg.date + '</span></td>';
                    tr_html += '<td align="left" class="msg-txt"><p>' + msg_text + '</p></td>';
                    $("<tr/>")
                        .attr('id', 'msg-' + msg.mid)
                        .attr('class', 'user-reply')
                        .html(tr_html)
                        .insertBefore($("#msg-reply-box"));
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function updateCounters() {
    var filters = $("#msg-list .msg-menu");
    filters.find(".friends .badge").text($("#msg-list tr.friend").length);
    filters.find(".strangers .badge").text($("#msg-list tr.stranger").length);
    filters.find(".unread .badge").text($("#msg-list tr.unread").length);
}

function filterRows() {
    var search_name = $(".search_area [name='search_text']").val();
    $(".user-info").each(function() {
        if ($(this).find('span').is(':Contains("' + search_name + '")')) {
            $(this).closest('tr').show();
        } else {
            $(this).closest('tr').hide();
        }
    });
}

$(function() {
    $("#reply").keypress(function(e) {
        var code = e.which || e.keyCode;
        if (code == 13) {
            var to_msg = $(this).attr('data-msg-id'), 
                to_user = $(this).attr('data-to-uid');
            addReply(to_msg, to_user);
        }
    }).val('');
    
    $(".search_area input:submit").click(filterRows);
    $(".search_area [name='search_text']").keypress(function(e) {
        var code = e.which || e.keyCode;
        if (code == 13) {
            filterRows();
        }
    }).val('');
    
    if($("tr.unread").length > 0) {
        var t = $("tr.unread:first").offset().top - 10 - $("#main .header").height();
        $("html, body").animate({
            scrollTop: t > 0 ? t : 0
        }, 500);
    }
    
    if($("#msg-list").length) {
        $(".msg-delete, .msg-block, .msg-unblock").click(function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
        
        $(".msg-delete").click(function() {
            deleteMessages($(this).attr("data-id"));
        });
        
        $(".msg-block").click(function() {
            updateBlackList('add', $(this).attr("data-uid"));
        });
        
        $(".msg-unblock").click(function() {
            updateBlackList('remove', $(this).attr("data-uid"));
        });
        
        updateCounters();
        
        $("#msg-list .msg-menu a").click(function(e) {
            e.preventDefault();
            var $this = $(this),
                cl = "";
            if($this.closest('li').hasClass('active')) {
                $this.closest('li').removeClass('active');
            } else {
                $("#msg-list .msg-menu").find('li.active').removeClass('active');
                $this.closest('li').addClass('active');
                cl = $this.attr('data-v');
            }
            $("#msg-list tbody tr").each(function() {
               if(cl == "" || $(this).hasClass(cl)) {
                   $(this).show();
               } else {
                   $(this).hide();
               }
            });
        });
    }
});