
function customFlipBack() {
    $("#me, #my-partner").flip(false);
    $("#heading").removeClass('white_bg');
    $("#heading span:first").html(s1Heading).removeClass('hide');
    $("#subheading span:first").html(s1SubHeading).removeClass('hide');
    $("#heading span:last").addClass('hide').html(s3Heading);
    $("#subheading span:last").addClass('hide').html(s3SubHeading);
    $("#qsubmit_1, #qsubmit_2, #question_box_2, #question_box_3, #question_box_4, .sub-btn2").addClass('hidenone');
    $("#question_box_1, .sub-btn1").removeClass('hidenone');
}

function setPartnerAnswersDefault() {
    if(has_subquestion) {
        flipTop(customFlipBack);
        $("#heading").removeClass('white_bg');
        initScroller("ud-scroller3", $("#q_3").val());
        initScroller("ud-scroller4", $("#q_4").val());
        $("#qsubmit_1, #question_box_1, #question_box_2, .sub-btn1").addClass('hidenone');
        $(".sub-btn2, #question_box_3").removeClass('hidenone');
    } else {
        flipTop();
        initScroller("ud-scroller2", $("#q_2").val());
        $("#qsubmit_1, #question_box_1").addClass('hidenone');
        $("#qsubmit_2, #question_box_2").removeClass('hidenone');
    }
}

$(function() {
    $.fn.setDefault = function(scroller_id, val) {
        initScroller(scroller_id, val);
    };

    $(document).on('click', ".prev-fst, .prev-lst, .option.fst, .option.lst", function(e) {
        var bottomSubheading = $("div.process_answer div:not('.hidenone') h4");
        bottomSubheading.pulse({delay: 1000 });
    });


    $.fn.saveSelection = function(whose_answer, link) {
        if(has_subquestion) {
            var answer = new Array(),
                status = 1,
                twice_val = false,
                selector = "";
            if($(this).attr('id') == 'question_box_1') {
                if($(".sub-btn1").hasClass('hidenone')) {
                    selector = '#question_box_1, #question_box_2';
                } else {
                    selector = '#question_box_1';
                    twice_val = true;
                }
            } else {
                if($(".sub-btn2").hasClass('hidenone')) {
                    selector = '#question_box_3, #question_box_4';
                } else {
                    selector = '#question_box_3';
                    twice_val = true;
                }
            }
            $(selector).find(".option_selector").each(function() {
                var active = $(this).find("a.active");
                if(active.attr('data-opt') == '-') {
                    status = 0;
                }
                answer.push(active.attr('data-opt'));
                if(twice_val) {
                    answer.push(active.attr('data-opt'));
                }
            });
            answer = answer.join('|');
        } else {
            var scroller = this.find(".option_selector"),
                active = scroller.find("a.active"),
                answer = active.attr('data-opt') == '-' ? '' : active.attr('data-opt'),
                status = answer != '' ? 1 : 0;
        }
        var data = {
            'id' : this.attr('data-qid'),
            'whose_answer' : whose_answer,
            'answer' : answer,
            'status' : status
        };
        saveAnswer(data, levelID, link);
    };

    initScroller("ud-scroller1", $("#q_1").val());

    if(has_subquestion) {
        function presetAnswers(n1, n2) {
            if($.trim($("#q_"+n2).val()) != "")
                return false;
            var predef = $("#ud-scroller"+n1+" .active").attr('data-opt');
            initScroller("ud-scroller"+n2, predef);
        }

        initScroller("ud-scroller2", $("#q_2").val());

        $("#submit_no1").click(function(e) {
            e.preventDefault();
            if(question_mode == 'single') {
                $(".q2:last").click();
            } else {
                $(".q1").click();
            }
            $("#heading span").pulse({ delay: 1000 });
        });

        $("#submit_no2").click(function(e) {
            e.preventDefault();
            $(".q2:last").click();
        });

        $("#submit_p1").click(function() {
            presetAnswers(1, 2);
            $(".sub-btn1, #question_box_1").addClass('hidenone');
            $("#question_box_2").removeClass('hidenone');
            if(question_mode == 'single') {
                $("#qsubmit_2").removeClass('hidenone');
            } else {
                $("#qsubmit_1").removeClass('hidenone');
            }
            var $heading = $("#heading");
            $heading.addClass("white_bg");
            $("span:first", $heading).html(s2Heading);
            $("#subheading span:first").html(s2SubHeading);
            $("span", $heading).pulse({ delay: 1000 });
        });

        $("#submit_p2").click(function() {
            presetAnswers(3, 4);
            $(".sub-btn2, #question_box_3").addClass('hidenone');
            $("#qsubmit_2, #question_box_4").removeClass('hidenone');
            var $heading = $("#heading");
            $heading.addClass("white_bg");
            $("span:last", $heading).html(s4Heading);
            $("#subheading span:last").html(s4SubHeading);
            $("span", $heading).pulse({ delay: 1000 });
         });
    }

    $(":radio[name=measure]").change(function() {
        var n = $(this).val(),
            arr = ['-', 'u1-', 'u2-', 'd1-', 'd2-'];
        $(".option_selector a").each(function() {
            if($(this).is('[data-opt'+n+']')) {
                var new_text = $(this).attr('data-opt'+n);
                if($.inArray(new_text, arr) === -1) {
                    $(this).text(new_text);
                }
            }
        });
        $(this).blur();
    });
});
