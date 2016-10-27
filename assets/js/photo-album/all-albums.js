function setAlbumTitle(obj, new_val) {
    obj.find('.edit-input').remove();
    obj.find('.glyphicon.glyphicon-ok-circle')
            .attr('class', 'glyphicon glyphicon-pencil')
            .attr('onclick', '');
    obj.find('.cancel-editmode').remove();
    if(new_val !== undefined) {
        obj.find('span.album-title').text(new_val).show();
    } else {
        obj.find('span.album-title').show();
    }
    obj.removeClass('editmode');
}

function updateAlbumTitle(obj) {
    var block = $(obj).closest('.block-title'),
        album_id = block.attr('data-id'),
        title = $.trim(block.find('.edit-input').val());
    if(title == '') {
        setAlbumTitle(block);
        return false;
    }
    block.css("cursor", "wait");
    $.ajax({
        url: '/photo/update_album_title',
        type: 'post',
        dataType: 'json',
        data: {
            'id' : album_id,
            'title' : title
        },
        success: function(data) {
            if (data.errors !== undefined) {
                if(data.errors == '') {
                    setAlbumTitle(block, title);
                } else {
                    alert(data.errors);
                }
            }
        },
        error: function (xhr, text_status, error_thrown) { if (text_status != "abort" && xhr.status !== 0) { requestFailed(); } },
        complete: function() {
            block.css("cursor", "default");
        }
    });
}

$(function() {
    
    $(document).on('click', ".cancel-editmode", function(){
        var block = $(this).closest('.block-title');
        setAlbumTitle(block);
    });
    
    $(".albums-cover .block-content").mouseover(function() {
        $(this).find('.under-content').css('visibility', 'visible');
        $(this).find('img').addClass('opacity_05');
    }).mouseout(function() {
        $(this).find('.under-content').css('visibility', 'hidden');
        $(this).find('img').removeClass('opacity_05');
    });
    
    $(".albums-cover .block-title").mouseover(function() {
        $(this).find('.glyphicon').css('visibility', 'visible');
    }).mouseout(function() {
        if(!$(this).hasClass('editmode')) {
            $(this).find('.glyphicon').css('visibility', 'hidden');
        }
    }).unbind('click').click(function() {
        if(!$(this).hasClass('editmode')) {
            $(this).addClass('editmode');
            var title = $(this).find('span.album-title');
            title.hide();
            $(this).find('.glyphicon')
                   .attr({'class' : 'glyphicon glyphicon-ok-circle', 'onclick' : 'updateAlbumTitle(this);'});
            $('<span/>')
                    .attr('class', 'glyphicon glyphicon-remove-circle cancel-editmode')
                    .appendTo($(this));
            $('<input type="text" class="edit-input"/>')
                    .val($.trim(title.text()))
                    .appendTo($(this));
        }
    });
        
    $('.album-img').each(function() {
        var $this = $(this);
        var slider = $(this).find('.slider').bxSlider({
            slideWidth: 229,
            minSlides: 3,
            maxSlides: 3,
            slideMargin: 5,
            moveSlides: 1,
            infiniteLoop: false,
            hideControlOnEnd: true,
            pager: false,
            speed: 100,
            onSliderLoad: function() {
                $this.css("visibility", "visible");
            }
        });
        
        $this.mousewheel(function(event, delta, deltaX, deltaY) {
            if (delta > 0) {
                slider.goToPrevSlide();
            }
            if (deltaY < 0) {
                slider.goToNextSlide();
            }
            event.stopPropagation();
            event.preventDefault();
        });
    });
});