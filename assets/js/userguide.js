// User's guide section
$(document).ready(function(e) {
    $("#userguide").click(function(e) {
        var userGuide = $(this).attr("data-id");
        if (userGuide == "0")
        {
            $(this).attr("data-id", "1");
            $(this).addClass("selected");
            $(this).html(lang.btn_quit_guide);
            $("#user-info, #dm-info, #g-info, #que-info, #nei-info, #en2-info").addClass("ug-user");
            Tipped.remove('.cmt-tooltip');
            var lclz = lang.guide.username;
			$.fn.userGuide("#user-info", lclz.heading, lclz.info, 98, -2, 0, 189, 14, 0, 50, 12.5);
        }
        else
        {
            $(this).attr("data-id", "0");
            $(this).removeClass("selected");
            $(this).html(lang.btn_user_guide);
            $("#user-info, #dm-info, #g-info, #que-info, #nei-info, #en2-info").removeClass("ug-user");
            Tipped.create('.cmt-tooltip', {containment: 'viewport', position: 'bottom', inline: true});
            $(".left-side, .right-side").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            $(".main").find(".bgov").remove();
        }

        // Username Slide
        $(".left-side").on("mouseover", "#user-info.ug-user .sblock-body", function(e) {
            e.stopPropagation();
			$("#dm-info, #g-info, #que-info, #nei-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            var lclz = lang.guide.username;
            if ($("#user-info").find(".ug_block").length == 0)
                $.fn.userGuide("#user-info", lclz.heading, lclz.info, 98, -2, 0, 189, 14, 0, 50, 12.5);
            if ($("#user-info .pro-img").find("span.bgov").length == 0)
                $("#user-info .pro-img").append("<span class=bgov></span>");
        });
        $(".left-side").on("mouseleave", "#user-info.ug-user .sblock-body", function(e) {
            $(this).find(".bgov").remove();
        });

        // Daily Mood Slide
        $(".left-side").on("mouseover", "#dm-info.ug-user .dm_s_i", function(e) {
            var lclz = lang.guide.daily_mood;
			$("#user-info, #g-info, #que-info, #nei-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#dm-info").find(".ug_block").length == 0)
                $.fn.userGuide("#dm-info", lclz.heading, lclz.info, 109, -2, 0, 189, 14, 0, 0, 12.5, '1%', '30px');
            else
            {
                $("#dm-info").find(".ug_body p").html(lclz.info).css("margin-top", "0px");
                $("#dm-info").find(".ug_body p").css("background-position", "1% 30px");
                $("#dm-info").find(".ug_body").css("height", "109px");
            }
            if ($("#dm-info .dm_s_i").find("span.bgov").length == 0)
                $("#dm-info .dm_s_i").append("<span class=bgov></span>");
        });
        $(".left-side").on("mouseover", "#dm-info.ug-user #ug_dm_comp", function(e) {
            var lclz = lang.guide.daily_mood;
			$("#user-info, #g-info, #que-info, #nei-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#dm-info").find(".ug_block").length == 0)
                $.fn.userGuide("#dm-info", lclz.heading, lclz.compare_info, 59, -2, 0, 189, 14, 0, 50, 12.5, '1%', '80%');
            else
            {
                $("#dm-info").find(".ug_body p").html(lclz.compare_info).css("margin-top", "50px");
                $("#dm-info").find(".ug_body p").css("background-position", "1% 80%");
                $("#dm-info").find(".ug_body").css("height", "59px");
            }
        });
        $(".left-side").on("mouseleave", "#dm-info.ug-user .dm_s_i", function(e) {
            $(this).find(".bgov").remove();
        });

        // Group Slide
        $(".left-side").on("mouseover", "#g-info.ug-user #trend-info", function(e) {
            var lclz = lang.guide.groups;
			$("#dm-info, #user-info, #que-info, #nei-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#g-info").find(".ug_block").length == 0)
                $.fn.userGuide("#g-info", lclz.heading, lclz.color_info, 119, -2, 0, 189, 14, 0, 0, 12.5, '1%', '40px');
            else
            {
                $("#g-info").find(".ug_body p").html(lclz.color_info).css("margin-top", "0px");
                $("#g-info").find(".ug_body p").css("background-position", "1% 40px");
                $("#g-info").find(".ug_body").css("height", "119px");
            }
            if ($("#g-info #trend-info").find("span.bgov").length == 0)
                $("#g-info #trend-info").append("<span class=bgov></span>");
        });
        $(".left-side").on("mouseover", "#g-info.ug-user #tshape-info", function(e) {
            var lclz = lang.guide.groups;
			$("#dm-info, #user-info, #que-info, #nei-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#g-info").find(".ug_block").length == 0)
                $.fn.userGuide("#g-info", lclz.heading, lclz.shape_info, 119, -2, 0, 189, 14, 0, 0, 12.5, '1%', '40px');
            else
            {
                $("#g-info").find(".ug_body p").html(lclz.shape_info).css("margin-top", "0px");
                $("#g-info").find(".ug_body p").css("background-position", "1% 40px");
                $("#g-info").find(".ug_body").css("height", "119px");
            }
            if ($("#g-info #tshape-info").find("span.bgov").length == 0)
                $("#g-info #tshape-info").append("<span class=bgov></span>");
        });
        $(".left-side").on("mouseleave", "#g-info.ug-user #trend-info, #g-info.ug-user #tshape-info", function(e) {
            $(this).find(".bgov").remove();
        });

        $(".left-side").on("mouseover", "#g-info.ug-user #gd-info", function(e) {
            var lclz = lang.guide.groups;
			$("#dm-info, #user-info, #que-info, #nei-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#g-info").find(".ug_block").length == 0)
                $.fn.userGuide("#g-info", lclz.heading, lclz.descr_info, 70, -2, 0, 189, 14, 0, 49, 12.5, '1%', '47px');
            else
            {
                $("#g-info").find(".ug_body p").html(lclz.descr_info).css("margin-top", "45px");
                $("#g-info").find(".ug_body p").css("background-position", "1% 47px");
                $("#g-info").find(".ug_body").css("height", "74px");
            }
        });
        $(".left-side").on("mouseover", "#g-info.ug-user #gp-info", function(e) {
            var lclz = lang.guide.groups;
			$("#dm-info, #user-info, #que-info, #nei-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#g-info").find(".ug_block").length == 0)
                $.fn.userGuide("#g-info", lclz.heading, lclz.group_link, 49, -2, 0, 189, 14, 0, 105, 12.5, '1%', '85%');
            else
            {
                $("#g-info").find(".ug_body p").html(lclz.group_link).css("margin-top", "105px");
                $("#g-info").find(".ug_body p").css("background-position", "1% 85%");
                $("#g-info").find(".ug_body").css("height", "49px");
            }
        });

        // Questionnaire Slide
        $(".right-side").on("mouseover", "#que-info.ug-user #qp-info", function(e) {
            var lclz = lang.guide.quiz;
			$("#dm-info, #user-info, #g-info, #nei-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#que-info").find(".ug_block").length == 0)
                $.fn.userGuide("#que-info", lclz.heading, lclz.past_info, 182, -2, 0, -198, 0, 14, 0, 12.5, '99%', '15px');
            else
            {
                $("#que-info").find(".ug_body p").html(lclz.past_info).css("margin-top", "0px");
                $("#que-info").find(".ug_body p").css("background-position", "99% 15px");
                $("#que-info").find(".ug_body").css("height", "182px");
            }
        });
        $(".right-side").on("mouseover", "#que-info.ug-user #qpr-info", function(e) {
            var lclz = lang.guide.quiz;
			$("#dm-info, #user-info, #g-info, #nei-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#que-info").find(".ug_block").length == 0)
                $.fn.userGuide("#que-info", lclz.heading, lclz.present_info, 182, -2, 0, -198, 0, 14, 0, 12.5, '99%', '15px');
            else
            {
                $("#que-info").find(".ug_body p").html(lclz.present_info).css("margin-top", "0px");
                $("#que-info").find(".ug_body p").css("background-position", "99% 15px");
                $("#que-info").find(".ug_body").css("height", "182px");
            }
        });
        $(".right-side").on("mouseover", "#que-info.ug-user .progress_pinfo", function(e) {
            var lclz = lang.guide.quiz;
			$("#dm-info, #user-info, #g-info, #nei-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#que-info").find(".ug_block").length == 0)
                $.fn.userGuide("#que-info", lclz.heading, lclz.progress_info, 142, -2, 0, -198, 0, 14, 40, 12.5, '99%', '40%');
            else
            {
                $("#que-info").find(".ug_body p").html(lclz.progress_info).css("margin-top", "40px");
                $("#que-info").find(".ug_body p").css("background-position", "99% 40%");
                $("#que-info").find(".ug_body").css("height", "142px");
            }
            if ($("#que-info .progress_pinfo").find("span.bgov").length == 0)
                $("#que-info .progress_pinfo").append("<span class=bgov></span>");
        });
        $(".right-side").on("mouseover", "#que-info.ug-user .progress_prinfo", function(e) {
            var lclz = lang.guide.quiz;
			$("#dm-info, #user-info, #g-info, #nei-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#que-info").find(".ug_block").length == 0)
                $.fn.userGuide("#que-info", lclz.heading, lclz.progress_info, 142, -2, 0, -198, 0, 14, 40, 12.5, '99%', '40%');
            else
            {
                $("#que-info").find(".ug_body p").html(lclz.progress_info).css("margin-top", "40px");
                $("#que-info").find(".ug_body p").css("background-position", "99% 40%");
                $("#que-info").find(".ug_body").css("height", "142px");
            }
            if ($("#que-info .progress_prinfo").find("span.bgov").length == 0)
                $("#que-info .progress_prinfo").append("<span class=bgov></span>");
        });
        $(".right-side").on("mouseleave", "#que-info.ug-user .progress_pinfo, #que-info.ug-user .progress_prinfo", function(e) {
            $(this).find(".bgov").remove();
        });

        $(".right-side").on("mouseover", "#que-info.ug-user .qreq_info", function(e) {
            var lclz = lang.guide.quiz;
			$("#dm-info, #user-info, #g-info, #nei-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#que-info").find(".ug_block").length == 0)
                $.fn.userGuide("#que-info", lclz.heading, lclz.requests_info, 132, -2, 0, -198, 0, 14, 50, 12.5, '99%', '80%');
            else
            {
                $("#que-info").find(".ug_body p").html(lclz.requests_info).css("margin-top", "50px");
                $("#que-info").find(".ug_body p").css("background-position", "99% 80%");
                $("#que-info").find(".ug_body").css("height", "132px");
            }
        });

        // Neighbour Slide
        $(".right-side").on("mouseover", "#nei-info.ug-user #ngreen_info", function(e) {
            var lclz = lang.guide.neighborhoods;
			$("#dm-info, #user-info, #g-info, #que-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#nei-info").find(".ug_block").length == 0)
                $.fn.userGuide("#nei-info", lclz.heading, lclz.color_info + lclz.green_info, 156, -2, 0, -198, 0, 14, 0, 12.5, '99%', '40px');
            else
            {
                $("#nei-info").find(".ug_body p").html(lclz.color_info + lclz.green_info);
                $("#nei-info").find(".ug_body p").css("background-position", "99% 40px");
            }
            if ($("#nei-info #ngreen_info").find("span.bgov").length == 0)
                $("#nei-info #ngreen_info").append("<span class=bgov></span>");
        });
        $(".right-side").on("mouseover", "#nei-info.ug-user #nred_info", function(e) {
            var lclz = lang.guide.neighborhoods;
			$("#dm-info, #user-info, #g-info, #que-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#nei-info").find(".ug_block").length == 0)
                $.fn.userGuide("#nei-info", lclz.heading, lclz.color_info + lclz.red_info, 156, -2, 0, -198, 0, 14, 0, 12.5, '99%', '40px');
            else
            {
                $("#nei-info").find(".ug_body p").html(lclz.color_info + lclz.red_info);
                $("#nei-info").find(".ug_body p").css("background-position", "99% 40px");
            }
            if ($("#nei-info #nred_info").find("span.bgov").length == 0)
                $("#nei-info #nred_info").append("<span class=bgov></span>");
        });
        $(".right-side").on("mouseover", "#nei-info.ug-user #nblue_info", function(e) {
            var lclz = lang.guide.neighborhoods;
			$("#dm-info, #user-info, #g-info, #que-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#nei-info").find(".ug_block").length == 0)
                $.fn.userGuide("#nei-info", lclz.heading, lclz.color_info + lclz.blue_info, 156, -2, 0, -198, 0, 14, 0, 12.5, '99%', '110px');
            else
            {
                $("#nei-info").find(".ug_body p").html(lclz.color_info + lclz.blue_info);
                $("#nei-info").find(".ug_body p").css("background-position", "99% 110px");
            }
            if ($("#nei-info #nblue_info").find("span.bgov").length == 0)
                $("#nei-info #nblue_info").append("<span class=bgov></span>");
        });
        $(".right-side").on("mouseover", "#nei-info.ug-user #nyellow_info", function(e) {
            var lclz = lang.guide.neighborhoods;
			$("#dm-info, #user-info, #g-info, #que-info, #en2-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#nei-info").find(".ug_block").length == 0)
                $.fn.userGuide("#nei-info", lclz.heading, lclz.color_info + lclz.yellow_info, 156, -2, 0, -198, 0, 14, 0, 12.5, '99%', '110px');
            else
            {
                $("#nei-info").find(".ug_body p").html(lclz.color_info + lclz.yellow_info);
                $("#nei-info").find(".ug_body p").css("background-position", "99% 110px");
            }
            if ($("#nei-info #nyellow_info").find("span.bgov").length == 0)
                $("#nei-info #nyellow_info").append("<span class=bgov></span>");
        });
        $(".right-side").on("mouseleave", "#nei-info.ug-user #ngreen_info, #nei-info.ug-user #nred_info, #nei-info.ug-user #nblue_info, #nei-info.ug-user #nyellow_info", function(e) {
            $(this).find(".bgov").remove();
        });

        // En2uition Slide
        $(".right-side").on("mouseover", "#en2-info.ug-user #en21_info", function(e) {
            var lclz = lang.guide.en2uition_slide;
			$("#dm-info, #user-info, #g-info, #que-info, #nei-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#en2-info").find(".ug_block").length == 0)
                $.fn.userGuide("#en2-info", lclz.heading, lclz.my_group_info, 106, -2, 0, -198, 0, 14, 0, 12.5, '99%', '15px');
            else
            {
                $("#en2-info").find(".ug_body p").html(lclz.my_group_info).css("margin-top", "0px");
                $("#en2-info").find(".ug_body p").css("background-position", "99% 15px");
                $("#en2-info").find(".ug_body").css("height", "106px");
            }
        });
        $(".right-side").on("mouseover", "#en2-info.ug-user #en22_info", function(e) {
            var lclz = lang.guide.en2uition_slide;
			$("#dm-info, #user-info, #g-info, #que-info, #nei-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#en2-info").find(".ug_block").length == 0)
                $.fn.userGuide("#en2-info", lclz.heading, lclz.curr_relationship, 81, -2, 0, -198, 0, 14, 25, 12.5, '99%', '50%');
            else
            {
                $("#en2-info").find(".ug_body p").html(lclz.curr_relationship).css("margin-top", "25px");
                $("#en2-info").find(".ug_body p").css("background-position", "99% 50%");
                $("#en2-info").find(".ug_body").css("height", "81px");
            }
        });
        $(".right-side").on("mouseover", "#en2-info.ug-user #en23_info", function(e) {
            var lclz = lang.guide.en2uition_slide;
			$("#dm-info, #user-info, #g-info, #que-info, #nei-info").find(".ug_block").animate({left: '-7px'}, 500, function() {
                $(this).remove();
            });
            if ($("#en2-info").find(".ug_block").length == 0)
                $.fn.userGuide("#en2-info", lclz.heading, lclz.past_relationship, 61, -2, 0, -198, 0, 14, 45, 12.5, '99%', '85%');
            else
            {
                $("#en2-info").find(".ug_body p").html(lclz.past_relationship).css("margin-top", "45px");
                $("#en2-info").find(".ug_body p").css("background-position", "99% 85%");
                $("#en2-info").find(".ug_body").css("height", "61px");
            }
        });

        // Slide Out
        $(".side-block").on("mouseleave", "", function(e) {
            e.preventDefault();
            e.stopPropagation();
            //$(this).find(".ug_block").animate({left: '-7px'}, 500, function() {
               // $(this).remove();
            //});
           // $(this).find(".bgov").remove();
        });
    });
    $.fn.userGuide = function(id, heading, info, height, t, r, l, il, ir, mt, font, arrowX, arrowY) {
        var info = '<div class="ug_block" style="top:' + t + 'px; right:' + r + 'px;"><div class=ug_iblock style="margin-right:' + ir + 'px; margin-left:' + il + 'px;">'
                + '<div class=ug_head>' + heading + '</div>'
                + '<div class=ug_body style="height:' + height + 'px;"><p class="' + (il > 0 ? "l" : "r") + '" style="margin-top:' + mt + 'px; font-size:' + font + 'px; background-position:' + arrowX + ' ' + arrowY + ';">' + info + '</p></div>'
                + '</div></div>';
        $(id).append(info);
        $(id).find(".ug_block").animate({left: l + 'px'}, {queue: false, duration: 500});
    };
});
