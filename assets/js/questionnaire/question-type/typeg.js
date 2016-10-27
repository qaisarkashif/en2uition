$(function() {
    $.fn.setDefault = function(field) {
        var choise = $.trim(field.val());
        if(choise !== '') {
            this.find('li:eq(' + choise + ')').removeClass('active').click();
        } else {
            this.find('li.active').removeClass('active');
        }
    };

    $.fn.saveSelection = function(whose_answer, link) {
        var choise = this.find("li.active").index();
        var data = {
            'id': this.attr('data-qid'),
            'whose_answer': whose_answer,
            'answer': choise === -1 ? '' : choise,
            'status': choise === -1 ? 0 : 1
        };
        saveAnswer(data, q_level_id, link);
    };
    $("#me, #my-partner").flip({'trigger': 'manual'});

    $('.typea').on('click', '.'+blue_col+' li', function(e) {
        toggleActiveClass(this);
        toggleHeadingBG(this, q_type)
    });

    $("#me-q").setDefault($("#q_1"));

    $(".q1").click(function(e) {
        e.preventDefault();
        $("#me-q").saveSelection("me", this);
    });

    $(".q2").click(function(e) {
        e.preventDefault();
        $("#partner-q").saveSelection("partner", this);
    });

});

function flipBackward() {
    $("#me, #my-partner").flip(false);
    $("#partner-q").removeClass('blue_move').addClass('hidenone');
    $(".my-partner").removeClass('move-mypartner-flip');
    $(".me").removeClass('move-me-flip').addClass('me-flip');
    $("#me-q").removeClass(grey_col + ' grey_move').addClass(blue_col);
    $("#question_box").find(".top_heading span:last, .innertop_heading span:last, .innerbot_heading span:last, btm-subheading span:last").addClass('hide');
    $("#question_box").find(".top_heading span:first, .innertop_heading span:first, .innerbot_heading span:first, btm-subheading span:first").removeClass('hide');
    $("#qsubmit_2").addClass('hidenone');
    $("#qsubmit_1").removeClass('hidenone');
}

function flipFwd() {
    $("#me, #my-partner").flip(true);
    $("#question_box").find(".top_heading span:first, .innertop_heading span:first, .innerbot_heading span:first, btm-subheading span:first").addClass('hide');
    $("#question_box").find(".top_heading span:last, .innertop_heading span:last, .innerbot_heading span:last, btm-subheading span:last").removeClass('hide');
    $("#me-q").removeClass(blue_col).addClass(grey_col + ' grey_move');
    $(".me").removeClass('me-flip').addClass('move-me-flip');
    $(".my-partner").addClass('move-mypartner-flip');
    $("#partner-q").addClass('blue_move').removeClass('hidenone');
    if (!$("#partner-q ul li").hasClass("active"))
    {
        $(".top_heading").attr("class", "top_heading");
        $(".bottom_text").attr("class", "bottom_text");
        $(".innertop_heading").attr("class", "innertop_heading");
        $(".innerbot_heading").attr("class", "innerbot_heading");
    }
    $(".step_backward").click(function(e) {
        e.preventDefault();
        flipBackward();
        $(".step_backward").off('click');
    });
}

function setPartnerAnswersDefault() {
    flipFwd();
    $("#partner-q").setDefault($("#q_2"));
    $("#qsubmit_1").addClass('hidenone');
    $("#qsubmit_2").removeClass('hidenone');
}

function toggleHeadingBG(obj, Type) {
    var topHeadCls = botHeadCls = "";
    if (Type == 'A') {
        topHeadCls = "innertop_heading";
        botHeadCls = "innerbot_heading";
    } else if (Type == 'B') {
        topHeadCls = "top_heading";
        botHeadCls = "bottom_text";
    }
    else if (Type == 'C') {
        topHeadCls = "top_heading";
        botHeadCls = "";
    }
    var ind = $(obj).index();
    if (!$(obj).hasClass("active"))
    {
        $("."+topHeadCls).removeClass("ith-bg-C-"+ind);
        $(ind <= 3 ? "."+topHeadCls : "."+botHeadCls).removeClass("ith-bg-"+ind);
        return(false);
    }
    if (Type == 'B') headingClass = "inner";
    $("."+topHeadCls).attr('class', topHeadCls);
    if (Type != 'C')
        $("."+botHeadCls).attr('class', botHeadCls);
    if (Type == 'C')
        $("."+topHeadCls).addClass("ith-bg-C-"+ind);
    else
        $(ind <= 3 ? "."+topHeadCls : "."+botHeadCls).addClass("ith-bg-"+ind);
}

