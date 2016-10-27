function filterRows(obj) {
    $(".view-answers").remove();
    var search_name = "";
    if($(obj).attr('type') == 'text') {
        search_name = $(obj).val();
    } else {
        search_name = $(obj).closest(".search_area").find("[name='search_text']").val();
    }
    $('.qu-list').find(".user-name").each(function() {
        if ($(this).is(':Contains("' + search_name + '")')) {
            $(this).closest('tr').show();
        } else {
            $(this).closest('tr').hide();
        }
    });
}

function loadAnswers(info, tr) {
    $(".view-answers").remove();
    var wait_img = $('<img/>').attr({'class' : 'wait-spinner', 'src' : '/assets/img/bx_loader.gif', 'alt' : 'loading...'}).css('margin', '5px 15px'),
        new_td = $('<td/>').attr('colspan', tr.find('td').length).append(wait_img),
        new_tr = $("<tr/>").attr('class', 'view-answers').append(new_td);
    tr.after(new_tr.css('background-color', tr.css('background-color')));
    
    $.ajax({
        url : '/get_shared_answers',
        type : 'post',
        dataType : 'json',
        data: info,
        success: function(data) {
            if(data.errors !== undefined) {
                if(data.errors == '') {
                    if(data.list_length > 0) {
                        var list = new Array();
                        $.each(data.list, function(i, v) {
                            if(i == 'measure_lang') { return; }
                            var li = '<li><span class="qnum">'+data.ttl+' #'+i+':</span>';
                            if((info.level_id == 1 || info.level_id == 8) && (i == 21 || i == 23)) {
                                var mlang = data.list.measure_lang,
                                    m1 = i == 21 ? mlang.inches : mlang.lbs,
                                    m2 = i == 21 ? mlang.cm : mlang.kgs;
                                li += '<div class="meas-radio"><label class="radio-inline"><input type="radio" value="" name="measure'+i+'" checked/> '+m1+'</label>';
                                li += '<label class="radio-inline"><input type="radio" value="2" name="measure'+i+'"/> '+m2+'</label></div>';
                            }
                            if(info.level_id == 7) {
                                if(i == 1) {
                                    li += '<p><span class="qtitle">' + v.title1 + '</span></p>';
                                    li += '<p><span class="qtitle">' + v.title2 + '</span></p>';
                                } else {
                                    li += '<p><span class="qtitle">' + v.main_title + '</span></p>';
                                    $.each(['mred', 'mgreen', 'pred', 'pgreen'], function(k,it) {
                                        li += '<p><span class="qtitle">' + v[it].title + '</span>'+v[it].value+'</p>';
                                    });
                                }
                            } else {
                                if(v.type == 'double') {
                                    li += '<p><span class="qtitle">' + v.title_me + '</span>'+v.me+'</p>';
                                    if(v.has_subquestion) {
                                        li += '<p><span class="qtitle">' + v.subtitle_me + '</span>'+v.me_sub+'</p>';
                                    }
                                    li += '<p><span class="qtitle">' + v.title_partner + '</span>'+v.partner+'</p>';
                                    if(v.has_subquestion) {
                                        li += '<p><span class="qtitle">' + v.subtitle_partner + '</span>'+v.partner_sub+'</p>';
                                    }
                                } else {
                                    li += '<p><span class="qtitle">' + v.title + '</span>'+v.me+'</p>';
                                    if(v.has_subquestion) {
                                        li += '<p><span class="qtitle">' + v.subtitle + '</span>'+v.me_sub+'</p>';
                                    }
                                }
                            }
                            li += '</li>';
                            list.push(li);
                        });
                        $("<ul/>")
                            .attr('class', 'answered-list')
                            .append(list.join(''))
                            .appendTo(".view-answers td");
                        if(info.level_id == 1 || info.level_id == 8) {
                            $(":radio[name=measure21], :radio[name=measure23]").unbind().change(function() {
                                var n = $(this).val(),
                                    box = $(this).closest('li'),
                                    cl = n == 2 ? '.meas' : '.meas2';
                                box.find(cl).addClass('hide');
                                box.find('.meas'+n).removeClass('hide');
                                $(this).blur();
                            });
                        }
                    } else {
                        $("<p/>").text(data.not_found_txt).css({'font-style' : 'italic', 'padding' : '5px 0 0 15px'}).appendTo(".view-answers td");
                    }
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } },
        complete: function() {
            $(".view-answers .wait-spinner").remove();
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
    
    $(".qu-privacy .active").click(function(e) {
        e.preventDefault();
        var $this = $(this),
            tr = $this.closest('tr');
        var info = {
            'type' : $("#qprivacy .be-head .active").attr('data-v'),
            'code' : $this.attr('data-code'),
            'level_id' : $this.attr('data-lid'),
            'user_id' : tr.attr('data-uid')
        };
        loadAnswers(info, tr);
    });
});