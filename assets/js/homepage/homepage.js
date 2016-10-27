var /*slider1 = null,
    slider2 = null,*/
    profiles_slider = null,
    profiles_request = false,
    comments_request = false,
    prosld_page = 1,
    prof_count = 0;

function loadComments() {
    var spinner = $(".homepage-comments .wait"),
        discussing = $(".homepage-comments .comments-box"),
        last_ids = {
            'profile_comment'  : -1,
            'photo_comment'    : -1,
            'topic_comment'    : -1,
            'photo'            : -1
        };
    $.each(last_ids, function(key, val) {
        if(discussing.find(".comment[data-target='"+key+"']").length) {
            var last_id = discussing
                    .find(".comment[data-target='"+key+"']:last")
                    .attr('data-id');
            last_ids[key] = last_id;
        }
    });
    spinner.removeClass('hide').css('visibility', 'visible');
    comments_request = true;
    $.ajax({
        url  : '/comment/get',
        type : 'post',
        data: {
            'last_ids' : last_ids
        },
        dataType: 'json',
        success: function(data) {
            comments_request = data.stop_request !== undefined ? data.stop_request : false;
            if (data.comments !== undefined) {
                displayComments(discussing, data, false);
                $(".magnific-popup").magnificPopup({
                    type: 'image'
                });
            }
        },
        error: function (xhr, text_status, error_thrown) {
            comments_request = false;
            if (text_status != "abort" && xhr.status !== 0) {
                requestFailed();
            }
        },
        complete: function() {
            spinner.css('visibility', 'hidden').addClass('hide');
        }
    });
}

function updateHomepageQuestionnaireRequests(arr) {
    var qtypes = {
        'past' : 0,
        'present' : 0
    };
    $.each(arr, function(i, item) {
        var opt = $.parseJSON(item.optional);
        qtypes[opt.qtype]++;
    });
    $(".req-past-counter .rpast-total")
        .text(qtypes.past)
        .css('visibility', qtypes.past > 0 ? 'visible' : 'hidden');
    $(".req-present-counter .rpresent-total")
        .text(qtypes.present)
        .css('visibility', qtypes.present > 0 ? 'visible' : 'hidden');
}

function initMouseWheelScroll(selector, slider) {
    $(selector).mousewheel(function(event, delta, deltaX, deltaY) {
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

function reloadProfiles(reset_page) {
    if(reset_page === undefined) {
        reset_page = true;
    }
    var selects = new Array('username', 'country', 'state', 'city', 'color', 'shape'),
        filters = {};
    $.each(selects, function(i, name) {
        var val = $("select[name='"+name+"']").val();
        if($.trim(val) != '') {
            filters[name] = $.trim(val);
        }
    });
    abortAjaxRequest(profiles_request);
    profiles_request = loadProfiles(filters, reset_page);
}

function loadProfiles(filters, reset_page) {
    $(".tpd-tooltip").remove();
    if(reset_page) {
        prosld_page = 0;
        $(".profiles-search").removeClass('stop-request');
    }
    if($(".profiles-search").hasClass('stop-request')) {
        return false;
    }
    filters.page = prosld_page;
    var request = $.ajax({
        'url' : '/homepage/search_users',
        'type' : 'post',
        'dataType' : 'json',
        'data' : filters,
        success: function(json) {
            var slider = $('.hp-sliders .profiles .slider'),
                my_id = $(":hidden[name=my_id]").val();
            if(reset_page) {
                slider.empty();
            }
            $.each(json.profiles, function(uid, data) {
                var slide = '<div class="slide"><a href="' + (data.uid == my_id ? '/profile' : '/visitor/uid-' + data.uid) + '">' +
                        '<img alt="pro-img" src="'+data.foto+'" title="'+data.username+'" class="username-tooltip"/></a></div>';
                slider.append(slide);
            });
            if(json.profiles.length > 0) {
                Tipped.create('.username-tooltip', { containment: 'viewport', position: 'bottom', inline: true });
                var curSlide = reset_page ? 0 : profiles_slider.getCurrentSlide();
                initProfileSliders(curSlide);
                prosld_page++;
            }
            if(json.stop_request !== undefined && json.stop_request) {
                $(".profiles-search").addClass('stop-request');
            } else {
                $(".profiles-search").removeClass('stop-request');
            }
        },
        error: function (xhr, text_status, error_thrown)
        {
            if (text_status != "abort" && xhr.status !== 0)
            {
                requestFailed();
            }
        }
    });
    return request;
}

function initProfileSliders(start_slide) {
    var options = {
        slideWidth: 155,
        minSlides: 6,
        maxSlides: 6,
        slideMargin: 6,
        moveSlides: 1,
        infiniteLoop: false,
        hideControlOnEnd: true,
        pager: false,
        speed: 100,
        startSlide: start_slide,
        onSliderLoad: function(){
            $(".hp-sliders .profiles").css("visibility", "visible");
            prof_count = $('.hp-sliders .profiles .slider .slide').length;
        },
        onSlideBefore : function() {
            $(".username-tooltip").mouseleave();
        },
        onSlideAfter: function($elem, oi, ni) {
            if(ni >= (prof_count - 6)) {
                reloadProfiles(false);
            }
        }
    };
    
    if(profiles_slider) {
        profiles_slider.reloadSlider(options);
    } else {
        var selector = '.hp-sliders .profiles .slider';
        profiles_slider = $(selector).bxSlider(options);
        initMouseWheelScroll(selector, profiles_slider);
    }
}

$(function() {
    Tipped.create('.username-tooltip', { containment: 'viewport', position: 'bottom', inline: true });
    loadComments();

    $(".magnific-popup").magnificPopup({
        type: 'image'
    });

    $(".trend-color").updown(0, 3, 0, colorSelection);
    $(".trend-shape").updown(0, 15, 0, shapeSelection);

    function colorSelection(target) {
        target.html(color[target.data("value")]);
        target.attr("id", colorid[target.data("value")]);
    }

    function shapeSelection(target) {
        target.html('Type<br/>'+shapeid[target.data("value")]);
        target.attr("id", shapeid[target.data("value")]);
    }

    $(".updown").click(function(e) {
        e.preventDefault();
        $("#shape-type img").removeClass();
        $("#shape-type img").attr("src", "/assets/img/shapes/" + ($(".trend-shape").attr("id").toLowerCase()) + ".png");
        $("#shape-type img").addClass("shape-" + $(".trend-color").attr("id").toLowerCase());
    });

    $(".tshape, .tcolor").mousewheel(function(event, delta, deltaX, deltaY) {
        if (delta > 0) {
            $(this).find(".updown_up").click();
        }
        if (deltaY < 0) {
            $(this).find(".updown_down").click();
        }
        event.stopPropagation();
        event.preventDefault();
    });

    $(".profiles-search select").select2({allowClear: true});

    $(".knob1, .knob2").knob({
        'readOnly': true,
        'draw': function() {
            $(this.i).closest("div[class^='req-']").css('visibility', 'visible');
            $(this.i).val(this.cv + '%');
        }
    });

    $("#user-info .pro-img").mouseenter(function() {
        $(".change-avatar").show();
    }).mouseleave(function() {
        $(".change-avatar").hide();
    });

    $("#frm-profile-pct input:file").change(function() {
        if (confirm("Upload a picture that you have chosen?")) {
            $("#frm-profile-pct").submit();
        }
    });

    /*slider1 = $('.hp-sliders .popular-topic .slider').bxSlider({
        slideWidth: 236,
        minSlides: 4,
        maxSlides: 4,
        slideMargin: 5,
        moveSlides: 1,
        infiniteLoop: false,
        hideControlOnEnd: true,
        pager: false,
        speed: 200,
        onSliderLoad: function(){
            $(".hp-sliders .popular-topic").css("visibility", "visible");
        }
    });
    initMouseWheelScroll('.hp-sliders .popular-topic .slider', slider1);

    slider2 = $('.hp-sliders .suggested-article .slider').bxSlider({
        slideWidth: 236,
        minSlides: 4,
        maxSlides: 4,
        slideMargin: 5,
        moveSlides: 1,
        infiniteLoop: false,
        hideControlOnEnd: true,
        pager: false,
        speed: 200,
        onSliderLoad: function(){
            $(".hp-sliders .suggested-article").css("visibility", "visible");
        }
    });
    initMouseWheelScroll('.hp-sliders .suggested-article .slider', slider2);*/
    
    initProfileSliders(0);
    
    $("#frm-profile-pct").submit(function(e) {
        e.preventDefault();

        var formData = new FormData($(this)[0]);
        $(".change-avatar").css('z-index', 0);
        $.ajax({
            url: '/profile/upload_profile_picture',
            type: 'POST',
            data: formData,
            dataType: 'json',
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.errors !== undefined) {
                    if (data.errors.length > 0) {
                        popupMessage("Profile photo:", '<h3 style="margin-top: 0; color: red;">Failed to upload a photo.</h3>' + data.errors.join(''));
                    } else {
                        var d = new Date();
                        var pro_img = $("div.pro-img .magnific-popup");
                        pro_img.attr('href', data.filepath_original + "?" + d.getTime());
                        pro_img.find('img').attr('src', data.filepath_preview + "?" + d.getTime());
                        showCropWindow(data.filepath_original + "?" + d.getTime());
                    }
                }
            },
            error: function (xhr, text_status, error_thrown) {
                if (text_status != "abort" && xhr.status !== 0) {
                    requestFailed();
                }
            },
            complete: function() {
                $(".change-avatar").css('z-index', 2);
            }
        });

        return false;
    });

    $(document).on('mouseover', '.tpd-tooltip', function() {
        $(this).remove();
    });

    $(".grp-descr").click(function(e) {
        e.preventDefault();
        var color = $(".trend-color").attr('id'),
            shape = $(".trend-shape").attr('id'),
            href = "/group-description" + ($.trim(color) && $.trim(shape) ? "/" + color + "/" + shape : "");
        window.location = href;
    });
	$(".show-profile").click(function(e) {
		$('.profiles').slideToggle({ direction: "up" }, 500);
    });

	var $scrollingDiv = $(".right-side, .left-side");
	$(window).scroll(function(){
        if($(window).scrollTop() + $(window).height() == $(document).height()) {
            if(comments_request === false) {
                loadComments();
            }
        }
	});
});