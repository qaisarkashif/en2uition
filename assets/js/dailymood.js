function sliderReady() {
    $("[data-slider]").css('visibility', 'visible');
}

$(function() {
    $("[data-slider]").bind("slider:my_event", function(event, dm) {
        if ($("#dm-switch").attr('data-v') == "on") {
            var dmv = dm.value.toFixed(0);
            $("#dailymood").val(dmv);
            $.ajax({
                url: '/homepage/update_dailymood',
                type: "POST",
                data: "&dm_id=" + dmv
            });
        }
    });

    $("#dm-switch").click(function() {
        var thisVal = $("#dm-switch").attr('data-v') == "on" ? "off" : "on";
        $.ajax({
            url: '/homepage/toggle_dailymood',
            type: "POST",
            success: function() {
                $("#dm-switch").attr('data-v', thisVal).text($("#dm-switch").attr('data-text'+thisVal));
                $(".dragger").toggle();
            }
        });
    });
});