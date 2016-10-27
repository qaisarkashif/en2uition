var comments_request = false;

function loadComments() {
    if($(".profile-comments").length === 0 || $(".profile-comments").hasClass("no-friend")) {
        return false;
    }
    comments_request = true;
    var spinner = $(".profile-comments .wait"),
        discussing = $(".profile-comments .comments-box"),
        last_id = discussing.find(".comment").length ? discussing.find(".comment:last").attr('data-id') : -1;
    spinner.removeClass('hide').css('visibility', 'visible');

    $.ajax({
        url      : '/comment/get',
        type     : 'post',
        data     : {
            'table'     : 'profile_comment',
            'target-id' : $("#profile-id").val(),
            'last_id'   : last_id
        },
        dataType : 'json',
        success: function(data) {
            comments_request = data.stop_request !== undefined ? data.stop_request : false;
            if (data.comments !== undefined) {
                data.id = $("#profile-id").val();
                data.target = 'profile_comment';
                displayComments(discussing, data);
            }
        },
        error: function (xhr, text_status, error_thrown) {
            comments_request = false;
            if (text_status != "abort" && xhr.status !== 0) { requestFailed(); }
        },
        complete: function() {
            spinner.css('visibility', 'hidden').addClass('hide');
        }
    });
}

function initSlider(obj) {
    var slider = obj.bxSlider({
        slideWidth: 312,
        slideMargin: 2,
        infiniteLoop: false,
        hideControlOnEnd: true,
        pager: false,
        speed: 200,
        onSliderLoad: function() {
            obj.css("visibility", "visible");
        }
    });
    return slider;
}

function updateProfileQuestionnaireRequests(arr) {
    var qtypes = {
        'past' : new Array(),
        'present' : new Array()
    };
    $.each(arr, function(i, item) {
        var opt = $.parseJSON(item.optional);
        if(qtypes[opt.qtype][opt.lnum] === undefined) {
            qtypes[opt.qtype][opt.lnum] = 0;
        }
        qtypes[opt.qtype][opt.lnum]++;
    });
    $.each(qtypes, function(type, info) {
        $.each(info, function(lvl, count) {
            var li = $("#" + type + "-info").find("[data-lvl='" + lvl + "']");
            li
              .find('.level-req-count')
              .text(count)
              .css('visibility', count > 0 ? 'visible' : 'hidden');
        });
    });
}

function unFriend() {
    if(typeof visitor_id !== 'undefined') {
        var friend_name = $.trim($(".visitor-name").text());
        if(!confirm("You are about to unfriend "+friend_name+"?")) {
            return false;
        }
        $.ajax({
            'url' : '/unfriend',
            'type' : 'get',
            'data' : {'friend_id' : visitor_id},
            'dataType' : 'json',
            success: function(data) {
                if(data.errors !== undefined) {
                    if(data.errors == '') {
                        location.reload();
                    } else {
                        alert(data.errors);
                    }
                }
            },
            error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } }
        });
    }
}

function bindMouseWheelEvent(selector, slider) {
    $(selector).unbind('mousewheel').mousewheel(function(event, delta, deltaX, deltaY) {
        if (delta > 0) {
            slider.goToPrevSlide();
        }
        if (deltaY < 0) {
            slider.goToNextSlide();
        }
        event.stopPropagation();
        event.preventDefault();
    });
}

$(function() {
    Tipped.create('.username-tooltip', { target: 'mouse', position: 'top' });
    loadComments();
    var public_photo_slider = initSlider($('.profile-img .slider'));
    var friends_profile_slider = initSlider($('.friends .slider'));

    var quiz_slider = $('.questionnaire-slider .slider').bxSlider({
        slideWidth: 191,
        moveSlides:1,
        infiniteLoop: false,
        hideControlOnEnd: false,
        pager: false,
        mode: 'fade',
        speed: 0,
        onSlideBefore: function($slideElement) {
            var type = $slideElement.attr('data-v');
            if(type == "past") {
                $("#present-info").addClass('hide');
            } else {
                $("#past-info").addClass('hide');
            }
            $("#"+type+"-info").removeClass('hide');
        }
    });

    $(".magnific-popup").magnificPopup({
        type: 'image'
    });

    bindMouseWheelEvent('.questionnaire-slider', quiz_slider);
    bindMouseWheelEvent('.shared-photos-slider', public_photo_slider);
    bindMouseWheelEvent('.profile-friend-slider', friends_profile_slider);

    if(typeof visitor_id !== 'undefined') {
        $(".level-privacy a").click(function(e) {
            e.preventDefault();
            if($(this).hasClass('request')) {
                var choise = $(this).closest('.level-privacy').find('.profile-level-privacy .active');

                if(choise.length > 0) {

                    var optional = {
                        'qtype' : $(this).attr('data-qtype'),
                        'lnum'  : $(this).attr('data-lnum'),
                        'codes' : new Array()
                    };

                    choise.each(function() {
                        optional.codes.push($(this).attr('data-code'));
                    });

                    sendRequest('question_privacy', $(this).attr('data-uid'), optional);
                }
            } else {
                if($(this).hasClass('active')) {
                    $(this).removeClass('active');
                } else {
                    $(this).addClass('active');
                }
            }
        });

        $(".btn-message").click(function(e) {
            e.preventDefault();
            var modal = $("#sendMessageModal");
            modal.find(".btn-success").prop('disabled', false);
            modal.find(".modal-body textarea").val('');
            modal.modal('show');
        });

        $("#sendMessageModal .btn-success").click(function(e) {
            e.stopPropagation();
            var $this = $(this);
            $this.prop('disabled', true);
            $.ajax({
                url : '/message/send',
                type : 'post',
                dataType : 'json',
                data : {
                    'to_user' : $("#send_to_user").val(),
                    'msg_text' : $("#sendMessageModal .modal-body textarea").val()
                },
                success : function(data) {
                    if(data.errors !== undefined) {
                        if(data.success_text !== undefined) {
                            $("#sendMessageModal .modal-body textarea").val('');
                            $("#sendMessageModal").modal('hide');
                        } else {
                            alert(data.errors);
                        }
                    }
                },
                error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } },
                complete: function() { $this.prop('disabled', false); }
            });
        });
    }


	var $scrollingDiv = $(".right-side, .left-side");
	$(window).scroll(function(){
		if ($(window).scrollTop() < 245)
			$scrollingDiv.stop().animate({"marginTop": (0 - $(window).scrollTop()) + "px"}, 0 );
        else if ($scrollingDiv.css("marginTop") != "-245px") {
            $scrollingDiv.css("marginTop", "-245px");
        }

        if($(window).scrollTop() + $(window).height() == $(document).height()) {
            if(comments_request === false) {
                loadComments();
            }
        }

        console.log($scrollingDiv.css("marginTop"));
	});

});