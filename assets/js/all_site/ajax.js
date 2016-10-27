$(document).ajaxError(function myErrorHandler(event, xhr, ajaxOptions, thrownError) {
    if (xhr.status == 400) {
        setTimeout(function() {
            document.location.href = '/signout';
        }, 500);
    }
});

function abortAjaxRequest(request) {
    if(request && request.readyState !== 4) {
        request.abort();
    }
}

function toggleActiveClass(obj) {
    if($(obj).hasClass('active')) {
        $(obj).removeClass('active');
    } else {
        $(obj).siblings().removeClass('active');
        $(obj).addClass('active');
    }
}

function popupMessage(title, msg) {
    var modal = $("#modal-window");
    modal.find(".modal-hdr-welcme").html('<h2>' + title + '</h2>');
    modal.find(".modal-body").html('<div style="padding: 20px;">' + msg + '</div>');
    modal.modal('show');
}

function alertMessage (txt) {
    if ($("#alertMsg").is("html *")) {
        $("#alertMsg").remove();
    }
    var Msg = '<div id="alertMsg" class="alertMsg"><div class="msgbody"><div class="msgtxt">' + txt + '</div></div></div>';
    $(Msg).appendTo(".container.main-container").hide().fadeIn(1000);
    setTimeout(function() {
        $("#alertMsg").fadeOut(1000);
    }, 5000);
}

function requestFailed() {
    alert('Request Failed.');
}

function checkUnreadMessages() {
    $.getJSON('/messages/unread/number', function(json) {
        $("#messages .badge").text(json.count);
        if(json.count > 0) {
            $("#messages .badge").removeClass('hide');
        } else {
            $("#messages .badge").addClass('hide');
        }
    })
    .done(function() { setTimeout(checkUnreadMessages, 1000 * 30); })
    .fail(function() { setTimeout(checkUnreadMessages, 1000 * 30); });
}

function checkRequests() {
    $.getJSON('/request/check', function(json) {
        $.each(json.requests, function(type, arr) {
            switch(type) {
                case 'friendship':
                    displayFriendshipRequests(arr);
                    break;
                case 'question_privacy':
                    if($(".questionnaire-requests").length && typeof updateHomepageQuestionnaireRequests == 'function') {
                        updateHomepageQuestionnaireRequests(arr);
                    }
                    if($(".questionnaire-slider:not(.visitor)").length && typeof updateProfileQuestionnaireRequests == 'function') {
                        updateProfileQuestionnaireRequests(arr);
                    }
                    break;
            }
        });
    })
    .done(function() { setTimeout(checkRequests, 1000 * 30); })
    .fail(function() { setTimeout(checkRequests, 1000 * 30); });
}

function checkNotifications() {
    $.getJSON('/notifications/get', function(json) {
        $.each(json.notifications, function(type, arr) {
            if(type == 'photo') {
                var aid = 'photo-notify',
                    block_id = '#photo-notifications';
            } else {
                var aid = 'profile-notify',
                    block_id = '#profile-notifications';
            }
            displayNotifications(arr, aid, block_id);
        });
    })
    .done(function() { setTimeout(checkNotifications, 1000 * 30); })
    .fail(function() { setTimeout(checkNotifications, 1000 * 30); });
}

function sendRequest(type, to_user, optional) {
    var postdata = {
        'type': type,
        'to_user': to_user
    };
    if(optional !== undefined) {
        postdata.optional = optional;
    }

    $.ajax({
        url: '/request/add',
        type: 'post',
        data: postdata,
        dataType: 'json',
        success: function(data) {
            if (data.errors !== undefined) {
                if(data.errors == '') {
                    if(data.success_msg !== undefined) {
                        alert(data.success_msg);
                    }
                    if(type == 'friendship') {
                        if(data.btn_unfriend !== undefined) {
                            var btn_html = '<span class="fr">' + data.btn_text + '</span><span class="unfr hide">' + data.btn_unfriend + '</span>';
                            $(".btn-friendship")
                                .addClass('friends')
                                .attr('onclick', 'unFriend(); return false;')
                                .html(btn_html);
                        } else {
                            $(".btn-friendship")
                                .removeClass('friends')
                                .attr('onclick', 'return false;')
                                .html(data.btn_text);
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

function responseToRequest(type, action, id) {
    $.ajax({
        url: '/request/response',
        type: 'post',
        data: {
            'type': type,
            'action': action,
            'id': id
        },
        dataType: 'json',
        success: function(data) {
            if (data.errors !== undefined) {
                if (data.errors == '') {
                    responseCallback(type, id, data.optional !== undefined ? data.optional : null);
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function responseCallback(type, id, optional) {
    switch (type) {
        case 'friendship':
            var elem = $("#friends-requests div.content[data-id='" + id + "']");
            if (elem.length) {
                elem.remove();
            }
            var count = $("#friends-requests .messages-wrapper div.content").length;
            if (count == 0) {
                $("#friends-requests .friendship").remove();
            }
            $("a#friends .badge").text(count);
            if(count > 0) {
                $("a#friends .badge").removeClass('hide');
            } else {
                $("a#friends .badge").addClass('hide');
            }
            if($(".btn-friendship").length) {
                $(".btn-friendship").text(optional.btn_text).attr('onclick', (optional.action == 'accept' ? '' : "sendRequest('friendship', " +optional.to_user+ "); ") + 'return false;');
            }
            break;
        case 'question_privacy':
            $("#qreq-"+id).empty();
            var remove_tr = true,
                tr = $("#qreq-"+id).closest('tr');
            tr.find('td:not(:first)').each(function() {
                if($.trim($(this).html()) != '') {
                    remove_tr = false;
                    return false;
                }
            });
            if(remove_tr) {
                tr.remove();
            }
            break;
    }
}

function displayFriendshipRequests(arr) {
    var count = arr.length;
    $("a#friends .badge").text(count);
    if(count > 0) {
        $("a#friends .badge").removeClass('hide');
    } else {
        $("a#friends .badge").addClass('hide');
    }
    $("#friends-requests .friendship").remove();
    if (count > 0) {
        $('<div/>')
                .attr('class', 'dropdown-menu dropdown dropdown-tip friendship frm-fri')
                .append($('<div/>').attr('class', 'messages-wrapper'))
                .appendTo("#friends-requests");
        var block = $("#friends-requests .messages-wrapper");
        $.each(arr, function(i, v) {
            var request = block.find('div.content[data-id="'+v.id+'"]');
            if (request.length == 0) {
                var html = '<div class="notification-item"><a href="/friend" class="pimg"><img src="'+v.ava+'" alt=""></a>'+
                            '<a href="/friend" class="uname"><h4 class="item-title">'+v.username+'</h4></a><p class="item-info">requested friendship <br>'+v.when+'</p>'+
                            '<div class="msg-btns"><a class="accept" onclick="responseToRequest(\''+v.type+'\', \'accept\', '+v.id+'); return false;">Accept</a>'+
                            '<a class="decline" onclick="responseToRequest(\''+v.type+'\', \'decline\', '+v.id+'); return false;">Decline</a></div></div>';
                $('<div/>')
                        .attr('class', 'content')
                        .attr('data-id', v.id)
                        .html(html)
                        .appendTo(block);
            }
        });
    }
}

function displayNotifications(arr, aid, block_id) {
    if($(block_id).length == 0) {
        return false;
    }

    // object to array
    var notificationIndexes = [];
    notificationIndexes = $.map(arr.items, function(val, idx) {
        return idx;
    });

    notificationIndexes.sort();
    notificationIndexes.reverse();
    var notificationArray = [];
    $.each(notificationIndexes, function(idx, val) {
        notificationArray.push(arr.items[val]);
    });

    var count = arr.count;
    $("a#"+aid+" .badge").text(count);
    if(count > 0) {
        $("a#"+aid+" .badge").removeClass('hide');
    } else {
        $("a#"+aid+" .badge").addClass('hide');
    }
    $(block_id+" .messages.woc-msg").remove();
    if (count > 0) {
        $('<div/>')
                .attr('class', 'dropdown-menu dropdown dropdown-tip messages woc-msg')
                .append($('<div/>').attr('class', 'messages-wrapper'))
                .appendTo(block_id);
        var block = $(block_id +" .messages-wrapper");
        $.each(notificationArray, function(time, items) {
            $.each(items, function(key, v) {
                var notify = block.find('div.content[data-id="'+v.id+'"]');
                if (notify.length == 0) {
                    var view_href = '';
                    if(v.type == 'profile_comment' || v.type == 'profile_comment_vote') {
                        view_href = v.optional == v.to_user ? '/profile' : '/visitor/uid-'+v.optional;
                    } else if(v.type == 'topic_comment' || v.type == 'topic_comment_vote') {
                        view_href = '/forum/topic-' + v.url_part;
                    } else {
                        view_href = '/photo/preset_page' + v.url_part;
                    }
                    var html = '<div class="notification-item"><a href="/visitor/uid-'+v.from_user+'" class="pimg"><img src="'+v.ava+'" alt=""></a>'+
                                '<a href="/visitor/uid-'+v.from_user+'" class="uname"><h4 class="item-title">'+v.username+'</h4></a><p class="item-info">'+v.notify_text+' <br>'+v.when+'</p>'+
                                '<div class="msg-btns"><a class="accept" href="'+view_href+'">View</a></div></div>';
                    $('<div/>')
                            .attr({'class' : 'content', 'data-id' : v.id})
                            .html(html)
                            .appendTo(block);
                }
            });
        });
    }
}

Date.prototype.stdTimezoneOffset = function() {
    var jan = new Date(this.getFullYear(), 0, 1);
    var jul = new Date(this.getFullYear(), 6, 1);
    return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
}

Date.prototype.dst = function() {
    return this.getTimezoneOffset() < this.stdTimezoneOffset();
}

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

$(function() {
    $.expr[":"].Contains = $.expr.createPseudo(function (arg) {
        return function (elem) {
            return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
        };
    });

    if ($("#top-nav").length && !$("#top-nav").hasClass('not-joined')) {
        checkRequests(); // updated with 30 seconds interval
        checkNotifications(); //updated with 30 seconds interval
        checkUnreadMessages(); // updated with 30 seconds interval
    }

    $(".btn-friendship, .btn-message").mouseenter(function () {
        if($(this).hasClass('friends')) {
            $(this).find('.fr').addClass('hide');
            $(this).find('.unfr').removeClass('hide');
            $(this).attr('onclick', 'unFriend(); return false;');
        }
    }).mouseleave(function () {
        if($(this).hasClass('friends')) {
            $(this).find('.unfr').addClass('hide');
            $(this).find('.fr').removeClass('hide');
            $(this).attr('onclick', 'return false;');
        }
    });

    var d = new Date();
    var tzo = -d.stdTimezoneOffset() / 60 + (d.dst() ? 1 : 0);
    if ($.cookie('timezoneoffset') === undefined) {
        $.cookie('timezoneoffset', tzo, {path: '/'});
    }
});
