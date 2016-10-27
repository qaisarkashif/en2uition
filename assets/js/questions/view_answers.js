function viewAnswers(lvl_id, type) {
    $("#see_answer_received")
        .css('cursor', 'wait')
        .attr('onclick', 'return false');
    $("#results .modal-body").empty();
    $.ajax({
        url : '/questions/answered',
        type : 'post',
        dataType : 'json',
        data: {'level_id' : lvl_id, 'type' : type},
        success: function(data) {
            if(data.errors !== undefined) {
                if(data.errors == '') {
                    var list = new Array();
                    $.each(data.list, function(i, v) {
                        if(i == 'measure_lang') { return; }
                        
                        var li = '<li><span class="qnum">'+data.ttl+' #'+i+':</span>';
                        
                        if((lvl_id == 1 || lvl_id == 8) && (i == 21 || i == 23)) {
                            var mlang = data.list.measure_lang,
                                m1 = i == 21 ? mlang.inches : mlang.lbs,
                                m2 = i == 21 ? mlang.cm : mlang.kgs;
                            li += '<div class="meas-radio"><label class="radio-inline"><input type="radio" value="" name="measure'+i+'" checked/> '+m1+'</label>';
                            li += '<label class="radio-inline"><input type="radio" value="2" name="measure'+i+'"/> '+m2+'</label></div>';
                        }
                        
                        if(lvl_id == 7) {
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
                        .appendTo("#results .modal-body");
                    if(lvl_id == 1 || lvl_id == 8) {
                        $(":radio[name=measure21], :radio[name=measure23]").unbind().change(function() {
                            var n = $(this).val(),
                                box = $(this).closest('li'),
                                cl = n == 2 ? '.meas' : '.meas2';
                            box.find(cl).addClass('hide');
                            box.find('.meas'+n).removeClass('hide');
                            $(this).blur();
                        });
                    }
                    $("#results").modal("show");
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } },
        complete: function() {
            $("#see_answer_received")
                .css('cursor', 'pointer')
                .attr('onclick', 'viewAnswers('+lvl_id+', \''+type+'\'); return false;');
        }
    });
}