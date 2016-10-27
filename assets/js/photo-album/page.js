var cur_ind = typeof cpsi !== 'undefined' ? parseInt(cpsi) + 1 : 1,
    album_request = false,
    photo_title_request = false,
    crequest = false,
    comments_request = false;

function updatePrivacySelect(privacy_code) {
    $("#privacy .privacy_opts a").removeClass("active");
    $("#privacy .privacy_opts a.pr-" + privacy_code).addClass('active');
}

function updateElementsVisibility(pcount) {
    var $cbox = $(".comments.photo-page .comments-box");
    $cbox.find(".comment, .new-comment").remove();
    if (pcount > 0) {
        $(".no-photo-available").addClass('hidden');
        $("div.album-photo-carousel, #lnk-delete-photo, #lnk-share-photo, #pagination, #privacy").removeClass('hidden');
        $("#pagination").find(".variable_page_number").val(cur_ind);
        $("#pagination .paging").find('span').text(pcount);
        $(".bridge-privacy").css('visibility', 'visible');
    } else {
        $("div.album-photo-carousel, #lnk-delete-photo, #lnk-share-photo, #pagination, #privacy").addClass('hidden');
        $(".bridge-privacy").css('visibility', 'hidden');
        $(".no-photo-available").removeClass('hidden');
    }
}

function albumIdChanged(new_id) {
    $("#lnk-upload-photo").find('a').attr('href', '/photo/album-' + new_id + '/edit');
    $("#lnk-delete-album").find('a').attr('href', '/photo/album-' + new_id + '/delete');
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
        data: {'album_id': new_id},
        dataType: 'json',
        success: function(data) {
            if (data.count > 0) {
                var photos_html = new Array();
                $.each(data.photos, function(i, photo) {
                    var image = '<img src="' + photo.medium + '" id="alb-photo-' + photo.id + '" data-realhref="'+photo.orig+'"';
                    image += ' alt="" data-pr="' + photo.privacy_code + '" data-id="' + photo.id + '" data-shared="' + photo.shared + '"/>';
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

function setPhotoTitle(obj, new_val) {
    obj.find('.edit-input').remove();
    obj.find('.glyphicon.glyphicon-ok-circle')
            .attr('class', 'glyphicon glyphicon-pencil')
            .attr('onclick', '');
    obj.find('.cancel-editmode').remove();
    if(new_val !== undefined) {
        obj.find('.photo-title').text(new_val).show();
    } else {
        obj.find('.photo-title').show();
    }
    obj.removeClass('editmode');
}

function updatePhotoTitle(obj) {
    var block = $(obj).closest('.bridge-heading'),
        photo_id = block.find('.photo-title').attr('data-id'),
        title = $.trim(block.find('.edit-input').val());
    if(title == '') {
        setPhotoTitle(block);
        return false;
    }
    block.css("cursor", "wait");
    $.ajax({
        url: '/photo/title/update',
        type: 'post',
        dataType: 'json',
        data: {
            'id' : photo_id,
            'title' : title
        },
        success: function(data) {
            if (data.errors !== undefined) {
                if(data.errors == '') {
                    setPhotoTitle(block, title);
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) {if (text_status != "abort" && xhr.status !== 0) {requestFailed();}},
        complete: function() {
            block.css("cursor", "default");
        }
    });
}

function updatePhotoPrivacy(obj_id, id, privacy_code) {
    $.ajax({
        url: '/photo/privacy/update',
        type: 'post',
        data: {
            'id':id,
            'privacy_code':privacy_code
        },
        dataType: 'json',
        success: function(data) {
            if(data.errors !== undefined) {
                if($.trim(data.errors) == "") {
                    $('#' + obj_id).attr({
                        'data-pr' : privacy_code,
                        'data-shared' : 0
                    });
                    if(privacy_code == '') {
                        $("#lnk-share-photo a").text('Share photo');
                        $("#lnk-share-photo").removeClass('hidden');
                    } else {
                        $("#lnk-share-photo").addClass('hidden');
                    }
                    updatePrivacySelect(privacy_code);
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) {
            if (text_status != "abort" && xhr.status !== 0) {
                requestFailed();
            }
        }
    });
}

function deletePhoto(obj_id, id) {
    $.ajax({
       url: '/photo/delete',
       type: 'post',
       data: {'id':id},
       dataType: 'json',
       success: function(data) {
           if(data.errors !== undefined) {
               if($.trim(data.errors) == "") {
                   $('#' + obj_id).remove();
                   cur_ind = 1;
                   initCarousel(null, true);
               } else {
                   alert(data.errors);
               }
           }
       },
       error: function (xhr, text_status, error_thrown) {if (text_status != "abort" && xhr.status !== 0) {requestFailed();}}
    });
}

function sharePhoto(obj_id, photo_id, action) {
    $.ajax({
       url: '/photo/share',
       type: 'post',
       data: {
           'photo_id' : photo_id,
           'action'   : action
       },
       dataType: 'json',
       success: function(data) {
           if(data.errors !== undefined) {
               if($.trim(data.errors) == "") {
                   $('#' + obj_id).attr('data-shared', action == 'share' ? '1' : '0');
                   $("#lnk-share-photo a").text(data.btn_text);
               } else {
                   alert(data.errors);
               }
           }
       },
       error: function (xhr, text_status, error_thrown) {if (text_status != "abort" && xhr.status !== 0) {requestFailed();}}
    });
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
        var new_pr_code = cur_photo.attr('data-pr');
        if (!$("#privacy .privacy_opts a.pr-" + new_pr_code).hasClass('active')) {
            updatePrivacySelect(new_pr_code);
        }
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

    $('.btn-right').unbind('click').click(function() {carousel.next();});
    $('.btn-left').unbind('click').click(function() {carousel.prev();});
    $('.btn-first').unbind('click').click(function() {carousel.first();});
    $('.btn-last').unbind('click').click(function() {carousel.last();});
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

    $("#lnk-delete-album").click(function(e) {
        if(!confirm("You really want to delete this album?")) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    });

    $(document).on('click', '#lnk-delete-photo a, #lnk-share-photo a', function(event) {
        event.preventDefault();
        event.stopPropagation();
        var cur_photo = $("#waterwheelCarousel").find("img.carousel-center");
        if (cur_photo.length) {
             var photo_id = cur_photo.attr('data-id'),
                obj_id = cur_photo.attr('id');
            if($(this).closest('li').attr('id') == 'lnk-delete-photo') {
                deletePhoto(obj_id, photo_id);
            } else {
                var action = cur_photo.attr('data-shared') == '1' ? 'unshare' : 'share';
                sharePhoto(obj_id, photo_id, action);
            }
        }
    });

    $("#privacy .privacy_opts a").click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        var cur_photo = $("#waterwheelCarousel").find("img.carousel-center");
        if (cur_photo.length) {
            updatePhotoPrivacy(cur_photo.attr('id'), cur_photo.attr('data-id'), !$(this).hasClass('active') ? $(this).attr('data-pr') : '');
        }
    });

    $(document).on('click', ".cancel-editmode", function(){
        var block = $(this).closest('.bridge-heading');
        setPhotoTitle(block);
    });

    $(".bridge-privacy .bridge-heading").mouseover(function() {
        $(this).find('.glyphicon').css('visibility', 'visible');
    }).mouseout(function() {
        if(!$(this).hasClass('editmode')) {
            $(this).find('.glyphicon').css('visibility', 'hidden');
        }
    }).unbind('click').click(function() {
        if(!$(this).hasClass('editmode')) {
            $(this).addClass('editmode');
            var title = $(this).find('span.photo-title');
            title.hide();
            $(this).find('.glyphicon')
                    .attr('class', 'glyphicon glyphicon-ok-circle')
                    .attr('onclick', 'updatePhotoTitle(this);');
            $('<span/>')
                    .attr('class', 'glyphicon glyphicon-remove-circle cancel-editmode')
                    .appendTo($(this));
            $('<input type="text" class="edit-input"/>')
                    .val($.trim(title.text()))
                    .appendTo($(this));
        }
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