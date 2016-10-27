function updateQuestionPrivacyCode(id, privacy_code) {
    $.ajax({
        url: '/questions/privacy/update',
        type: 'post',
        data: {'id': id, 'privacy_code': privacy_code},
        dataType: 'json',
        success: function(data) {
            if (data.errors !== undefined) {
                if ($.trim(data.errors) == "") {
                    $("#privacy .privacy_opts a").removeClass("active");
                    $("#privacy .privacy_opts a.pr-" + privacy_code).addClass('active');
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function saveAnswer(info, level_id, link) {
    $.ajax({
        url: '/questions/answer/save',
        type: 'post',
        data: info,
        dataType: 'json',
        success: function(data) {
            if(data.errors !== undefined) {
                if(data.errors == '') {
                    updateProgressRing(level_id, link);
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function updateProgressRing(level_id, link) {
    $.ajax({
        url: '/questionnaire/progress/get',
        type: 'get',
        data: {'level_id' : level_id},
        dataType: 'json',
        success: function(data) {
            if(data.errors !== undefined) {
                if(data.errors == '') {
                    $(".knob").val(data.progress).trigger('change');
                    if(data.progress >= 99.9) {
                        $("#analyze").removeClass('hide');
                        var a = $(".navigation .selected").next('li').find('a');
                        a.removeAttr('onclick').removeClass('cmt-tooltip');
                    } else {
                        $("#analyze").addClass('hide');
                        $(".navigation .selected").nextAll('li').each(function() {
                            $(this)
                                .find('a')
                                .attr({
                                    'onclick' : 'return false;',
                                    'title' : 'Please answer previous levels before moving on to this level'
                                })
                                .addClass('cmt-tooltip');
                        });
                        Tipped.create('.cmt-tooltip',{
                            containment: 'viewport',
                            position: 'bottom',
                            inline: true,
                            onShow: function(content, elem) {
                                if(!$(elem).hasClass('cmt-tooltip')) { $('.tpd-tooltip').remove(); }
                            }
                        });
                    }
                    if(link !== undefined && $(link).hasClass('q1')) {
                        setPartnerAnswersDefault();
                    } else if(link !== undefined && $(link).hasClass('q2') && !$(link).hasClass('just-save')) {
                        window.location = $(link).attr('href');
                    }
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
    });
}

function flipTop(callback) {
    $("#me, #my-partner").flip(true);
    var h = $("#heading, #subheading");
    h.find("span:first").addClass('hide');
    h.find("span:last").removeClass('hide');


    $(".step_backward").click(function(e) {
        e.preventDefault();
        if(callback !== undefined) {
            callback();
        } else {
            flipBack();
        }
        $(".step_backward").off('click');
    });
}

function flipBack() {
    $("#me, #my-partner").flip(false);
    var h = $("#heading, #subheading");
    h.find("span:last").addClass('hide');
    h.find("span:first").removeClass('hide');
    $("#qsubmit_2").addClass('hidenone');
    $("#qsubmit_1").removeClass('hidenone');
    if(document.getElementById("range_box_1") && document.getElementById("range_box_2")) {
        $("#range_box_2").addClass('hidenone');
        $("#range_box_1").removeClass('hidenone');
    } else {
        $("#question_box_2").addClass('hidenone');
        $("#question_box_1").removeClass('hidenone');
    }
    if(document.getElementById("md-yn1")) {
        if($("#md-yn1 .boolean_question_yes").hasClass('active')) {
            $("#question_box_1 .btn-other-half").css('visibility', 'visible');
        }
        $("#ethnicity1_2").addClass('hidenone');
        $("#ethnicity1_1").removeClass('hidenone');
    }
}

$(function() {
    $(".knob").knob({
        'readOnly': true,
        'draw': function() {
            var v = parseFloat(this.cv).toFixed(2);
            $(this.i).val(Number(v) + '%');
        }
    });

    //	Play Gear
	$.fn.playGear = function (imgPath) {
		this.animate('fast', function()
		{
			$(this).css('background', 'url(' + imgPath + '?x=' + Date.now() + ')  no-repeat center -65px');
			$('#q_container').animate('slow', 200, function() {$(this).css('background','none');}).animate('slow', 1);
		}).animate('fast');
	};

    $("#heading span").pulse({ delay: 500 });


    // if play-gears cookie is set, play the gears and unset the cookie
    if ($.cookie("play-gears") === "1") {
        $("#q_content").playGear('/assets/img/gear.gif');
        $.cookie("play-gears", "0", {path: "/"});
    }

    $("a.play-gears").click(function(e) {
        $.cookie("play-gears", "1", {path: "/"});
    });

    $("#pagination").on("keypress", ".variable_page_number", function(e) {
        if($(this).is('[readonly]')) {
            return false;
        }
        var code = e.which || e.keycode;
        if (code === 13) {
            var qn = parseInt($(this).val()),
                all = parseInt($("#pagination").attr('data-all'));
            if(qn >= 1 && qn <= all) {
                var link = $("#pagination .full_step_backward").attr('href');
                window.location = link.replace(/q1$/, 'q'+qn);
            } else {
                return false;
            }
        } else if (code !== 8 && code !== 0 && (code < 48 || code > 57)) {
            return false;
        }
    });

    $("#pagination").on("blur", ".variable_page_number", function() {
        if ($.trim($(this).val()) == '') {
            $(this).val($("#pagination").attr('data-cur'));
        }
    });

    Tipped.create('.cmt-tooltip',{
        containment: 'viewport',
        position: 'bottom',
        inline: true,
        onShow: function(content, elem) {
            if(!$(elem).hasClass('cmt-tooltip')) { $('.tpd-tooltip').remove(); }
        }
    });
});

(function($) {
    $.fn.pulse = function(options) {
        var opts = $.extend({}, $.fn.pulse.defaults, options);
        return this.each(function() {
            if ($(this).is(":animated")) {
                $(this).stop(true, true).fadeTo(opts.speed, 1);
            }

            $(this).fadeTo(opts.speed, 1);
            $(this).delay(opts.delay);
            for (var i = 0; i < opts.pulses; ++i) {
                $(this).fadeTo(opts.speed, opts.fadeLow).fadeTo(opts.speed, opts.fadeHigh);
            }

            $(this).fadeTo(opts.speed, 1);
        });
    };

    $.fn.pulse.defaults = {
        delay: 300,
        speed: 300,
        pulses: 3,
        fadeLow: 0.2,
        fadeHigh: 1
    };
})(jQuery);
