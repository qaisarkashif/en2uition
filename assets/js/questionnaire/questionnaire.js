function getPrivacyQuestion(lvl_id, code) {
    $("#question_list").empty();
    $("#rst-block, #rst-block .wait-img").removeClass('hide');
    $.ajax({
        url: '/questionnaire/privacy_question/list',
        type: 'post',
        dataType: 'json',
        data: {
            'lvl_id': lvl_id,
            'code': code
        },
        success: function(data) {
            if (data.list_length > 0) {
                var li_arr = new Array();
                var i = 1, j = 1, bg_gray = true;
                $.each(data.list, function(n, txt) {
                    var _class = j % 2 === 0 ? 'even' : 'odd';
                    if (bg_gray) {
                        _class += " bg-gray";
                    }
                    var li = '<li class="' + _class + '">' + n + '. ' + txt + '</li>';
                    li_arr.push(li);
                    i++;
                    j++;
                    if (i == 3) {
                        i = 1;
                        bg_gray = !bg_gray;
                    }
                });
                $("#question_list").append(li_arr.join(''));
                $("#question_list li:even").addClass('odd');
                $("#question_list li:odd").addClass('even');
                $("#question_list li:last").css('border-bottom', 'none');
            } else {
                $("<li/>")
                        .attr('class', 'no-found')
                        .text(data.no_found_txt)
                        .appendTo("#question_list");
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } },
        complete: function() {
            $("#rst-block .wait-img").addClass('hide');
        }
    });
}

function toggleElements() {
	$(".q-level-left, .q-level-right").hide().removeClass("li");
	var elem = $(".q-level ul li .questionnaire-past-level");
	elem.removeClass('active');
	elem.find("span").show();
	elem.find("font").hide();
}

function filterRows(obj) {
    var search_name = "";
    if($(obj).attr('type') == 'text') {
        search_name = $(obj).val();
    } else {
        search_name = $(obj).closest(".search_area").find("[name='search_text']").val();
    }
    $(obj).closest('.ql-list').find(".user-name").each(function() {
        if ($(this).is(':Contains("' + search_name + '")')) {
            $(this).closest('tr').show();
        } else {
            $(this).closest('tr').hide();
        }
    });
}

$(function() {
    $(".search_area input:submit").click(function(e) {
        e.preventDefault();
        filterRows(this);
    });
    
    $(".search_area [name='search_text']").keypress(function(e) {
        var code = e.which || e.keyCode;
        if (code == 13) {
            filterRows(this);
        }
    }).val('');
    
    $(".questionnaire-past .q-level ul li.ql-rst:last").addClass("last");

    $(".review-privacy-ans .profile-level-privacy a").click(function(e) {
        e.preventDefault();
        if ($(this).hasClass('active')) {
            $("#rst-block").addClass('hide');
            $("#question_list").empty();
            $(this).removeClass('active');
        } else {
            $(".review-privacy-ans .profile-level-privacy a.active").removeClass('active');
            $(this).addClass('active');
            var id = $(this).attr('data-id');
            var code = $(this).attr('data-code');
            getPrivacyQuestion(id, code);
        }
    });

    $(".q-level ul li .questionnaire-past-level").mouseover(function(e) {
        toggleElements();
        $(this).addClass('active');
        $(this).next().show().addClass("li");
        $(this).prev().show();
        $(this).find("span").hide();
        $(this).find("font").show();
    });

	$(".q-level-left, .q-level-right").mouseleave(toggleElements);
	//$(".q-level-left").mousemove(toggleElements);
    $(".q-level ul").on("mouseleave", "li.ql-rst:first-of-type, li.ql-rst:last-of-type", toggleElements);
    
    if(active_box != "") {
        $('html, body').animate({
            scrollTop: $(active_box).offset().top
        }, 1000);
    }
    
    $(".edit-links a").click(function(e) {
        e.preventDefault();
        var box = $(this).closest('.edit-links'),
            pid = box.attr('data-id'),
            mbox = $(this).closest('td');
        if($(this).hasClass('edit')) {
            $(this).addClass('hide');
            box.find('.save, .cancel').removeClass('hide');
            mbox.find(".profile-level-privacy a").click(function(e) {
                e.preventDefault();
                $(this).toggleClass('active');
            });
        } else if($(this).hasClass('save')) {
            var choise = new Array();
            mbox.find(".profile-level-privacy .active").each(function() {
                choise.push($(this).attr('data-code'));
            });
            $.ajax({
                url: '/questionnaire/update_friends_access',
                type: 'post',
                dataType: 'json',
                data: {
                    'pid'  : pid,
                    'lnum' : box.attr('data-lnum'),
                    'type' : box.attr('data-qtype'),
                    'codes' : choise
                },
                success: function(data) {
                    if(data.errors !== undefined) {
                        if(data.errors == '') {
                            if(choise.length) {
                                mbox.find(".profile-level-privacy a").unbind('click');
                                box.find('.edit').removeClass('hide');
                                box.find('.save').addClass('hide');
                                box.find('.cancel').addClass('hide').attr('data-code', choise.join('|'))
                            } else {
                                mbox.empty();
                                var tr = mbox.closest('tr'),
                                    tr_remove = true;
                                tr.find('td:not(:first)').each(function() {
                                    if($.trim($(this).html()) != '') {
                                        tr_remove = false;
                                        return false;
                                    }
                                });
                                if(tr_remove) {
                                    tr.remove();
                                }
                            }
                        } else {
                            alert(data.errors);
                            box.find('.cancel').click();
                        }
                    }
                },
                error: function() {
                    requestFailed();
                    box.find('.cancel').click();
                }
            });
        } else if($(this).hasClass('cancel')) {
            mbox.find(".profile-level-privacy a").unbind('click');
            mbox.find(".profile-level-privacy .active").removeClass('active');
            $.each($(this).attr('data-code').split('|'), function(i, code) {
               mbox.find(".profile-level-privacy [data-code='"+code+"']").addClass('active'); 
            });
            box.find('.edit').removeClass('hide');
            box.find('.save, .cancel').addClass('hide');
        }        
    });
    
    $(document).on('mouseover', '.tpd-tooltip', function() {
        $(this).remove();
    });
});