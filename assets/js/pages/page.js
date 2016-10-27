$(function() {
    $('ul.tabs li').click(function() {
        var tab_id = $(this).attr('data-tab');
        if ($(this).hasClass("active")) {
            $(this).removeClass('active');
            $('.fmenu #' + tab_id).fadeOut(400);
            return(false);
        }
        $('.fmenu #' + tab_id).slideDown(400).siblings().slideUp(400);
        $(this).addClass('active').siblings().removeClass('active');
    });

    $('.slider3').bxSlider({
        slideWidth: 420,
        minSlides: 2,
        maxSlides: 2,
        slideMargin: 30,
        infiniteLoop: false,
        hideControlOnEnd: false,
        moveSlides: 1,
        pager: false,
        speed: 200
    });
});