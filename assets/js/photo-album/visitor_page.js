var cur_ind = typeof cpsi !== 'undefined' ? parseInt(cpsi) + 1 : 1,
    album_request = false,
    photo_title_request = false,
    crequest = false,
    comments_request = false;

function updateElementsVisibility(pcount) {
    var $cbox = $(".comments.photo-page .comments-box");
    $cbox.find(".comment, .new-comment").remove();
    if (pcount > 0) {
        $(".no-photo-available").addClass('hidden');
        $("div.album-photo-carousel, #pagination").removeClass('hidden');
        $("#pagination").find(".variable_page_number").val(cur_ind);
        $("#pagination .paging").find('span').text(pcount);
    } else {
        $("div.album-photo-carousel, #pagination").addClass('hidden');
        $(".bridge-privacy").css('visibility', 'hidden');
        $(".no-photo-available").removeClass('hidden');
    }
}

function albumIdChanged(new_id) {
    abortAjaxRequest(comments_request);
    abortAjaxRequest(album_request);
    abortAjaxRequest(photo_title_request);
    crequest = false;
    album_request = reloadAlbumPhoto(new_id);
}

function reloadAlbumPhoto(new_id) {
    updateElementsVisibility(0);
    $(".no-photo-available").addClass('hidden');
    $("div.album-photo-carousel").removeClass('hidden');
    var spinner = '<img src="/assets/img/waiting.gif" alt="loading..." class="spinner"/>';
    $("#waterwheelCarousel").html(spinner);
    var request = $.ajax({
        url: '/photo/get_album_photos',
        type: 'post',
        data: {'album_id': new_id, 'visitor_id' : visitor_id},
        dataType: 'json',
        success: function(data) {
            if (data.count > 0) {
                var photos_html = new Array();
                $.each(data.photos, function(i, photo) {
                    var image = '<img src="' + photo.medium + '" data-realhref="' + photo.orig + '" id="alb-photo-' + photo.id + '" alt="" data-id="' + photo.id + '"/>';
                    photos_html.push(image);
                });
                $("#waterwheelCarousel").html(photos_html.join(''));
                initCarousel(data.count);
            } else {
                $("#waterwheelCarousel").empty();
                updateElementsVisibility(0);
            }
        },
        error: function(xhr, text_status, error_thrown) {
            if (text_status != "abort" && xhr.status !== 0) {
                $("#waterwheelCarousel").empty();
                alert('An error occurred while uploading photos.\r\nPlease reload the page.');
            }
        }
    });
    return request;
}

function loadPhotoDetails(id) {
    if($(".bridge-privacy .bridge-heading").hasClass('editmode')) {
        $(".bridge-privacy").find(".cancel-editmode").click();
    }
    $(".bridge-privacy").css('visibility', 'hidden');
    var request = $.ajax({
        url: '/photo/get_data',
        type: 'post',
        data: {'id':id},
        dataType: 'json',
        success: function(data) {
            $("#lnk-share-photo a").text(data.shared_text);
            $(".bridge-privacy .photo-title")
                .attr('data-id', data.id !== undefined ? data.id : 0)
                .text(data.title !== undefined ? data.title : '');
            var like_box = $(".photo-like-box");
            like_box.find('.like').attr('onclick', "vote(this, 'photo', 'like', "+id+");");
            like_box.find('.dislike').attr('onclick', "vote(this, 'photo', 'dislike', "+id+");");
            data.target = 'photo';
            data.id = id;
            updateVoteStats(like_box, data);
            $(".bridge-privacy").css('visibility', 'visible');
        },
        error: function (xhr, text_status, error_thrown) {
            if (text_status != "abort" && xhr.status !== 0) {
                requestFailed();
            }
        }
    });
    return request;
}

function loadPhotoComments(id, clear_box) {
    var spinner = $(".comments .wait"),
        discussing = $(".comments.photo-page .comments-box");
    if(discussing.length === 0)
        return false;
    var last_id = discussing.find(".comment").length ? discussing.find(".comment:last").attr('data-id') : -1;
    spinner.css('visibility', 'visible');

    if(clear_box) {
        discussing.find(".comment, .new-comment").remove();
        last_id = -1;
    }

    crequest = true;
    var request = $.ajax({
        url: '/comment/get',
        type: 'post',
        data: {
            'table'     : 'photo_comment',
            'target-id' : id,
            'last_id'   : last_id
        },
        dataType: 'json',
        success: function(data) {
            crequest = data.stop_request !== undefined ? data.stop_request : false;
            if (data.comments !== undefined) {
                data.id = id;
                data.target = 'photo_comment';
                displayComments(discussing, data);
            }
        },
        error: function (xhr, text_status, error_thrown) {
            crequest = false;
            if (text_status != "abort" && xhr.status !== 0) {
                requestFailed();
            }
        },
        complete: function() {
            spinner.css('visibility', 'hidden');
        }
    });
    return request;
}

function animCompleted() {
    var cur_photo = $("#waterwheelCarousel").find("img.carousel-center");
    if (cur_photo.length) {
        var id = cur_photo.attr('data-id');
        abortAjaxRequest(comments_request);
        abortAjaxRequest(photo_title_request);
        photo_title_request = loadPhotoDetails(id);
        crequest = false;
        comments_request = loadPhotoComments(id, true);
    }
}

function initCarousel(photo_count, full) {
    $("div.album-photo-carousel").removeClass('hidden');
    if(photo_count === undefined || photo_count === null) {
        photo_count = $("#waterwheelCarousel img").length;
    }
    if(full !== undefined && full === true) {
        $("#waterwheelCarousel img").attr('style', '');
    }

    if(cur_ind > photo_count) {
        cur_ind = 1;
    }

    var carousel = $("#waterwheelCarousel").waterwheelCarousel({
        opacityMultiplier: 0.25,
        forcedImageWidth: 673,
        forcedImageHeight: 423,
        startingItem: cur_ind,
        movedToCenter: function($newCenterItem) {
            $("#pagination").find(".variable_page_number").val($newCenterItem.index() + 1);
        },
        clickedCenter:function($newCenterItem) {
            $.magnificPopup.open({
                items: {
                    src: $newCenterItem.attr('data-realhref')
                },
                type: 'image'
            });
        }
    });

    $('.btn-right').unbind('click').click(function() { carousel.next(); });
    $('.btn-left').unbind('click').click(function() { carousel.prev(); });
    $('.btn-first').unbind('click').click(function() { carousel.first(); });
    $('.btn-last').unbind('click').click(function() { carousel.last(); });
    $('#waterwheelCarousel').unbind('mousewheel').mousewheel(function(event, delta, deltaX, deltaY) {
        if (delta > 0) {
            carousel.prev();
        }
        if (deltaY < 0) {
            carousel.next();
        }
        event.stopPropagation();
        event.preventDefault();
    });
    $("#pagination .variable_page_number").unbind('keypress').keypress(function(event) {
        if (event.which == 13 || event.keyCode == 13)
        {
            var index = parseInt($(this).val()) - 1;
            if(index >= 0) {
                carousel.moveTo(index);
            }
        }
    });

    updateElementsVisibility(photo_count);
}

$(function() {
    var alb_slider = $('.album-privacy-slider .slider').bxSlider({
        startSlide: typeof csi !== 'undefined' ? csi : 0,
        slideWidth: 550,
        minSlides: 1,
        maxSlides: 1,
        slideMargin: 0,
        moveSlides: 1,
        infiniteLoop: false,
        hideControlOnEnd: false,
        pager: false,
        mode: 'fade',
        speed: 100,
        onSliderLoad: function() {
            $(".album-privacy-slider").css("visibility", "visible");
        },
        onSlideBefore: function(slideElement) {
            albumIdChanged(slideElement.attr('data-id'));
        }
    });

    initCarousel();

    $('.album-privacy-slider').unbind('mousewheel').mousewheel(function(event, delta, deltaX, deltaY) {
        if (delta > 0) {
            alb_slider.goToPrevSlide();
        }
        if (deltaY < 0) {
            alb_slider.goToNextSlide();
        }
        event.stopPropagation();
        event.preventDefault();
    });

	var $scrollingDiv = $(".right-side");
	$(window).scroll(function(){
		if ($(window).scrollTop() < 498)
			$scrollingDiv.stop().animate({"marginTop": (0 - $(window).scrollTop()) + "px"}, 0 );
        else if ($scrollingDiv.css("MarginTop") != "-498px") {
            $scrollingDiv.css("marginTop", "-498px");
        }

        if($(window).scrollTop() + $(window).height() == $(document).height()) {
            if(crequest === false) {
                var cp = $("#waterwheelCarousel").find("img.carousel-center");
                comments_request = loadPhotoComments(cp.attr('data-id'), false);
            }
        }
	});
});
