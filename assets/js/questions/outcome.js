function initQuestionBox(box_id) {
    var box = $(box_id),
        ans = $('#oc-q1 .qanswer').val().split(',');
    if (box_id == '#oc-q1') {
        var blue = true;
        box.find('.oca').removeClass("yellow red green").each(function(i) {
            if (ans[i] == "1") {
                $(this).removeClass('white').addClass(blue ? 'blue' : 'blue pink');
                blue = !blue;
            } else {
                $(this).removeClass('blue pink').addClass('white');
            }
        });
        loadYellowPart();
    } else {
        initElements(box, "me");
        box.find("#me, #my-partner").flip(false);
        box.find(".partner-oc-txt").addClass('hide');
        box.find(".me-oc-txt").removeClass('hide');
        $(box_id + " .process_answer #sub2").addClass("hidenone");
        $(box_id + " .process_answer #sub1").removeClass("hidenone");
    }
}

function initElements(box, whose_answer) {
    var ans = $('#oc-q1 .qanswer').val().split(','),
        b_index = ans.indexOf('1'),
        p_index = ans.lastIndexOf('1'),
        answ = box.find('[name="'+whose_answer+'-answer"]').val().split(',');
    box.find(".oca").removeClass('red green yellow br-right').addClass('white');
    for(var i = b_index; i <= p_index; i++) {
        var oca_class = 'yellow';
        if(answ[i] !== undefined && answ[i] == '1') { oca_class = 'red'; }
        if(answ[i] !== undefined && answ[i] == '2') { oca_class = 'green'; }
        box.find(".oca:eq("+i+")").addClass(oca_class);
    }
    var last_vis = 'qa1';
    box.find('.qa').each(function() {
       if($(this).find('.yellow, .green, .red').length == 0) {
           $(this).closest('[class$="box"]').hide();
       } else {
           $(this).closest('[class$="box"]').show();
           last_vis = $(this).attr('id');
       }
    });
    box.find("#"+last_vis+" .oca:last").addClass('br-right');
}

function closeAccordion(ID, moveTo) {
    var box = $("#oc-q" + ID);
    box.find(".oct_privacy_opts").addClass("hide");
    box.find(".accordion-toggle").removeClass("open");
    box.find(".accordion-content").slideUp(500, function() {
        if(moveTo !== undefined) {
            var elem = $("#oc-q"+moveTo),
                tgl = elem.find('.accordion-toggle');
            if(tgl.hasClass('open')) {
                scrollHere('#'+elem.attr('id'), 100);
            } else {
                tgl.click();
            }
        }
    });
}

function scrollHere(selector, offset) {
    $('body,html').animate({scrollTop: $(selector).offset().top - offset}, 500);
    return false;
}

function loadYellowPart() {
    var box = $("#oc-q1");
    box.find('.oca').removeClass("yellow red green");
    var start = box.find(".oca.blue:not(.pink)").index("#oc-q1 .oca"),
        end = box.find(".oca.blue.pink").index("#oc-q1 .oca");
    for (var i = start + 1; i < end; i++) {
        box.find(".oca:eq(" + i + ")").addClass("yellow");
    }
}

function saveAnswer(qnum, save_and_exit, moveTo) {
    if(save_and_exit === undefined) {
        save_and_exit = false;
    }
    var box = $("#oc-q" + qnum),
        qid = box.attr('data-qid'),
        info = {
            'status' : 0,
            'answer' : new Array(),
            'id'     : qid
        };
    if (qnum == 1) {
        info.whose_answer = 'main_outcome';
        box.find('.oca').each(function() {
            info.answer.push($(this).hasClass('blue') ? 1 : 0);
        });
        var ending_rel = $(".ending-rel a.active");
        info.ending_rel = ending_rel.length ? ending_rel.attr('data-v') : ending_rel.length;
        info.mark_rel = box.find(".oca.blue").length > 1 ? 2 : (box.find(".oca.blue").length === 1 ? 1 : 0);        
        if(box.find(".oca.blue").length > 1 && ending_rel.length) {
            info.status = 1;
        }
    } else {
        info.whose_answer = moveTo == 'flip' ? 'me' : 'partner';
        box.find('.oca').each(function() {
            var a = 0;
            if($(this).hasClass('red')) { a = 1; info.status = 1; }
            else if($(this).hasClass('green')) { a = 2; info.status = 1; }
            info.answer.push(a);
        });
    }
    info.answer = info.answer.join(',');
    
    $.ajax({
        url : '/answer/save',
        type : "post",
        data : info,
        dataType : 'json',
        success : function(data) {
            if(data.errors !== undefined) {
                if(data.errors == '') {
                    if(qnum == 1) {
                        box.find(".qstatus").val(info.status);
                        box.find(".qanswer").val(info.answer);
                        if(info.status == 1) {
                            box.addClass("ma-compl pa-compl");
                        } else {
                            box.removeClass("ma-compl pa-compl");
                        }
                    } else {
                        box.find(moveTo == 'flip' ? '[name="me-answer"]' : '[name="partner-answer"]').val(info.answer);
                        if(info.status == 1) {
                            box.addClass(moveTo == 'flip' ? "ma-compl" : "pa-compl");
                        } else {
                            box.removeClass(moveTo == 'flip' ? "ma-compl" : "pa-compl");
                        }
                    }
                    if(save_and_exit) {
                        window.location = "/questionnaire/past";
                    } else if(moveTo !== undefined) {
                        toggleAnalyzeButton();
                        if(moveTo == 'flip') {
                            box.find("#me, #my-partner").flip(true);
                            box.find(".me-oc-txt").addClass('hide');
                            box.find(".partner-oc-txt").removeClass('hide');
                            box.find(".process_answer #sub1").addClass("hidenone");
                            box.find(".process_answer #sub2").removeClass("hidenone");
                            initElements(box, "partner");
                        } else if(moveTo == -1) {
                            closeAccordion(qnum);
                        } else {
                            closeAccordion(qnum, moveTo);
                        }
                    }
                } else { 
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) {if (text_status != "abort" && xhr.status !== 0) {requestFailed();}}
    });
}

function updatePrivacyCode(id, code, link) {
    $.ajax({
        url: '/questions/privacy/update',
        type: 'post',
        data: {
            'id': id, 
            'privacy_code': code
        },
        dataType: 'json',
        success: function(data) {
            if (data.errors !== undefined) {
                if ($.trim(data.errors) == "") {
                    $(link).siblings().removeClass("active");
                    $(link).addClass('active');
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function toggleAnalyzeButton() {
    var n = $(".outcome div[id^='oc-q']:not(.ma-compl.pa-compl)").length;
    if(n > 0) {
        $("#goto-analyze").addClass('hide');
    } else {
        $("#goto-analyze").removeClass('hide');
    }
}

$(function() {
    toggleAnalyzeButton();
    
    $(".oct_privacy_opts a").click(function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
    
    $(".ending-rel a").click(function(e) {
        e.preventDefault();
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
        }
    });

    $("#oc-q1 .oca").click(function() {
        $("#oc-q1 .oca").removeClass("yellow").addClass('white');
        if ($(this).hasClass('blue') && !$("#oc-q1 .oca.blue.pink").length) {
            $(this).removeClass('blue');
            $(".be-head a:last").removeClass('active pink');
            $(".be-head a:first").addClass('active');
        } else {
            if ($("#oc-q1 .oca.blue:not(.pink)").length) {
                var b = $("#oc-q1 .oca.blue:not(.pink)").index("#oc-q1 .oca");
                if ($(this).hasClass('pink')) {
                    $(this).removeClass("blue pink");
                } else if ($(this).index("#oc-q1 .oca") > b) {
                    $("#oc-q1 .oca.blue.pink").removeClass("blue pink");
                    $(this).addClass('blue pink');
                }
            } else {
                if ($(this).attr('data-id') == 1) {
                    $(this).addClass('blue');
                    $(".be-head a:first").removeClass('active');
                    $(".be-head a:last").addClass('active pink');
                } else {
                    alertMessage("Please choose a box from the first year");
                }
            }
        }
        loadYellowPart();
    });
    
    $("[id^='oc-q']:not(#oc-q1) .oca").click(function() {
        if($(this).hasClass('yellow') || $(this).hasClass('green') || $(this).hasClass('red')) {
            if($(this).hasClass('red')) {
                $(this).removeClass('red').addClass('green');
            } else if($(this).hasClass('green')) {
                $(this).removeClass('green').addClass('yellow');
            } else if($(this).hasClass('yellow')) {
                $(this).removeClass('yellow').addClass('red');
            }
        } else {
            alertMessage("Please choose a time during your relationship");
        }
    });

    $(".ocq_content").on("mouseover", ".white, .green, .red, .blue, .pink", function() {
        var id = $(this).attr("data-id"),
            htxt = $(this).attr("alt");
        $(".ocq_content #ss" + id).html(htxt);
    }).on("mouseleave", ".white, .green, .red, .blue, .pink", function() {
        var id = $(this).attr("data-id");
        $(".ocq_content #ss" + id).html("");
    });
    
    $('.accordion-toggle').on('click', function(event) {
        event.preventDefault();
        var show_privacy = !$(this).hasClass("open");
        if($(this).hasClass("main")) {
            $(".accordion-toggle .oct_privacy_opts").addClass('hide');
            var selector = !$(this).hasClass("open") ? ".open, .accordion-toggle.main" : ".main";
            $(".accordion-toggle"+selector).each(function() {
                $(this).toggleClass('open');
                $(this).next('.accordion-content').slideToggle(250);
            });
            scrollHere("#oc-q1", 100);
        } else {
            if($("#oc-q1 .qstatus").val() == 1){
                closeAccordion(0);
                var box = $(this).closest("[id^='oc-q']"),
                    box_id = '#'+box.attr('id'),
                    accordion = $(this),
                    accordionContent = accordion.next('.accordion-content');
                if(!accordion.hasClass('open')) {
                    initQuestionBox(box_id);
                }
                accordion.toggleClass("open");
                accordionContent.slideToggle(250, function() {
                    scrollHere(box_id, 100);
                });
            } else {
                alertMessage("Please select duration first from Begining VS Ending");
                return false;
            }
        }
        if(show_privacy) {
            $(this).find(".oct_privacy_opts").removeClass("hide");
        } else {
            $(this).find(".oct_privacy_opts").addClass("hide");
        }
    });
    
    $("#me, #my-partner").flip({ trigger: 'manual' });
    scrollHere("#oc-q1", 50);
    initQuestionBox("#oc-q1");
});