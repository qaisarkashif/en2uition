function initScroller(id, opt) {
    if (opt === undefined || opt == "") {
        opt = '-';
    }
    var scroller = document.getElementById(id);
    var mousewheelevt = (/Firefox/i.test(navigator.userAgent)) ? "DOMMouseScroll" : "mousewheel"; //FF doesn't recognize mousewheel as of FF3.x
    if (scroller.attachEvent) //if IE (and Opera depending on user setting)
        scroller.attachEvent("on" + mousewheelevt, rotateoption);
    else if (scroller.addEventListener) //WC3 browsers
        scroller.addEventListener(mousewheelevt, rotateoption, false);
    var active = $(scroller).find('a[data-opt="' + opt + '"]');
    setScroller($(scroller), active);
}

function rotateoption(e) {
    var evt = window.event || e; //equalize event object
    var active = $(this).find("a.active");
    var delta = evt.detail ? evt.detail * (-120) : evt.wheelDelta; //delta returns +120 when wheel is scrolled up, -120 when scrolled down
    if (delta <= -120 && active.next('a').attr('data-opt') != 'd1-') {//mousewheel down
        setScroller($(this), active.next('a'));
    } else if (delta >= 120 && active.prev('a').attr('data-opt') != 'u1-') { //mousewheel up
        setScroller($(this), active.prev('a'));
    }

    $("div.process_answer div:not('.hidenone') h4").pulse({delay: 2000 });

    if (evt.preventDefault) //disable default wheel action of scrolling page
        evt.preventDefault();
    else
        return false;
}

function setScroller(scroller, active_opt) {
    scroller.find('a').removeClass('active fst lst prev-fst prev-lst').addClass('hide');
    active_opt.prev('a').removeClass('hide').addClass('prev-fst');
    active_opt.prev('a').prev('a').removeClass('hide').addClass('fst');
    active_opt.addClass('active').removeClass('hide');
    active_opt.next('a').removeClass('hide').addClass('prev-lst');
    active_opt.next('a').next('a').removeClass('hide').addClass('lst');
}

$(function() {
    $.fn.mousehold = function(timeout, f) {
        if (timeout && typeof timeout == 'function') {
            f = timeout;
            timeout = 100;
        }
        if (f && typeof f == 'function') {
            var timer = 0;
            var fireStep = 0;
            return this.each(function() {
                $(this).mousedown(function() {
                    fireStep = 1;
                    var ctr = 0;
                    var t = this;
                    timer = setInterval(function() {
                        ctr++;
                        f.call(t, ctr);
                        fireStep = 2;
                    }, timeout);
                });

                clearMousehold = function() {
                    clearInterval(timer);
                    if (fireStep == 1)
                        f.call(this, 1);
                    fireStep = 0;
                }

                $(this).mouseout(clearMousehold);
                $(this).mouseup(clearMousehold);
            })
        }
    };

    if(question_mode == 'double') {
        $("#me, #my-partner").flip({'trigger': 'manual'});
    }

    $(".go_down, .go_up").mousehold(function() {
        var scroller = $(".option_selector:visible");
        var active = scroller.find("a.active");
        if ($(this).hasClass('go_up') && active.prev('a').attr('data-opt') != 'u1-') {
            setScroller(scroller, active.prev('a'));
        } else if ($(this).hasClass('go_down') && active.next('a').attr('data-opt') != 'd1-') {
            setScroller(scroller, active.next('a'));
        }
        var bottomSubheading = $("div.process_answer div:not('.hidenone') h4");
        bottomSubheading.pulse({delay: 1000 });
    });

    $(document).on('click', ".prev-fst, .prev-lst, .option.fst, .option.lst", function(e) {
        e.preventDefault();
        var scroller = $(".option_selector:visible"),
            active = scroller.find("a.active"),
            dopt = $(this).attr('data-opt');
        if($(this).hasClass('prev-fst') && dopt != 'u1-') {
            setScroller(scroller, active.prev('a'));
        } else if($(this).hasClass('fst') && dopt != 'u2-') {
            setScroller(scroller, active.prev('a').prev('a'));
        } else if($(this).hasClass('prev-lst') && dopt != 'd1-') {
            setScroller(scroller, active.next('a'));
        } else if($(this).hasClass('lst') && dopt != 'd2-') {
            setScroller(scroller, active.next('a').next('a'));
        }
    });

    $(".q1").click(function(e) {
        e.preventDefault();
        $("#question_box_1").saveSelection("me", this);
        $("#heading span").pulse({ delay: 1000 });
    });

    $(".q2").click(function(e) {
        e.preventDefault();
        if(question_mode == 'single') {
            $("#question_box_1").saveSelection("both", this);
        } else {
            $("#question_box_2").saveSelection("partner", this);
        }
    });
});
